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

namespace app\supplier\validate;

use think\Validate;

/**
 * 节点验证器
 * @package app\admin\validate
 * @author 蔡伟明 <314013107@qq.com>
 */
class Res extends Validate
{
    //定义验证规则
    protected $rule = [
        'sid|供应商名称'  => 'require',
        'res|物品' => 'require',
        'uid|推荐人'      => 'require',
    ];

    //定义验证提示
    protected $message = [
        'sid.require'    => '请选择供应商名称',
        'res.require' => '请输入物品名称',
        'uid.require'     => '请选择推荐人',
    ];

}
