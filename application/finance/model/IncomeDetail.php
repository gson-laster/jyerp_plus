<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/1 0001
 * Time: 15:12
 */

namespace app\finance\model;


use think\Model;

class IncomeDetail extends Model
{

    protected $table = '__CONTRACT_INCOME_DETAIL__';
    
    public static function getList($id) {
    	return self::where(['pid' => $id]) -> select();
    }


}