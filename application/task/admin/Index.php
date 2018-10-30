<?php
	
namespace app\task\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\task\model\Task_detail as Task_detailModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use think\Db;
/**
 * 任务控制器
 * @author HJP
 */
class Index extends Admin
{
	public function index()
	{
//		$data = Task_detailModel::getTree(0);
//		
//		dump($data);die;
		// 获取查询条件
		$map = $this->getMap();
		// 数据列表
		$data_list = Task_detailModel::where($map)->paginate();
		// 分页数据
		$page = $data_list->render();
		//获取昵称
		$nickname = Task_detailModel::get_nickname();
		// 使用ZBuilder快速创建数据表格
		return ZBuilder::make('table')
		->setPageTitle('任务列表')//标题
		->addColumns([
			['id','ID'],
			['name','任务名称'],
			['uid','发起人','','',$nickname],
			['zrid','责任人','','',$nickname],
			['helpid','协助人','callback', function($value){
		    	return Task_detailModel::get_helpname($value);
		    }],
			['start_time','开始时间'],
			['end_time','结束时间'],
			['content','任务说明'],
			['status','状态','status','',[-1=>'未开始',0=>'进行中',1=>'已完成']],
			['right_button','操作','btn'],
		])
		->addTopButtons(['add' => ['title' => '发起任务'],'delete'])//添加顶部按钮
		->addRightButtons(['edit','delete' => ['data-tips' => '删除任务将无法恢复。']])//添加右侧按钮
		->setRowList($data_list)//设置表格数据
		->setTableName('task_detail')
		->fetch();
	}
	//发起任务
	public function add()
	{
		$name = session('user_auth')['role_name'];
		if($this->request->isPost()){
			$data = $this->request->post();
			//验证
						
			//验证失败输出错误信息
			$data['helpid'] = ','.$data['helpid'];
			if ($model = Task_detailModel::create($data)){
				//记入行为
				
				$this->success('新增成功',url('index'));
			}else{
				$this->error('新增失败');
			}
		}						
		return ZBuilder::make('form')
		->addFormItems([
			['hidden','uid',UID],
			['hidden','zrid',UID],
			['hidden','helpid'],
			['text','name','任务名称','<span class="text-danger">必填</span>'],
			['text','zrname','责任人','<span class="text-danger">必填</span>',$name],
			['text','helpname','协助人'],			
		])
		->addDate('start_time', '开始时间')
		->addDate('end_time', '结束时间')
		->addTextarea('content', '任务说明')
		->addRadio('status','状态','',[-1 => '未开始',0 => '进行中',1 =>'已完成'],-1)
		->setExtraHtml(outhtml2())
		->setExtraJs(outjs2())
		->fetch();
	}
	//删除任务
	public function delete($ids = null){
		if($ids == null) $this->error('参数错误');
		return $this->setStatus('delete');
	}
	//编辑任务
	public function edit($id = null){
		if($id == null) $this->error('参数错误');
		if($this->request->isPost()){
			$data = $this->request->post();
			//验证
						
			//验证失败输出错误信息
			$data['helpid'] = ','.$data['helpid'];
			if($model = Task_detailModel::update($data)){
				//记入行为
				
				return $this->success('修改成功', url('index'));
			}else{
				return $this->error('修改失败');
			}			
		}
			$info = Task_detailModel::where('id', $id)->find();
			//获取昵称
			$nickname = Task_detailModel::get_nickname();
			$zrid = $info['zrid'];
			$helpid = $info['helpid'];
			$helpmane = Task_detailModel::get_helpname($helpid);
			// 使用ZBuilder快速创建表单
			return ZBuilder::make('form')
			->addFormItems([
				['hidden', 'id'],
				['hidden','uid'],
				['hidden','zrid'],
				['hidden','helpid'],
				['text','name','任务名称'],
				['text','zrname','责任人','',$nickname[$zrid]],
				['text','helpname','协助人','',$helpmane],				
			])
			->addDate('start_time', '开始时间')
			->addDate('end_time', '结束时间')
			->addTextarea('content', '任务说明')
			->addRadio('status','状态','',[-1 => '未开始',0 => '进行中',1 =>'已完成'],-1)
			->setExtraHtml(outhtml2())
			->setExtraJs(outjs2())
			->setFormData($info)
			->fetch();
	}
	//我的任务
	public function mytask(){
		// 获取查询条件
		$map = $this->getMap();		 
		// 数据列表		
		$data_list = Task_detailModel::where($map)->where(['zrid'=>UID])->whereOr("locate(',".UID.",',`helpid`)>0")->paginate();
		// 分页数据
		$page = $data_list->render();
		//获取昵称
		$nickname = Task_detailModel::get_nickname();
		// 使用ZBuilder快速创建数据表格
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
		$start = [
			'title' => '点击开始',
			'icon' => 'fa fa-fw fa-toggle-right',
			'class' => 'btn btn-xs btn-default ajax-get confirm',
			'href' => url('start',['id'=>'__id__']),
			'data-title' => '确定开始'
		];
		$complete = [
			'title' => '点击完成',
			'icon' => 'fa fa-fw fa-check',
			'class' => 'btn btn-xs btn-default ajax-get confirm',
			'href' => url('complete',['id'=>'__id__']),
			'data-title' => '确定已完成'
		];
		return ZBuilder::make('table')
		->setPageTitle('我的任务')//标题
		->addColumns([
			['id','ID'],
			['name','任务名称'],			
			['zrid','责任人','','',$nickname],
			['helpid','协助人','callback', function($value){
		    	return Task_detailModel::get_helpname($value);
		    }],
			['start_time','开始时间'],
			['end_time','结束时间'],
			['content','任务说明'],
			['status','状态','status','',[-1=>'未开始',0=>'进行中',1=>'已完成']],
			['right_button','操作','btn'],
		])
		->addRightButton('task_list',$task_list,true) // 查看右侧按钮 
		->addRightButton('start',$start) // 查看右侧按钮 
		->addRightButton('complete',$complete) // 查看右侧按钮 
		->replaceRightButton(['status' => ['in', '0,1']],'','start')//不显示开始
		->replaceRightButton(['status' => ['in', '-1,1']],'','complete')//不显示完成
		->setRowList($data_list)//设置表格数据
		->setTableName('task_detail')
		->fetch();
	}
	public function start($id = null){
		if($id == null) $this->error('缺少参数');
		$map = ['status' => 0];
		if($model = Task_detailModel::where(['id'=>$id])->update($map)){
			$this->success('操作成功');
		}else{
			$this->error('操作失败');
		}
	}
	public function complete($id = null){
		if($id == null) $this->error('缺少参数');
		$map = ['status' => 1];
		if($model = Task_detailModel::where(['id'=>$id])->update($map)){
			$this->success('操作成功');
		}else{
			$this->error('操作失败');
		}
	}
	public function task_list($id = null){
		if($id == null) $this->error('参数错误');			
			$info = Task_detailModel::where('id', $id)->find();
			//获取昵称
			$nickname = Task_detailModel::get_nickname();
			$zrid = $info['zrid'];
			$helpid = $info['helpid'];
			$helpmane = Task_detailModel::get_helpname($helpid);
			// 使用ZBuilder快速创建表单
			return ZBuilder::make('form')
			->hideBtn('submit')
			->addFormItems([
				['hidden', 'id'],
				['hidden','uid'],
				['hidden','zrid'],
				['hidden','helpid'],
				['text:6','name','任务名称','','','','disabled'],
				['text:6','zrname','责任人','',$nickname[$zrid],'','disabled'],
				['text:6','helpname','协助人','',$helpmane,'','disabled'],
				['date:6','start_time', '开始时间','','','','disabled'],
				['date:6','end_time', '结束时间','','','','disabled'],
				['textarea','content', '任务说明','','','disabled'],				
			])
			->addRadio('status','状态','',[-1 => '未开始',0 => '进行中',1 =>'已完成'],-1,'','disabled')
			->setFormData($info)
			->fetch();

	}
	
	}
