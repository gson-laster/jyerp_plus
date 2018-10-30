<?php
//订单
namespace app\sales\model;
use think\Model as ThinkModel;
use think\Db;
class Order extends ThinkModel
{
	protected $table = '__SALES_ORDER__';
	protected $autoWriteTimestamp = true;
	public static function getList($map = [], $order = []){
		$data_list = self::view('sales_order', true)
		->view('admin_user',['nickname'=>'zrid'],'admin_user.id=sales_order.zrid','left')
		->where($map)
    	->order($order)
    	->paginate();
    	return $data_list;
	}
	public static function getOne($id = ''){
		$monophyletic = self::where('id',$id)->value('monophyletic');
		switch ($monophyletic)
		{
			case 0:
				return '';
				break;
			case 1:
				return self::view('sales_order', true)
				->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_order.zrid','left')
				->view('admin_user b',['nickname'=>'zdname'],'b.id=sales_order.zdid','left')
		    	->view('admin_organization',['title'=>'bm'],'admin_organization.id=sales_order.oid','left')
		    	->view('sales_opport',['name'=>'monophycode'],'sales_opport.id=sales_order.monophycode')
		    	->where(['sales_order.id'=>$id])
		    	->find();
		    	break;
		    case 2:
		    	return self::view('sales_order', true)
				->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_order.zrid','left')
				->view('admin_user b',['nickname'=>'zdname'],'b.id=sales_order.zdid','left')
		    	->view('admin_organization',['title'=>'bm'],'admin_organization.id=sales_order.oid','left')
		    	->view('sales_offer',['name'=>'monophycode'],'sales_offer.id=sales_order.monophycode')
		    	->where(['sales_order.id'=>$id])
		    	->find();
		    	break;
	    	case 3:
		    	return self::view('sales_order', true)
				->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_order.zrid','left')
				->view('admin_user b',['nickname'=>'zdname'],'b.id=sales_order.zdid','left')
		    	->view('admin_organization',['title'=>'bm'],'admin_organization.id=sales_order.oid','left')
		    	->view('sales_contract',['name'=>'monophycode'],'sales_contract.id=sales_order.monophycode')
		    	->where(['sales_order.id'=>$id])
		    	->find();
		    	break;
		}
	}
	//获取合同名称
	public static function getName(){
		$map['status'] = 1;
		$result = self::where($map)->column('id,name');
		return $result;
	}
	//获取单源明细
	public static function getDetail($id = ''){
		$getDetail = self::view('sales_order',['id','monophycode','zrid','oid','customer_name','phone'])
		->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_order.zrid','left')
    	->view('admin_organization',['title'=>'department'],'admin_organization.id=sales_order.oid','left')
		->where(['sales_order.id'=>$id])
		->find();	
 		return $getDetail;
	}
}
