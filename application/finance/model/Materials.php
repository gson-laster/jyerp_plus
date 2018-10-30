<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/1 0001
 * Time: 10:42
 */

namespace app\contract\model;


use think\Model;
use think\db;
class Materials extends Model
{
    protected $table = '__CONTRACT_MATERIALS__';
    protected $autoWriteTimestamp = true;
    public static function getList($map = [], $order = [])
    {
        $data_list = self::view('contract_materials')

            ->view("supplier_list", ['name'=>'lname'], 'supplier_list.id=contract_materials.supplier', 'left')//供应商
            ->view('tender_materials a',['obj_id'],'a.id=contract_materials.snumber','left')//项目id
            ->view('tender_obj',['name'=>'obname'],'tender_obj.id=a.obj_id','left')
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }
    public static function getOne($id = '')
    {
        $data_list = self::view('contract_materials h', true)
            ->view("admin_user u", ['nickname'=>'authorizedname'], 'u.id=h.authorized', 'left')
            
            ->view('tender_materials a',['obj_id'],'a.id=h.snumber','left')
            ->view('tender_obj',['name'=>'objname'],'tender_obj.id=a.obj_id','left')//项目
            ->view('tender_materials',['name'=>'snumber'],'tender_materials.id=h.snumber','left')
            ->view('supplier_list',['name'=>'supplier'],'supplier_list.id=h.supplier')//供应商
           
            ->where(['h.id'=>$id])
            ->find();
        return $data_list;
    }
    //查看
    public static function getDetail($map = [])
    {
        $data_list = self::view('contract_materials_detail', true)
            ->view("stock_material", ['name','version','unit'], 'stock_material.id=contract_materials_detail.itemsid', 'left')
            ->where($map)
            ->paginate();
        return $data_list;
    }
    public static function getMaterials($id){
        return db::name('contract_materials_detail')->where('pid',$id)->column('itemsid');
    }
    
    public static function getName(){
    		$data = Db::name('contract_materials')->where('id','>',0)->column('id,name');
    		return $data;
    	}
    	
    	
    public static function getItem($id){
    	
    	$data = Db::name('tender_materials')->where('id',$id)->value('obj_id');
    	return $data;
    	}
    	
    public static function getDetail1($id = ''){
		$getDetail = self::view('tender_materials h','obj_id')
		->view('tender_obj',['name'=>'objname'],'tender_obj.id=h.obj_id','left')
		->where('h.id',$id)
		->find();
		//dump($getDetail);die;
 		return $getDetail;
 	}	
}