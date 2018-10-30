<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/29 0029
 * Time: 11:00
 */

namespace app\finance\model;
use think\Model;
use think\DB;
class Gather extends Model
{
    protected $table = '__FINANCE_GATHER__';
		protected $autoWriteTimestamp = true;

    public static function getList($map=[], $order=[]){
        $data_list = self::view('finance_gather',true)
            ->view('supplier_list obj',['name'=>'sname'],'obj.id=finance_gather.supplier','left')//供应商
            ->view('finance_manager m',['accmount'=>'maccount'],'m.id=finance_gather.account','left')//公司账户
            ->where($map)
            //->where('finance_gather.status',1)
            ->order($order)
            ->paginate();
        return $data_list;
    }
    public static function getOne($id =''){
    			 $data_list = self::view('finance_gather',true)
                ->view('supplier_list',['name'=>'sname'],'supplier_list.id=finance_gather.supplier','left')//供应商
            		->view('finance_manager',['accmount'=>'maccount'],'finance_manager.id=finance_gather.account','left')//公司账户
            		->view('contract_income',['title'=>'pact'],'contract_income.id=finance_gather.pact','left')//合同名称
                ->where('finance_gather.id',$id)
                ->find();
            //dump($data_list);die;
            return $data_list;
    	}


    public static function getSum(){
        $data_list = DB::query('select sum(money) form dp_finance_gather');
       
        return $data_list;
    }
}