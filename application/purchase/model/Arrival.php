<?php
namespace app\purchase\model;
use app\user\model\User as UserModel;
use think\Model;


class Arrival extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__PURCHASE_ARRIVAL__';

    //日志列表
    public static function getList($map=[], $order = []){
        $data_list = self::view('purchase_arrival', true)
            ->view('admin_user', ['nickname'=>'consignee_user'], 'admin_user.id=purchase_arrival.consignee', 'left')
            ->view('admin_user b',['nickname'=>'ciagouyuan'],'b.id=purchase_arrival.cid','left')
            ->view('supplier_list', ['name'=>'supplier_name'], 'supplier_list.id=purchase_arrival.sid', 'left')
            ->view('purchase_type', ['name' => 'purchase_type_name'], 'purchase_type.id=purchase_arrival.ctype', 'left')
            ->view('admin_organization', ['title' => 'purchase_organization_name'], 'admin_organization.id=purchase_arrival.oid', 'left')
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }    

    public static function getOne($id){
        $data_list = self::view('purchase_arrival', true)
            ->view('supplier_list', ['name'=>'supplier_name'], 'supplier_list.id=purchase_arrival.sid', 'left')
            ->view('purchase_type', ['name' => 'purchase_type_name'], 'purchase_type.id=purchase_arrival.ctype', 'left')
            ->view('admin_organization', ['title' => 'purchase_organization_name'], 'admin_organization.id=purchase_arrival.oid', 'left')
            ->where(['purchase_arrival.id'=>$id])
            ->find();
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
 
    public static function getMaterial($map = [])
    {
        $data_list = self::view('purchase_arrival_material', true)        
        ->view("stock_material", ['name','version','unit','type','house_id'], 'stock_material.id=purchase_arrival_material.wid', 'left')      
        ->view('stock_house',['name'=>'ckname'],'stock_material.house_id=stock_house.id','left')
        ->view("supplier_list",['name'=>'sname'],'purchase_arrival_material.supplier_id=supplier_list.id','left')
        ->where($map)
        ->paginate();
        return $data_list;
    }
	//关联采购入库 HJP
    //获取到货主题
    public static function getName(){
    	$result = array();
		$map['status'] = 1;
    	$getName = self::where($map)->select();
    	foreach($getName as $v){
    	$result['0'] = '其他';
			$result[$v['id']] = $v['name'];
		}
		return $result;
    }
    //获取供应商
 	public static function getSid(){
    	$result = array();
    	$getSid = self::view('purchase_arrival',['id','sid'])
        ->view('supplier_list', ['name'=>'supplier_name'], 'supplier_list.id=purchase_arrival.sid', 'left')    	
    	->select();
    	foreach($getSid as $v){
			$result[$v['id']] = $v['supplier_name'];
		}
		return $result;
    }
    //获取采购员
    public static function getCid(){
    	$result = array();
    	$getCid = self::view('purchase_arrival',['id','cid'])
    	->view("admin_user", ['nickname'=>'cname'], 'admin_user.id=purchase_arrival.cid', 'left')       	
    	->select();
    	foreach($getCid as $v){
			$result[$v['id']] = $v['cname'];
		}
		return $result;
    }
    //获取采购部门
    public static function getOid(){
    	$result = array();
    	$getOid = self::view('purchase_arrival',['id','oid'])
        ->view('admin_organization', ['title' => 'purchase_organization_name'], 'admin_organization.id=purchase_arrival.oid', 'left')       	
    	->select();
    	foreach($getOid as $v){
			$result[$v['id']] = $v['purchase_organization_name'];
		}
		return $result;
    }
}


