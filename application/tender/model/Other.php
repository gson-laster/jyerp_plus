<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5 0005
 * Time: 17:48
 */

namespace app\tender\model;


use think\Model;

class Other extends Model
{
    public static function getList($map,$order){
        $data_list = self::view('tender_obj','id,name')
            ->view('finance_hire',['money'=>'hire_money'],'tender_obj.id=finance_hire.item')
            ->view('finance_other',['money'=>'other_money'],'tender_obj.id=finance_other.item')
            ->view('finance_stuff',['payed'=>'material_money'],'tender_obj.id=finance_stuff.item')
            ->where($map)
            ->paginate($order);
        //$data_list['profit'] = $data_list['income'] -$data_list['material_money'] -$data_list['hire_money']-$data_list['other_money'];

        return $data_list;
    }
}