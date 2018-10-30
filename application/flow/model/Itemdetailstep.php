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

/**
 * 模型
 * @package app\admin\model
 */
class Itemdetailstep extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__FLOW_ITEMDETAIL_STEP__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
}