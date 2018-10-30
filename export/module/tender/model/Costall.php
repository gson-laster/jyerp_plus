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
class Costall extends Model
{
    protected $table = '__TENDER_OBJ__';
    public static function getList($map){
        $data_list = self::view('tender_obj')
            ->where($map)
            ->paginate();
           foreach($data_list as $key=>&$value){
           			$payed = db::name('finance_stuff')->field('sum(payed) as payeds')->where('item',$value['id'])->find();
           			$value['payeds'] = $payed['payeds'];
           			
           			$other = db::name('finance_other')->field('sum(money) as others')->where('item',$value['id'])->find();
           			$value['others'] = $other['others'];
           			
           			$hire = db::name('finance_hire')->field('sum(money) as hires')->where('item',$value['id'])->find();
           			$value['hires'] = $hire['hires'];
           			
           			$salary = db::name('tender_salary')->field('sum(practical) as salary')->where('obj_id',$value['id'])->find();
           		  $value['salary'] = $salary['salary'];
           			//$income = db::name('contract_income')->where('attach_item',$value['id'])->value('money');
           			//$value['income'] = ($income);
            }
        return $data_list;
    }
}