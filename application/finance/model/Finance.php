<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/25 0025
 * Time: 15:01
 */

namespace app\finance\model;


use think\Model;

class Finance extends Model
{
    protected $table = '__FINANCE_INFO__';



    public static function getList($map=[], $order=[]){
        $data_list = self::view('finance_info')
            ->view('admin_organization',['title'=>'detitle'],'admin_organization.id=finance_info.depot','left')//所属部门        
            ->view('admin_user u',['nickname'=>'unickname'],'u.id=finance_info.zrid','left')//报销人
            ->view('tender_obj obj',['name'=>'item'],'obj.id=finance_info.item','left')//项目
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }
    public static function getOne($id=''){
        $data_list = self::view('finance_info i')
            ->view('tender_obj o',['name'=>'oname'],'o.id=i.item','left')//所属项目
            ->view('money_project p',['name'=>'pname'],'p.id=i.project','left')//报销科目
            ->view('admin_organization or',['title'=>'ortitle'],'or.id=i.depot','left')//部门
            ->view('admin_position po',['title'=>'potitle'],'po.id=i.work','left')//职位
            ->view('admin_user u',['nickname'=>'unickname'],'u.id=i.zrid','left')//报销人
            ->view('admin_user',['nickname'=>'maker'],'admin_user.id=i.maker','left')//填报人
            ->where('i.id',$id)
            ->select();
            //dump($data_list);die;
        return $data_list;
    }

}