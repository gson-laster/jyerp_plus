<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\produce\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\produce\model\Workcenter as WorkcenterModel;
use app\produce\model\Technology as TechnologyModel;
use app\produce\model\Procedure as ProcedureModel;


/**
 * 标准工序控制器
 * @package app\produce\admin
 */
class Procedure extends Admin
{
    /**
     * 标准工序列表
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('produce_procedure.create_time desc');
        // 数据列表
        $data_list = ProcedureModel::getList($map,$order);
             
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['produce_procedure.name' => '工艺名称','admin_user.nickname' => '建档人']) // 设置搜索框
            ->addOrder('produce_procedure.create_time') // 添加排序                     
            ->addFilter('produce_procedure.wc_id',WorkcenterModel::getTree())
            ->addFilter('produce_procedure.status',['0'=>'关闭','1'=>'启用'])
            ->addColumns([ // 批量添加数据列
                ['__INDEX__', '序号'], 
            	['name', '工序名称'],
            	['code', '工序代码'],
            	['wc_id', '所属车间', WorkcenterModel::getTree()],
            	['nickname', '建档人'],           	
            	['create_time', '建档时间','datetime'],
            	['status', '启用状态状态','switch','',['0'=>'关闭','1'=>'启用']],
            	['right_button', '操作', 'btn']
            ])
            ->addTopButtons('add,delete') // 批量添加顶部按钮
            ->addRightButtons('edit,delete')    
            
            ->setRowList($data_list) // 设置表格数据            
            ->fetch(); // 渲染模板
    }

    /**
     * 新增
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function add()
    {
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            
            // 验证
            $result = $this->validate($data, 'Procedure');
            if (true !== $result) $this->error($result);
			$data['uid']=UID;
            if ($result = ProcedureModel::create($data)) {
                // 记录行为
            	$details    = '详情：工序ID('.$result['id'].'),建档人ID('.$result['uid'].')';
                action_log('produce_procedure_add', 'produce_procedure', $result['id'], UID, $details);
                $this->success('新建成功', 'index');
            } else {
                $this->error('新建失败');
            }
        }
        
        $js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
   	$('#form_group_technology').after('<div class="form-group col-md-12 col-xs-12" id="form_group_select"><button class="btn btn-xs btn-info" type="button" id="select">选择工艺</button></div>');        		
        		
	$('#select').click(function(){
        	var technology = $("#technology").val();	 
	
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择工艺',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/produce/procedure/choose/technology/'+technology 
			});
	});    
});
        		var removeFromArray = function (arr, val) {
				    var index = $.inArray(val, arr);
				    if (index >= 0)
				        arr.splice(index, 1);
				    return arr;
				};

        		function delTechnology(obj,id){
    				var ids = $("#technology").val();
        			var idsArr=ids.split(",");   
	   				ids = removeFromArray(idsArr, id);       		
        			ids = idsArr.join(",");	       		    
        			$("#technology").val(ids);
        			$(obj).parents('tr').remove();
        			
    			}
            </script>
EOF;
        
        
        // 显示添加页面
        return ZBuilder::make('form')
        	->setPageTitle('新建工序')
        	->addGroup(
        			[
        				'添加工序' =>[	        						
	        						['text', 'name','工序名称'],
	        						['text', 'code','工序代码'],
	        						['select', 'wc_id','所属工作中心','',WorkcenterModel::getTree()],
	        						['radio', 'is_other', '是否外协','',['0'=>'否','1'=>'是'],0],
	        						['wangeditor', 'description', '工序描述'],
	        						['textarea', 'note', '备注'],
	        						['radio', 'status', '启用状态','',['0'=>'关闭','1'=>'启用'],1]
        			               ],
        				'添加工艺' =>[
        							['hidden', 'technology'],    						
        						   ]
        			])
			->setExtraJs($js)
            ->fetch();
    }   
    
    /**
     * 弹出工艺列表
     * @author 黄远东 <641435071@qq.com>
     */
    public function choose($technology = '')
    {
    	// 获取查询条件
    	$map = $this->getMap();
    	$map['produce_technology.status'] = 1; 
    	$map['produce_technology.id'] = ['not in',$technology];
    	$order = $this->getOrder();
    	// 数据列表
    	$data_list = TechnologyModel::getList($map,$order);
    
    	$js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
					
    if($('tbody tr:first').hasClass('table-empty')){
    	$('#pick').hide();
    }			
	$('#pick').click(function(){
			var chk = $('tbody .active');
    		var ids = '';

    		if($("#form_group_technology_name",parent.document).length>0){ 
				var html = '';	
	    		chk.each(function(){
	    			ids += $(this).find('.ids').val()+','; 
	    			html += '<tr><td>'+$.trim($(this).find('td').eq(1).text())+'</td><td>'+$.trim($(this).find('td').eq(2).text())+'</td><td>'+$.trim($(this).find('td').eq(3).text())+'</td><td>'+$.trim($(this).find('td').eq(4).text())+'</td><td><a href="javascript:;" onclick="delTechnology(this,\''+$.trim($(this).find('td').eq(1).text())+'\')">删除</a></td></tr>';	
	    			    			     			
	   			});	
			}else{    			
				var html = '<div class="form-group col-md-12 col-xs-12" id="form_group_technology_name"><table class="table table-bordered"><tbody><tr><td>工艺ID</td><td>工艺名称</td><td>工艺代码</td><td>建档人</td><td>操作</td></tr>';	
    		chk.each(function(){
    			ids += $(this).find('.ids').val()+','; 
    			
    			html += '<tr><td>'+$.trim($(this).find('td').eq(1).text())+'</td><td>'+$.trim($(this).find('td').eq(2).text())+'</td><td>'+$.trim($(this).find('td').eq(3).text())+'</td><td>'+$.trim($(this).find('td').eq(4).text())+'</td><td><a href="javascript:;" onclick="delTechnology(this,\''+$.trim($(this).find('td').eq(1).text())+'\')">删除</a></td></tr>';	
    			    			     			
   			});	
    		html += '</tbody></table></div>';	    
			}
    		
    		ids = ids.slice(0,-1);	
    		
    		if(ids){
	    		var technology = $("#technology",parent.document).val();
	    		if(technology){
	    			ids = technology+','+ids;
	    		}    			    		    			
	    		var idsArr=ids.split(",");   
	   			idsArr.sort();
	    		idsArr = $.unique(idsArr);
	   			ids = idsArr.join(",");	
	    				
				$("#technology",parent.document).val(ids);
    			if($("#form_group_technology_name",parent.document).length>0){
     				$("#form_group_technology_name tbody",parent.document).append(html);
    
    			}else{
    				$("#form_group_select",parent.document).after(html);
    			}
	        	
    		}				 		
			//当你在iframe页面关闭自身时
			var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
			parent.layer.close(index); //再执行关闭
	});
});
            </script>
EOF;
    	$btn_pick = [
    			'title' => '选择',
    			'icon'  => 'fa fa-plus-circle',
    			'class' => 'btn btn-xs btn-success',
    			'id' => 'pick'
    	];

    	return ZBuilder::make('table')
    	->setSearch(['produce_technology.name' => '工艺名称','admin_user.nickname' => '建档人']) // 设置搜索框
    	->addOrder('produce_technology.create_time') // 添加排序
    	->addColumns([ // 批量添加数据列
    			['id', 'ID'],
    			['name', '工艺名称'],
    			['code', '工艺代码'],
    			['nickname', '建档人'],
    			['create_time', '建档时间','datetime'],
    			['status', '启用状态状态',['0'=>'关闭','1'=>'启用']],
    	])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js)
    	->addTopButton('pick', $btn_pick)
    	->assign('empty_tips', '暂无工艺')
    	->fetch('choose'); // 渲染页面
    }

    /**
     * 编辑
     * @param null $id id
     * @author 黄远东<6414350717@qq.com>
     * @return mixed
     */
    public function edit($id = null)
    {
        if ($id === null) $this->error('缺少参数');

        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            
            // 验证
            $result = $this->validate($data, 'Procedure');
            if (true !== $result) $this->error($result);

            if (ProcedureModel::update($data)) {
                // 记录行为
            	$details    = '详情：工序ID('.$data['id'].'),修改人ID('.UID.')';
                action_log('produce_procedure_edit', 'produce_procedure', $id, UID, $details);
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }
        
        $js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
   	$('#form_group_technology').after('<div class="form-group col-md-12 col-xs-12" id="form_group_select"><button class="btn btn-xs btn-info" type="button" id="select">选择工艺</button></div>');        		
	 
     var technology = $("#technology").val();	        		
     $.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/produce/procedure/tech/technology/"+technology,
			success: function(data){
        		$("#form_group_select",parent.document).after(data);				
			}
		});
        		
        		
	$('#select').click(function(){
        	var technology = $("#technology").val();	 
	
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择通知单位',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/produce/procedure/choose/technology/'+technology 
			});
	});    
});
        		var removeFromArray = function (arr, val) {
				    var index = $.inArray(val, arr);
				    if (index >= 0)
				        arr.splice(index, 1);
				    return arr;
				};

        		function delTechnology(obj,id){
    				var ids = $("#technology").val();
        			var idsArr=ids.split(",");   
	   				ids = removeFromArray(idsArr, id);       		
        			ids = idsArr.join(",");	       		    
        			$("#technology").val(ids);
        			$(obj).parents('tr').remove();
        			
    			}
            </script>
EOF;

        $data_list = ProcedureModel::getOne($id);
        // 显示编辑页面
        return ZBuilder::make('form')   
        	->setPageTitle('修改工序')
            ->addGroup(
        			[
        				'编辑工序' =>[	  
        							['hidden', 'id'],
	        						['text', 'name','工序名称'],
	        						['text', 'code','工序代码'],
	        						['select', 'wc_id','所属工作中心','',WorkcenterModel::getTree()],
	        						['radio', 'is_other', '是否外协','',['0'=>'否','1'=>'是'],0],
	        						['wangeditor', 'description', '工序描述'],
	        						['textarea', 'note', '备注'],
	        						['radio', 'status', '启用状态','',['0'=>'关闭','1'=>'启用'],1]
        			               ],
        				'编辑工艺' =>[
        							['hidden', 'technology'],

        						   ]
        			]) 
            ->setFormData($data_list)
            ->setExtraJs($js)
            ->fetch();
    }

    /**
     * 编辑生成工艺表格
     * @param array $record 行为日志
     * @author 黄远东<641435071@qq.com>
     * @return mixed
     */
    public function tech($technology = '')
    {    	
    	if($technology == '') {
    		$html = '';
    	}else{
    		$map = ['produce_technology.id'=>['in',($technology)]];
    		$order = '';
    		$data = $data_list = TechnologyModel::getList($map,$order);
    		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_technology_name"><table class="table table-bordered"><tbody><tr><td>工艺ID</td><td>工艺名称</td><td>工艺代码</td><td>建档人</td><td>操作</td></tr>';
    		foreach ($data as $k => $v){
    			$html.='<tr><td>'.$v['id'].'</td><td>'.$v['name'].'</td><td>'.$v['code'].'</td><td>'.$v['nickname'].'</td><td><a href="javascript:;" onclick="delTechnology(this,\''.$v['id'].'\')">删除</a></td></tr>';
    		
    		}
    		$html .= '</tbody></table></div>';
    		
    	}
    	return $html;
    }
    
    /**
     * 删除
     * @param array $record 行为日志
     * @author 黄远东<641435071@qq.com>
     * @return mixed
     */
    public function delete($record = [])
    {
   		$ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    	// 删除节点
    	if (ProcedureModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		$details = '工序ID('.$ids.'),操作人ID('.UID.')';
    		action_log('produce_procedure_delete', 'produce_procedure', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }
    
    /*
     
     * 等级*/
	public function level(){
		
    	return $this -> fetch();
		
	}

}