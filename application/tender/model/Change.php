<?php
namespace app\tender\model;
use app\user\model\User as UserModel;
use think\Model;


class Change extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__CONSTRUCTIONSITE_CHANGE__';

    //日志列表
    public static function getList($map=[], $order = []){
        $data_list = self::view('constructionsite_change', true)
            ->view('admin_user', 'nickname', 'admin_user.id=constructionsite_change.wid', 'left')
            ->view('tender_obj', ['name' => 'xname'], 'tender_obj.id=constructionsite_change.xid')
            ->where($map)
            ->order($order)
            ->paginate();
         
        return $data_list;
    }

    //获取单个日志
    public static function getChange($cid){
        $change = self::view('constructionsite_change', true)
            ->view('admin_user', 'nickname', 'admin_user.id=constructionsite_change.wid', 'left')
            ->view('tender_obj', ['name' => 'xname'], 'tender_obj.id=constructionsite_change.xid')
            ->where(['constructionsite_change.id'=>$cid])
            ->find();
        return $change;
    }
    
    public static function getht($xid){
    	$data_list = self::view('tender_obj','sale')
    			->view('sales_contract','name','tender_obj.sale=sales_contract.id')
    			->where('tender_obj.id',$xid)
    			->find();
    			//dump($data_list);die;
    			return $data_list;
    	}
 

}


