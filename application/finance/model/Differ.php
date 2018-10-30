<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/30 0030
 * Time: 15:02
 */

namespace app\finance\model;


use think\Model;

class Differ extends Model
{
    protected $table= '__FINANCE_DIFFER__';


    public static function getList($map=[], $order=[]){
        $data_list = self::view('finance_ g')
            ->view('admin_user u',['nickname'=>'uname'],'u.id=g.name','left')//收款人
            ->view('tender_obj obj',['name'=>'sname'],'obj.id=g.supplier','left')//供应商
            ->view('finance_manager m',['account'=>'maccount'],'m.id=g.account','left')//公司账户
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }
}