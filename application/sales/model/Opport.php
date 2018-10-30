<?php
	//机会
namespace app\sales\model;

use think\Model as ThinkModel;
use think\Db;
class Opport extends ThinkModel
{
	// 设置当前模型对应的完整数据表名称
	protected $table = '__SALES_OPPORT__';
	// 自动写入时间戳
	protected $autoWriteTimestamp = true;
	public static function getList($map = [], $order = []){
		$data_list = self::view('sales_opport', true)
		->view('admin_user',['nickname'=>'zrid'],'admin_user.id=sales_opport.zrid','left')
		->view('supplier_client',['name'=>'customer_name','phone'],'supplier_client.id=sales_opport.customer_name','left')
		->view('supplier_clienttype',['name'=>'supplier_clienttype'],'supplier_clienttype.id=supplier_client.type','left')
		->where($map)
    	->order($order)
    	->paginate();
    	return $data_list;
	}
	public static function getOne($id = ''){
		$data_list = self::view('sales_opport', true)
		->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_opport.zrid','left')
		->view('admin_user b',['nickname'=>'zdid'],'b.id=sales_opport.zdid','left')
		->view('supplier_client',['name'=>'customer','type','phone'],'supplier_client.id=sales_opport.customer_name','left')
		->view('supplier_clienttype',['name'=>'supplier_clienttype'],'supplier_clienttype.id=supplier_client.type','left')
    	->view('admin_organization',['title'=>'bm'],'admin_organization.id=sales_opport.department','left')
		->where(['sales_opport.id'=>$id])
    	->find();
    	return $data_list;
	}
	 //获取单源明细
	public static function get_Detail($id = ''){
		$getDetail = self::view('supplier_client',['phone','type'])
		->view('supplier_clienttype',['name'=>'supplier_clienttype'],'supplier_clienttype.id=supplier_client.type','left')
		->where(['supplier_client.id'=>$id])
		->find();	
 		return $getDetail;
	}
	//获取机会名称
	public static function getName(){
		$map['status'] = 1;
		$result = self::where($map)->column('id,name');
		return $result;
	}
	//获取客户名称
	public static function customer_name(){
		$result = array();
		$customer_name = self::view('sales_opport',['id','customer_name'])
		->view('supplier_client',['name'=>'customer_name'],'supplier_client.id=sales_opport.customer_name','left')
		->paginate();
 		foreach($customer_name as $v){
 			$result[$v['id']] = $v['customer_name'];
 		}
 		return $result;
	}
	public static function getDetail($id = ''){
		$getDetail = self::view('sales_opport',['id','customer_name','zrid','department'])
		->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_opport.zrid','left')
    	->view('admin_organization',['title'=>'department'],'admin_organization.id=sales_opport.department','left')
		->view('supplier_client',['name'=>'customer_name','phone'],'supplier_client.id=sales_opport.customer_name','left')
		->where(['sales_opport.id'=>$id])
		->find();		
 		return $getDetail;
	}
	
	//获取客户电话
	public static function getPhone(){
		$result = array();
		$getPhone = self::view('sales_opport',['id','customer_name'])
		->view('supplier_client',['phone'],'supplier_client.id=sales_opport.customer_name','left')
		->paginate();
 		foreach($getPhone as $v){
 			$result[$v['id']] = $v['phone'];
 		}
 		return $result;
	}
}
