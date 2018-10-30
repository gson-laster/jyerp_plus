<?php
namespace app\constructionsite\model;
use app\user\model\User as UserModel;
use think\Model;


class Log extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__CONSTRUCTIONSITE_LOG__';

    //日志列表
    public static function getList($map=[], $order = []){
        $data_list = self::view('constructionsite_log', true)
            ->view('produce_workcenter', 'name,header', 'produce_workcenter.id=constructionsite_log.cid', 'left')
            ->view('admin_user', 'nickname', 'admin_user.id=constructionsite_log.wid', 'left')
            ->view('tender_obj', ['name' => 'xname'], 'tender_obj.id=constructionsite_log.xid')
            ->where($map)
            ->order($order)
            ->paginate();
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


