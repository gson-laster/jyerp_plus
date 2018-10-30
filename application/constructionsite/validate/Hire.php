<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/30 0030
 * Time: 16:23
 */

namespace app\constructionsite\Validate;


use think\Validate;

class Hire extends Validate
{
    //定义验证规则
    protected $rule = [
        'name|计划名称' => 'require',
        'obj_id|项目名称' => 'require',
        'mlid|需用明细'=>'require',

    ];
    // 验证提示
    protected $message = [
        'obj_id.require' => '请选择项目',
        'mlid.require' => '请选择需用明细',
    ];
}