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

namespace app\produce\validate;

use think\Validate;

/**
 * 工作中心验证器
 * @package app\produce\validate
 * @author 黄远东<64143571@qq.com>
 */
class Workcenter extends Validate
{
    //定义验证规则
    protected $rule = [      
        //'header|责任人'    => 'require',
        'name|工作中心名称'  => 'require',
    	'code|工作中心编号'  => 'number',
    	'org_id|所属部门'  => 'require',		
    ];

    //定义验证提示
    protected $message = [
        'name.require' => '请输入中心名称',
        'org_id.require'    => '请选择所属部门',
    ];
}
