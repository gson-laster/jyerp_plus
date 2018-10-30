<?php
	//材料计划
namespace app\tender\model;
use think\Model as ThinkModel;
use think\Db;
class Materials extends ThinkModel
{
	protected $table = '__TENDER_MATERIALS__';
	protected $autoWriteTimestamp = true;
	public static function getList($map = [], $order = [])
	{
		$data_list = self::view('tender_materials', true)
    	->view("admin_user", ['nickname'=>'authorizedname'], 'admin_user.id=tender_materials.authorized', 'left')
		->view('tender_obj',['name'=>'obj_id'],'tender_obj.id=tender_materials.obj_id','left')
		->where($map)
		->order($order)
		->paginate();
		//dump($data_list);die;
		return $data_list;
	}
	public static function getOne($id = '')
    {
    	$data_list = self::view('tender_materials', true)
    	->view("admin_user", ['nickname'=>'authorizedname'], 'admin_user.id=tender_materials.authorized', 'left')
		  ->view('tender_obj',['name'=>'obj_id'],'tender_obj.id=tender_materials.obj_id','left')
    	->where(['tender_materials.id'=>$id]) 
    	->find();
    	return $data_list;
    }
	//查看
	public static function getDetail($map = [])
	{
		$data_list = self::view('tender_materials_detail', true)
    	->view("stock_material", ['name','version','unit'], 'stock_material.id=tender_materials_detail.itemsid', 'left')
		->view('stock_stock',['number'],'stock_stock.materialid=tender_materials_detail.itemsid','left')
    	->where($map)
    	->paginate();
    	return $data_list;  	
	} 
	public static function getMaterials($id){		
		return db::name('tender_materials_detail')->where('pid',$id)->column('itemsid');
	}
	
	public static function getStype($id){
		$data_list = Db::name('tender_materials')->where('status_type',$id)->select();
		//dump($data_list);
		return $data_list;
		}
}
