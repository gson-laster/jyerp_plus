<?php
namespace app\common\validate;
use think\Validate;
/**
 * 资产列表验证器
 * @package app\asstes\validate
 * @author HJP
 */
class FirstPay extends Validate
{
	//定义验证规则
	protected $rule = [
		'name|账户昵称' => 'require',
		'accmount|账户' => 'require',
		'bank|开户银行' => 'require',
		'first_money|期初金额' => 'require|number',
		'big_money|金额大写' => 'require',
		'operator|录入人' => 'require',
	];
	
}
