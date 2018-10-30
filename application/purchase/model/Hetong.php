<?php
namespace app\purchase\model;
use app\user\model\User as UserModel;
use think\Model;


class Hetong extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__PURCHASE_HETONG__';

    //日志列表
    public static function getList($map=[], $order = []){
        $data_list = self::view('purchase_hetong', true)
            ->view('admin_user puser', ['nickname'=>'purchase_nickname'], 'puser.id=purchase_hetong.purchase_uid', 'left')
            ->view('supplier_list', ['name'=>'supplier_name'], 'supplier_list.id=purchase_hetong.supplier_id', 'left')
            ->view('purchase_type', ['name' => 'purchase_type'], 'purchase_type.id=purchase_hetong.purchase_type', 'left')
            ->view('admin_organization', ['title' => 'purchase_organization'], 'admin_organization.id=purchase_hetong.purchase_organization', 'left')
            ->where($map)
						->where("locate(',".UID.",',`helpid`)>0")
            ->order($order)
            ->paginate();
            //dump($data_list);die;
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

        $one = self::view('purchase_hetong', true)
            ->view('admin_user puser', ['nickname'=>'purchase_nickname'], 'puser.id=purchase_hetong.purchase_uid', 'left')
            ->view('purchase_plan',['name'=>'source_id'],'purchase_plan.id=purchase_hetong.source_id','left')

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
        
        //->view('purchase_hetong_material',['plan_num'],'purchase_plan_material.aid=purchase_plan.id','left')
        ->view("supplier_list",['name'=>'sname'],'purchase_hetong_material.supplier_id=supplier_list.id','left')
        ->where($map)
        ->paginate();
        return $data_list;
    }
    
    
    
    public static function getPlans($map=[]){
    	 $data_list = self::view('purchase_plan', true)        
        ->view("stock_material", ['name','version','unit','price'], 'stock_material.id=purchase_plan_material.wid', 'left') 
        ->view('purchase_plan_material',['plan_num'],'purchase_plan_material.aid=purchase_plan.id','left')
       
    	  ->where($map)
    	  ->paginate();
    	  
    		return $data_list;
    	
    	}

}


