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
use app\produce\model\Materials as MaterialsModel;
use app\produce\model\MaterialsList as MaterialsListModel;
use app\produce\model\TechnologyLine as TechnologyLineModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
/**
 * 物料清单控制器
 * @package app\produce\admin
 */
class Materials extends Admin
{
    /**
     * 工作中心列表
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('produce_materials.create_time desc');
        // 数据列表
        $data_list = MaterialsModel::getList($map,$order);
             
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['produce_materials.code' => 'BOM编号']) // 设置搜索框
            ->addFilter('produce_materials.status',['0'=>'关闭','1'=>'启用'])
            ->addColumns([ // 批量添加数据列
                ['__INDEX__', '序号'], 
            	['code', 'BOM编号'],
            	['type', 'BOM类型',['0'=>'工程BOM','1'=>'生产BOM','2'=>'销售BOM','3'=>'成本BOM']],
            	['name', '主题'],
            	['version', '版本'],
            	['technology_line', '工艺路线',TechnologyLineModel::getTree()],
            	['create_time', '建档时间','datetime'],
            	['status', '启用状态','status','',['0'=>'关闭','1'=>'启用']],
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
            $result = $this->validate($data, 'Materials');
            if (true !== $result) $this->error($result);
			$data['uid']=UID;
            if ($results = MaterialsModel::create($data)) {
            	foreach($data['mid'] as $k => $v){
            		$info = array();
            		$info = [
            				'pmid'=>$results['id'],
            				'smid'=>$v,
            				'loss_rate'=>$data['loss_rate'][$k],
            				'quota'=>$data['quota'][$k],
            				'is_key'=>$data['is_key'][$k],
            				'status'=>$data['mstatus'][$k],
            				'note'=>$data['mnote'][$k]
            		];
            		MaterialsListModel::create($info);            	
            	}
            	
                // 记录行为
            	$details    = '物料清单：ID('.$results['id'].'),建档人ID('.$results['uid'].')';
                action_log('produce_waterials_add', 'produce_waterials', $results['id'], UID, $details);     
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }
        
        $js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
    $('#form_group_materials_list').after('<div class="form-group col-md-12 col-xs-12" id="form_group_select"><button class="btn btn-xs btn-info" type="button" id="select">选择子件</button></div>');       
	$('#pid_name').click(function(){
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择父件',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/produce/materials/choose_pm'
			});		 
	});	
    $('#technology_line_name').click(function(){
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择工艺路线',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/produce/materials/choose_line'
			});		 
	});	
        		
   $('#select').click(function(){
        	var materials = $("#materials_list").val();	 
	
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择子件',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/produce/materials/choose_materials/materials/'+materials 
			});
	});   
});
        		var removeFromArray = function (arr, val) {
				    var index = $.inArray(val, arr);
				    if (index >= 0)
				        arr.splice(index, 1);
				    return arr;
				};

        		function delMaterials(obj,id){
    				var ids = $("#materials_list").val();
        			var idsArr=ids.split(",");   
	   				ids = removeFromArray(idsArr, id);       		
        			ids = idsArr.join(",");	       		    
        			$("#materials_list").val(ids);
        			$(obj).parents('tr').remove();
        			
    			}
            </script>
EOF;
      
        // 显示添加页面
        return ZBuilder::make('form')
        	->setPageTitle('新建物料清单')
        	->addGroup(
        			[
        				'物料清单信息' =>[
        							['hidden', 'technology_line'],
        							['hidden', 'pid'],
			            			['text', 'code','BOM编号'],
			            			['select', 'type','BOM类型','',['0'=>'工程BOM','1'=>'生产BOM','2'=>'销售BOM','3'=>'成本BOM'],0],
			            			['text', 'name','主题'],
			            			['text','version', '版本'],
			            			['text','technology_line_name', '工艺路线'],
        							['text','pid_name', '父件','请选择父件'],
									['radio', 'status', '启用状态','',['0'=>'关闭','1'=>'启用'],1],
			            			['textarea', 'note', '备注'],    
        				],
        				'清单明细' =>[
        					['hidden', 'materials_list'],
        					
        				]
        			])
			->setExtraJs($js)
            ->fetch();
    } 
    
    /**
     * 弹出工艺列表
     * @author 黄远东 <641435071@qq.com>
     */
    public function choose_materials($materials = '')
    {
    	// 查询
    	$map = $this->getMap();
    	$map['status'] = 1;
    	$map['id'] = ['not in',$materials];
    	// 排序
    	$order = $this->getOrder('create_time desc');
    	// 数据列表
    	$data_list = MaterialModel::getList($map,$order);
    
    	$js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
			
    if($('tbody tr:first').hasClass('table-empty')){
    	$('#pick').hide();
    }
	$('#pick').click(function(){
			var chk = $('tbody .active');
    		var ids = '';
    
    		if($("#form_group_materials_name",parent.document).length>0){
				var html = '';
	    		chk.each(function(){
	    			ids += $(this).find('.ids').val()+',';
	    			html += '<tr><td>'+$.trim($(this).find('td').eq(2).text())+'</td><td>'+$.trim($(this).find('td').eq(6).text())+'</td><td>'+$.trim($(this).find('td').eq(5).text())+'</td><td>定额</td><td>损耗率</td><td>是否关键件</td><td>使用状态</td><td>备注</td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim($(this).find('td').eq(1).text())+'\')">删除</a></td></tr>';
	    			    
	   			});
			}else{
				var html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>子件名称</td><td>规格</td><td>单位</td><td>定额</td><td>损耗率</td><td>是否关键件</td><td>使用状态</td><td>备注</td><td>操作</td></tr>';
    		chk.each(function(){
    			ids += $(this).find('.ids').val()+',';
    
    			html += '<tr><input type="hidden" name="mid[]" value="'+$.trim($(this).find('td').eq(1).text())+'"><td>'+$.trim($(this).find('td').eq(2).text())+'</td><td>'+$.trim($(this).find('td').eq(6).text())+'</td><td>'+$.trim($(this).find('td').eq(5).text())+'</td><td><input type="text" name="quota[]"></td><td><input type="number" name="loss_rate[]"></td><td><select name="is_key[]"><option value="1">是</option><option value="0">否</option></select></td><td><select name="mstatus[]"><option value="1">启用</option><option value="0">停用</option></select></td><td><input type="text" name="mnote[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim($(this).find('td').eq(1).text())+'\')">删除</a></td></tr>';
    
   			});
    		html += '</tbody></table></div>';
			}
    
    		ids = ids.slice(0,-1);
    
    		if(ids){
	    		var materials = $("#materials_list",parent.document).val();
	    		if(materials){
	    			ids = materials+','+ids;
	    		}
	    		var idsArr=ids.split(",");
	   			idsArr.sort();
	    		idsArr = $.unique(idsArr);
	   			ids = idsArr.join(",");
	    
				$("#materials_list",parent.document).val(ids);
    			if($("#form_group_materials_name",parent.document).length>0){
     				$("#form_group_materials_name tbody",parent.document).append(html);
    
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
    
    	// 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['name' => '物品名称']) // 设置搜索框
            ->addOrder('id,create_time') // 添加排序
            ->addFilter('type',MaterialTypeModel::getTree())
            ->setPageTitle('选择子件')
            ->addColumns([ // 批量添加数据列
                ['id', '编号'], 
            	['name', '物品名称'],
            	['code', '物品编号'],
            	['type', '物品类型',MaterialTypeModel::getTree()],
            	['unit', '单位'],
            	['version', '规格型号',],
            	['price_tax', '含税售价'],
            	['color', '颜色'],
            	['brand', '品牌'],
            	['status', '启用状态', 'status'],
            ])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js)
    	->addTopButton('pick', $btn_pick)
    	->assign('empty_tips', '暂无数据')
    	->fetch('choose'); // 渲染页面
    }

    /**
     * 选择工艺路线
     * @author 黄远东 <641435071@qq.com>
     */
    public function choose_line()
    {
    	// 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('produce_technology_line.create_time desc');
        // 数据列表
        $data_list = TechnologyLineModel::getList($map,$order);
    	
    
    	$js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
	$('.table-builder input:checkbox').click(function(){	
			var technology_line = $(this).val();
        	var technology_line_name = $.trim($(this).parents('tr').find('td').eq(2).text());
			$("#technology_line",parent.document).val(technology_line);
        	$("#technology_line_name",parent.document).val(technology_line_name);
			//当你在iframe页面关闭自身时
			var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
			parent.layer.close(index); //再执行关闭
	});
});
            </script>
EOF;
    
    	return ZBuilder::make('table')
            ->setSearch(['produce_technology_line.name' => '工艺路线名称','admin_user.nickname' => '建档人']) // 设置搜索框
            ->addOrder('produce_technology_line.create_time') // 添加排序                     
            ->addFilter('produce_technology_line.status',['0'=>'关闭','1'=>'启用'])
            ->setPageTitle('选择工艺路线')
            ->addColumns([ // 批量添加数据列
                ['__INDEX__', '序号'], 
            	['name', '工艺路线名称'],
            	['code', '工艺路线代码'],
            	['is_main', '是否主打工艺','status','',['0'=>'否','1'=>'是']],
            	['good_name', '物品'],
            	['nickname', '建档人'],           	
            	['create_time', '建档时间','datetime'],
            	['status', '启用状态状态','switch','',['0'=>'关闭','1'=>'启用']],
            ])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js)
    	->assign('empty_tips', '暂无数据')
    	->fetch('choose'); // 渲染页面
    }
    
    /**
     * 选择工艺路线
     * @author 黄远东 <641435071@qq.com>
     */
    public function choose_pm()
    {
    	// 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('create_time desc');
        // 数据列表
        $data_list = MaterialModel::getList($map,$order);
    	 
    
    	$js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
	$('.table-builder input:checkbox').click(function(){
			var pid = $(this).val();
        	var pid_name = $.trim($(this).parents('tr').find('td').eq(2).text());
			$("#pid",parent.document).val(pid);
        	$("#pid_name",parent.document).val(pid_name);
			//当你在iframe页面关闭自身时
			var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
			parent.layer.close(index); //再执行关闭
	});
});
            </script>
EOF;
    
    	// 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['name' => '物品名称']) // 设置搜索框
            ->addOrder('id,create_time') // 添加排序
            ->addFilter('type',MaterialTypeModel::getTree())
            ->setPageTitle('选择父件')
            ->addColumns([ // 批量添加数据列
                ['__INDEX__', '序号'], 
            	['name', '物品名称'],
            	['code', '物品编号'],
            	['type', '物品类型',MaterialTypeModel::getTree()],
            	['unit', '单位'],
            	['version', '规格型号',],
            	['price_tax', '含税售价'],
            	['color', '颜色'],
            	['brand', '品牌'],
            	['status', '启用状态', 'status'],
            ])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js)
    	->assign('empty_tips', '暂无数据')
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
            $result = $this->validate($data, 'Workcenter');
            if (true !== $result) $this->error($result);

            if (WorkcenterModel::update($data)) {
                // 记录行为
            	$details    = '详情：中心ID('.$data['id'].'),修改人ID('.UID.')';
                action_log('produce_workcenter_edit', 'produce_workcenter', $id, UID, $details);
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }
        
        $js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
	$('#nickname').click(function(){
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择用户',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/produce/workcenter/choose'
			});
	});
});
            </script>
EOF;

        $data_list = WorkcenterModel::getOne($id);
        // 显示编辑页面
        return ZBuilder::make('form')   
        	->setPageTitle('修改工作中心')
            ->addFormItems([
						['hidden', 'id'],
						['hidden', 'header'],            		
            			['text', 'name','工作中心名称'],
            			['text', 'code','工作中心编号'],
            			['text', 'nickname','选择中心责任人'],
						['select', 'org_id','所属部门','',OrganizationModel::getTree()],
            			['wangeditor', 'description', '车间描述'],
            			['textarea', 'note', '备注'],
            			['radio', 'is_key', '是否为关键中心','',['0'=>'否','1'=>'是'],0],
						['radio', 'status', '启用状态','',['0'=>'关闭','1'=>'启用'],1],								
				])
            ->setFormData($data_list)
            ->setExtraJs($js)
            ->fetch();
    }
    
    /**
     * 弹出用户列表
     * @author 黄远东 <641435071@qq.com>
     */
    public function choose()
    {
    	// 获取查询条件
    	$map = $this->getMap();
    	$order = $this->getOrder();
    	// 数据列表   	 
    	$data_list = UserModel::where($map)->order($order)->paginate();    
    	// 分页数据
    	$page = $data_list->render();
    
    	$js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
	$('.table-builder input:checkbox').click(function(){
			var uid = $(this).val();
        	var nickname = $.trim($(this).parents('tr').find('td').eq(3).text());
			$("#header",parent.document).val(uid);
        	$("#nickname",parent.document).val(nickname);
			//当你在iframe页面关闭自身时
			var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
			parent.layer.close(index); //再执行关闭
	});
});
            </script>
EOF;
    
    	// 使用ZBuilder快速创建数据表格
    	return ZBuilder::make('table')
    	->setTableName('admin_user') // 设置数据表名
    	->setSearch(['id' => 'ID', 'username' => '用户名', 'nickname' => '姓名']) // 设置搜索参数
    	->addOrder('id,role,organization,position,is_on')
    	->addFilter('role', RoleModel::getTree2())
    	->addFilter('organization', OrganizationModel::getTree(null, false))
    	->addFilter('position', PositionModel::getTree(null, false))
    	->addFilter('is_on', ['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职'])
    	->addColumns([ // 批量添加列
    			['id', 'ID'],
    			['username', '用户名'],
    			['nickname', '姓名'],
    			['role', '角色', RoleModel::getTree2()],
    			['organization', '部门', OrganizationModel::getTree()],
    			['position', '职位', PositionModel::getTree()],
    			['create_time', '创建时间', 'datetime'],
    			['is_on', '在职状态',['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职']],
    	])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js)
    	->assign('empty_tips', '暂无需要添加证件的用户')
    	->fetch('choose'); // 渲染页面
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
    	if (MaterialsModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		$details = '物料清单ID('.$ids.'),操作人ID('.UID.')';
    		action_log('produce_materials_delete', 'produce_materials', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }

}