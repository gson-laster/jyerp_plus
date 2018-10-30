<?php
namespace app\asstes\validate;
use think\Validate;
/**
 * 资产列表验证器
 * @package app\asstes\validate
 * @author HJP
 */
class Select extends Validate
{
	//定义验证规则
	protected $rule = [
		'categoryid|类别' => 'require',
		'name|名称' => 'require',
		'uid|申请人' => 'require',
		'procurement|采购人' => 'require',
		'money|发票金额(元)' => 'require|number',
		'invoice_time|发票日期' => 'require',
	];
	// 验证提示
	protected $message = [
		'categoryid.require' => '请选择类别',
		'name.require' => '请输入名称',
	];
	
}
