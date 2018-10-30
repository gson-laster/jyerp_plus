<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18 0018
 * Time: 14:51
 */

namespace app\purchase\validate;


use think\Validate;

class Order extends Validate
{
    //定义验证规则
    protected $rule = [
        'name|主题' => 'require',
//      'supplier_id|供应商' => 'require',
//      'source|源单类型' => 'require',
        'source_id|源单号' => 'require',
        'purchase_uid|采购员' => 'require',
        'purchase_type|采购类别' => 'require',
        'purchase_organization|采购部门' => 'require',
        'balance_type|结算方式' => 'require',
        'arrival_type|交货方式' => 'require',
        'money_type|币种' => 'require',
        'rate|汇率' => 'require',
        'is_add_tax|是否为增值税'=> 'require',
    ];

    //定义验证提示
    protected $message = [

    ];
}