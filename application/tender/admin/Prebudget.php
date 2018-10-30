<?php
	
namespace app\tender\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use think\Db;
use app\tender\model\Prebudget as PrebudgetModel;
/**
 * 招标控制器
 * @author HJP
 */
class Prebudget extends Admin
{
	//招标文件购买申请列表
	public function unbudget()
	{
		// 获取查询条件
		$map = $this->getMap();
		// 数据列表
		$data_list = PrebudgetModel::getList();
		// 分页数据
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
		
		return ZBuilder::make('table')
		->setSearch(['id' => '编号', 'type' => '项目类型', 'applicant' => '申请人']) // 设置搜索参数
		->hideCheckbox()
		->addFilter('type') // 添加筛选
		->setPageTitle('待算项目列表')
		->addColumns([
			['code','编号'],
			['name','名称'],
			['found_time','发现时间','date'],
			['status_pre', '预算结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
			['right_button','操作','btn'],
		])
		->addOrder(['id','time']) // 添加排序
		->addTopButtons(['delete'])//添加顶部按钮
		//->addRightButtons(['delete' => ['data-tips' => '删除申请将无法恢复。']])
		->addRightButton('task_list',$task_list,true)
		->setRowList($data_list)//设置表格数据
		->setTableName()
		->fetch();
	}
	//查看
	public function task_list($id){
	
	
			//验证失败输出错误信息
	
		$info = PrebudgetModel::getOne($id);
		//dump($info);
		//exit;
		$info['found_time'] = date('Y-m-d',$info['found_time']);
		//获取昵称
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addFormItems([
			['static:6','name','机会名称'],
			['static:6','customer_name','客户名称'],
			['static:6','phone','客户电话'],
			['static:6','found_time','发现时间'],
			['static:6','zrid','申请人'],
			['archives','file','附件'],	
			['static','note','备注'],						
		])
		->setFormData($info)
		->fetch();

	}
	//添加项目
	public function add(){
		
		if($this->request->isPost()){
			$data = $this->request->post();
			$data['date'] = strtotime($data['date']);
			// 验证
			$data['maker']=UID;
			//dump($data);DIE;
			//if($data['pre_date'] < 1) $this -> error('预计所需天数不能小于1');
			$result = $this->validate($data,'Prebudget');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			
			if($model = PrebudgetModel::create($data)){
				//记入行为
				flow_detail($data['name'],'tender_prebudget','tender_prebudget','tender/prebudget/task_lists',$model['id']);
				
				
				$this->success('新增成功！',url('unbudget'));
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
                $(function(){
                   $('#money').attr('oninput','return Edit1Change();');				   					
                });
				var j=chineseNumber(document.getElementById("money").value);
				document.getElementById("big_money").value=j;		
				function Edit1Change(){			
					document.getElementById("big_money").value=chineseNumber(document.getElementById("money").value);
				}
                
                
                
            </script>
EOF;




		return Zbuilder::make('form')
		->addFormItems([
			['text:6','name','预算主题'],
			['select:6','item','预算对象','',PrebudgetModel::getName()],
			['number:6','pre_date','预算所需天数'],
			['number:6','money','预算总额'],
			['text:6','big_money','大写金额'],	
			['date:6','date','日期','',date('Y-m-d')],									
			['text:6','maker','预算人','',get_nickname(UID)],
			['files','money_detail','预算详细'],
			['files','page','图纸'],
			//['files','materials','备料单'],
			['textarea','note','备注'],
		])
		->setExtraJs($js)
		->js('chineseNumber')
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
 
	
	
	//已预算列表
	public function budget(){
		
		$map = $this->getMap();
		$order = $this->getOrder('create_time desc');
		// 数据列表
		$data_lists = PrebudgetModel::getbudget($map, $order);
		foreach($data_lists as $key => &$value){
			$value['money'] = '￥ '.number_format($value['money'],2);
		}
		// 分页数据
		$task_lists = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_lists',['id'=>'__id__'])
		];				
		return Zbuilder::make('table')
		->addColumns([
			['id','序号'],
			['name','预算主题'],
			['sname','预算对象'],
			['money','预算总额'],
			['big_money','大写金额'],	
			['pre_date','预计所需天数'],									
			['date','日期', 'date'],									
			['mname','预算人'],
			['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],			
			['right_button','操作','btn'],
		])
        ->addTimeFilter('tender_prebudget.date') // 添加时间段筛选
		
    	->addOrder('tender_prebudget.id,tender_prebudget.date') // 添加排序
		->addRightButton('tasks_list',$task_lists,true)
		->setExtraJs()
		->js('chineseNumber')
		->hideCheckbox()
		->setRowList($data_lists)
		->fetch();
		
		}
		
		
		public function task_lists($id){
			
		$map = $this->getMap();
		$map['tender_prebudget.id']=$id;
			//验证失败输出错误信息
		$info = PrebudgetModel::getOneBudget($map);
		$info['date'] = date('Y-m-d',$info['date']);
		$info['money'] = '￥'.number_format($info['money'],2);
		//获取昵称
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addFormItems([
			['static:6','name','预算主题'],
			['static:6','item','预算对象'],
			['static:6','money','预算总额'],
			['static:6','big_money','大写金额'],	
			['static:6','pre_date','预计所需天数'],									
			['static:6','date','日期'],									
			['static:6','maker','预算人'],
			['archives','money_detail','预算详细'],
			['archives','page','图纸'],
			//['files','materials','备料单'],
			['static','note','备注'],
		])
		->setFormData($info)
		->fetch();
			
			
			
			}
	

}
