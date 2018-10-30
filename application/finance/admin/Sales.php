<?php
namespace app\finance\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use think\Db;
use app\finance\model\Sales as SalesModel;
/**
 *  施工日志
 */
class Sales extends Admin
{
	//
	public function index(){
		// 获取查询条件
		$map = $this->getMap();
		// 排序
		$order = $this->getOrder('sales_contract.create_time desc');
		// 数据列表
		$data_list = SalesModel::getList($map,$order);
		foreach($data_list as $key => &$value){
			$value['money'] = '￥ '.number_format($value['money'],2);
		}
		// 分页数据
		$page = $data_list->render();
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-search',
			'href' => url('task_list',['id'=>'__id__'])
		];
		return ZBuilder::make('table')
		->setPageTitle('合同列表')
		->setSearch(['sales_contract.name' => '合同名称','customer_name'=>'客户名称'], '', '', true)
		->addOrder(['code','document_time']) // 添加排序
		->addColumns([
			['code','编号'],
			['name','合同名称'],
			['customer_name','客户名称'],
			['phone','客户联系方式'],		
			['money','合同金额'],	
			['document_time','开始时间','date'],
			['adderss','签约地点'],
			['zrid','业务员'],	
			['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],			
			['right_button','操作','btn'],
		])
		->addOrder(['code','document_time']) // 添加排序
		->addRightButton('task_list',$task_list,true) // 查看右侧按钮 
		->setRowList($data_list)//设置表格数据
		->fetch();
	}

    //查看
	public function task_list($id = null){
		if($id == null) $this->error('参数错误');
		$info = SalesModel::getOne($id);
        $info->document_time = date('Y-m-d',$info['document_time']);
        $info->create_time = date('Y-m-d',$info['create_time']);
        $info->end_time = date('Y-m-d',$info['end_time']);
				$info['money'] = '￥'.number_format($info['money'],2);
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addFormItems([
			['hidden', 'id'],
			['static:4','name','合同名称'],
		
			['static:4','customer_name','客户名称'],
			['static:4','phone','客户联系方式(手机)'],
			['select:4','paytype','支付方式','',[-2=>'转账',-1=>'支付宝',0=>'微信',1=>'支票',2=>'现金']],
			['select:4','goodtype','交货方式','',[0=>'一次性交货',1=>'分批交货']],
			['select:4','transport','运送方式','',[-1=>'空运',0=>'海运',1=>'快递']],
			['select:4','currency','币种','',[-1=>'美元',0=>'人民币',1=>'欧元']],
			['static:4','parities','汇率%'],
			['static:4','money','合同金额'],
			['static:4','zrid','业务员'],
		
			['static:4','document_time','开始日期'],
			['static:4','adderss','签约地址'],	
			['static:4','end_time','截止日期'],					
			['static:4','zdid','制单人'],	
			['static:4','create_time','制单时间'],				
		])
		->setFormData($info)
		->fetch();
	}

}   
