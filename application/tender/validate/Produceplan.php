<?php
namespace app\tender\validate;
use think\Validate;
/**
 * 投标项目验证器
 * @package app\asstes\validate
 * @author HJP
 */
class Produceplan extends Validate
{
	//定义验证规则
	protected $rule = [
		'obj_id|项目名称' => 'require',
		'name|生产主题' => 'require',
		'date|日期'	=> 'require'	
	];
	// 验证提示
	protected $message = [
		'obj_id.require' => '请选择项目名称',
		'name.require' => '请填写生产主题',
		'date.require' => '日期不能为空',
	];
	
}
