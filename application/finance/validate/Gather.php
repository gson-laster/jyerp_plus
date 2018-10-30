<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/29 0029
 * Time: 10:56
 */

namespace app\finance\validate;


use think\Validate;

class Gather extends Validate
{
    protected $rule = [
        'date|收款日期' => 'require',
        'pact|合同名称' => 'require',
        'supplier|供应商' => 'require',
        'account|公司账户' => 'require',
        'money|收款金额' => 'require',
        'gtype|收款类型' => 'require',
        'name|收款人'=>'require',
        'maker|录入人' => 'require',

    ];
}