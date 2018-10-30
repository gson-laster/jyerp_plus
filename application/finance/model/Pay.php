<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28 0028
 * Time: 10:03
 */

namespace app\finance\model;


use think\Model;
use app\finance\model\Bank;
use Think\Db;
class Pay extends Model
{
    protected $table  = '__FINANCE_HIRE__';



    public static function getList($map=[], $order=[]){
    	  $data_list = array();
        $data_list = self::view('finance_hire')
        	 	->view('admin_user',['nickname'=>'unickname'],'admin_user.id=finance_hire.maker','left')//填报人
            ->view('tender_contract_hire a',['obj_id'],'a.id=finance_hire.pact','left')//项目id
            ->view('tender_obj',['name'=>'item'],'tender_obj.id=a.obj_id','left')
            ->view('tender_contract_hire b',['supplier'],'b.id=finance_hire.pact','left')//供应商
            ->view('supplier_list',['name'=>'supplier'],'supplier_list.id=b.supplier')
            ->where($map)
            ->where('finance_hire.status',1)
            ->order($order)
            ->paginate();
            //dump($data_list);die;
        return $data_list;
    }
   
    




    public function get_account(){



        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        $arr['list'] = [
            Bank::WHERE('status=1')->column('id,bank')
        ]; //数据
        return json($arr);


    }
    
    
     public static function getItem($id){
    	
    	$data= Db::name('tender_contract_hire')->where('id',$id)->value('obj_id');
    	return $data;
    	}
    public static function getSupplier($id){
    	
    	$data= Db::name('tender_contract_hire')->where('id',$id)->value('supplier');
    	return $data;
    	}
 
    
    
    public static function getEdit($id){
    	  $data_list = array();
        $data_list = self::view('finance_hire h',true)
        		->view('admin_user',['nickname'=>'unickname'],'admin_user.id=h.maker','left')//填报人
          	->view('tender_contract_hire a',['obj_id'],'a.id=h.pact','left')//项目
          	->view('tender_obj',['name'=>'item'],'tender_obj.id=a.obj_id','left')
            ->view('tender_contract_hire b',['supplier'],'b.id=h.pact','left')//供应商
            ->view('supplier_list',['name'=>'supplier'],'supplier_list.id=b.supplier' )
            ->view('finance_manager m',['name'=>'bname'],'m.id=h.bank_name','left')//
            ->view('finance_manager ma',['accmount'=>'aname'],'ma.id=h.bank_name','left')
            ->view('tender_contract_hire',['name'=>'hname'],'tender_contract_hire.id=h.pact')
            ->where('h.id',$id)
            ->find();        
            //dump($data_list);
            return $data_list;
    }
		public static function getDetail($id = ''){
		$getDetail = self::view('tender_contract_hire','obj_id,supplier')
		->view('tender_obj',['name'=>'objname'],'tender_obj.id=tender_contract_hire.obj_id','left')
    ->view('supplier_list',['name'=>'suname'],'supplier_list.id=tender_contract_hire.supplier','left')
		->where('tender_contract_hire.id',$id)
		->find();	
 		return $getDetail;
	}
}