<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18 0018
 * Time: 14:51
 */

namespace app\purchase\validate;


use think\Validate;

class Informmoney extends Validate
{
    //定义验证规则
    protected $rule = [
        'name|主题' => 'require',

     
        'contract|销售合同' => 'require',
        'money|金钱' => 'require',

    ];

    //定义验证提示
    protected $message = [

    ];
}