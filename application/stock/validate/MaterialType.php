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

namespace app\stock\validate;

use think\Validate;

/**
 * 物资类型验证器
 * @package app\admin\validate
 * @author 黄远东<641435071@qq.com>
 */
class MaterialType extends Validate
{
    //定义验证规则
    protected $rule = [      
        'pid|父类型'    => 'require',
        'title|节点标题'  => 'require',
    	'code|类型编号'  => 'length:6,32',    	
    ];

    //定义验证提示
    protected $message = [
        'pid.require'    => '请选择父类型',
    ];
}
