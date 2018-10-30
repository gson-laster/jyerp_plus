<?php
namespace app\finance\model;

use think\Model;

class Factsalary extends Model
{
		protected $autoWriteTimestamp = true;
   	protected $table = '__TENDER_FACT_SALARY__';
    public static function getList($map = [], $order = [])
    {
    	$data_list = self::view('tender_fact_salary', true)    	
    	->view("tender_obj", ['name'=>'obj_id'], 'tender_obj.id=tender_fact_salary.obj_id', 'left')
    	->view('admin_user',['nickname'=>'zdid'],'admin_user.id=tender_fact_salary.zdid','left')   
      ->where($map)
    	->order($order)
    	->paginate();
    	return $data_list;
    }
    
    public static function getOne($id){
    	$data_list = self::view('tender_fact_salary', true)    	
    	->view("tender_obj", ['name'=>'obj_id'], 'tender_obj.id=tender_fact_salary.obj_id', 'left')
    	->view('admin_user',['nickname'=>'zdid'],'admin_user.id=tender_fact_salary.zdid','left')   
      ->where('tender_fact_salary.id',$id)
    	->paginate();
    	return $data_list;
    	
    	}

}