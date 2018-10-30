<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 16:22
 */

namespace app\contract\validate;


use think\Validate;

class Hire extends Validate

{
    //定义验证规则
    protected $rule = [
        'name|合同名称' => 'require',
        'obj_id|项目名称' => 'require',
        'mlid|需求明细'=>'require',
        'number|合同编号' => 'require',
        'plan|租赁计划' => 'require',
        'obj_id|所属项目' => 'require',
        'money|合同金额' => 'require|number',
        'ctype|合同类型' => 'require',
        'start_time|开始日期' => 'require',
        'end_time|结束日期' => 'require',
        'supplier|供应商' => 'require',
        'ftype|结算方式' => 'require',
        'paytype|付款方式' => 'require',
        'premoney|预付金额' => 'require|number',
        'bzmoney|保证金' => 'require|number',
        'people|参与人员' => 'require',
        'create_uid|填报人' => 'require',
        'note|付款条件' => 'require',
//      'notes|主要条款' => 'require' 

    ];
    // 验证提示
    protected $message = [
        'obj_id.require' => '请选择项目',
        'mlid.require' => '请选择需用明细',
    ];
}