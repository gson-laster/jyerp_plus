<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18 0018
 * Time: 14:51
 */

namespace app\purchase\validate;


use think\Validate;

class Money extends Validate
{
    //定义验证规则
    protected $rule = [
        'title|主题' => 'require',
//      'sid|供应商' => 'require',
//      'ptype|源单类型' => 'require',
        'pnumber|源单号' => 'require',
        'askuid|询价员' => 'require',
        'cid|采购类别' => 'require',
        'oid|采购部门' => 'require',
        'price_number|询价次数' => 'require',
        'price_time|询价日期' => 'require',
        'price_type|币种' => 'require',
        'rate|汇率' => 'require',
        'is_add|是否为增值税'=> 'require',
        'wid|制单人' => 'require',
        'create_time|制单日期' => 'require',

    ];

    //定义验证提示
    protected $message = [

    ];
}