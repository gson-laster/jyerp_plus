<?php
namespace app\constructionsite\model;
use app\user\model\User as UserModel;
use think\Model;
use think\Db;


class Page extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__TENDER_FACTPIC__';

    //日志列表
    public static function getList($map = [], $order = [])
    {
    	$data_list = self::view('tender_factpic', true)    	
    	->view("admin_user", ['nickname'], 'admin_user.id=tender_factpic.uid', 'left')   
		->view("tender_obj",['name'=>'obj_id'],'tender_obj.id=tender_factpic.obj_id','left')
    	->where($map)
    	->order($order)
    	->paginate();
    	return $data_list;
    }
      public static function getOne($id = '',$map = [])
    {
    	$data_list = self::view('tender_factpic', true)
    	->view("admin_user", ['nickname'=>'uid','organization'], 'admin_user.id=tender_factpic.uid', 'left')   
    	->view('tender_obj',['name'=>'obj_id'],'tender_obj.id=tender_factpic.obj_id','left')
    	->where(['tender_factpic.id'=>$id]) 
    	->where($map)
    	->find();
    	return $data_list;
    }  
    
    
    

    //获取单个日志
    public static function getLog($lid){
        $log = self::view('constructionsite_log', true)
            ->view('produce_workcenter', 'name,header', 'produce_workcenter.id=constructionsite_log.cid', 'left')
            ->view('admin_user', 'nickname', 'admin_user.id=constructionsite_log.wid', 'left')
            ->view('tender_obj', ['name' => 'xname'], 'tender_obj.id=constructionsite_log.xid')
            ->where(['constructionsite_log.id'=>$lid])
            ->find();
        return $log;
    }
    
}


