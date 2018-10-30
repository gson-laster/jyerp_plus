<?php
namespace app\tender\validate;
use think\Validate;
/**
 * 投标项目验证器
 * @package app\asstes\validate
 * @author HJP
 */
class Materials extends Validate
{
	//定义验证规则
	protected $rule = [
		'name|计划主题' => 'require',
		'obj_id|项目名称' => 'require',
		'mlid|需用明细' => 'require',
		'xysl|需用数量' => 'require',
		'ckjg|参考价格' => 'require',
		'xj|小计' => 'require',
	];
	// 验证提示
	protected $message = [
		'obj_id.require' => '请选择项目',
		'mlid.require' => '请选择需用明细',
	];
	
}
