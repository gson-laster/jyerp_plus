<?php
	//合同
namespace app\sales\model;
use think\Model as ThinkModel;
use think\Db;
class Delivery extends ThinkModel
{
	protected $table = '__SALES_DELIVERY__';
	protected $autoWriteTimestamp = true;
	public static function getList($map = [], $order = []){
		$data_list = self::view('sales_delivery', true)
		->view('admin_user',['nickname'=>'zrid'],'admin_user.id=sales_delivery.zrid','left')
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
				return self::view('sales_delivery', true)
				->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_delivery.zrid','left')
				->view('admin_user b',['nickname'=>'zdname'],'b.id=sales_delivery.zdid','left')
		    	->view('admin_organization',['title'=>'bm'],'admin_organization.id=sales_delivery.oid','left')
		    	->view('sales_opport',['name'=>'monophycode'],'sales_opport.id=sales_delivery.monophycode')
		    	->where(['sales_delivery.id'=>$id])
		    	->find();
		    	break;
		    case 2:
		    	return self::view('sales_delivery', true)
				->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_delivery.zrid','left')
				->view('admin_user b',['nickname'=>'zdname'],'b.id=sales_delivery.zdid','left')
		    	->view('admin_organization',['title'=>'bm'],'admin_organization.id=sales_delivery.oid','left')
		    	->view('sales_offer',['name'=>'monophycode'],'sales_offer.id=sales_delivery.monophycode')
		    	->where(['sales_delivery.id'=>$id])
		    	->find();
		    	break;
	    	case 3:
		    	return self::view('sales_delivery', true)
				->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_delivery.zrid','left')
				->view('admin_user b',['nickname'=>'zdname'],'b.id=sales_delivery.zdid','left')
		    	->view('admin_organization',['title'=>'bm'],'admin_organization.id=sales_delivery.oid','left')
		    	->view('sales_contract',['name'=>'monophycode'],'sales_contract.id=sales_delivery.monophycode')
		    	->where(['sales_delivery.id'=>$id])
		    	->find();
		    	break;
		    case 4:
		    	return self::view('sales_delivery', true)
				->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_delivery.zrid','left')
				->view('admin_user b',['nickname'=>'zdname'],'b.id=sales_delivery.zdid','left')
		    	->view('admin_organization',['title'=>'bm'],'admin_organization.id=sales_delivery.oid','left')
		    	->view('sales_order',['name'=>'monophycode'],'sales_order.id=sales_delivery.monophycode')
		    	->where(['sales_delivery.id'=>$id])
		    	->find();
		    	break;
		}
	}
	 //查看
	public static function getDetail($map = [])
	{
		$data_list = self::view('sales_delivery_detail', true)
    	->view("stock_material", ['name','version','unit','price'], 'stock_material.id=sales_delivery_detail.itemsid', 'left') 
    	->where($map)
    	->paginate();
    	return $data_list;  	
	} 
	
	//取物品id
    public static function getMaterials($id){		
		return db::name('sales_delivery_detail')->where('pid',$id)->column('itemsid');
	}
	 //获取单源明细
	public static function get_Detail($id = ''){
		$getDetail = db::name('sales_delivery')
		->alias('t')
        ->field('t.customer_name,t.uid,t.oid,t.goodaddrss,t.addrss,a.nickname uid,b.title department')
		->join('admin_user a','t.uid=a.id','left')
		->join('admin_organization b','t.oid=b.id','left')
		->where('t.id',$id)
		->find();		
 		return $getDetail;
	}
    //关联销售出库 HJP
    //获取销售发货主题
    public static function getName(){
    	$result = array();
		$map['status'] = 1;
    	$getName = self::where($map)->select();
    	foreach($getName as $v){
    	$result['0'] = '其他';
			$result[$v['id']] = $v['name'];
		}
		return $result;
    }
    //获取客户名称
 	public static function getSid(){
    	$result = array();
    	$getName = self::select();
    	foreach($getName as $v){
			$result[$v['id']] = $v['customer_name'];
		}
		return $result;
    }
    //获取发货地址
 	public static function getFadd(){
    	$result = array();
    	$getName = self::select();
    	foreach($getName as $v){
			$result[$v['id']] = $v['goodaddrss'];
		}
		return $result;
    }
    //获取收货地址
 	public static function getSadd(){
    	$result = array();
    	$getName = self::select();
    	foreach($getName as $v){
			$result[$v['id']] = $v['addrss'];
		}
		return $result;
    }
    //获取业务员
    public static function getCid(){
    	$result = array();
    	$getCid = self::view('sales_delivery',['id','zrid'])
    	->view("admin_user", ['nickname'], 'admin_user.id=sales_delivery.zrid', 'left')       	
    	->select();
    	foreach($getCid as $v){
			$result[$v['id']] = $v['nickname'];
		}
		return $result;
    }
    //获取销售部门
    public static function getOid(){
    	$result = array();
    	$getOid = self::view('sales_delivery',['id','oid'])
        ->view('admin_organization', ['title' => 'department'], 'admin_organization.id=sales_delivery.oid', 'left')       	
    	->select();
    	foreach($getOid as $v){
			$result[$v['id']] = $v['department'];
		}
		return $result;
    }
}
