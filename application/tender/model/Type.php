<?php
	//合同
namespace app\tender\model;
use think\Model as ThinkModel;
use think\Db;
class Type extends ThinkModel
{
	protected $table = '__TENDER_TYPE__';
	protected $autoWriteTimestamp = true;
	//获取项目类型
	public static function get_type()
	{
		$result = array();
		$where['status'] = ['egt',1];
		$type = self::where($where)->select();
		foreach($type as $v){
			$result[0] = '暂无';
			$result[$v['id']] = $v['name'];
		}
		return $result;
	}
}
