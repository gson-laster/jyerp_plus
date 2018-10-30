<?php
	//计划
namespace app\sales\model;

use think\Model as ThinkModel;
use think\Db;
class Plan extends ThinkModel
{
	// 设置当前模型对应的完整数据表名称
	protected $table = '__SALES_PLAN__';
	// 自动写入时间戳
	protected $autoWriteTimestamp = true;
	public static function getName(){
		$map['status'] = 1;
		$result = self::where($map)->column('id,name');
		return $result;
	}
	 public static function getList($map = [], $order = [])
    {
    	$data_list = self::view('sales_plan', true) 
    	->view('tender_prebudget',['name'=>'item'],'sales_plan.item=tender_prebudget.id','left')//销售来源
    	->view('admin_user',['nickname'=>'zrid'],'admin_user.id=sales_plan.zrid','left') 
    	->where($map)
    	->order($order)
    	->paginate();
    	return $data_list;
    }
    public static function getOne($id = '')
    {
    	$data_list = self::view('sales_plan', true)
    	->view('tender_prebudget',['name'=>'item'],'sales_plan.item=tender_prebudget.id','left')//销售来源
    	->view('admin_user',['nickname'=>'zrid'],'admin_user.id=sales_plan.zrid','left')
    	->view('admin_user b',['nickname'=>'zdid'],'b.id=sales_plan.zdid','left')
    	->view('admin_organization',['title'=>'department'],'admin_organization.id=sales_plan.department','left')
    	->where(['sales_plan.id'=>$id])
    	//dump($data_list);die;
    	->find();
    	return $data_list;
    }	
	//获取单源明细
public static function getDetail($id = ''){
		$getDetail = self::view('sales_plan',['id','zrid','department'])
		->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_plan.zrid','left')
    	->view('admin_organization',['title'=>'department'],'admin_organization.id=sales_plan.department','left')		
		->where(['sales_plan.id'=>$id])
		->find();		
 		return $getDetail;
	}
	//获取单源明细
	public static function get_Detail($id = ''){
		$getDetail = self::view('tender_prebudget',['item','money'])
		->view('sales_opport',['customer_name','zrid','department'],'sales_opport.id=tender_prebudget.item','left')
		->view('supplier_client',['name'=>'customer_name','phone'],'supplier_client.id=sales_opport.customer_name','left')
		->view('admin_organization',['title'=>'oid'],'admin_organization.id=sales_opport.department','left')
		->view('admin_user',['nickname'=>'zrname'],'admin_user.id=sales_opport.zdid','left')
		->where(['tender_prebudget.id'=>$id])
		->find();		
 		return $getDetail;
	}
}
