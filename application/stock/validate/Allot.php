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
class Allot extends Validate
{
 //定义验证规则
	protected $rule = [
		'name|调拨主题' => 'require',
		'mlid|需用明细' => 'require',
		'zrid|调拨负责人' => 'require',
		'yhid|所属部门' => 'require',
		'drid|调入仓库' => 'require',
		'dhid|调货部门' => 'require',
		'dcid|调出仓库' => 'require',
	];
	// 验证提示
	protected $message = [
		'mlid.require' => '请选择需用明细',
		'drid.require' => '请选择调入仓库',
		'dhid.require' => '请选择调货部门',
	];
	
}
