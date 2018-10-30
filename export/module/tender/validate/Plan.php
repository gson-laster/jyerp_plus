<?php
namespace app\tender\validate;
use think\Validate;
/**
 * 投标项目验证器
 * @package app\asstes\validate
 * @author HJP
 */
class Plan extends Validate
{
	//定义验证规则
	protected $rule = [
		'name|项目名称' => 'require',
		'money|招投标文件费用' => 'require|number',
		'time|日期'	=> 'require'	
	];
	// 验证提示
	protected $message = [
		'name.require' => '请选择项目名称',
	];
	
}
