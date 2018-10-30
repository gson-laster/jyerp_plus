<?php
namespace app\common\validate;
use think\Validate;
/**
 * 资产列表验证器
 * @package app\asstes\validate
 * @author HJP
 */
class Index extends Validate
{
	//定义验证规则
	protected $rule = [
		'name|账户昵称' => 'require',
		'accmount|账户' => 'require',
		'bank|开户银行' => 'require',
		'address|地址' => 'require',
		'date|开户日期' => 'require',
		'operator|经办人' => 'require',
		'ismoneyaccount|是否现金账户' => 'require',
		'status|是否停用' => 'require',
	];
	
}
