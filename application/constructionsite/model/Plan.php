<?php
namespace app\constructionsite\model;
use app\user\model\User as UserModel;
use think\Model;


class Plan extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__CONSTRUCTIONSITE_PLAN__';

    //日志列表
    public static function getList($map=[], $order = []){
        $data_list = self::view('constructionsite_plan', true)
            ->view('admin_user', 'nickname', 'admin_user.id=constructionsite_plan.wid', 'left')
            ->view('tender_obj', ['name' => 'xname'], 'tender_obj.id=constructionsite_plan.xid')
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
 

}


