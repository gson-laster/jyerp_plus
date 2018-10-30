<?php
namespace app\supplier\model;
use app\user\model\User as UserModel;
use think\Model;


class Res extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__SUPPLIER_RES__';

    //日志列表
    public static function getList($map=[], $order = []){
        $data_list = self::view('supplier_res', true)
            ->view('admin_user', 'nickname', 'admin_user.id=supplier_res.uid', 'left')
            ->view('supplier_list', ['name' => 'sname'], 'supplier_list.id=supplier_res.sid')
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }

    //获取单个日志
    public static function getOne($lid){
        $log = self::view('supplier_res', true)
            ->view('admin_user', 'nickname', 'admin_user.id=supplier_res.uid', 'left')
            ->view('supplier_list', ['name' => 'sname'], 'supplier_list.id=supplier_res.sid')
            ->where(['supplier_res.id'=>$lid])
            ->find();
        return $log;
    }
 

}


