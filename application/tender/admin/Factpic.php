<?php
	
namespace app\tender\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\tender\model\Obj as ObjModel;
use app\tender\model\Type as TypeModel;
use app\tender\model\Factpic as FactpicModel;
use app\user\model\Organization as OrganizationModel;
use app\task\model\Task_detail as Task_detailModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use think\Db;
/**
 * 招标控制器
 * @author HJP
 */
class Factpic extends Admin
{
	//招标文件购买申请列表
	public function index()
	{
		// 获取查询条件
		$map = $this->getMap();
		// 数据列表
		$data_list = FactpicModel::getList($map);
		//dump($data_list);die;               
		//获取昵称
		// 分页数据
		$page = $data_list->render();
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
		
		return ZBuilder::make('table')
		->addTimeFilter('tender_factpic.date')
		->addFilter(['obj_id'=>'tender_obj.name']) // 添加筛选
		->addFilter(['nickname'=>'admin_user.nickname'])
		->hideCheckbox()
		->setPageTitle('生产计划列表')
		->addColumns([
			['__INDEX__','序号'],
			['number','编号'],
			['date','日期','date'],
			['name','主题'],
			['obj_id','项目名称'],	
			['nickname','制单人'],
			['right_button', '操作', 'btn']
		])
		->addOrder(['id','time']) // 添加排序
		->addRightButton('task_list',$task_list,true)

		->setRowList($data_list)//设置表格数据
		
		->setTableName('produce_plan')
		->fetch();
	}
	//查看
public function task_list($id = null){
		if($id == null) $this->error('参数错误');
	
		$info = FactpicModel::getOne($id);
		$info['date'] = date('Y-m-d',$info['date']);
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addFormItems([
			['static:4','date','日期'],
			['static:4','name','生产主题'],
			['static:4','obj_id','项目'],
			['static:4','uid','制单人'],
			['static','note','备注'],
			['archives','file','生产图纸']								
		])
		->setFormData($info)
		->fetch();
	}
	//添加项目
	public function add(){
		$name = session('user_auth')['role_name'];
		if($this->request->isPost()){
			$data = $this->request->post();
			// 验证
			$data['number'] = 'SG'.date('YmdHis',time());
			$data['uid'] = UID;
			$data['date'] = strtotime($data['date']);
			$result = $this->validate($data, 'Factpic');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			//验证失败错误信息
			//查看人员，隔开
			
			if($model = FactpicModel::create($data)){
				//记入行为
				
				$this->success('新增成功！',url('index'));
			}else{
				$this->error('新增失败！');
			}
		}
		$js = <<<EOF
            <script type="text/javascript">
            		//上传文件id 
            	var dp_file_upload_success = function () {
  					$('#fileid').val($('#path').val());
				};
                $(function(){  
                
                	        var aa = $('#unit1').find('option');
                	        var bb = $('#type1').find('option');
                	        var cc = $('#type2').find('option');
                	        
	                $('#name').change(function(){
	                	var nameid = $(this).find('option:selected').val();
	                	aa.each(function(){
	                		if($(this).val() == nameid){
                			  $('#unit').val($(this).text());
	                		}	                		
	                	});
	                	bb.each(function(){
	                		if($(this).val() == nameid){
                			  $('#type').attr('data-id',$(this).text());                			          			  
	                		}	
	                	});
	                	cc.each(function(){               	        	
								if($(this).val() == $('#type').attr('data-id')){
									$('#type').val($(this).text());						
								}                	        	
                	        })
	                })
                });
            </script>
EOF;
		$date = date("Y-m-d");
		return Zbuilder::make('form')
		->addFormItems([
			['date:4','date','日期','',date('Y-m-d')],
			['text:4','name','主题'],
			['select:4','obj_id','项目','',ObjModel::get_nameid()],
			['static:4','uid','制单人','',get_nickname(UID)],
			['textarea','note','备注'],
			['files','file','生产图纸']								
		])
		->setExtraJs($js)
		->fetch();
	}
	//编辑项目
	public function edit($id = null){
		if($id == null) $this->error('参数错误');
		if($this->request->isPost()){
			$data = $this->request->post();
			// 验证
			$result = $this->validate($data, 'Plan');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			if($model = ObjModel::update($data)){
				//记录行为
				
				return $this->success('修改成功',url('index'));
			}else{
				return $this->error('修改失败');
			}
		}
		$get_type = TypeModel::get_type();
		$info = PlanModel::where('id',$id)->find();
		$applicant = $info['applicant'];
		//获取昵称
			$nickname = Task_detailModel::get_nickname();
		$js = <<<EOF
            <script type="text/javascript">
                $(function(){  
                	        var aa = $('#unit1').find('option');
                	        var bb = $('#type1').find('option');
                	        var cc = $('#type2').find('option');               	        
	                $('#name').change(function(){
	                	var nameid = $(this).find('option:selected').val();
	                	aa.each(function(){
	                		if($(this).val() == nameid){
                			  $('#unit').val($(this).text());
	                		}	                		
	                	});
	                	bb.each(function(){
	                		if($(this).val() == nameid){
                			  $('#type').attr('data-id',$(this).text());                			          			  
	                		}	
	                	});
	                	cc.each(function(){               	        	
								if($(this).val() == $('#type').attr('data-id')){
									$('#type').val($(this).text());						
								}                	        	
                	        })
	                })
                });
            </script>
EOF;
$date = date("Y-m-d");
$unit = ObjModel::get_unit();
$get_typeid = ObjModel::get_typeid();
$get_type = TypeModel::get_type();			
		return ZBuilder::make('form')
		->addFormItems([
			['hidden', 'id'],
			['hidden','applicant',UID],		
			['static:6','appliname','申请人','',$nickname[$applicant]],
			['select:6','name','项目名称','',ObjModel::get_nameid()],
			['text:6','type','项目类型'],
			['select:6','type2','项目类型','',$get_type,'','','hidden'],
			['select:6','type1','项目类型','',$get_typeid,'','','hidden'],			
			['select:6','unit1','建设单位','',$unit,'','','hidden'],
			['text:6','unit','建设单位'],
			['number:6','money','招投标文件费用(元)'],			
			['date:6','time','日期','',$date],									
			['file:6','path','文件路径'],
			['textarea','note','备注'],
			['radio','status','状态', '', ['禁用', '启用'], 1],
		])
		->setFormData($info)
		->setExtraJs($js)
		->fetch();
	}
	//删除计划
	public function delete($ids = null){
		if($ids == null) $this->error('参数错误');
		return $this->setStatus('delete');
	}
 

	

}
