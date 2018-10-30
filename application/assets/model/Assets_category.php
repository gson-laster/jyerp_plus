<?php
namespace app\assets\model;

use think\Model as ThinkModel;
use think\Db;

class Assets_category extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__ASSETS_CATEGORY__';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
   /**
     * 获取论坛类型列表     
     */
     public static function getType()
    {
    	$result = array();
    	$where['status'] = ['egt', 1];
    
    	// 获取菜单
    	$category = self::where($where)->select();
    	foreach ($category as $v) {
    		$result[$v['id']] = $v['category'];
    	}
    	return $result;
    }
}
