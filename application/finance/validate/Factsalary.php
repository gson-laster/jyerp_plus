<?php
namespace app\finance\validate;
use think\Validate;
/**
 * 投标项目验证器
 * @package app\asstes\validate
 * @author HJP
 */
class Factsalary extends Validate
{
	//定义验证规则
	protected $rule = [
		'obj_id|项目名称' => 'require',
		'fact|实发工资' => 'require',
	];
	protected $message = [
		'obj_id.require' => '请选择项目',
		'fact.require'=>'实发工资不能为空',
	];
	
	
}
