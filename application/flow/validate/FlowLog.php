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

namespace app\flow\validate;

use think\Validate;

/**
 * 
 * @package app\admin\validate
 * @author 蔡伟明 <314013107@qq.com>
 */
class FlowLog extends Validate
{
    //定义验证规则
    protected $rule = [
        'result|是否同意' => 'require',
    ];

    //定义验证提示
    protected $message = [
        'result.require' => '请选择是否同意',
    ];
}
