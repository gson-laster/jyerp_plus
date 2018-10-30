<?php
	
namespace app\tender\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\tender\model\Obj as ObjModel;
use app\tender\model\Type as TypeModel;
use app\tender\model\Plan as PlanModel;
use app\user\model\Organization as OrganizationModel;
use app\task\model\Task_detail as Task_detailModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use think\Db;
/**
 * 招标控制器
 * @author HJP
 */
class Plan extends Admin
{
	//招标文件购买申请列表
	public function index()
	{
		// 获取查询条件
		$map = $this->getMap();
		// 数据列表
		$data_list = PlanModel::where($map)->paginate();
		foreach ($data_list as $key => &$value) { 
				 $name = get_file_name($value['fileid']);           
                 $path = $value['path'];                              
                   $value['path'] = '<a href="'. $path.'"
                        data-toggle="tooltip" title="点击查看">'.$name.'</a>';                        
        }   
		//获取昵称
		$nickname = Task_detailModel::get_nickname();
		$get_nameid = ObjModel::get_nameid();
		// 分页数据
		$page = $data_list->render();
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
		
		return ZBuilder::make('table')
		->setSearch(['id' => '编号', 'type' => '项目类型', 'applicant' => '申请人']) // 设置搜索参数
		->addFilter('type') // 添加筛选
		->addFilter('name',$get_nameid) // 添加筛选
		->setPageTitle('招标文件购买申请列表')
		->addColumns([
			['id','编号'],
			['applicant','申请人','','',$nickname],
			['name','项目名称','','',$get_nameid],
			['type','项目类型'],
			['unit','建设单位'],	
			['money','招投标文件费(元)'],	
			['time','投标日期'],
			['path', '附件', '', '', '', 'js-gallery'],
			['status','状态','switch'],
			['right_button','操作','btn'],
		])
		->addOrder(['id','time']) // 添加排序
		->addTopButtons(['delete'])//添加顶部按钮
		->addRightButtons(['edit','delete' => ['data-tips' => '删除申请将无法恢复。']])
		->setRowList($data_list)//设置表格数据
		->addRightButton('task_list',$task_list) // 查看右侧按钮 
		->setTableName('tender_plan')
		->fetch();
	}
	//查看
	public function task_list($id = null){
		if($id == null) $this->error('参数错误');
		if($this->request->isPost()){
			$data = $this->request->post();
			//验证
			
			//验证失败输出错误信息
			if($model = PlanModel::update($data)){
				//记录行为
				
				return $this->success('修改成功',url('index'));
			}else{
				return $this->error('修改失败');
			}
		}
		$get_type = TypeModel::get_type();
		$info = PlanModel::where('id',$id)->find();
		$applicant = $info['applicant'];
		$name = get_file_name($info['fileid']);
		$info['path'] = $name;
		//获取昵称
			$nickname = Task_detailModel::get_nickname();
			$get_nameid = ObjModel::get_nameid();		
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addFormItems([
			['hidden','id'],		
			['text:4','appliname','申请人','',$nickname[$applicant],'','disabled'],
			['select:4','name','项目名称','',$get_nameid,'','disabled'],
			['text:4','type','项目类型','','','','disabled'],
			['text:4','unit','建设单位','','','','disabled'],
			['text:4','money','招投标文件费用(元)','','','','disabled'],			
			['date:4','time','日期','','','','disabled'],								
			['text:4','path','文件路径','','','','disabled'],
			['textarea','note','备注','','','disabled'],
			['radio','status','状态', '', ['禁用', '启用'], 1,'','disabled'],			
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
			$result = $this->validate($data, 'Plan');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			$data['path'] = get_file_path($data['path']);
			//验证失败错误信息
			//查看人员，隔开
			
			if($model = PlanModel::create($data)){
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
$unit = ObjModel::get_unit();
$get_typeid = ObjModel::get_typeid();
$get_type = TypeModel::get_type();
		return Zbuilder::make('form')
		->addFormItems([
			['hidden', 'fileid'],
			['hidden','applicant',UID],		
			['static:6','appliname','申请人','',$name,'','disabled'],
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
