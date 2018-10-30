<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\mobile\model;

use think\Model as ThinkModel;
use Think\Db;

/**
 * 日志模型
 * @package app\cms\model
 */
class Builder extends ThinkModel
{
//	protected static $treeList = [];
	/*
	 
	 * 获取部门对应的员工列表*/
	public static function getList(){
		$items = [];
		$item = Db::name('admin_user') -> where('status', 1)  -> field('id,nickname,organization') -> select();
		//所有部门 
		$organization = Db::name('admin_organization') -> where('status', 1)  -> field('id,pid,title') -> select();
		foreach($item as $v) {
			if(!isset($items[$v['organization']])){
				$items[$v['organization']] = [];
			}
			array_push($items[$v['organization']], $v);
		}

		$arr = [];
		foreach($organization as $v){
			if(!isset($arr[$v['pid']])){
				$arr[$v['pid']] = [];
			}
			array_push($arr[$v['pid']], $v);
		
		}
		return $items;
	}


	
	//层级树方法
	
	//paramer   
//	public static function getTree($data, $pid = 0){
//		$tree = [];
//		foreach($data as $k => $v)
//		{
//		 	$tree
//		}
//		return $tree;
//	}
	
}