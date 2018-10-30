<?php
namespace app\administrative\model;
use app\user\model\User as UserModel;
use think\Model;


class Customer extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__ADMINISTRATIVE_CUSTOMER__';

    //我的客户列表
    public static function getMylist($map=[], $order = []){
        $map['add_user_id'] = UID;
        $data_list = self::view('administrative_customer', 'id,name,contact,mobile_tel,wechat,is_open')        
        ->where($map)
        ->order($order)
        ->paginate();
        return $data_list;
    }
    //公司客户列表
    public static function getCompanylist($map=[], $order = []){
        $map['is_open'] = 1;
        $data_list = self::view('administrative_customer', 'id,name,contact,mobile_tel,wechat')
        ->view('admin_user','nickname','administrative_customer.add_user_id=admin_user.id','left')        
        ->where($map)
        ->order($order)
        ->paginate();
        return $data_list;
    }


 

}


