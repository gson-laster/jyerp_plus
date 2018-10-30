<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/6 0006
 * Time: 09:12
 */

namespace app\tender\model;


use think\Model;

class Manage extends Model
{
    public static function getList($map,$order){
        $data_list = self::view('tender_obj','id,name')
            ->view('contract_list',['money'=>'income'],'tender_obj.id=contract_list.attach_item')//合同金额
            ->view('finance_hire',['money'=>'hire_money'],'tender_obj.id=finance_hire.item')
            ->view('finance_other',['money'=>'other_money'],'tender_obj.id=finance_other.item')
            ->view('finance_stuff',['payed'=>'material_money'],'tender_obj.id=finance_stuff.item')
            ->where($map)
            ->paginate($order);
        //$data_list['profit'] = $data_list['income'] -$data_list['material_money'] -$data_list['hire_money']-$data_list['other_money'];

        return $data_list;
    }
}