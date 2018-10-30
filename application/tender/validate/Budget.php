<?php
namespace app\tender\validate;

use think\Validate;


class Budget extends Validate {
	protected $rule = [
		'title|项目预算主题' => 'require',
		'obj_id|预算项目' => 'require',
		'budget|预算成本' => 'require|number|gt:0',
		'big_budget|预算金额大写' => 'require',
		'start_time|开始日期' => 'require',
		'end_time|预算金额大写' => 'require',
	];
}

?>