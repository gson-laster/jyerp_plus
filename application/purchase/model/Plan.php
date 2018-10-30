<?php
namespace app\purchase\model;
use app\user\model\User as UserModel;
use think\Model;
use think\Db;


class Plan extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__PURCHASE_PLAN__';

    //日志列表
    public static function getList($map=[], $order = []){
        $data_list = self::view('purchase_plan', true)
            ->view('admin_user puser', ['nickname'=>'pnickname'], 'puser.id=purchase_plan.pid', 'left')
            ->view('admin_user wuser', ['nickname'=>'wnickname'], 'wuser.id=purchase_plan.wid', 'left')
            ->view('admin_user cuser', ['nickname'=>'cnickname'], 'cuser.id=purchase_plan.cid', 'left')
            ->view('admin_organization', ['title' => 'oname'], 'admin_organization.id=purchase_plan.oid', 'left')
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
 
    public static function getMaterial($map = [])
    {
        $data_list = self::view('purchase_plan_material', true)        
        ->view("stock_material", ['name','version','unit','price'], 'stock_material.id=purchase_plan_material.wid', 'left') 
        //加了一张供应商表，袁志凡改     
        ->view("supplier_list",['name'=>'sname'],'purchase_plan_material.supplier=supplier_list.id','left')
        ->view("stock_house",['name'=>'ckname'],'stock_material.house_id=stock_house.id')
        ->where($map)
        ->paginate();
        return $data_list;
    }

    public static function getOne($lid){
        $log = self::view('purchase_plan', true)
            ->view('admin_user', ['nickname'=>'wnickname'], 'admin_user.id=purchase_plan.wid', 'left')
            ->view("admin_user b", ['nickname'=>'pnickname'], 'b.id=purchase_plan.pid', 'left')
            ->view("admin_user c", ['nickname'=>'cnickname'], 'c.id=purchase_plan.cid', 'left')
            ->view('admin_organization', ['title' => 'oname'], 'admin_organization.id=purchase_plan.oid', 'left')
            ->view('purchase_type', ['name' => 'tname'], 'purchase_type.id=purchase_plan.tid')
            ->view('tender_materials',['name'=>'prate'],'tender_materials.id=purchase_plan.prate')
            ->where(['purchase_plan.id'=>$lid])
            ->find();
        return $log;
    }
    
    
    public static function getMes(){
    	$data_list = Db::name('purchase_plan')->where('status',1)->column('id,name');
    	return $data_list;
    	}
    public static function getid($id=''){
    	$data_list  = self::view('purchase_plan','tid,oid,cid')
    	->where('status',1)
    	->where('id',$id)   	
    	->find();
  		//dump($data_list);die;
    	return $data_list;
    	}
      public static function getMaterials($map = [])
    {
        $data_list = self::view('purchase_money_material', true)        
        ->view("stock_material", ['name','version','unit','price'], 'stock_material.id=purchase_money_material.wid', 'left')      
        ->view('stock_house',['name'=>'ckname'],'stock_material.house_id=stock_house.id','left')
     		->view("supplier_list",['name'=>'sname'],'purchase_money_material.supplier=supplier_list.id','left')
        ->where($map)
        ->paginate();
        return $data_list;
    }
    
    
    
       public static function getPlans($map=[]){
    	 $data_list = self::view('purchase_plan_material', true)        
        ->view("stock_material", ['name','version','unit','price'], 'stock_material.id=purchase_plan_material.wid', 'left')
  
    	  ->where($map)
    	  ->paginate();
    		return $data_list;
    	
    	}
    	
    	
    	
    	
    	
//  public static function get_Detail(){
//  	$data=self::view('purchase_plan',true)
//  	->view('purchase_type',['name'=>'tname'],'purchase_plan.tid=purchase_type.id','left')
//  	->view('admin_')
//  }
}


