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

namespace app\notice\validate;

use think\Validate;

/**
 * 公告类型验证器
 * @package app\notice\validate
 * @author 黄远东 <641435071@qq.com>
 */
class Cate extends Validate
{
    //定义验证规则
    protected $rule = [             
        'title|公告类型'  => 'require|unique:notice_cate',
    	'sort|排序'  => 'number',   	
    ];

}
