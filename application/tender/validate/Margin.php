<?php
namespace app\tender\validate;
use think\Validate;
/**
 * 投标项目验证器
 * @package app\asstes\validate
 * @author HJP
 */
class Margin extends Validate
{
	//定义验证规则
	protected $rule = [
		'name|项目名称' => 'require',
		'unit|收取单位' => 'require',
		'bank|开户行' => 'require',
		'account|账号' => 'require|number|length:19',
		'money|保证金金额' => 'require|number',
		'item_time|交款日期日期'	=> 'require',
		'back_time|预计回款日' => 'require',	
	];
	// 验证提示
	protected $message = [
		'name.require' => '请选择项目名称',
	];
	
}
