<?php
namespace app\tender\validate;
use think\Validate;
/**
 * 投标项目验证器
 * @package app\asstes\validate
 * @author HJP
 */
class Type extends Validate
{
	//定义验证规则
	protected $rule = [
		'name|类型名称' => 'require',		
	];
	// 验证提示
	protected $message = [
		
	];
	
}
