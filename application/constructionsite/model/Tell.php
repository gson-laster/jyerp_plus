<?php
namespace app\constructionsite\model;
use app\user\model\User as UserModel;
use think\Model;


class Tell extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__CONSTRUCTIONSITE_TELL__';

    //日志列表
    public static function getList($map=[], $order = []){
        $data_list = self::view('constructionsite_tell', true)
            ->view('admin_user', 'nickname', 'admin_user.id=constructionsite_tell.wid', 'left')
            ->view('tender_obj', ['name' => 'xname'], 'tender_obj.id=constructionsite_tell.xid')
            ->view('produce_workcenter', 'name', 'produce_workcenter.id=constructionsite_tell.cid', 'left')
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }

    //获取单个日志
    public static function getTell($cid){
        $tell = self::view('constructionsite_tell', true)
            ->view('admin_user', 'nickname', 'admin_user.id=constructionsite_tell.wid', 'left')
            ->view('tender_obj', ['name' => 'xname'], 'tender_obj.id=constructionsite_tell.xid')
            ->view('produce_workcenter', 'name', 'produce_workcenter.id=constructionsite_tell.cid', 'left')
            ->where(['constructionsite_tell.id'=>$cid])
            ->find();
        return $tell;
    }
 

}


