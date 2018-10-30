<?php
namespace app\tender\validate;
use think\Validate;

class Alreadysalary extends Validate
{

	protected $rule = [
		'obj_id|项目' => 'require',
		'already|计划工资' => 'require',
		's_time|开始时间'=>'require',
		'e_time|结束时间'=>'require',
	];
	protected $message = [
		'obj_id.require' => '项目不能为空',
		'already.require'=>'计划工资不能为空',
	];
	
	
}
