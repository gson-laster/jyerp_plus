<?php
namespace app\finance\model;

use think\Model as ThinkModel;

class Finance_receipts extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__FINANCE_RECEIPTS__';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    /*
     
     * 获取合同收款数据列表*/
    public static function getList($map = '', $order = '') {
				$data_list =  self::view('finance_receipts')
				->view('tender_obj',['name'=>'item'],'tender_obj.id=finance_receipts.item','left')//项目   	
				->view('contract_income',['title','nail','money'],'contract_income.id=finance_receipts.title')
				->where($map)
				//->where('finance_receipts.status',1)
				->order($order)
				->paginate();
				//dump($data_list);die;
    	return $data_list;
    	
    }
	public static function one($map){
		return self::view('finance_receipts')
		-> view('admin_user', 'nickname', 'finance_receipts.operator = admin_user.id', 'left')
		->view('tender_obj',['name'=>'item'],'tender_obj.id=finance_receipts.item','left')
		->view('contract_income i',['title'],'i.attach_item=finance_receipts.item','left')
		->view('contract_income i1',['nail'],'i1.attach_item=finance_receipts.item','left')
	  ->view('contract_income i2',['money'],'i2.attach_item=finance_receipts.item','left')
		-> where('finance_receipts.id='.$map['id']) -> find();
	}
	    /*
     
     * 编辑,添加*/
    public static function white($map = [], $data = []) {
    	if(is_null($map)) {
    		self::create($data);
    	} else {
    		self::where($map) -> update($data);
    	}
    }
    
    public static function getDetail($id = ''){
		//dump($id);die;
		$getDetail = self::view('contract_income','nail,money')
		->where('contract_income.id',$id)
		->find();	
		//dump($getDetail);die;
 		return $getDetail;
	}
    
}
