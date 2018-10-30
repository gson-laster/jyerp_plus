<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5 0005
 * Time: 17:39
 */

namespace app\tender\model;


use think\Model;
use think\db;
class Clear extends Model
{
    protected $table = '__TENDER_CLEAR__';   
    protected $autoWriteTimestamp = true;
 		public static function getList($map = [], $order = [])
	{
		$data_list = self::view('tender_clear', true)
    	->view("admin_user", ['nickname'=>'authorized'], 'admin_user.id=tender_clear.authorized', 'left')
		->view('tender_obj',['name'=>'obj_id'],'tender_obj.id=tender_clear.obj_id','left')
		->where($map)
		->order($order)
		->paginate();
		//dump($data_list);die;
		return $data_list;
	}
	public static function getOne($id = '')
    {
    	$data_list = self::view('tender_clear', true)
    	->view("admin_user", ['nickname'=>'authorized'], 'admin_user.id=tender_clear.authorized', 'left')
			->view('tender_obj',['name'=>'obj_id'],'tender_obj.id=tender_clear.obj_id','left')
    	->where(['tender_clear.id'=>$id]) 
    	->find();
    	return $data_list;
    }
    public static function getMaterials($id){		
		return db::name('tender_clear_detail')->where('pid',$id)->column('itemsid');
	}
	
	
	
		public static function getDetail($map = [])
	{
		$data_list = self::view('tender_clear_detail', true)
    	->view("stock_material", ['name','version','unit','price'], 'stock_material.id=tender_clear_detail.itemsid', 'left')
    	//加了一张仓库表，为了的到仓库字段，：袁志凡改
    	->view('stock_house',['name'=>'ckname'],'stock_material.house_id=stock_house.id')
		->view('stock_stock',['number'],'stock_stock.materialid=tender_clear_detail.itemsid','left')
    	->where($map)
    	->select();
    	return $data_list;  	
	} 
}