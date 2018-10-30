<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/1 0001
 * Time: 14:14
 */

namespace app\contract\validate;


use think\Validate;

class Income extends Validate
{
    protected $rule = [
        "date|日期" =>"require",
        "number|合同编号" =>"require",
        "title|合同标题" =>"require",
        "attach_item|所属项目" =>"require",
        "type|合同类型" =>"require",
        "begin_date|开始日期" =>"require",
        "end_date|结束日期" =>"require",
        "money|合同金额" =>"require",
        //"big|金额大写" =>"require",
        "nail|甲方" =>"require",
        "second_party|乙方" =>"require",
        "operator|签订人" =>"require",
        "pay_type|付款方式" =>"require",
        //"balance|结算方式" =>"require",
        //"advances_received|预收款" =>"require",
        //"bail|保证金" =>"require",
        //"collection_terms|收款条件" =>"require",
       // "main_requirements|主要条款" =>"require",
    ];
}