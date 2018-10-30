<?php
namespace app\tender\validate;
use think\Validate;
/**
 * 投标项目验证器
 * @package app\asstes\validate
 * @author HJP
 */
class Prebudget extends Validate
{
	//定义验证规则
	protected $rule = [
		'name|预算主题' => 'require',
		'money|预算总额'=>'require',	
		'date|预算时间'=>'require'	,	
		'item|销售机会'=>'require'	,	
		'money|预算总额'=>'require',	
		'pre_date|预算天数' => 'require|number|gt:0'
	];
	// 验证提示
	protected $message = [
		'name.require|预算主题' => '预算主题不能为空',
		'money.require'=>'预算总额不能为空'	,	
		'date.require'=>'预算日期不能为空'	,	
		'item.require'=>'销售机会不能为空'	,	
		'money.require'=>'预算总额不能为'	,	
	];
	
}
