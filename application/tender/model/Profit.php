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
        $data_list = self::view('tender_obj',true)
        -> view('sales_contract', 'money as sales_contract_money','tender_obj.sale=sales_contract.id', 'left')
        -> view('tender_budget', 'budget', 'tender_obj.id=tender_budget.obj_id', 'left')
            //->view('tender_hire',['obj_id'=>'obj_id'],'tender_hire.id=tender_hire_detail.pid','left')
           // ->view('tender_hire',['name'=>'objname'])
            ->where($map)
            ->paginate();
            //dump($data_list);die;
          
           foreach($data_list as $key=>&$value){
           			$res['obj_id'] = $value['id'];
           			$res ['status'] = 1;
           			$payed1 = implode(db::name('tender_materials')->where($res)->column('id'),',');
           			$payed =db::name('tender_materials_detail')->where(['pid'=>['in',$payed1]])->field('sum(xj) as payeds')->find();
           			$value['payeds'] = $payed['payeds'];
           			
           			//$other = db::name('finance_other')->field('sum(money) as others')->where($res)->find();
           			//$value['others'] = $other['others'];
           			
           			$hire1 = implode(db::name('tender_hire')->where($res)->column('id'),',');
           			$hire =db::name('tender_hire_detail')->where(['pid'=>['in',$hire1]])->field('sum(xj) as hires')->find();
           			$value['hires'] = $hire['hires'];
           			//dump($hire1);die;
           			
           			$res1['obj_id'] =$value['id'] ;
           			//$res1['status'] = 1;
           			
           			$salary = db::name('tender_already_salary')->field('sum(already) as salary')->where($res1)->find();
           		  $value['salary'] = $salary['salary'];
           			
           			$res2 ['attach_item'] = $value['id'];
           			//$res2['status'] = 1;
           			//$income = db::name('contract_income')->where($res2)->value('money');
           			$value['sales_contract_money'] = ($value['sales_contract_money']);
           			
          			$value['tax'] = $tax;
           			
           			if(!$tax ==null){
           				$value['profit'] =($value['sales_contract_money']-$value['budget']-$value['salary'])*(1-$tax);
           				}else{
           				$value['profit'] =($value['sales_contract_money']-$value['budget']-$value['salary']);
           			}
           			if($value['sales_contract_money']==0){
           				$value['per_profit'] =0;
           				}else
           			$value['per_profit'] = round($value['profit']/$value['sales_contract_money'],2);
           	}
 					//dump($data_list);die;
           return $data_list;
            }
        
    }