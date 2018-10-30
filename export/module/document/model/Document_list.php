<?php
namespace app\document\model;
use think\Model as ThinkModel;
use think\Db;
class Document_list extends ThinkModel
{
	// 设置当前模型对应的完整数据表名称
	protected $table = '__DOCUMENT_LIST__';
	// 自动写入时间戳
	protected $autoWriteTimestamp = true;
	
}
