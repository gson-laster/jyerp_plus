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
use app\produce\model\Cost as CostModel;
use app\produce\model\CostList as CostListModel;
use app\user\model\User as UserModel;
use app\user\model\Role as RoleModel;
use app\produce\model\Production as ProductionModel;
use app\user\model\Organization as OrganizationModel;
use app\user\model\Position as PositionModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
/**
 * 成本核算
 * @package app\produce\admin
 */
class Cost extends Admin
{
    /**
     * 成本核算
     * @return mixed
     */
    public function index()
    {
        // 查询
        $map = $this->getMap();
		//$map['produce_cost.status'] = 1;
        // 排序
        $order = $this->getOrder('produce_cost.create_time desc');
        // 数据列表
        $data_list = CostModel::getDataa($map,$order);
		$task_list = [
						'title' => '查看详情',
						'icon' => 'fa fa-fw fa-eye',
						'href' => url('task_list',['rw_id'=>'__rw_id__'])
						];    
        return ZBuilder::make('table')
            ->setSearch(['produce_cost.code' => '单据编号']) // 设置搜索框
			->hideCheckbox()
            ->addColumns([ // 批量添加数据列       
            	['code', '单据编号'],
            	['name', '填报名称'],
				['obj_id','项目名称'],            	
            	['rw_name', '任务名称'],				
            	['zrid', '制单人'],
            	['create_time', '制单时间','datetime'],
            	['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
            	['right_button', '操作', 'btn']
            ])           
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
			//dump($data);die;
            // 验证
            //$result = $this->validate($data, 'Plan');
            //if (true !== $result) $this->error($result);
			$data['code'] = 'CBHS'.date('YmdHis',time());
			$data['rw_id'] = $data['plan_id'];
            if ($results = CostModel::create($data)) {
            	foreach($data['clid'] as $k => $v){
            		$info = array();
            		$info = [
            				'pmid'=>$results['id'],
							'smid'=>$v,
							'cpid'=>$data['cpid'][$k],            				           				
            				'snum'=>$data['snum'][$k],
            				'note'=>$data['mnote'][$k]
            		];
            		CostListModel::create($info);            	
            	}
            	 flow_detail($data['name'],'produce_cost','produce_cost','produce/cost/task_list',$results['id']);
                // 记录行为
            	//$details    = '生产任务：ID('.$results['id'].'),建档人ID('.$results['uid'].')';
                //action_log('produce_plan_add', 'produce_plan', $results['id'], UID, $details);     
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }
        
        $js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
			$('#form_group_materials_list').after('<div class="form-group col-md-12 col-xs-12" id="form_group_select"><button class="btn btn-xs btn-info" type="button" id="select">查看明细</button></div><div class="h_html"></div>');
  	$('#plan_name').click(function(){
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择主生产计划',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/produce/cost/choose_plan'
			});		 
	});  
$('#select').click(function(){
        var plan_id = $('#plan_id').val()
		$.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/produce/cost/get_num/plan_id/"+plan_id,
			success: function(data){														
				$(".h_html").html(data);			
			}
		});
	}); 	
});
        	
            </script>
EOF;
      
        // 显示添加页面
        return ZBuilder::make('form')
        	->setPageTitle('新建生产计划')
        	->addGroup(
        			[
        				'成本核算' =>[
							['hidden', 'zrid'],
							['hidden', 'plan_id'],
							['hidden', 'obj_id'],
							['text', 'name','制单名称'],
							['text','plan_name', '选择生产任务'],
							['text','obj_name','项目','','','','disabled'],
							['text','zrname', '负责人'],
							['files','file', '附件'],
							['textarea', 'note', '备注'],    
        				],
        				'成本核算明细' =>[
        					['hidden', 'materials_list'],       					
        				]
        			])			
			->setExtraHtml(outhtml2())
			->setExtraJs($js.outjs2())	
            ->fetch();
    } 
	
	 public function get_num($plan_id = ''){
		if($plan_id == ''){
			return $html='';
		}		
		$data = CostModel::getDetail($plan_id);
		//dump($data);
		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>成品名称</td><td>生产数量</td><td>BOM</td><td>材料名称</td><td>材料单价</td><td>材料应用数量</td><td>材料实用数量</td><td>备注</td></tr>';
    		foreach ($data as $k => $v){  
    			$html.='<tr><td rowspan="'.$v['cpnum'].'">'.$v['cpname'].'</td><td rowspan="'.$v['cpnum'].'">'.$v['produce_num'].'</td><td rowspan="'.$v['cpnum'].'">'.$v['Bname'].'</td>';
					foreach ($v['cl'] as $a => $b){
						$html .='<input type="hidden" name="cpid[]" value="'.$v['smid'].'"><input type="hidden" name="clid[]" value="'.$b['smid'].'"><td>'.$b['clname'].'</td><td>'.$b['price'].'</td><td>'.$b['quota']*$v['produce_num'].'</td><td><input type="number" name="snum[]"></td><td><input type="text" name="mnote[]"></td></tr>';
					}		
					//dump($v['cl']);
        		}   		
				//ie;
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
            $result = $this->validate($data, 'Plan');
            if (true !== $result) $this->error($result);
			
            if (PlanModel::update($data)) {
            	
            	$mlist = explode(',',$data['plan_list']);
            	$oldmlist = explode(',',$data['old_plan_list']);
            	$dif = array_diff($oldmlist,$mlist);
            	PlanListModel::where(['smid'=>['in',$dif],'ppid'=>$id])->delete();
            	
            	foreach($data['mid'] as $k => $v){
            		$info = array();
            		if($data['mlid'][$k]){
            			$info = [
            					'id'=>$data['mlid'][$k],
            					'smid'=>$v,
            					'plan_num'=>$data['plan_num'][$k],
            					'require_num'=>$data['require_num'][$k],
            					'start_time'=>$data['start_time'][$k],
            					'end_time'=>$data['end_time'][$k],
            					'note'=>$data['mnote'][$k]
            			];
            			PlanListModel::update($info);
            		}else{         			
            			$info = [
            					'ppid'=>$data['id'],
            					'smid'=>$v,
            					'plan_num'=>$data['plan_num'][$k],
            					'require_num'=>$data['require_num'][$k],
            					'start_time'=>$data['start_time'][$k],
            					'end_time'=>$data['end_time'][$k],
            					'note'=>$data['mnote'][$k]
            			];
            			PlanListModel::create($info);
            		}		
            	}
                // 记录行为
            	$details    = '详情：生产计划ID('.$data['id'].'),修改人ID('.UID.')';
                action_log('produce_plan_edit', 'produce_plan', $id, UID, $details);
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }
        
        $js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
    $('#form_group_plan_list').after('<div class="form-group col-md-12 col-xs-12" id="form_group_select"><button class="btn btn-xs btn-info" type="button" id="select">选择子件</button></div>');       
	 var ppid = $("#id").val();
    var plan_list = $("#plan_list").val();
    $("#old_plan_list").val($("#plan_list").val());    			     		 		
    $.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/produce/plan/tech/ppid/"+ppid+"/plan_list/"+plan_list,
			success: function(data){
        		$("#form_group_select",parent.document).after(data);				
			}
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
			  content: '/admin.php/produce/plan/choose_header'
			});		 
	});	
        		
   $('#select').click(function(){
        	var plans = $("#plan_list").val();	 
	
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择明细',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/produce/plan/choose_materials/plans/'+plans 
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
    				var ids = $("#plan_list").val();
        			var idsArr=ids.split(",");   
	   				ids = removeFromArray(idsArr, id);       		
        			ids = idsArr.join(",");	       		    
        			$("#plan_list").val(ids);
        			$(obj).parents('tr').remove();
        			
    			}
            </script>
EOF;

        $data_list = PlanModel::getOne($id);
       
        // 显示添加页面
        return ZBuilder::make('form')
        	->setPageTitle('编辑生产计划')
        	->addGroup(
        			[
        				'生产计划信息' =>[
        							['hidden','id'],
        							['hidden', 'header'],
        							['hidden', 'org_id'],
			            			['text', 'code','单据编号'],
			            			['text', 'name','主题'],
			            			['text','header_name', '负责人'],
        							['text','org_name', '部门'],
        							['files','enclosure', '附件'],
			            			['textarea', 'note', '备注'],    
        				],
        				'生产计划明细' =>[
        					['hidden', 'plan_list'],
        					['hidden', 'old_plan_list'],
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
    public function tech($ppid = '',$plan_list = '')
    {
    	if($plan_list == '') {
    		$html = '';	
    	}else{
    		$map = ['produce_plan_list.ppid'=>$ppid,'stock_material.id'=>['in',($plan_list)]];
    		$order = '';
    		$data = $data_list = PlanListModel::getList($map,$order);
    		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品编号</td><td>物品名称</td><td>规格</td><td>单位</td><td>计划生产数量</td><td>需求数量</td><td>计划开工日期</td><td>计划完工日期</td><td>备注</td><td>操作</td></tr>';
    		foreach ($data as $k => $v){ 
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['smid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['code'].'</td><td>'.$v['name'].'</td><td>'.$v['version'].'</td><td>'.$v['unit'].'</td><td><input type="text" name="plan_num[]" value="'.$v['plan_num'].'"></td><td><input type="number" name="require_num[]" value="'.$v['require_num'].'"></td><td><input type="text" name="start_time[]" value="'.$v['start_time'].'"></td><td><input type="text" name="end_time[]" value="'.$v['end_time'].'"></td><td><input type="text" name="mnote[]" value="'.$v['note'].'"></td><td><a href="javascript:;" onclick="delMaterials(this,\''.$v['smid'].'\')">删除</a></td></tr>';
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
	    			html += '<tr><input type="hidden" name="mid[]" value="'+$.trim($(this).find('td').eq(1).text())+'"><input type="hidden" name="mlid[]" value="0"><td>'+$.trim($(this).find('td').eq(3).text())+'</td><td>'+$.trim($(this).find('td').eq(2).text())+'</td><td>'+$.trim($(this).find('td').eq(6).text())+'</td><td>'+$.trim($(this).find('td').eq(5).text())+'</td><td><input type="number" name="plan_num[]"></td><td><input type="number" name="require_num[]"></td><td><input type="text" name="start_time[]"></td><td><input type="text" name="end_time[]"></td><td><input type="text" name="mnote[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim($(this).find('td').eq(1).text())+'\')">删除</a></td></tr>';
    
	   			});
			}else{
				var html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品编号</td><td>物品名称</td><td>规格</td><td>单位</td><td>计划生产数量</td><td>需求数量</td><td>计划开工日期</td><td>计划完工日期</td><td>备注</td><td>操作</td></tr>';
    		chk.each(function(){
    			ids += $(this).find('.ids').val()+',';
    
    			html += '<tr><input type="hidden" name="mid[]" value="'+$.trim($(this).find('td').eq(1).text())+'"><input type="hidden" name="mlid[]" value="0"><td>'+$.trim($(this).find('td').eq(3).text())+'</td><td>'+$.trim($(this).find('td').eq(2).text())+'</td><td>'+$.trim($(this).find('td').eq(6).text())+'</td><td>'+$.trim($(this).find('td').eq(5).text())+'</td><td><input type="number" name="plan_num[]"></td><td><input type="number" name="require_num[]"></td><td><input type="text" name="start_time[]"></td><td><input type="text" name="end_time[]"></td><td><input type="text" name="mnote[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim($(this).find('td').eq(1).text())+'\')">删除</a></td></tr>';
    
   			});
    		html += '</tbody></table></div>';
			}
    
    		ids = ids.slice(0,-1);
    
    		if(ids){
	    		var materials = $("#plan_list",parent.document).val();
	    		if(materials){
	    			ids = materials+','+ids;
	    		}
	    		var idsArr=ids.split(",");
	   			idsArr.sort();
	    		idsArr = $.unique(idsArr);
	   			ids = idsArr.join(",");
	  
				$("#plan_list",parent.document).val(ids);
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
		$map['produce_production.status'] = 1;
        // 排序
        $order = $this->getOrder('produce_production.create_time desc');
        // 数据列表
        $data_list = ProductionModel::getList($map,$order);
    
    	$js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
	$('.table-builder input:checkbox').click(function(){
			var pid = $(this).val();
        	var plan_name = $.trim($(this).parents('tr').find('td').eq(3).text());
    		var obj_id = $.trim($(this).parents('tr').find('td').eq(4).text());
			var obj_name = $.trim($(this).parents('tr').find('td').eq(5).text());
			$("#plan_id",parent.document).val(pid);
        	$("#plan_name",parent.document).val(plan_name);
    		$("#obj_id",parent.document).val(obj_id);
			$("#obj_name",parent.document).val(obj_name);
			//当你在iframe页面关闭自身时
			var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
			parent.layer.close(index); //再执行关闭
	});
});
            </script>
EOF;
    
    	// 使用ZBuilder快速创建数据表格
    	return ZBuilder::make('table')
            ->setSearch(['produce_production.code' => '单据编号']) // 设置搜索框
            ->addFilter('produce_production.org_id',OrganizationModel::getTree())
            ->addColumns([ // 批量添加数据列
                ['id', '序号'], 
            	['code', '单据编号'],
            	['name', '主题'],
				['obj_id', '项目序号'],
				['obj_name', '项目名称'],
            	['bname', '负责人'],
            	['org_id', '部门',OrganizationModel::getTree()],
            	['nickname', '制单人'],
            	['create_time', '制单时间','datetime'],           	
            ])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js)
    	->assign('empty_tips', '暂无数据')
    	->setTableName('produce_production')
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
    	if (PlanModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		$details = '生产任务ID('.$ids.'),操作人ID('.UID.')';
    		action_log('produce_plan_delete', 'produce_plan', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }
	public function task_list($rw_id = null){
		if($rw_id == null) $this->error('缺少参数id');
		$info = CostModel::getList($rw_id);
		$this->assign('info',$info);
		return $this->fetch();
	}
}