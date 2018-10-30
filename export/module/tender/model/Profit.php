<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5 0005
 * Time: 15:26
 */

namespace app\tender\model;


use think\Model;
use think\Db;
class Profit extends Model
{
    public static function getList($map,$tax = ''){
        $data_list = self::view('tender_obj','id,name')
            ->where($map)
            ->paginate();
           foreach($data_list as $key=>&$value){
           			$res['item'] = $value['id'];
           			$res ['status'] = 1;
           			$payed = db::name('finance_stuff')->field('sum(payed) as payeds')->where($res)->find();
           			$value['payeds'] = $payed['payeds'];
           			
           			$other = db::name('finance_other')->field('sum(money) as others')->where($res)->find();
           			$value['others'] = $other['others'];
           			
           			$hire = db::name('finance_hire')->field('sum(money) as hires')->where($res)->find();
           			$value['hires'] = $hire['hires'];
           			
           			
           			$res1['obj_id'] =$value['id'] ;
           			$res1['status'] = 1;
           			
           			$salary = db::name('tender_salary')->field('sum(practical) as salary')->where($res1)->find();
           		  $value['salary'] = $salary['salary'];
           			
           			$res2 ['attach_item'] = $value['id'];
           			$res2['status'] = 1;
           			$income = db::name('contract_income')->where($res2)->value('money');
           			$value['income'] = ($income);
           			
          			$value['tax'] = $tax;
           			
           			if(!$tax ==null){
           				$value['profit'] =($value['income']-$value['payeds']-$value['others']-$value['hires']-$value['salary'])*$tax;
           				}else{
           				$value['profit'] =($value['income']-$value['payeds']-$value['others']-$value['hires']-$value['salary']);
           			}
           			if($value['income']==0){
           				$value['per_profit'] =0;
           				}else
           			$value['per_profit'] = round($value['profit']/$value['income'],2);
           	}
 
           return $data_list;
            }
        
    }