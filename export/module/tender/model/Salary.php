<?php
namespace app\tender\model;

use think\Model;
use think\Db;
class Salary extends Model
{
		protected $autoWriteTimestamp = true;
   	//protected $table = '__TENDER_SALARY__';
   
    
    public static function getOne($id){
    	$data_list = self::view('tender_salary', true)    	
    	->view("tender_obj", ['name'=>'obj_id'], 'tender_obj.id=tender_salary.obj_id', 'left')
    	->view('admin_user',['nickname'=>'zdid'],'admin_user.id=tender_salary.zdid','left')   
      ->where('tender_salary.id',$id)
    	->paginate();
    	return $data_list;
    	
    	}
     public static function getList($map){
        $data_list = self::view('tender_obj','id,name')
            ->where($map)
            ->paginate();
           foreach($data_list as $key=>&$value){
           			$res['obj_id'] = $value['id'];
           			$already = db::name('tender_already_salary')->field('sum(already) as alreadys')->where($res)->find();
           			$value['alreadys'] = $already['alreadys'];
           			
           			$fact = db::name('tender_fact_salary')->field('sum(fact) as facts')->where($res)->find();
           			$value['facts'] = $fact['facts'];          	
           	}
 					 //dump($data_list);die;
           return $data_list;
            }
            
            
      public static function getFact($id){
      	$data_list = Db::name('tender_fact_salary')->where('obj_id',$id)->order('s_time')->select();    	
      	//dump($data_list);die;
      	return $data_list;
      	
      	
      	
      	
      	}
      public static function getAlready($id){
      	$data_list = Db::name('tender_already_salary')->where('obj_id',$id)->order('s_time')->select();     	
      	//dump($data_list);die;
      	return $data_list;
      	}
      
      
      
     /*
      *获取单项目 id ,name alreadys(总计划工资)
     	*/
      public static function getASum($id){
      	
        $data_list = self::view('tender_obj','id,name')
            ->where('id',$id)
            ->paginate();
           foreach($data_list as $key=>&$value){
           			$res['obj_id'] = $value['id'];
           			$already = db::name('tender_already_salary')->field('sum(already) as alreadys')->where($res)->find();
           			$value['alreadys'] = $already['alreadys']; 			
           	}
           return $data_list;
            }
            
    	 /*
      *获取单项目 id ,name facts(实发总工资)
     	*/
    	public static function getFSum($id){
        $data_list = self::view('tender_obj','id,name')
            ->where('id',$id)
            ->paginate();
           foreach($data_list as $key=>&$value){
           			$res['obj_id'] = $value['id'];
           			$fact = db::name('tender_fact_salary')->field('sum(fact) as facts')->where($res)->find();
           			$value['facts'] = $fact['facts'];          	
           	}
           return $data_list;
            }
    	
    	
    	

}