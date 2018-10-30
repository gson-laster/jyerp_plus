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
use app\produce\model\Production as ProductionModel;
use app\produce\model\Materials as MaterialsModel;
use app\produce\model\ProductionList as ProductionListModel;
use app\user\model\User as UserModel;
use app\user\model\Role as RoleModel;
use app\user\model\Organization as OrganizationModel;
use app\user\model\Position as PositionModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\tender\model\Obj;
use app\tender\model\Obj as ObjModel;
/**
 * 生产任务管理控制器
 * @package app\produce\admin
 */
class Production extends Admin
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
        $order = $this->getOrder('produce_production.create_time desc');
        // 数据列表
        $data_list = ProductionModel::getList($map,$order);
        $task_list = [
						'title' => '查看详情',
						'icon' => 'fa fa-fw fa-eye',
						'href' => url('task_list',['id'=>'__id__'])
						];    
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['produce_production.code' => '单据编号']) // 设置搜索框
            ->addColumns([ // 批量添加数据列
                ['__INDEX__', '序号'], 
            	['code', '单据编号'],
            	['name', '主题'],
            	['obj_name','项目'],
            	['plan_name', '源单类型'],
            	['bname', '负责人'],
            	['org_id', '生产部门',OrganizationModel::getTree()],  	
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
            $result = $this->validate($data, 'Production');
            if (true !== $result) $this->error($result);
			$data['uid']=UID;
			$data['code'] = 'SCRW'.date('YmdHis',time());
            if ($results = ProductionModel::create($data)) {
            	foreach($data['mid'] as $k => $v){
            		$info = array();
            		$info = [
            				'ppid'=>$results['id'],
            				'smid'=>$v,
							'produce_num'=>$data['produce_num'][$k],
							'BOMid'=>$data['BOMid'][$k],
            				'plan_time'=>strtotime($data['plan_time'][$k]),
            				'end_time'=>strtotime($data['end_time'][$k]),
            				'ysc_num'=>$data['ysc_num'][$k],
            				'yrk_num'=>$data['yrk_num'][$k],
            				'yb_num'=>$data['yb_num'][$k],
            				'sj_num'=>$data['sj_num'][$k],
            		];
            		ProductionListModel::create($info);            	
            	}
            	 flow_detail($data['name'],'produce_production','produce_production','produce/production/task_list',$results['id']);
                // 记录行为
            	$details    = '生产任务：ID('.$results['id'].'),建档人ID('.$results['uid'].')';
                action_log('produce_production_add', 'produce_production', $results['id'], UID, $details);        
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
			  content: '/admin.php/produce/production/choose_plan'
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
			  content: '/admin.php/produce/production/choose_header'
			});		 
	});	
        		
    $('#org_name').click(function(){
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择部门',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/produce/production/choose_org'
			});		 
	});	   
});
        		
            </script>
EOF;
      $bom = MaterialsModel::column('id,name');
        $html = ' <script type="text/javascript">
            var bom_select = \'<select name="BOMid[]">';
        foreach ($bom as $key => $value) {
            $html.='<option value="'.$key.'">'.$value.'</option>';
            
        }
        $html.='</select>\';
        </script>';
        // 显示添加页面
        return ZBuilder::make('form')
        	->setPageTitle('新建生产任务')
        	->addGroup(
        			[
        				'基本信息' =>[
							['hidden', 'plan_id'],
							['hidden', 'header'],
							['hidden', 'org_id'],
							['hidden', 'obj_id'],
							['text', 'name','主题'],							
							['text','plan_name', '选择生产计划'],
							['text','obj_name','项目','','','','disabled'],
							['select','jg_type','加工类型','',[-1=>'普通',0=>'返修',1=>'拆件']],
							['text','org_name', '选择生产部门'],
							['text','header_name', '选择负责人'],        							
							['files','enclosure', '附件'],
							['date:4','time','制单时间','',date('Y-m-d')],
							['textarea', 'note', '备注'],    
        				],
        				'生产任务明细' =>[
        					['hidden', 'materials_list'],        					
        				]
        			])
			->setExtraJs($js.$html)
			->js('production')
            ->fetch();
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
            $result = $this->validate($data, 'Production');
            if (true !== $result) $this->error($result);
			
            if (ProductionModel::update($data)) {
            	
            	$mlist = explode(',',$data['production_list']);
            	$oldmlist = explode(',',$data['old_production_list']);
            	$dif = array_diff($oldmlist,$mlist);
            	ProductionListModel::where(['smid'=>['in',$dif],'ppid'=>$id])->delete();
            	
            	foreach($data['mid'] as $k => $v){
            		$info = array();
            		if($data['mlid'][$k]){
            			$info = [
            					'id'=>$data['mlid'][$k],
            					'smid'=>$v,
            					'produce_num'=>$data['produce_num'][$k],
	            				'plan_time'=>$data['plan_time'][$k],
	            				'end_time'=>$data['end_time'][$k],
	            				'ysc_num'=>$data['ysc_num'][$k],
	            				'yrk_num'=>$data['yrk_num'][$k],
	            				'yb_num'=>$data['yb_num'][$k],
	            				'sj_num'=>$data['sj_num'][$k],
	            				'hg_num'=>$data['hg_num'][$k],
	            				'bhg_num'=>$data['bhg_num'][$k],
            			];
            			ProductionListModel::update($info);
            		}else{         			
            			$info = [
            					'ppid'=>$data['id'],
            					'smid'=>$v,
            					'produce_num'=>$data['produce_num'][$k],
	            				'plan_time'=>$data['plan_time'][$k],
	            				'end_time'=>$data['end_time'][$k],
	            				'ysc_num'=>$data['ysc_num'][$k],
	            				'yrk_num'=>$data['yrk_num'][$k],
	            				'yb_num'=>$data['yb_num'][$k],
	            				'sj_num'=>$data['sj_num'][$k],
	            				'hg_num'=>$data['hg_num'][$k],
	            				'bhg_num'=>$data['bhg_num'][$k],
            			];
            			ProductionListModel::create($info);
            		}		
            	}
                // 记录行为
            	$details    = '详情：生产任务ID('.$data['id'].'),修改人ID('.UID.')';
                action_log('produce_production_edit', 'produce_production', $id, UID, $details);
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }
        
        $js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
    $('#form_group_production_list').after('<div class="form-group col-md-12 col-xs-12" id="form_group_select"><button class="btn btn-xs btn-info" type="button" id="select">查看生产计划</button></div>');       
	var ppid = $("#id").val();
    var production_list = $("#production_list").val();
    $("#old_production_list").val($("#production_list").val());    			     		 		
    $.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/produce/production/tech/ppid/"+ppid+"/production_list/"+production_list,
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
			  content: '/admin.php/produce/production/choose_plan'
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
			  content: '/admin.php/produce/production/choose_header'
			});		 
	});	
        		
    $('#org_name').click(function(){
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择部门',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/produce/production/choose_org'
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
    				var ids = $("#production_list").val();
        			var idsArr=ids.split(",");   
	   				ids = removeFromArray(idsArr, id);       		
        			ids = idsArr.join(",");	       		    
        			$("#production_list").val(ids);
        			$(obj).parents('tr').remove();
        			
    			}
            </script>
EOF;

        $data_list = ProductionModel::getOne($id);
        $obj_id = Obj::get_nameid();
        // 显示添加页面
        return ZBuilder::make('form')
        	->setPageTitle('编辑生产任务')
        	->addGroup(
        			[
        				'生产任务信息' =>[
        							['hidden','id'],
        							['hidden', 'plan_id'],
        							['hidden', 'header'],
        							['hidden', 'org_id'],
			            			['text', 'code','单据编号'],
			            			['text', 'name','主题'],
			            			['select','obj_id','项目','',$obj_id],
        							['text','plan_name', '主生产计划'],
			            			['text','header_name', '负责人'],
        							['text','org_name', '部门'],
        							['files','enclosure', '附件'],
			            			['textarea', 'note', '备注'],    
        				],
        				'生产任务明细' =>[
        					['hidden', 'materials_list'],
        					['hidden', 'old_production_list'],
        				]
        			])       			
        	->setFormData($data_list)
			->setExtraJs($js)
            ->fetch();
    }
    public function get_num($plan_id = ''){
		if($plan_id == ''){
			return $html='<span>请选择生产计划</span>';
		}
		 $bom = MaterialsModel::column('id,name');
        $html2 = '<select name="BOMid[]">';
        foreach ($bom as $key => $value) {
            $html2.='<option value="'.$key.'">'.$value.'</option>';           
        }
        $html2.='</select>';
		$map = ['ppid'=>$plan_id];
		$data = PlanModel::getDetail($map);
		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>计划生产数量</td><td>生产数量</td><td>BOM</td><td>计划开工日期</td><td>计划完工日期</td><td>已生产数量</td><td>已入库数量</td><td>已报数量</td><td>实检数量</td></tr>';
    		foreach ($data as $k => $v){  
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['smid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['plan_num'].'</td><td><input type="number" name="produce_num[]"></td><td>'.$html2.'</td><td><input type="text" name="plan_time[]" value="'.date('Y-m-d',$v['start_time']).'"></td><td><input type="text" name="end_time[]" value="'.date('Y-m-d',$v['end_time']).'"></td><td><input type="number" name="ysc_num[]"></td><td><input type="number" name="yrk_num[]"></td><td><input type="number" name="yb_num[]"></td><td><input type="number" name="sj_num[]"></td></tr>';
        		}   		
    		$html .= '</tbody></table></div>';
		return $html;
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
			$bom = MaterialsModel::column('id,name');
    		$map = ['produce_production_list.ppid'=>$ppid,'stock_material.id'=>['in',($materials_list)]];
    		$order = '';
    		$data  = ProductionModel::getDetail($map,$order);
    		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>需生产数量</td><td>BOM</td><td>计划开工日期</td><td>计划完工日期</td><td>已生产数量</td><td>已入库数量</td><td>已报数量</td><td>实检数量</td></tr>';
    		foreach ($data as $k => $v){  
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['smid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['produce_num'].'</td><td>'.$bom[$v['BOMid']].'</td><td>'.date('Y-m-d',$v['plan_time']).'</td><td>'.date('Y-m-d',$v['end_time']).'</td><td>'.$v['ysc_num'].'</td><td>'.$v['yrk_num'].'</td><td>'.$v['yb_num'].'</td><td>'.$v['sj_num'].'</td></tr>';
        		}   		
    		$html .= '</tbody></table></div>';
    
    	}
    	return $html;
    }
       
    /**
     * 弹出物资列表
     * @author 黄远东 <641435071@qq.com>
     */
    public function choose_materials($materials = '',$pid = null)
    {    	
	$map['status'] = 1;
	if($pid!==null){
		$map['type'] = $pid;
		$map['id'] = ['not in',$materials];		
		$data = MaterialModel::where($map)->select();			
		$html = '';	 
		if($data){									
				foreach($data as $k => $v){								
				$html .='<tr>                                    	
			                <td class="text-center">
			                    <label class="css-input css-checkbox css-checkbox-primary">
			                        <input class="ids" onclick="che(this)" type="checkbox" name="ids[]" value="'.$v['id'].'"><span></span>
			                    </label>
			                </td>			             
		                    <td>'.$v['id'].'</td>
		                    <td>'.$v['name'].'</td>
			                <td>'.$v['code'].'</td>
		                    <td>'.$v['unit'].'</td>
		                    <td>'.$v['version'].'</td>
		                    <td>'.$v['price_tax'].'</td>
		                    <td>'.$v['color'].'</td>		                   
		                    <td>'.$v['brand'].'</td>
		                    <td>'.$v['status'].'</td>	                                                                                                    		                                                         
	          			</tr>';
			}				
		}else{
			$html .='<tr class="table-empty">
                        <td class="text-center empty-info" colspan="10">
                            <i class="fa fa-database"></i> 暂无数据<br>
                        </td>
                    </tr>';
		}  
		return $html;		
	}
	 	$data = MaterialModel::where($map)->select();
		$this->assign('data',$data);
		$this->assign('resulet',MaterialTypeModel::getOrganization());    
    	// 查询
    	$map = $this->getMap();
    	$map['id'] = ['not in',$materials];
    	// 排序
    	$order = $this->getOrder('create_time desc');
    	// 数据列表
    	$data_list = MaterialModel::getList($map,$order);    
    	$btn_pick = [
    			'title' => '选择',
    			'icon'  => 'fa fa-plus-circle',
    			'class' => 'btn btn-xs btn-success',
    			'id' => 'pick'
    	];   
    	    $js = <<<EOF
            <script type="text/javascript">
                $('#pick').after('<input id="pickinp" type="hidden" name="materialsid">');
                	$('#pickinp').val({$materials});
            </script>
EOF;
 $bom = MaterialsModel::column('id,name');
        $html = ' <script type="text/javascript">
            var bom_select = \'<select name="BOMid[]">';
        foreach ($bom as $key => $value) {
            $html.='<option value="'.$key.'">'.$value.'</option>';
            
        }
        $html.='</select>\';
        </script>';
    	// 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['name' => '物品名称']) // 设置搜索框
            ->addOrder('id,create_time') // 添加排序
            ->setPageTitle('选择物品')
            ->addColumns([ // 批量添加数据列
                ['id', '编号'], 
            	['name', '物品名称'],           	
            	['version', '规格型号',],
            	['unit', '单位'],
            	['status', '启用状态', 'status'],
            ])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js.$html)
    	->js('production')
    	->addTopButton('pick', $btn_pick)
    	->assign('empty_tips', '暂无数据')
    	->fetch('admin@choose/choose'); // 渲染页面
    }
    
    /**
     * 选择主生产计划
     * @author 黄远东 <641435071@qq.com>
     */
    public function choose_plan()
    {
    	// 查询
        $map = $this->getMap();
		$map['produce_plan.status'] = 1;
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
            ->setSearch(['produce_plan.code' => '单据编号']) // 设置搜索框
            ->addFilter('produce_plan.org_id',OrganizationModel::getTree())
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
			$("#header",parent.document).val(uid);
        	$("#header_name",parent.document).val(nickname);   		
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
     * 选择部门
     * @author 黄远东 <641435071@qq.com>
     */
    public function choose_org()
    {
    	// 获取查询条件
    	$map = $this->getMap();
    	$order = $this->getOrder();
    	// 数据列表
    	$data_list = OrganizationModel::where($map)->order($order)->paginate();

    	$js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
	$('.table-builder input:checkbox').click(function(){
			var oid = $(this).val();
        	var title = $.trim($(this).parents('tr').find('td').eq(2).text());
			$("#org_id",parent.document).val(oid);
        	$("#org_name",parent.document).val(title);
			//当你在iframe页面关闭自身时
			var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
			parent.layer.close(index); //再执行关闭
	});
});
            </script>
EOF;
    
    	// 使用ZBuilder快速创建数据表格
    	return ZBuilder::make('table')
    	->setTableName('admin_organization') // 设置数据表名
    	->setSearch(['title' => '部门名称']) // 设置搜索参数
    	->addOrder('id')
    	->addColumns([ // 批量添加列
    			['id', 'ID'],
    			['title', '部门名称'],	
    	])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js)
    	->assign('empty_tips', '暂无数据')
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
    	if (ProductionModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		$details = '生产任务ID('.$ids.'),操作人ID('.UID.')';
    		action_log('produce_production_delete', 'produce_production', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }
    
    
    
    
 //查看
    public function task_list($id = null){
    	if($id == null) $this->error('参数错误');		
		$info = ProductionModel::getOne($id);
		$info['materials_list'] = implode(ProductionModel::getMaterials($id),',');
		$info->create_time = date('Y-m-d',$info['create_time']);
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addGroup([
		'基本信息'=>[    
			['hidden','id'], 
			['static:4','code','单据编号'],
			['static:4','name','主题',],	
			['static:4','obj_id','项目名称',],	
			['static:4','plan_name','生产计划'],
			['select','jg_type','加工类型','',[-1=>'普通',0=>'返修',1=>'拆件']],
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
		->js('production')
		->fetch();
    }

}