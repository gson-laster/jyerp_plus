<?php
	
namespace app\sales\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\sales\model\Opport as OpportModel;
use app\sales\model\Plan as PlanModel;
use app\user\model\Organization as OrganizationModel;
use app\task\model\Task_detail as Task_detailModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use app\supplier\model\Client as ClientModel;
use app\supplier\model\Clienttype as ClienttypeModel;
use app\supplier\model\Clientphone as ClientphoneModel;
use think\Db;
/**
 * 销售机会控制器
 * @author HJP
 */
class Opport extends Admin
{	
	//销售机会
	public function index(){
		// 获取查询条件
		$map = $this->getMap();
		// 排序
		$order = $this->getOrder('sales_opport.create_time desc');
		// 数据列表
		$data_list = OpportModel::getList($map,$order);
		// 分页数据
		$page = $data_list->render();
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-search',
			'href' => url('task_list',['id'=>'__id__'])
		];
		return ZBuilder::make('table')
		->setPageTitle('机会列表')
		->setSearch(['sales_opport.name' => '机会名称', 'customer_name' => '客户名称'], '', '', true) // 设置搜索参数
		->addFilter(['supplier_clienttype'=>'supplier_clienttype.name']) // 添加筛选
		->addFilter('sales_opport.type',[0=>'互联网',1=>'电话']) // 添加筛选
		->addColumns([
			['code','编号'],
			['name','机会名称'],
			['customer_name','客户名称'],
			['phone','客户联系方式(手机)'],
			['supplier_clienttype','客户类型'],
			['type','机会类型',[0=>'互联网',1=>'电话']],
			['found_time','发现时间','date'],
			['zrid','业务员'],
			['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
			['right_button','操作','btn'],
		])
		->addOrder(['code','found_time']) // 添加排序
		->addTopButtons(['delete'])//添加顶部按钮
		->addRightButtons(['edit','delete' => ['data-tips' => '删除机会将无法恢复。']])
		->addRightButton('task_list',$task_list,true) // 查看右侧按钮 
		->setRowList($data_list)//设置表格数据
		->fetch();
	}
	//添加销售机会
	public function add(){
		$name = session('user_auth')['role_name'];
		if($this->request->isPost()){
			$data = $this->request->post();
			// 验证
				//dump($data);die;
			$result = $this->validate($data, 'Opport');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			$data['code'] = 'XSJH'.date('YmdHis',time());
            $data['found_time'] = strtotime($data['found_time']);
            $data['zdid'] = UID;
			//查看人员，隔开
			$data['helpid'] = ','.$data['helpid'];
			if($model = OpportModel::create($data)){
				flow_detail($data['name'],'sales_opport','sales_opport','sales/opport/task_list',$model['id']);
				//记入行为
				
				$this->success('新增成功！',url('index'));
			}else{
				$this->error('新增失败！');
			}
		}
		$date = date('Y-m-d');
		return Zbuilder::make('form')
		->addFormItems([
			['hidden','zrid'],
			['hidden','zdid',UID],
			['hidden','helpid'],
			['text:4','name','机会名称'],
			['select:4','customer_name','客户名称','',ClientModel::getName()],
			['text:4','supplier_clienttype','客户类型','','','','disabled'],
			['text:4','phone','客户电话','','','','disabled'],
			['select:4','type','机会类型','',[0=>'互联网',1=>'电话']],
			['date:4','found_time','发现时间','',$date],
			['text:4','zrname','业务员'],
			['static:4','zdname','制单人','',get_nickname(UID)],
			['static:4','create_time','制单时间','',$date],
			['select:4','department','所属部门','', OrganizationModel::getMenuTree2(), '', url('get_user'), 'user_id', 'organization'],
			['textarea:8','helpname','可查看人员'],
			['files','file','附件'],	
			['textarea','note','备注'],						
		])
		->setExtraHtml(outhtml2())
		->setExtraJs(outjs2())
		->js('Opport')
		->fetch();
	}
	public function get_Detail($customer_name = ''){
			$data = OpportModel::get_Detail($customer_name);
		return $data;
	}
	public function edit($id = null){
		if($id == null) $this->error('参数错误');
		if($this->request->isPost()){
			$data = $this->request->post();
			// 验证
			$result = $this->validate($data, 'Opport');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			if($model = OpportModel::update($data)){
				//记录行为
				
				return $this->success('修改成功',url('index'));
			}else{
				return $this->error('修改失败');
			}
		}
		$info = OpportModel::getOne($id);
		$info['helpname'] = Task_detailModel::get_helpname($info['helpid']);
		return ZBuilder::make('form')
		->addFormItems([
			['hidden', 'id'],
			['hidden','zrid'],
			['hidden','helpid'],
			['text:4','name','机会名称'],
			['select:4','customer_name','客户名称','',ClientModel::getName()],
			['text:4','supplier_clienttype','客户类型','','','','disabled'],	
			['text:4','phone','客户电话','','','','disabled'],
			['select:4','type','机会类型','',[0=>'互联网',1=>'电话']],
			['date:4','found_time','发现时间'],
			['text:4','zrname','业务员'],	
			['static:4','zdid','制单人'],
			['static:4','create_time','制单时间'],		
			['select:4','department','所属部门','', OrganizationModel::getMenuTree2()],
			['textarea:8','helpname','可查看人员'],	
			['files','file','附件'],	
			['textarea','note','备注'],						
		])
		->setExtraHtml(outhtml2())
		->setExtraJs(outjs2())
		->js('opport')
		->setFormData($info)
		->fetch();
	}
	//查看
	public function task_list($id = null){
		if($id == null) $this->error('参数错误');
		$info = OpportModel::getOne($id);
        $info->create_time = date('Y-m-d',$info['create_time']);
        $info->found_time = date('Y-m-d',$info['found_time']);
		$info['helpname'] = Task_detailModel::get_helpname($info['helpid']);
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addFormItems([
			['hidden', 'id'],
			['static:4','name','机会名称'],
			['static:4','customer','客户名称'],
			['static:4','supplier_clienttype','客户类型'],
			['static:4','phone','客户电话'],
			['select:4','type','机会类型','',[0=>'互联网',1=>'电话']],		
			['static:4','found_time','发现时间'],
			['static:4','zrname','业务员'],
			['static:4','zdid','制单人'],
			['static:4','create_time','制单时间'],
			['static:4','bm','所属部门'],
			['static','helpname','可查看人员'],			
			['archives','file','附件'],		
			['static','note','备注'],			
		])
		->setFormData($info)
		->fetch();
	}
	//删除计划
	public function delete($record = [])
    {
   		$ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    	// 删除节点
    	if (OpportModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		//$details = '生产任务ID('.$ids.'),操作人ID('.UID.')';
    		//action_log('produce_plan_delete', 'produce_plan', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }
	
}
