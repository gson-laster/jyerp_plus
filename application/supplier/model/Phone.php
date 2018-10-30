<?php
namespace app\supplier\model;
use app\user\model\User as UserModel;
use think\Model;


class Phone extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__SUPPLIER_PHONE__';

    //日志列表
    public static function getList($map=[], $order = []){
        $data_list = self::view('supplier_phone', true)
            ->view('admin_user', 'nickname', 'admin_user.id=supplier_phone.uid', 'left')
            ->view('supplier_list', ['name' => 'sname'], 'supplier_list.id=supplier_phone.sid')
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }

    //获取单个日志
    public static function getOne($lid){
        $log = self::view('supplier_phone', true)
            ->view('admin_user', 'nickname', 'admin_user.id=supplier_phone.uid', 'left')
            ->view('supplier_list', ['name' => 'sname'], 'supplier_list.id=supplier_phone.sid')
            ->where(['supplier_phone.id'=>$lid])
            ->find();
        return $log;
    }
 
     public static function phoneType(){
        return [1=>'手机',2=>'电话',3=>'微信',4=>'qq',5=>'邮件',6=>'传真'];
     }

     public static function phoneCause(){
        return [1=>'寻找新客户',2=>'老客户跟踪',3=>'电话回访'];
     }
     
     
    
    //导出数据
    public static function exportData($map=[], $order = []){
        $data_list = self::view('supplier_phone', true)
            ->view('admin_user', 'nickname', 'admin_user.id=supplier_phone.uid', 'left')
            ->view('supplier_list', ['name' => 'sname'], 'supplier_list.id=supplier_phone.sid')
            ->where($map)
            ->where($order)
            ->select();
        return $data_list;
    }
}


