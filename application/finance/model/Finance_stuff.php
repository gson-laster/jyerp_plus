<?php
namespace app\finance\model;

use think\Model as ThinkModel;
use think\Db;
/*
 
 * 材料付款*/
class Finance_stuff extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__FINANCE_STUFF__';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    /*
     
     * 账户信息*/
    public static function getList($map = [], $order = []) {
    	$data_list = self::view('finance_stuff',true)
    	      ->view('contract_materials a',['obj_id'],'a.id=finance_stuff.source_number','left')//项目id
            ->view('tender_obj',['name'=>'item'],'tender_obj.id=a.obj_id','left')
            ->view('contract_materials b',['supplier'],'b.id=finance_stuff.source_number','left')//供应商
            ->view('supplier_list',['name'=>'supplier'],'supplier_list.id=b.supplier')
    		-> where($map)->where('finance_stuff.status',1) -> order($order) -> paginate();
    		//dump($data_list);die;
    		return $data_list;
    		
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
    public static function one($map = []) {
        $data_list = self::view('finance_stuff',true)
        //源单类型
        ->view('contract_materials f',['name'=>'souname'],'f.id=finance_stuff.source_number','left')//源单号
    	 	->view('finance_manager',['name'=>'mname'], 'finance_manager.id=finance_stuff.account')
    		->view('contract_materials a',['obj_id'],'a.id=finance_stuff.source_number','left')//项目
        ->view('tender_obj',['name'=>'item'],'tender_obj.id=a.obj_id','left')
        ->view('contract_materials b',['supplier'],'b.id=finance_stuff.source_number','left')//供应商
        ->view('supplier_list',['name'=>'supplier'],'supplier_list.id=b.supplier' )
        ->view('admin_user',['nickname'=>'operator'],'admin_user.id=finance_stuff.operator')//经办人      
    		-> where('finance_stuff.id ='.$map['id']) -> find();
    		
    		//dump($data_list);die;
    		return $data_list;
    }
    
    public static function getItem($id){
    	
    	$data= Db::name('contract_materials')->where('id',$id)->value('obj_id');
    	return $data;
    	}
    public static function getSupplier($id){
    	
    	$data= Db::name('contract_materials')->where('id',$id)->value('supplier');
    	return $data;
    	}
    
    public static function getDetail($id = ''){
		$getDetail = self::view('contract_materials','obj_id,supplier')
		->view('tender_obj',['name'=>'objname'],'tender_obj.id=contract_materials.obj_id','left')
    ->view('supplier_list',['name'=>'suname'],'supplier_list.id=contract_materials.supplier','left')
		->where('contract_materials.id',$id)
		->find();	
		//dump($getDetail);die;
 		return $getDetail;
	}
    
    
    
}
