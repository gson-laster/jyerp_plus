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
class Sales extends Model
{
		protected $table = '__SALES_CONTRACT__';
		protected $autoWriteTimestamp = true;
		public static function getList($map = [], $order = []){
		$data_list = self::view('sales_contract', true)
			->view('admin_user',['nickname'=>'zrid'],'admin_user.id=sales_contract.zrid','left')
			->view('supplier_client',['name'=>'customer_name'],'supplier_client.id=sales_contract.customer_name')
			->view('supplier_client a' ,['phone'],'a.id=sales_contract.customer_name','left')
			->where($map)
    	->order($order)
    	->paginate();
    return $data_list;
    }
    public static function getOne($id = ''){
		$data_list = self::view('sales_contract', true)
		->view('admin_user',['nickname'=>'zrid'],'admin_user.id=sales_contract.zrid','left')
		->view('admin_user a',['nickname'=>'zdid'],'a.id= sales_contract.zdid','left')
		->view('supplier_client',['name'=>'customer_name'],'supplier_client.id=sales_contract.customer_name')
		->view('supplier_client b' ,['phone'],'b.id=sales_contract.customer_name','left')
		->view('admin_organization',['title'=>'oid'],'admin_organization.id=sales_contract.oid','left')
		->view('sales_plan',['name'=>'monophycode'],'sales_plan.id=sales_contract.monophycode','left')
		->where(['sales_contract.id'=>$id])
    	->find();
    	return $data_list;
	}
    
    
    
}