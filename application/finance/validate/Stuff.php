<?php
namespace app\common\validate;
use think\Validate;
/**
 * 资产列表验证器
 * @package app\asstes\validate
 * @author HJP
 */
class stuff extends Validate
{
	//定义验证规则
	protected $rule = [
		'date|日期' => 'require',
//		'number|付款编号' => 'require',
		'name|付款名称' => 'require',
		'type|源单类型' => 'require',
		'account|银行账户' => 'require',
		'moneyed|已付款金额' => 'require',
		'payed|已支付金额' => 'require',
		'stock|累计入库金额' => 'require',
		'allpay|累计付款金额' => 'require',
		'notpay|未支付金额' => 'require',
		'operator|经办人' => 'require',
		'pay|付款金额' => 'require',
		'info|(供)账户信息' => 'require',
	];
	
}
