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
class Bad extends Validate
{
 //定义验证规则
	protected $rule = [
		'name|报损主题' => 'require',
		'mlid|需用明细' => 'require',
		'zrid|经办人' => 'require',
		'bsbm|报损部门' => 'require',
		'ck|仓库' => 'require',
	];
	// 验证提示
	protected $message = [
		'mlid.require' => '请选择需用明细',
		'ck.require' => '请选择仓库',
		'bsbm.require' => '请选择报损部门',
	];
	
}
