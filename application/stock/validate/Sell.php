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
class Sell extends Validate
{
 //定义验证规则
	protected $rule = [
		'name|出库主题' => 'require',
		'deliveryid|销售发货订单' => 'require',
		'mlid|需用明细' => 'require',
		'zrid|经办人' => 'require',
		'ckbm|出库部门' => 'require',
		'ckid|出库人' => 'require',
	];
	// 验证提示
	protected $message = [
		'deliveryid.require' => '请选择销售发货订单',
		'mlid.require' => '请选择需用明细',
		'ckbm.require' => '请选择出库部门',
	];
	
}
