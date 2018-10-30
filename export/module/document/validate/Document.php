<?php
namespace app\document\validate;
use think\Validate;
/**
 * 文档验证器
 * @package app\document\validate
 * @author HJP
 */
class Document extends Validate
{
	// 定义验证规则
	protected $rule = [
		'name|目录名称' => 'require',
		'pidname|父级目录' => 'require',
		'type|文件上传' => 'require',
	];
	// 定义验证提示
	protected $message = [
		'name.require' => '请输目录名称',
		'pidname.require' => '请输入父级目录',
		'type.require' => '请上传文件',
	];
	//定义验证场景
	protected $scene = [
		//上传
		'upfile' => ['type'],
		//新添目录
		'add' => ['name', 'pidname'],
	];
}
