<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\flow\model;

use think\Model;
use think\Db;

/**
 * 流程类型
 * @package app\admin\model
 */
class FlowWork extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__FLOW_WORK__';

    public static function myflow($map = [], $order = [])
    {
        $data_list = db::name('flow_work')
                ->alias('w')
                ->field('w.id,w.title,w.update_time,w.create_time,w.user_name,w.user_id,w.step,w.fid,admin_flow.title as ftitle')
                ->join('admin_flow','w.fid=admin_flow.id','left')
                ->where($map)
                ->order($order)
                ->paginate();
         return $data_list;
    }

}