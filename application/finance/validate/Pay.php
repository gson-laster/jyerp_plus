<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28 0028
 * Time: 10:11
 */

namespace app\finance\validate;


use think\Validate;

class Pay extends Validate
{
    //定义验证规则
    protected $rule = [
        'name|付款名称' => 'require',
        'pact|租赁合同' => 'require',
        
        
        'bank_name|开户行名称' => 'require',
        'accmount|银行账户' => 'require',
        'money|付款金额' => 'require',
        'maker|填报人' => 'require',
        'date|日期' => 'require',
    ];
}