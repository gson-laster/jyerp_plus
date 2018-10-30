<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18 0018
 * Time: 11:14
 */

namespace app\purchase\model;


use think\Model;
use think\Db;

class Money extends Model
{
    protected $table = '__PURCHASE_MONEY__';


    //获取询价数据
    public static function getList($map=[], $order=[]){
            $data_list = self::view('purchase_money')
            ->view('admin_user',['nickname'=>'anickname'],'admin_user.id=purchase_money.askuid','left')//询价员
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }

    public static function getOne($id=''){
            $data_list = self::view('purchase_money')
            ->view('admin_user', ['nickname'=>'wnickname'], 'admin_user.id=purchase_money.wid', 'left')
            ->view("admin_user b", ['nickname'=>'anickname'], 'b.id=purchase_money.askuid', 'left')
            ->view('admin_organization', ['title' => 'oname'], 'admin_organization.id=purchase_money.oid', 'left')
            ->view('purchase_type', ['name' => 'tname'], 'purchase_type.id=purchase_money.cid')
            ->view('purchase_plan',['cid'=>'cuserid'],'purchase_money.pnumber=purchase_plan.id','left')
            ->view('admin_user a',['nickname'=>'cgyname'],'purchase_plan.cid=a.id','left')
//          ->view('supplier_list', ['name' => 'sname'], 'supplier_list.id=purchase_money.sid')
            ->where('purchase_money.id',$id)
            ->find();
        return $data_list;


    }

    // public static function getOne($lid){
    //     $log = self::view('purchase_money', true)
    //         ->view('admin_user', ['nickname'=>'wnickname'], 'admin_user.id=purchase_money.wid', 'left')
    //         ->view("admin_user b", ['nickname'=>'anickname'], 'b.id=purchase_money.askuid', 'left')
    //         //->view("admin_user c", ['nickname'=>'cnickname'], 'b.id=purchase_money.pid', 'left')
    //         ->view('admin_organization', ['title' => 'oname'], 'admin_organization.id=purchase_money.oid', 'left')
    //         ->view('purchase_type', ['name' => 'tname'], 'purchase_type.id=purchase_money.tid')
    //         ->where(['purchase_plan.id'=>$lid])
    //         ->find();
    //     return $log;
    // }




    public static function getMaterial($map = [])
    {
        $data_list = self::view('purchase_money_material', true)        
        ->view("stock_material", ['name','version','unit','price'], 'stock_material.id=purchase_money_material.wid', 'left')      
        ->view('stock_house',['name'=>'ckname'],'stock_material.house_id=stock_house.id','left')
     		->view("supplier_list",['name'=>'sname'],'purchase_money_material.supplier=supplier_list.id','left')
        ->where($map)
        ->paginate();
        return $data_list;
    }
}