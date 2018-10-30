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
use app\user\model\User as UserModel;
use app\user\model\Role as RoleModel;
use app\user\model\Organization as OrganizationModel;
use app\user\model\Position as PositionModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\tender\model\Obj as ObjModel;
/**
 * 生产计划控制器
 * @package app\produce\admin
 */
class Plan extends Admin
{
    /**
     * 生产计划列表
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('produce_plan.create_time desc');
        // 数据列表
        $data_list = PlanModel::getList($map,$order);
        $task_list = [
						'title' => '查看详情',
						'icon' => 'fa fa-fw fa-eye',
						'href' => url('task_list',['id'=>'__id__'])
						];    
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['produce_plan.code' => '单据编号']) // 设置搜索框
            ->addColumns([ // 批量添加数据列
                ['__INDEX__', '序号'], 
            	['code', '单据编号'],
            	['name', '主题'],
            	['obj_id', '项目名称', ObjModel::get_nameid(1)],
            	['bname', '负责人'],
            	['org_id', '部门',OrganizationModel::getTree()],
            	['nickname', '制单人'],
                ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],            	
            	['create_time', '制单时间','datetime'],
            	['right_button', '操作', 'btn']
            ])
            ->addTopButtons('add,delete')
            // 批量添加顶部按钮
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
            $result = $this->validate($data, 'Plan');
            if (true !== $result) $this->error($result);
			$data['uid']=UID;
			$data['header']=$data['zrid'];
			$data['code'] = 'SCJH'.date('YmdHis',time());
            if ($results = PlanModel::create($data)) {
            	foreach($data['mid'] as $k => $v){
            		$info = array();
            		$info = [
            				'ppid'=>$results['id'],
            				'smid'=>$v,
            				'plan_num'=>$data['plan_num'][$k],            				
            				'start_time'=>strtotime($data['start_time'][$k]),
            				'end_time'=>strtotime($data['end_time'][$k]),
            				'note'=>$data['mnote'][$k]
            		];
            		PlanListModel::create($info);            	
            	}            	
                // 记录行为
            	$details    = '生产任务：ID('.$results['id'].'),建档人ID('.$results['uid'].')';
                action_log('produce_plan_add', 'produce_plan', $results['id'], UID, $details);
                flow_detail($data['name'],'produce_plan','produce_plan','produce/plan/task_list',$results['id']);                                     
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }		     
        // 显示添加页面
        return ZBuilder::make('form')
        	->setPageTitle('新建生产计划')
        	->addGroup(
        			[
        				'新增生产计划' =>[
							['hidden', 'zrid'],
							['text', 'name','主题'],
							['select', 'obj_id','项目名称', '', ObjModel::get_nameid(1)],
							['text','zrname', '负责人'],
							['select','org_id', '部门','', OrganizationModel::getMenuTree2()],
							['files','enclosure', '附件'],
							['date:4', 'time', '制单时间','',date('Y-m-d')],		
							['textarea', 'note', '备注'],    
        				],
        				'新增生产计划明细' =>[
        					['hidden', 'materials_list'],        					
        				]
        			])
			->setExtraHtml(outhtml2())
			->setExtraJs(outjs2())
			->js('plan')
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
						['select', 'obj_id','项目'],
						['select','obj_id', '项目名称','', ObjModel::get_nameid()],
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
    public function tech($ppid = '',$materials_list = '')
    {
    	if($materials_list == '' || $materials_list == 'undefined') {
    		$html = '';	
    	}else{
    		$map = ['produce_plan_list.ppid'=>$ppid,'stock_material.id'=>['in',($materials_list)]];
    		$order = '';
    		$data = $data_list = PlanModel::getDetail($map,$order);
			
    		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>规格</td><td>单位</td><td>计划生产数量</td><td>计划开工日期</td><td>计划完工日期</td><td>备注</td></tr>';
    		foreach ($data as $k => $v){ 
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['smid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['version'].'</td><td>'.$v['unit'].'</td><td>'.$v['plan_num'].'</td><td>'.date('Y-m-d',$v['start_time']).'</td><td>'.date('Y-m-d',$v['end_time']).'</td><td>'.$v['note'].'</td></tr>';
    		}   		
    		$html .= '</tbody></table></div>';
    
    	}
		
    	return $html;
    }
       
    /**
     * 弹出物资列表
     * @author 黄远东 <641435071@qq.com>
     */
    //弹出
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
		                    <td>'.$v['code'].'</td>
		                    <td>'.$v['name'].'</td>
		                    <td>'.$v['version'].'</td>
		                    <td>'.$v['unit'].'</td>
		                    <td>'.$v['price'].'</td>
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
    	// 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['name' => '物品名称']) // 设置搜索框
            ->addOrder('id,create_time') // 添加排序
            ->setPageTitle('选择物品')
            ->addColumns([ // 批量添加数据列
                ['id', '序号'], 
                ['code', '编号'], 
            	['name', '物品名称'],           	
            	['version', '规格型号',],
            	['unit', '计量单位'],
            	['price', '参考价格(元)'],
            	['status', '启用状态', 'status'],
            ])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js)
    	->js('plan')
    	->addTopButton('pick', $btn_pick)
    	->assign('empty_tips', '暂无数据')
    	->fetch('admin@choose/choose'); // 渲染页面
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
    
    
  	//查看
    public function task_list($id = null){
    	if($id == null) $this->error('参数错误');		
		$info = PlanModel::getOne($id);
		$info['materials_list'] = implode(PlanModel::getMaterials($id),',');
		$info->create_time = date('Y-m-d',$info['create_time']);
		return ZBuilder::make('form')
		->addGroup([
		'生产计划信息'=>[    
			['hidden','id'], 
			['static:4','code','单据编号'],
			['static:4','name','主题',],	
			['static:4','obj_id','项目名称',],	
			['static:4','header_name', '负责人'],	
			['static:4','org_name', '部门'],		
			['archives','file','附件'],	
			['static:4','create_time','制单时间'],
			['static','note','备注'],												
		],
          '生产计划明细' =>[
            ['hidden', 'materials_list'],
            ['hidden', 'old_plan_list'],
          ]			
		])
		->hideBtn(['submit'])
		->setFormData($info)
		->js('plan')
		->fetch();
    }

}