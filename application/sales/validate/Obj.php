<?php
namespace app\sales\validate;
use think\Validate;
/**
 * 投标项目验证器
 * @package app\asstes\validate
 * @author HJP
 */
class Obj extends Validate
{
	//定义验证规则
	protected $rule = [
		'name|项目名称' => 'require',
		'address|项目地址' => 'require',
		//'start_time|计划开始日期' => 'require',
		'unit|建设单位' => 'require',
		'contact|联系人' => 'require',
		'phone|联系电话' => 'require|number|length:11',
		
	];
	// 验证提示
	protected $message = [
		'type.require' => '请选择类型',
	];
	
}
