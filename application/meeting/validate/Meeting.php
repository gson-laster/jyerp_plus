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

namespace app\meeting\validate;

use think\Validate;

/**
 * 日志证器
 * @package app\cms\validate
 * @author 黄远东 <641435071@qq.com>
 */
class Meeting extends Validate
{
    // 定义验证规则
    protected $rule = [
    	'name|会议室名' => 'require',
    	'r_number|容纳人数' => 'require|number',
    	'r_resource|配置资源' => 'require',
    ];


}
