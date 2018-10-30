<?php
namespace app\supplier\model;
use think\Model;


class Type extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__SUPPLIER_TYPE__';

    //日志列表
    public static function getList($map=[], $order = []){
        $data_list = self::view('supplier_type', true)
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }
}


