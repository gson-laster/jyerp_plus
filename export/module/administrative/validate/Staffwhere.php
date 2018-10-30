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

namespace app\administrative\validate;

use think\Validate;

/**
 * 奖惩验证器
 * @package app\admin\validate
 * @author 蔡伟明 <314013107@qq.com>
 */
class Staffwhere extends Validate
{
    //定义验证规则
    protected $rule = [      
        'user_id|员工'    => 'require',
        'oid|部门'  => 'require',
        'staff_where|去向'  => 'require',
    	'start_time|开始时间'  => 'require',
    	'end_time|结束时间'  => 'require',    	
    ];

}
