<?php
namespace app\assets\validate;
use think\Validate;
/**
 * 资产验证器
 * @package app\assets\validate
 * @author HJP
 */
class Asstes extends Validate
{
	// 定义验证规则
	protected $rule = [
		'category|类别名称' => 'require',
	];
	// 定义验证提示
	protected $message = [
		'category.require' => '请输入类别名称',
	];
}
