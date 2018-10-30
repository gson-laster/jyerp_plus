<?php
namespace app\common\validate;
use think\Validate;
/**
 * 资产列表验证器
 * @package app\asstes\validate
 * @author HJP
 */
class receipts extends Validate
{
	//定义验证规则
	protected $rule = [
    	'money|报销金额' => 'require|number',
    	'zrid|报销人' => 'require',
		'item|所属项目' => 'require'
	];
	
	
}
