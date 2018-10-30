<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18 0018
 * Time: 14:51
 */

namespace app\purchase\validate;


use think\Validate;

class Arrival extends Validate
{
    //定义验证规则
    protected $rule = [
        'name|主题' => 'require',
//      'sid|供应商' => 'require',
//      'ptype|源单类型' => 'require',
        'pnumber|源单号' => 'require',
        'ctype|采购类别' => 'require',
        'oid|采购部门' => 'require',
        'balance_type|结算方式' => 'require',
        'arrival_type|交货方式' => 'require',
        'currency|币种' => 'require',
        'rate|汇率' => 'require',
        'is_add_tax|是否为增值税'=> 'require',
        'look_user|可查看人员'=> 'require',
        'consignee_time|点收时间'=> 'require',
        'consignee|点收人'=> 'require',
    ];

    //定义验证提示
    protected $message = [

    ];
}