<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28 0028
 * Time: 14:54
 */

namespace app\finance\validate;


use think\Validate;

class Other extends Validate
{
    //定义验证规则
    protected $rule = [
        'payer|付款人' => 'require',
        'part|部门' => 'require',
        'supplier|供应商' => 'require',
        'account|公司账户' => 'require',
        'money|付款金额' => 'require',
        'ptype|付款类型' => 'require',
        'pway|支付方式' => 'require',
        'maker|经办人' => 'require',
        'item|项目' => 'require',
        'money|付款金额' => 'require',
        'date|时间'=>'require'
    ];
}