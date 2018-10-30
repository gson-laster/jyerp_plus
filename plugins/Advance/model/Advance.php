<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5 0005
 * Time: 15:26
 */

namespace plugins\Advance\model;


use think\Model;
use think\Db;
class Advance extends Model
{
    public static function getStatus(){
    	
    	
    	
    	
    	
    	
 		
	    }
    
    
    
    public static function getObj(){
    	
    	$data_list = self::view('tender_obj',true)
        ->order('tender_obj.start_time desc')
        ->limit('5')
    	->select();    	
    	//dump($data_list);die;
    	
    	return $data_list;

    	}
    	
}