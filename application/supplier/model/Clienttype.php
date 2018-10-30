<?php
namespace app\supplier\model;
use think\Model;


class Clienttype extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__SUPPLIER_CLIENTTYPE__';

    //日志列表
    public static function getList($map=[], $order = []){
        $data_list = self::view('supplier_clienttype', true)
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }
}


