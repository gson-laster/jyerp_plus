<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/30 0030
 * Time: 14:07
 */

namespace app\finance\model;


use think\Model;

class Outnote extends Model
{
    protected $table = '__FINANCE_OUTNOTE__';


    public static function getList($map=[], $order=[]){
        $data_list = self::view('finance_outnote out')
            ->view('tender_obj obj',['name'=>'iname'],'obj.id=out.iname','left')//项目
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }



}