<?php
namespace app\sales\validate;
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
		'name|计划名称' => 'require',
		'start_time|开始时间' => 'require',
		'end_time|结束时间' => 'require',
		'low_money|参考报价' => 'require|number',
		'total_money|计划报价' => 'require|number',		
	];
	// 验证提示
	protected $message = [
		'type.require' => '请选择计划类型',
		'department.require' => '请选择所属部门',
	];
	
}
