<?php
	//合同
namespace app\sales\model;
use think\Model as ThinkModel;
use think\Db;
class Contract extends ThinkModel
{
	protected $table = '__SALES_CONTRACT__';
	protected $autoWriteTimestamp = true;
	public static function getList($map = [], $order = []){
		$data_list = self::view('sales_contract', true)
		->view('admin_user',['nickname'=>'zrid'],'admin_user.id=sales_contract.zrid','left')
		->view('supplier_client',['name'=>'customer_name'],'supplier_client.id=sales_contract.customer_name','left')
		->view('supplier_client a' ,['phone'],'a.id=sales_contract.customer_name','left')
		->where($map)
		->where("locate(',".UID.",',`helpid`)>0")
    	->order($order)
    	->paginate();
    	return $data_list;
	}
	public static function getOne1($id = ''){
		$monophyletic = self::where('id',$id)->value('monophyletic');
		switch ($monophyletic)
		{
			case 0:
				return '';
				break;
			case 1:
				return self::view('sales_contract', true)
				->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_contract.zrid','left')
				->view('admin_user b',['nickname'=>'zdname'],'b.id=sales_contract.zdid','left')
		    	->view('admin_organization',['title'=>'bm'],'admin_organization.id=sales_contract.oid','left')
		    	->view('sales_opport',['name'=>'monophycode'],'sales_opport.id=sales_contract.monophycode')
		    	->where(['sales_contract.id'=>$id])
		    	->find();
		    	break;
		    case 2:
		    	return self::view('sales_contract', true)
				->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_contract.zrid','left')
				->view('admin_user b',['nickname'=>'zdname'],'b.id=sales_contract.zdid','left')
		    	->view('admin_organization',['title'=>'bm'],'admin_organization.id=sales_contract.oid','left')
		    	->view('sales_offer',['name'=>'monophycode'],'sales_offer.id=sales_contract.monophycode')
		    	->where(['sales_contract.id'=>$id])
		    	->find();
		    	break;
		}
	}
	public static function getOne($id = ''){
		$data_list = self::view('sales_contract', true)
		->view('admin_user',['nickname'=>'zrid'],'admin_user.id=sales_contract.zrid','left')
		->view('admin_organization',['title'=>'oid'],'admin_organization.id=sales_contract.oid','left')
		->view('sales_plan',['name'=>'monophycode'],'sales_plan.id=sales_contract.monophycode','left')
		->view('supplier_client',['name'=>'customer_name'],'supplier_client.id=sales_contract.customer_name','left')
		->view('supplier_client a' ,['phone'],'a.id=sales_contract.customer_name','left')
		->where(['sales_contract.id'=>$id])
    	->find();
    	return $data_list;
	}
	//获取合同名称
	public static function getName($status_item = -1){
		$map['status'] = 1;
		if ($status_item != -1) {
			$map['status_item'] = $status_item;
		}
		$result = self::where($map)->column('id,name');
		return $result;
	}
	
	public static function getCname(){
		$data_list = Db::name('sales_contract')->where('status',1)->column('id,name');
		return $data_list;
		//dump($data_list);die;
		
		}
	//获取单源明细
	public static function getDetail($id = ''){
		$getDetail = self::view('sales_contract',['id','monophycode','zrid','oid','customer_name','phone'])
		->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_contract.zrid','left')
    	->view('admin_organization',['title'=>'department'],'admin_organization.id=sales_contract.oid','left')
		->where(['sales_contract.id'=>$id])
		->find();	
 		return $getDetail;
	}
		//获取单源明细
	public static function get_Detail($id = ''){
		$getDetail = self::view('sales_plan',['customer_name','zrid','department','phone'])
		->view('admin_organization',['title'=>'ooid'],'admin_organization.id=sales_plan.department','left')
		->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_plan.zdid','left')
		->where(['sales_plan.id'=>$id])
		->find();		
 		return $getDetail;
	}
	public static function getMobileList($map = [], $order = []){
		$data_list = self::view('sales_contract', true)
		->view('admin_user',['nickname'=>'zrid'],'admin_user.id=sales_contract.zrid','left')
		->view('supplier_client',['name'=>'customer_name'],'supplier_client.id=sales_contract.customer_name','left')
		->view('supplier_client a' ,['phone'],'a.id=sales_contract.customer_name','left')
		->where($map)
		->where("locate(',".UID.",',`helpid`)>0")
    	->order($order)
    	->paginate(config('mobilePage'));
    	return $data_list;
	}
}
