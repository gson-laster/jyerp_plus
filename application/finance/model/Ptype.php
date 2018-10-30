<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28 0028
 * Time: 15:14
 */

namespace app\finance\model;


use think\Model;
use think\Db;
class Ptype extends Model
{
    protected $table = '__FINANCE_PTYPE__';
    
    
    public static function getName(){
    	$data_list = Db::name('finance_ptype')->where('status=1')->column('id,name');
    	return $data_list;
    	
    	}
}