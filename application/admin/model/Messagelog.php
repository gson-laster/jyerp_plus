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

namespace app\admin\model;

use think\Model;


/**
 * 日志记录模型
 * @package app\admin\model
 */
class Messagelog extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__ADMIN_MESSAGE_LOG__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    /**
     * 获取所有日志
     * @param array $map 条件
     * @param string $order 排序
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public static function getAll($map = [], $order = '')
    {
        $map['receive_user_id'] = ['=',UID];
        $data_list = self::view('admin_message_log', true)
            ->view('admin_message_action', 'title,module', 'admin_message_action.id=admin_message_log.action_id', 'left')
            ->view('admin_user', 'nickname', 'admin_user.id=admin_message_log.send_user_id', 'left')
            ->view('admin_module', ['title' => 'module_title'], 'admin_module.name=admin_message_action.module')
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }


     public static function getMessageCount()
    {
        
         return self::where('status',0)->where(['receive_user_id' => UID])->count();
    }

}