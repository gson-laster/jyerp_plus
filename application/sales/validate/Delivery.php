<?php
namespace app\sales\validate;
use think\Validate;
/**
 * 投标项目验证器
 * @package app\asstes\validate
 * @author HJP
 */
class Delivery extends Validate
{
	//定义验证规则
	protected $rule = [
		'name|收货名称' => 'require',
		'customer_name|客户名称' => 'require',
		'phone|客户联系方式(手机)' => 'require|number|length:11',
		'deliveryphone|收货人电话(手机)' => 'require|number|length:11',
		'money|运费金额' => 'require|number',
		'deliveryman|收货人' => 'require',
		'addrss|收货地址' => 'require',
		'goodaddrss|发货地址' => 'require',
	];
	// 验证提示
	protected $message = [
		'monophyletic.require' => '请选择源单号',
		'oid.require' => '请选择所属部门',
		'paytype.require' => '请选择支付方式',
		'goodtype.require' => '请选择交货方式',
		'transport.require' => '请选择运送方式',
	];
	
}
