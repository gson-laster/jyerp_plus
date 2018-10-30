<?php
namespace app\sales\validate;
use think\Validate;
/**
 * 投标项目验证器
 * @package app\asstes\validate
 * @author HJP
 */
class Order extends Validate
{
	//定义验证规则
	protected $rule = [
		'name|订单名称' => 'require',
		'customer_name|客户名称' => 'require',
		'phone|客户联系方式(手机)' => 'require|number|length:11',
		'document_time|开始日期' => 'require',
		'zrid|业务员' => 'require',
		'end_time|截止日期'	=> 'require',
	];
	// 验证提示
	protected $message = [
		'monophyletic.require' => '请选择源单类型',
		'oid.require' => '请选择所属部门',
		'paytype.require' => '请选择支付方式',
		'goodtype.require' => '请选择交货方式',
		'transport.require' => '请选择运送方式',
	];
	
}
