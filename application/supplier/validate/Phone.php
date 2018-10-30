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
class Phone extends Validate
{
    //定义验证规则
    protected $rule = [
        'sid|供应商名称'  => 'require',
        'name|主题' => 'require',
        'uid|我方联络人'      => 'require',
        'susername|供应商联络人'      => 'require',
        'stime|联络时间'      => 'require',
        'cause|联络事由'      => 'require',
    ];

    //定义验证提示
    protected $message = [
        'sid.require'    => '请选择供应商名称',
        'name.require' => '请输入主题',
        'susername.require' => '请输入供应商联络人',
        'stime.require' => '请输入联络时间',
        'uid.require'     => '请选择我方联络人',
        'cause.require'     => '请选择联络事由',
        'type.require'     => '请选择联络事由',
    ];

}
