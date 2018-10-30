<?php
namespace app\purchase\model;
use app\user\model\User as UserModel;
use think\Model;


class Order extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__PURCHASE_ORDER__';

    //
    public static function getList($map=[], $order = []){
        $data_list = self::view('purchase_order', true)
            ->view('admin_user puser', ['nickname'=>'purchase_nickname'], 'puser.id=purchase_order.purchase_uid', 'left')
            ->view('supplier_list', ['name'=>'supplier_name'], 'supplier_list.id=purchase_order.supplier_id', 'left')
            ->view('purchase_type', ['name' => 'purchase_type_name'], 'purchase_type.id=purchase_order.purchase_type', 'left')
            ->view('admin_organization', ['title' => 'purchase_organization_name'], 'admin_organization.id=purchase_order.purchase_organization', 'left')
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }

    //获取单个日志
    public static function getTell($pid){
        $tell = self::view('constructionsite_plan', true)
           ->view('admin_user', 'nickname', 'admin_user.id=constructionsite_plan.wid', 'left')
            ->view('tender_obj', ['name' => 'xname'], 'tender_obj.id=constructionsite_plan.xid')
            ->where(['constructionsite_plan.id'=>$pid])
            ->find();
        return $tell;
    }
 
    public static function getOne($lid){
        $one = self::view('purchase_order', true)
            ->view('admin_user puser', ['nickname'=>'purchase_nickname'], 'puser.id=purchase_order.purchase_uid', 'left')
            ->view('supplier_list', ['name'=>'supplier_name'], 'supplier_list.id=purchase_order.supplier_id', 'left')
            ->view('purchase_type', ['name' => 'purchase_type_name'], 'purchase_type.id=purchase_order.purchase_type', 'left')
            ->view('admin_organization', ['title' => 'purchase_organization_name'], 'admin_organization.id=purchase_order.purchase_organization', 'left')
            ->where(['purchase_order.id'=>$lid])
            ->find();
        return $one;
    }

    public static function getMaterial($map = [])
    {
        $data_list = self::view('purchase_order_material', true)        
        ->view("stock_material", ['name','version','unit','price'], 'stock_material.id=purchase_order_material.wid', 'left')      
        ->view('stock_house',['name'=>'ckname'],'stock_material.house_id=stock_house.id','left')
        ->view("supplier_list",['name'=>'sname'],'purchase_order_material.supplier=supplier_list.id','left')
        ->where($map)
        ->paginate();
        return $data_list;
    }
}


