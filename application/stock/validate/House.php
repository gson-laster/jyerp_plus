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
 * 仓库验证器
 * @package app\produce\validate
 * @author 黄远东<64143571@qq.com>
 */
class House extends Validate
{
    //定义验证规则
    protected $rule = [      
        //'header|责任人'    => 'require',
        'name|仓库名称'  => 'require|unique:stock_house',
    	'type|仓库类型'  => 'require',
    	'zrid|仓库负责人'  => 'require',		
    ];

    //定义验证提示
    protected $message = [
    		'name.unique' => '仓库名称不能重复',    		
    ];
}
