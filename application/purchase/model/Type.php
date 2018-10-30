<?php
namespace app\purchase\model;
use think\Model;
use think\Db;


class Type extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__PURCHASE_TYPE__';

    public static function getList($map=[], $order = []){
        $data_list = self::view('purchase_type', true)
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }

    public static function typeList(){
        return db::name('purchase_type')->where('status=1')->select();
    }
}


