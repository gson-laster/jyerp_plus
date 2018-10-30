<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/25 0025
 * Time: 16:58
 */

namespace app\finance\validate;


use think\Validate;

class Standby extends Validate
{
    //定义验证规则
    protected $rule = [
        'zrname|领用人' => 'require',
        'part|部门' => 'require',
        'year_money|本年领取金额' => 'require',
        'money|金额' => 'require',
        'item|项目' => 'require',
        'time|日期' => 'require',
        'maker|经办人' => 'require',

    ];
}