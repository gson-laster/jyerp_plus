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
use app\produce\model\Plan as PlanModel;
use app\produce\model\PlanList as PlanListModel;
use app\produce\model\Mateplan as MateplanModel;
use app\produce\model\MateplanList as MateplanListModel;
use app\user\model\User as UserModel;
use app\user\model\Role as RoleModel;
use app\user\model\Organization as OrganizationModel;
use app\user\model\Position as PositionModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\tender\model\Materialsdetail as MaterialsdetailModel;
use app\tender\model\Materials as MaterialsModel;
/**
 * 物料需求计划控制器
 * @package app\produce\admin
 */
class Mateplan extends Admin
{
    /**
     * 物料需求计划列表
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('produce_mateplan.create_time desc');
        // 数据列表
        $data_list = MateplanModel::getList($map,$order);
        $task_list = [
						'title' => '查看详情',
						'icon' => 'fa fa-fw fa-eye',
						'href' => url('task_list',['id'=>'__id__'])
						];         
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['produce_mateplan.code' => '单据编号']) // 设置搜索框
            ->addColumns([ // 批量添加数据列
                ['__INDEX__', '序号'], 
            	['code', '单据编号'],
            	['name', '主题'],
            	['bname', '负责人'],
            	['org_id', '部门',OrganizationModel::getTree()],
            	['plan_name', '主生产计划'],
            	['nickname', '制单人'],
            	['create_time', '制单时间','datetime'],
				['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
            	['right_button', '操作', 'btn']
            ])
            ->addTopButtons('add,delete') // 批量添加顶部按钮
            ->addRightButtons('delete')   
			 ->addRightButton('task_list',$task_list,true)  
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
            $result = $this->validate($data, 'Mateplan');
            if (true !== $result) $this->error($result);
			$data['uid']=UID;
			$data['code'] = 'WLXQ'.date('YmdHis',time());
            if ($results = MateplanModel::create($data)) {
            	foreach($data['mid'] as $k => $v){
            		$info = array();
            		$info = [
            				'pmid'=>$results['id'],
            				'smid'=>$v,
            				'sjsl'=>$data['sjsl'][$k]
            		];
            		MateplanListModel::create($info);            	
            	}
            	flow_detail($data['name'],'produce_mateplan','produce_mateplan','produce/mateplan/task_list',$results['id']);
                // 记录行为
            	$details    = '物料需求计划：ID('.$results['id'].'),建档人ID('.$results['uid'].')';
                action_log('produce_mateplan_add', 'produce_mateplan', $results['id'], UID, $details);        
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }
        
        $js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {

	  
	$('#plan_name').click(function(){
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择主生产计划',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/produce/mateplan/choose_plan'
			});				
	});		
	$('#header_name').click(function(){
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择负责人',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/produce/mateplan/choose_header'
			});		 
	});	
	
});
            </script>
EOF;
      
        // 显示添加页面
        return ZBuilder::make('form')
        	->setPageTitle('新建物料需求计划')
        	->addGroup(
        			[
        				'新建物料需求计划' =>[
							['hidden', 'plan_id'],
							['hidden', 'obj_id'],
							['hidden', 'header'],
							['hidden', 'org_id'],
							['text', 'name','主题'],
							['text','plan_name', '主生产计划'],
							['text','header_name', '负责人'],
							['text','org_name', '部门'],
							['files','enclosure', '附件'],
							['textarea', 'note', '备注'],    
        				],
        				'新建物料需求明细' =>[
        					['hidden', 'materials_list'],     					
        				]
        			])
			->setExtraJs($js)
			->js('mateplan')
            ->fetch();
    } 
        
	public function get_obj($obj_id = ''){
		if($obj_id == ''){
			return $html='<span>请选择生产计划</span>';
		}
		$materialsid = MaterialsModel::where('obj_id',$obj_id)->value('id');
		$map = ['pid'=>$materialsid];
		$data = MaterialsModel::getDetail($map);
		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>材料需用数量</td><td>库存数量</td><td>预计采购数量</td><td>实际采购数量</td></tr>';
    		foreach ($data as $k => $v){ 
				$v['number'] = $v['number'] ? $v['number'] : 0;
				$i = $v['xysl'] - $v['number'];
				$i = $i<0 ? 0 : $i; 
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['itemsid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['xysl'].'</td><td>'.$v['number'].'</td><td>'.$i.'</td><td><input type="number" name="sjsl[]"></td></tr>';
    		}   		
    		$html .= '</tbody></table></div>';
		return $html;
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
            $result = $this->validate($data, 'Mateplan');
            if (true !== $result) $this->error($result);
			
            if (MateplanModel::update($data)) {
            	
            	$mlist = explode(',',$data['mateplan_list']);
            	$oldmlist = explode(',',$data['old_mateplan_list']);
            	$dif = array_diff($oldmlist,$mlist);
            	MateplanListModel::where(['smid'=>['in',$dif],'pmid'=>$id])->delete();
            	
            	foreach($data['mid'] as $k => $v){
            		$info = array();
            		if($data['mlid'][$k]){
            			$info = [
            					'id'=>$data['mlid'][$k],
            					'smid'=>$v,
            					'mao_num'=>$data['mao_num'][$k],
            					'plan_num'=>$data['plan_num'][$k],
            					'plan_time'=>$data['plan_time'][$k],
            					'source'=>$data['source'][$k],
            					'note'=>$data['mnote'][$k]
            			];
            			MateplanListModel::update($info);
            		}else{         			
            			$info = [
            					'pmid'=>$data['id'],
            					'smid'=>$v,
            					'mao_num'=>$data['mao_num'][$k],
            					'plan_num'=>$data['plan_num'][$k],
            					'plan_time'=>$data['plan_time'][$k],
            					'source'=>$data['source'][$k],
            					'note'=>$data['mnote'][$k]
            			];
            			MateplanListModel::create($info);
            		}		
            	}
                // 记录行为
            	$details    = '详情：物料需求计划ID('.$data['id'].'),修改人ID('.UID.')';
                action_log('produce_mateplan_edit', 'produce_mateplan', $id, UID, $details);
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }
        
        $js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
    $('#form_group_mateplan_list').after('<div class="form-group col-md-12 col-xs-12" id="form_group_select"><button class="btn btn-xs btn-info" type="button" id="select">选择明细</button></div>');       
	var pmid = $("#id").val();
    var mateplan_list = $("#mateplan_list").val();
    $("#old_mateplan_list").val($("#mateplan_list").val());    			     		 		
    $.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/produce/mateplan/tech/pmid/"+pmid+"/mateplan_list/"+mateplan_list,
			success: function(data){
        		$("#form_group_select",parent.document).after(data);				
			}
		});    		
    
    $('#plan_name').click(function(){
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择主生产计划',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/produce/mateplan/choose_plan'
			});		 
	});
        		
	$('#header_name').click(function(){
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择负责人',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/produce/mateplan/choose_header'
			});		 
	});	
   		
   $('#select').click(function(){
        	var plans = $("#mateplan_list").val();	 
	
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择明细',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/produce/mateplan/choose_materials/plans/'+plans 
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
    				var ids = $("#mateplan_list").val();
        			var idsArr=ids.split(",");   
	   				ids = removeFromArray(idsArr, id);       		
        			ids = idsArr.join(",");	       		    
        			$("#mateplan_list").val(ids);
        			$(obj).parents('tr').remove();
        			
    			}
            </script>
EOF;

        $data_list = MateplanModel::getOne($id);
       
        // 显示添加页面
        return ZBuilder::make('form')
        	->setPageTitle('编辑生产计划')
        	->addGroup(
        			[
        				'编辑生产计划信息' =>[
							['hidden','id'],
							['hidden', 'plan_id'],
							['hidden', 'header'],
							['hidden', 'org_id'],
							['text', 'code','单据编号'],
							['text', 'name','主题'],
							['text','plan_name', '主生产计划'],
							['text','header_name', '负责人'],
							['text','org_name', '部门'],
							['files','enclosure', '附件'],
							['textarea', 'note', '备注'],    
        				],
        				'编辑生产计划明细' =>[
        					['hidden', 'mateplan_list'],
        					['hidden', 'old_mateplan_list'],
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
    public function tech($ppid = '',$materials_list = '')
    {
    		if($materials_list == '' || $materials_list == 'undefined') {
    		$html = '';	
    	}else{
    		$map = ['produce_mateplan_list.pmid'=>$ppid,'stock_material.id'=>['in',($materials_list)]];
    		$order = '';
    		$data  = MateplanModel::getDetail($map,$order);
    		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>需采购数量</td></tr>';
    		foreach ($data as $k => $v){  
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['smid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['sjsl'].'</td></tr>';
        		}   		
    		$html .= '</tbody></table></div>';
    
    	}
    	return $html;
    }
       
    /**
     * 弹出物资列表
     * @author 黄远东 <641435071@qq.com>
     */
    public function choose_materials($plans = '')
    {
    	// 查询
    	$map = $this->getMap();
    	$map['status'] = 1;
    	$map['id'] = ['not in',$plans];
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
	    			html += '<tr><input type="hidden" name="mid[]" value="'+$.trim($(this).find('td').eq(1).text())+'"><input type="hidden" name="mlid[]" value="0"><td>'+$.trim($(this).find('td').eq(3).text())+'</td><td>'+$.trim($(this).find('td').eq(2).text())+'</td><td>'+$.trim($(this).find('td').eq(6).text())+'</td><td>'+$.trim($(this).find('td').eq(5).text())+'</td><td><input type="text" name="mao_num[]"></td><td><input type="number" name="plan_num[]"></td><td><input type="text" name="plan_time[]"></td><td><input type="text" name="source[]"></td><td><input type="text" name="mnote[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim($(this).find('td').eq(1).text())+'\')">删除</a></td></tr>';
    
	   			});
			}else{
				var html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品编号</td><td>物品名称</td><td>规格</td><td>单位</td><td>毛需求量</td><td>应计划数量</td><td>计划供料日期</td><td>物料来源</td><td>备注</td><td>操作</td></tr>';
    		chk.each(function(){
    			ids += $(this).find('.ids').val()+',';
    
    			html += '<tr><input type="hidden" name="mid[]" value="'+$.trim($(this).find('td').eq(1).text())+'"><input type="hidden" name="mlid[]" value="0"><td>'+$.trim($(this).find('td').eq(3).text())+'</td><td>'+$.trim($(this).find('td').eq(2).text())+'</td><td>'+$.trim($(this).find('td').eq(6).text())+'</td><td>'+$.trim($(this).find('td').eq(5).text())+'</td><td><input type="number" name="mao_num[]"></td><td><input type="number" name="plan_num[]"></td><td><input type="text" name="plan_time[]"></td><td><input type="text" name="source[]"></td><td><input type="text" name="mnote[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim($(this).find('td').eq(1).text())+'\')">删除</a></td></tr>';
    
   			});
    		html += '</tbody></table></div>';
			}
    
    		ids = ids.slice(0,-1);
    
    		if(ids){
	    		var materials = $("#mateplan_list",parent.document).val();
	    		if(materials){
	    			ids = materials+','+ids;
	    		}
	    		var idsArr=ids.split(",");
	   			idsArr.sort();
	    		idsArr = $.unique(idsArr);
	   			ids = idsArr.join(",");
	  
				$("#mateplan_list",parent.document).val(ids);
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
    	->setPageTitle('选择明细')
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
    	->setTableName('stock_material')
    	->fetch('choose'); // 渲染页面
    }
    
    /**
     * 选择主生产计划
     * @author 黄远东 <641435071@qq.com>
     */
    public function choose_plan()
    {
    	// 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('produce_plan.create_time desc');
        // 数据列表
        $data_list = PlanModel::getList($map,$order);
    	$js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
	$('.table-builder input:checkbox').click(function(){
			var pid = $(this).val();
        	var plan_name = $.trim($(this).parents('tr').find('td').eq(3).text()); 
			var obj_id = $.trim($(this).parents('tr').find('td').eq(4).text());    			
			$("#plan_id",parent.document).val(pid);
        	$("#plan_name",parent.document).val(plan_name);
    		$("#obj_id",parent.document).val(obj_id);
			//当你在iframe页面关闭自身时
			var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
			parent.layer.close(index); //再执行关闭
	});
});
            </script>
EOF;
    
    	// 使用ZBuilder快速创建数据表格
    	return ZBuilder::make('table')
            ->setSearch(['produce_plan.code' => '单据编号']) // 设置搜索框
            ->addFilter('produce_plan.org_id',OrganizationModel::getTree())
            ->addColumns([ // 批量添加数据列
                ['id', '序号'], 
            	['code', '单据编号'],				
            	['name', '主题'],
				['obj_id','项目ID'],
				['obj_name','项目名称'],
            	['bname', '负责人'],				
            	['org_id', '部门',OrganizationModel::getTree()],
            	['nickname', '制单人'],
            	['create_time', '制单时间','datetime'],           	
            ])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js)
    	->assign('empty_tips', '暂无数据')
    	->setTableName('produce_plan')
    	->fetch('choose'); // 渲染页面
    }
       
    /**
     * 选择负责人
     * @author 黄远东 <641435071@qq.com>
     */
    public function choose_header()
    {
    	// 获取查询条件
    	$map = $this->getMap();
    	$order = $this->getOrder();
    	// 数据列表   	 
    	$data_list = UserModel::view('admin_user', true)    	
    	->view("admin_organization", ['title'], 'admin_organization.id=admin_user.organization', 'left')   
    	->where($map)
    	->order($order)
    	->paginate();   
    	// 分页数据
    	$page = $data_list->render();
    
    	$js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
	$('.table-builder input:checkbox').click(function(){
			var uid = $(this).val();
        	var nickname = $.trim($(this).parents('tr').find('td').eq(3).text());
    		var org_id = $.trim($(this).parents('tr').find('td').eq(5).text());
    		var org_name = $.trim($(this).parents('tr').find('td').eq(6).text());
			$("#header",parent.document).val(uid);
        	$("#header_name",parent.document).val(nickname);
    		$("#org_id",parent.document).val(org_id);
    		$("#org_name",parent.document).val(org_name);
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
    	->setSearch(['admin_user.id' => 'ID', 'admin_user.username' => '用户名', 'admin_user.nickname' => '姓名']) // 设置搜索参数
    	->addOrder('admin_user.id,admin_user.organization,admin_user.position,admin_user.is_on')
    	->addFilter('admin_user.role', RoleModel::getTree2())
    	->addFilter('admin_organization.title')
    	->addFilter('admin_user.position', PositionModel::getTree(null, false))
    	->addFilter('admin_user.is_on', ['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职'])
    	->addColumns([ // 批量添加列
    			['id', 'ID'],
    			['username', '用户名'],
    			['nickname', '姓名'],
    			['role', '角色', RoleModel::getTree2()],
    			['organization', '部门编号'],
    			['title', '部门名称'],
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
    	if (MateplanModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		//$details = '物料需求计划ID('.$ids.'),操作人ID('.UID.')';
    		//action_log('produce_mateplan_delete', 'produce_mateplan', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }
	 //查看
    public function task_list($id = null){
    	if($id == null) $this->error('参数错误');		
		$info = MateplanModel::getOne($id);
		$info['materials_list'] = implode(MateplanModel::getMaterials($id),',');
		$info->create_time = date('Y-m-d',$info['create_time']);
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addGroup([
		'基本信息'=>[    
			['hidden','id'], 
			['static:4','code','单据编号'],
			['static:4','name','主题',],		
			['static:4','plan_name','生产计划'],
			['static:4','header_name', '负责人'],	
			['static:4','org_name', '部门'],		
			['archives','file','附件'],	
			['static:4','create_time','制单时间'],
			['static','note','备注'],	
		],
          '生产任务明细' =>[
            ['hidden', 'materials_list'],
            ['hidden', 'old_plan_list'],
          ]			
		])
		->setFormData($info)
		->js('mateplan')
		->fetch();
    }

}