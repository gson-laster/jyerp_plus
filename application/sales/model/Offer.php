<?php
	//报价
namespace app\sales\model;
use think\Model as ThinkModel;
use think\Db;
class Offer extends ThinkModel
{
	protected $table = '__SALES_OFFER__';
	protected $autoWriteTimestamp = true;
	public static function getList($map = [], $order = []){
		$data_list = self::view('sales_offer', true)
		->view('admin_user',['nickname'=>'zrid'],'admin_user.id=sales_offer.zrid','left')
		->view('sales_opport',['name'=>'monophycode','customer_name'],'sales_opport.id=sales_offer.monophycode','left')
		->view('supplier_list',['name'=>'customer','phone'],'supplier_list.id=sales_opport.customer_name','left')
		->where($map)
    	->order($order)
    	->paginate();
    	return $data_list;
	}
	public static function getOne($id = ''){
		$data_list = self::view('sales_offer', true)
		->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_offer.zrid','left')
		->view('admin_user b',['nickname'=>'zdid'],'b.id=sales_offer.zdid','left')
		->view('sales_opport',['name'=>'monophycode','customer_name'],'sales_opport.id=sales_offer.monophycode','left')
		->view('supplier_list',['name'=>'customer','phone'],'supplier_list.id=sales_opport.customer_name','left')
    	->view('admin_organization',['title'=>'bm'],'admin_organization.id=sales_offer.department','left')
		->where(['sales_offer.id'=>$id])
    	->find();
    	return $data_list;
	}			
	 //获取单源明细
	public static function get_Detail($id = ''){
		$getDetail = self::view('sales_opport',['customer_name'])
		->view('supplier_list',['name'=>'customer_name','phone'],'supplier_list.id=sales_opport.customer_name','left')
		->where(['sales_opport.id'=>$id])
		->find();	
 		return $getDetail;
	}
	//获取报价名称
	public static function getName(){
		$map['status'] = 1;
		$result = self::where($map)->column('id,name');
		return $result;
	}
	//获取单源明细
	public static function getDetail($id = ''){
		$getDetail = self::view('sales_offer',['id','monophycode','zrid','department'])
		->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_offer.zrid','left')
    	->view('admin_organization',['title'=>'department'],'admin_organization.id=sales_offer.department','left')
    	->view('sales_opport',['customer_name'],'sales_opport.id=sales_offer.monophycode')
		->view('supplier_list',['name'=>'customer_name','phone'],'supplier_list.id=sales_opport.customer_name','left')
		->where(['sales_offer.id'=>$id])
		->find();		
 		return $getDetail;
	}
}
