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

namespace app\purchase\validate;

use think\Validate;

/**
 * 节点验证器
 * @package app\admin\validate
 * @author 蔡伟明 <314013107@qq.com>
 */
class Plan extends Validate
{
    //定义验证规则
    protected $rule = [      
        'name|主题'    => 'require',
        'tid|采购类型'    => 'require',
        'ptime|计划时间'    => 'require',
        'pid|计划员'    => 'require',
        'oid|采购部门'    => 'require',
        'cid|采购员'    => 'require',
        //去掉
//      'ptype|源单类型'    => 'require',
        
        
    ];

}
