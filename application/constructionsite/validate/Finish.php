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

namespace app\constructionsite\validate;

use think\Validate;

/**
 * 节点验证器
 * @package app\admin\validate
 * @author 蔡伟明 <314013107@qq.com>
 */
class Finish extends Validate
{
    //定义验证规则
    protected $rule = [      
        'item|完工项目'    => 'require',
        's_time|开工日期'    => 'require',
        'e_time|完工日期'    => 'require',
        'obj_time|工期'    => 'require',
        'date|日期'    => 'require',
    ];

}
