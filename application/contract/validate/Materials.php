<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/1 0001
 * Time: 10:33
 */

namespace app\contract\validate;


use think\Validate;

class Materials extends Validate
{
    //定义验证规则
    protected $rule = [
        'name|计划主题' => 'require',
        'mlid|需用明细' => 'require',
        'xysl|需用数量' => 'require',
        'ckjg|参考价格' => 'require',
        'xj|小计' => 'require',
        
        'number|合同编号'=>'require',
        'name|合同名称'=>'require',
        'money|合同金额'=>'require',
        'paytype|支付方式'=>'require',
        'ftype|结算方式'=>'require',
        'handle_type|交货方式'=>'require',  
        'note|付款条件'=>'require',
        'people|我方签约人'=>'require', 
    ];
    // 验证提示
    protected $message = [
    
        'mlid.require' => '请选择需用明细',
    ];
}