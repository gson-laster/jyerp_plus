<?php
	
namespace app\tender\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\tender\model\Obj as ObjModel;
use app\tender\model\Type as TypeModel;
use app\user\model\Organization as OrganizationModel;
use app\task\model\Task_detail as Task_detailModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use think\Db;
use app\sales\model\Contract;
/**
 * 招标控制器
 * @author HJP
 */
class Index extends Admin
{
	//投标项目列表
	public function index()
	{
		// 获取查询条件
		$map = $this->getMap();
		// 数据列表
		$data_list = ObjModel::where($map)->paginate();     
		//获取昵称
		$nickname = Task_detailModel::get_nickname();
		// 分页数据
		$page = $data_list->render();
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
		$get_type = TypeModel::get_type();
		return ZBuilder::make('table')
		->setSearch(['code' => '编号', 'name' => '项目名称'], '', '', true) // 设置搜索参数
		->addFilter('type',$get_type) // 添加筛选
		->setPageTitle('项目列表')
		->addColumns([
			['code','编号'],
			['name','项目名称'],
			['type','项目类型','','',$get_type],
			['address','项目地址'],
			['zrid','责任人','','',$nickname],
			//['tender_time','投标日期','date'],
			['unit','建设单位'],
			['contact','联系人'],
			['phone','联系人电话'],
			['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
			['right_button','操作','btn'],
		])
		->addOrder(['code']) // 添加排序
		->addTopButtons(['delete'])//添加顶部按钮
		->addRightButtons(['edit','delete' => ['data-tips' => '删除项目将无法恢复。']])
		->setRowList($data_list)//设置表格数据
		->addRightButton('task_list',$task_list) // 查看右侧按钮 
		->setTableName('tender_obj')
		->fetch();
	}
	//查看
	public function task_list($id = null){
		if($id == null) $this->error('参数错误');
		if($this->request->isPost()){
			$data = $this->request->post();
			//验证
			
			//验证失败输出错误信息
			if($model = ObjModel::update($data)){
				//记录行为
				
				return $this->success('修改成功',url('index'));
			}else{
				return $this->error('修改失败');
			}
		}
		$get_type = TypeModel::get_type();
		$info = ObjModel::where('id',$id)->find();
			
		$get_sale = Contract::getCname();
		//dump($id);die;
		
		//获取昵称
			$nickname = Task_detailModel::get_nickname();
			$zrid = $info['zrid'];	
			$lxid = $info['lxid'];				
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addFormItems([
			['hidden', 'id'],
			['text:6','name','项目名称','','','','disabled'],
			['select:4','sale','销售合同','',$get_sale,'','disabled'],
			['date:4','start_time','计划开始日期','','','','disabled'],
			['date:4','end_time','计划结束日期','','','','disabled'],
			['text:6','address','项目地址','','','','disabled'],
			['textarea','info','项目简介','','','disabled'],
			['text:4','obj_time','工程工期(天)','','','','disabled'],
			['text:4','estimate','工程量估算(元)','','','','disabled'],
			['text:4','cost','工程造价(元)','','','','disabled'],
			['text:4','profit','预期利润(元)','','','','disabled'],
			['select:4','type','项目类型','',$get_type,'','disabled'],
			['text:4','zrname','项目追踪人','',$nickname[$zrid],'','disabled'],
			['select:4','bmid','所属部门','',OrganizationModel::getMenuTree2(),'','disabled'],						
			
			['text:4','unit','建设单位','','','','disabled'],
			['text:4','contact','联系人','','','','disabled'],
			['number:4','phone','联系电话','','','','','','disabled'],
			['text:4','lxaddrss','联系地址','','','','disabled'],
			['static:4','lxname','立项人','',$nickname[$lxid]],
			['archives','file','附件'],		
			['textarea','note','备注','','','disabled'],		
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
			$result = $this->validate($data, 'Obj');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			//验证失败错误信息
			//查看人员，隔开
			$data['code'] = 'XMGL'.date('YmdHis',time());
			$data['start_time'] = strtotime($data['start_time']);
			$data['end_time'] = strtotime($data['end_time']);

			if($model = ObjModel::create($data)){
				flow_detail($data['name'],'tender_obj','tender_obj','tender/index/task_list',$model['id']);
				//记入行为
				
				$this->success('新增成功！',url('index'));
			}else{
				$this->error('新增失败！');
			}
		}
		return ZBuilder::make('form')
		->addFormItems([
			['hidden','zrid'],
			['hidden','lxid',UID],
			['text:4','name','项目名称'],
			['select:4','sale','销售合同','',Contract::getName()],
			['date:4','start_time','计划开始日期'],
			['date:4','end_time','计划结束日期'],
			['text:6','address','项目地址'],
			['textarea','info','项目简介'],
			['text:4','obj_time','工程工期(天)'],
			['text:4','estimate','工程量估算(元)'],
			['text:4','cost','工程造价(元)'],
			['text:4','profit','预期利润(元)'],
			['select:4','type','项目类型','',TypeModel::get_type()],
			['text:4','zrname','项目追踪人'],
			['select:4','bmid','所属部门','',OrganizationModel::getMenuTree2()],			
			['text:4','unit','建设单位'],
			['text:4','contact','联系人'],
			['number:4','phone','联系电话'],
			['text:4','lxaddrss','联系地址'],
			['static:4','lxname','立项人','',$name],
			['files','file','附件'],
			['textarea','note','备注'],							
		])
		->setExtraHtml(outhtml2())
		->setExtraJs(outjs2())
		->fetch();
	}
	//编辑项目
	public function edit($id = null){
		if($id == null) $this->error('参数错误');
		if($this->request->isPost()){
			$data = $this->request->post();
			$data['start_time'] = strtotime($data['start_time']);
			$data['end_time'] = strtotime($data['end_time']);	
			// 验证
			$result = $this->validate($data, 'Obj');
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
		$info = ObjModel::where('id',$id)->find();
		//获取昵称
			$nickname = Task_detailModel::get_nickname();
			$zrid = $info['zrid'];
			$lxid = $info['lxid'];
		return ZBuilder::make('form')
		->addFormItems([
			['hidden', 'id'],
			['hidden','zrid'],
			['text:6','name','项目名称'],
			
			['date:4','start_time','计划开始日期'],
			['date:4','end_time','计划结束日期'],
			['text:6','address','项目地址'],
			['textarea','info','项目简介'],
			['text:4','obj_time','工程工期(天)'],
			['text:4','estimate','工程量估算(元)'],
			['text:4','cost','工程造价(元)'],
			['text:4','profit','预期利润(元)'],
			['select:4','type','项目类型','',$get_type],
			['text:4','zrname','项目追踪人','',$nickname[$zrid]],
			['select:4','bmid','所属部门','',OrganizationModel::getMenuTree2()],						
			//['date:4','tender_time','日期'],			
			['text:4','unit','建设单位'],
			['text:4','contact','联系人'],
			['number:4','phone','联系电话'],
			['text:4','lxaddrss','联系地址'],
			['static:4','lxname','立项人','',$nickname[$lxid]],
			['archives','file','附件'],		
			['textarea','note','备注'],
			['radio','status','状态', '', ['禁用', '启用'], 1],
		])
		->setExtraHtml(outhtml2())
		->setExtraJs(outjs2())
		->setFormData($info)
		->fetch();
	}
	//删除计划
	public function delete($ids = null){
		if($ids == null) $this->error('参数错误');
		return $this->setStatus('delete');
	}

}
