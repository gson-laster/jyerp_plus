<?php
namespace app\task\model;
use think\Model as ThinkModel;
use think\Db;
class Task_detail extends ThinkModel
{
	// 设置当前模型对应的完整数据表名称
	protected $table = '__TASK_DETAIL__';
	// 自动写入时间戳
	protected $autoWriteTimestamp = true;
	//递归部门和公司数据
	public static function getTree($data, $pid=0){
		$map['status'] = 1;
		$data = Db::name('admin_organization')->where($map)->field('id,pid,title as text')->select();
		$data2 = Db::name('admin_user')->where($map)->field('id,organization as pid,nickname as text')->select();
		$num = count($data) - 1;
		$max = $data[$num]['id'];
		foreach($data2 as $k => $v){
			$v['uid'] = $v['id'];
			$v['id'] = $max + $v['id'];
			$tree[] = $v;
		}
		//把两个数组合并一个数组
		$data = array_merge($data, $tree);
		$data = array_reverse($data);
		$tree = '';
		foreach($data as $k => &$v){			
			if($v['pid'] == $pid){
				$v['nodes'] = self::getTree($data, $v['id']);
				unset($v['pid']);
				$tree[] = $v;
			}
		}
	
		
		return $tree;
	}
	public static function get_nickname(){
		$result = array();
    	$where['status'] = ['egt', 1];   
    	// 获取名称
    	$category = Db::name('admin_user')->where($where)->select();
    	foreach ($category as $v) {
    		$result[$v['id']] = $v['nickname'];
    	}
    	return $result;
	}
	public static function get_helpname($str = ''){
		$str = trim($str, ',');
		$data = explode(',', $str);
		$result = '';
		if($str == 0){
			$result = '暂无';
			return $result;
		}
		foreach($data as $k => $v){			
			$result .= self::get_nickname()[$v].",";			
		}
		$result = rtrim($result, ',');
		return $result;
	}
	
}
