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

namespace app\mobile\validate;
use think\Validate;


class BuildMeeting extends Validate
{
    //定义验证规则
    protected $rule = [      
        'title|会议主题'   => 'require',
    	'm_time|会议日期'  => 'require',
        's_time|开始时间'  => 'require',
    	'e_time|结束时间'  => 'require',    		
    	'compare|主持人'  => 'require',    		
    	'user_id|参会部门'  => 'require',    		
    ];    
}
