<?php
namespace app\sales\validate;
use think\Validate;
/**
 * 投标项目验证器
 * @package app\asstes\validate
 * @author HJP
 */
class Opport extends Validate
{
	//定义验证规则
	protected $rule = [
		'name|机会名称' => 'require',
		'customer_name|客户名称' => 'require',
		'found_time|发现时间' => 'require',
		'zrid|业务员' => 'require',	
		'type|机会类型'	=> 'require'
	];
	// 验证提示
	protected $message = [
		'customer_name.require' => '请选择客户名称',
		'type.require' => '请选择机会类型',
		'department.require' => '请选择所属部门',
	];
	
}
