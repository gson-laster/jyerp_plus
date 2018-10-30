<?php
namespace app\purchase\model;
use app\user\model\User as UserModel;
use think\Model;


class Ask extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__PURCHASE_ASK__';

    //日志列表
    public static function getList($map=[], $order = []){
        $data_list = self::view('purchase_ask', true)
            ->view('admin_user', 'nickname', 'admin_user.id=purchase_ask.aid', 'left')
            ->view('admin_organization', ['title' => 'oname'], 'admin_organization.id=purchase_ask.oid', 'left')
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }

    //获取单个日志
    public static function getOne($lid){
        $log = self::view('purchase_ask', true)
            ->view('admin_user', 'nickname', 'admin_user.id=purchase_ask.aid', 'left')
            ->view('admin_organization', ['title' => 'oname'], 'admin_organization.id=purchase_ask.oid', 'left')
            ->view('purchase_type', ['name' => 'tname'], 'purchase_type.id=purchase_ask.tid')
            ->where(['purchase_ask.id'=>$lid])
            ->find();
        return $log;
    }
 
    public static function getMaterial($map = [])
    {
        $data_list = self::view('purchase_ask_material', true)        
        ->view("stock_material", ['name','version','unit','price','color'], 'stock_material.id=purchase_ask_material.wid', 'left')      
        ->where($map)
        ->paginate();
        return $data_list;
    }

}


