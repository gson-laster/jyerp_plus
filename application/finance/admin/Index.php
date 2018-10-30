<?php
namespace app\finance\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\finance\model\Finance_manager as finance_managerModel;
use think\Db;

/*
 账户信息
 * */
class Index extends Admin {
	protected $finance_manager;
    protected function _initialize(){
        parent::_initialize();
        $this -> finance_manager = new finance_managerModel();
    }
	public function Index(){
		$map = $this -> getMap();
		$order = $this -> getOrder();
		$data_list = finance_managerModel::getList($map, $order);
		
		 $task_list = [
						'title' => '查看详情',
						'icon' => 'fa fa-fw fa-eye',
						'href' => url('edit',['id'=>'__id__'])
						];
		return ZBuilder::make('table')
		  ->addColumns([ // 批量添加列
			['id', '编号'],
	        ['name', '账户名称'],
	        ['accmount', '账户'],
	        ['bank', '开户银行'],
	        ['address', '地址'],
	        ['date', '开户日期', 'date'],
	        ['nickname', '经办人'],
	        ['ismoneyaccount', '是否现金用户', [0=>'否', 1=>'是']],
	        ['status', '是否停用', 'switch'],
	        ['create_time', '创建时间', 'datetime'],
	        ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
	        ['right_button', '操作','btn'],
	    ])
        ->addTopButton('add') // 添加顶部按钮
		->setPageTitle('账户信息')
		->addOrder('date,create_time')
		->addRightButton('edit',$task_list,true)
		->addRightButton('delete')
		->addTopButton('delete') // 添加顶部按钮
		->setTableName('finance_manager') // 指定数据表名
		->setSearch(['finance_manager.name' => '账户名称','bank' => '开户银行'], '', '', true) // 设置搜索参数
		->addTimeFilter('date') // 添加时间段筛选
		->setRowList($data_list)
		->fetch();
	}
	public function add(){
		
		if($this -> request -> ispost()){
			$data = $this -> request ->post();
			$r = $this -> Validate($data, 'Index');
			if(true !== $r) $this -> error($r);
			$data['date'] = strtotime($data['date']);
			finance_managerModel::white(null, $data);
            $this->success('添加成功', 'index');
		}						
		return ZBuilder::make('form')
		->addFormItems([
		// 批量添加表单项
			['hidden','operator',UID],
			['text:6', 'name', '账户名称'],
			['text:6', 'accmount','账户'],
			['text:6', 'bank','开户银行'],
			['text:6', 'address','地址'],
			['date:6', 'date','开户日期'],
			['static:6','opername', '经办人','',get_nickname(UID)],
			['radio:6', 'ismoneyaccount','是否现金用户', '', [0=>'否', 1=>'是'],1],			
			['radio:6', 'status','是否启用', '', [0=>'否', 1=>'是'],1],			
			['textarea','note','备注'],
		])
		->fetch();
	
	}
	public function edit($id = null){
		if($this -> request -> ispost()){
			$data = $this -> request ->post();
			$r = $this -> Validate($data, 'Index');
			if(true !== $r) $this -> error($r);
			$data['date'] = strtotime($data['date']);
			finance_managerModel::white(['id' => $id] ,$data);
            $this->success('添加成功', 'index');
		}
		if (null == $id) $this -> error('参数错误');
		$data_list = $this -> finance_manager -> where('id', $id) -> find();
		$data_list['opename'] = Db::name('admin_user')->where('id',$data_list['operator'])->value('nickname');
		$data_list['date'] = date('Y-m-d',$data_list['date']);
		
		$arr = [0=>'否', 1=>'是'];
		$data_list['ismoneyaccount'] =$arr[$data_list['ismoneyaccount']];
		$arr1 = [0=>'否', 1=>'是'];
		$data_list['status'] = $arr[$data_list['status']];
		
		
		return ZBuilder::make('form')
		->addFormItems([
		// 批量添加表单项
			['static:6', 'name', '账户名称'],
			['static:6', 'accmount','账户'],
			['static:6', 'bank','开户银行'],
			['static:6', 'address','地址'],
			['static:6', 'date','开户日期'],
			['static:6', 'opename','经办人'],			
			['static:6', 'ismoneyaccount','是否现金用户'],			
			['static:6', 'status','是否启用', ''],			
			['static','note','备注'],
		])
		-> setFormData($data_list)
		->HideBtn('submit')
		->fetch();
	
	}
	public function delete($ids = null){
		if(null == $ids) $this -> error('参数错误');
     	return $this->setStatus('delete');
	}
}
