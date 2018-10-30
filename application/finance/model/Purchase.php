<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28 0028
 * Time: 10:03
 */

namespace app\finance\model;


use think\Model;

use Think\Db;
class Purchase extends Model
{
    // ���õ�ǰģ�Ͷ�Ӧ���������ݱ�����
    protected $table = '__PURCHASE_HETONG__';

    //��־�б�
    public static function getList($map=[], $order = []){
        $data_list = self::view('purchase_hetong', true)
            ->view('admin_user puser', ['nickname'=>'purchase_nickname'], 'puser.id=purchase_hetong.purchase_uid', 'left')
            ->view('supplier_list', ['name'=>'supplier_name'], 'supplier_list.id=purchase_hetong.supplier_id', 'left')
            ->view('purchase_type', ['name' => 'purchase_type_name'], 'purchase_type.id=purchase_hetong.purchase_type', 'left')
            ->view('admin_organization', ['title' => 'purchase_organization_name'], 'admin_organization.id=purchase_hetong.purchase_organization', 'left')
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }

    //��ȡ������־
    public static function getTell($pid){
        $tell = self::view('constructionsite_plan', true)
           ->view('admin_user', 'nickname', 'admin_user.id=constructionsite_plan.wid', 'left')
            ->view('tender_obj', ['name' => 'xname'], 'tender_obj.id=constructionsite_plan.xid')
            ->where(['constructionsite_plan.id'=>$pid])
            ->find();
        return $tell;
    }
    
    public static function getOne($lid){

        $one = self::view('purchase_hetong', true)
            ->view('admin_user puser', ['nickname'=>'purchase_nickname'], 'puser.id=purchase_hetong.purchase_uid', 'left')
            ->view('supplier_list', ['name'=>'supplier_name'], 'supplier_list.id=purchase_hetong.supplier_id', 'left')
            ->view('purchase_type', ['name' => 'purchase_type_name'], 'purchase_type.id=purchase_hetong.purchase_type', 'left')
            ->view('admin_organization', ['title' => 'purchase_organization_name'], 'admin_organization.id=purchase_hetong.purchase_organization', 'left')
            ->where(['purchase_hetong.id'=>$lid])
            ->find();
        return $one;
    }

    public static function getMaterial($map = [])
    {
        $data_list = self::view('purchase_hetong_material', true)        
        ->view("stock_material", ['name','version','unit','price'], 'stock_material.id=purchase_hetong_material.wid', 'left')      
        ->view('stock_house',['name'=>'ckname'],'stock_material.house_id=stock_house.id','left')
        ->view("supplier_list",['name'=>'sname'],'purchase_hetong_material.supplier_id=supplier_list.id','left')
        ->where($map)
        ->paginate();
        return $data_list;
    }

}