<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/25 0025
 * Time: 16:16
 */

namespace app\finance\validate;


use think\Validate;

class Finance extends Validate
{
    //定义验证规则
    protected $rule = [
        'title|报销名称' => 'require',
        'name|报销人' => 'require',
        'item|所属项目' => 'require',
        'depot|部门' => 'require',
        'work|职位' => 'require',
        'project|报销科目' => 'require',
        'money|报销金额' => 'require',
        'bx_time|报销时间' => 'require',
        'sum|累计报销' => 'require',
        'maker|填报人' => 'require',
        'time|开单日期' => 'require',
    ];
}