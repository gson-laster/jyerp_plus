<?php
namespace app\tender\model;

use think\Model;

class Alreadysalary extends Model
{
		protected $autoWriteTimestamp = true;
   	protected $table = '__TENDER_ALREADY_SALARY__';
    public static function getList($map = [], $order = [])
    {
    	$data_list = self::view('tender_already_salary', true)    	
    	->view("tender_obj", ['name'=>'obj_id'], 'tender_obj.id=tender_already_salary.obj_id', 'left')
    	->view('admin_user',['nickname'=>'zdid'],'admin_user.id=tender_already_salary.zdid','left')   
      ->where($map)
    	->order($order)
    	->paginate();
    	return $data_list;
    }
    
    public static function getOne($id){
    	$data_list = self::view('tender_already_salary', true)    	
    	->view("tender_obj", ['name'=>'obj_id'], 'tender_obj.id=tender_already_salary.obj_id', 'left')
    	->view('admin_user',['nickname'=>'zdid'],'admin_user.id=tender_already_salary.zdid','left')   
      ->where('tender_already_salary.id',$id)
    	->paginate();
    	return $data_list;
    	
    	}

}