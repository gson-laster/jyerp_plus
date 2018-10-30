<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5 0005
 * Time: 17:48
 */

namespace app\tender\model;
use think\Model;
use think\Db;
class Prebudget extends Model
{
		protected $table = '__TENDER_PREBUDGET__';
		protected $autoWriteTimestamp = true;
		//待算项目
		
    public static function getList(){
        $data_list = Db::name('sales_opport')->where('status=1')->select();
        //dump($data_list);die;    
        return $data_list;
    }
    //查看待算项目
    public static function getOne($id){
    		$data_list = self::view('sales_opport',true)
    		->view('supplier_client',['name'=>'customer_name'],'supplier_client.id=sales_opport.customer_name','left')//客户姓名
    		->view('supplier_client c','phone','c.id=sales_opport.customer_name','left')//手机号
    		->view('admin_user',['nickname'=>'zrid'],'admin_user.id=sales_opport.zrid','left')
    		->where('sales_opport.id',$id)
    		->find();
    		
    		
    		
    		
    	//->where('sales_opport.status',1)
    		
    		//->select();
    		return $data_list;
    	
    	}
    	
    public static function getName(){
    	 $data_list = Db::name('sales_opport')->where('status=1 AND status_pre=0')->column('id,name');
        //dump($data_list);die;    
        return $data_list;   	
    	}
    	
    public static function getbudget($map = [], $order = []){
    	//$map['tender_prebudget.status'] = 1;
    	$data_list = self::view('tender_prebudget',true)
    	->view('sales_opport',['name'=>'sname'],'sales_opport.id=tender_prebudget.item','left')
    	->view('admin_user',['nickname'=>'mname'],'admin_user.id=tender_prebudget.maker','left')
    	->where($map)
    	->order($order)
    	->select();
    	//dump($data_list);die;    
    	return $data_list;
    	}
    	
    	
    	
    public static function getOneBudget($map){
    	$data_list = self::view('tender_prebudget')
    		->view('sales_opport',['name'=>'item'],'sales_opport.id=tender_prebudget.item','left')//客户姓名
    		
    		->view('admin_user',['nickname'=>'maker'],'admin_user.id=tender_prebudget.maker','left')
    		//->where('sales_opport.status_pre',0)
    		//->where('sales_opport.status=1')
    		//->where('id',$id)  		
    		->where($map)
    		->find();
    		return $data_list;
    	}
    public static function getOnesale(){
    	$data_list = Db::name('tender_prebudget')->where('status',1)->column('id,name');
    	return $data_list;
    	} 
    	
    	
    public static function getMoney(){
    	$data_list = Db::name('tender_prebudget')->where('status',1)->select();
    	return $data_list;
    	
    	}
}