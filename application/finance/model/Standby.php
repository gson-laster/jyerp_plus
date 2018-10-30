<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/25 0025
 * Time: 17:02
 */

namespace app\finance\model;


use think\Model;
use think\Db;
class Standby extends Model
{
    protected $table = '__STANDBY_INFO__';


    public static function getList($map=[], $order=[]){
        $data_list = self::view('standby_info s')
            ->view('admin_organization',['title'=>'depot'],'admin_organization.id=s.part','left')//部门
            ->view('admin_user u',['nickname'=>'nanickname'],'u.id=s.zrid','left')//领取人
            ->view('_tender_obj obj',['name'=>'item'],'obj.id=s.item','left')//项目
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }


    public static function getOne($id=''){
        $data_list = self::view('standby_info s')
            ->view('admin_organization o',['title'=>'detitle'],'o.id=s.part','left')//部门
            ->view('admin_user u',['nickname'=>'nanickname'],'u.id=s.zrid','left')//领取人
            ->view('tender_obj obj',['name'=>'obj_name'],'obj.id=s.item','left')//项目
            ->view('admin_user us',['nickname'=>'unickname'],'us.id=s.maker','left')//经办人
            ->where('s.id',$id)
            ->paginate();
        return $data_list;
    }
    
    public static function getname($id){
    	$data_list = Db::name('admin_user')->where('id',$id)->value('nickname');
    	return $data_list;
}
}