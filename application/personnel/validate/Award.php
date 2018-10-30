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

namespace app\personnel\validate;

use think\Validate;

/**
 * 奖惩验证器
 * @package app\admin\validate
 * @author 蔡伟明 <314013107@qq.com>
 */
class Award extends Validate
{
    //定义验证规则
    protected $rule = [      
        'uid|用户'    => 'require',
        'award_type|奖惩类型'  => 'require',
    	'award_cate|奖惩项目'  => 'require',
    	'money'  => 'number',    	
    ];

    //定义验证提示
    protected $message = [
        'module.require' => '请选择所属模块',
        'pid.require'    => '请选择所属节点',
    ];
}
