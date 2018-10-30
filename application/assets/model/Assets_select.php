<?php
namespace app\assets\model;

use think\Model as ThinkModel;
use app\user\model\Organization as OrganizationModel;
use think\Db;

class Assets_select extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__ASSETS_SELECT__';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    public static function get_name()
    {
       $result = array();
    	$where['status'] = ['egt', 1];
    
    	// 获取菜单
    	$category = Db::name('admin_user')->where($where)->select();
    	foreach ($category as $v) {    		
    		$result[$v['id']] = $v['nickname'];
    	}
    	return $result;
    }
    //获取部门
	public static function get_bm_name()
    {
       $result = array();
    	$where['status'] = ['egt', 1];   
    	// 获取菜单
    	$category = OrganizationModel::where($where)->select();
    	foreach ($category as $v) {
    		$result[0] = '暂无';
    		$result[$v['id']] = $v['title'];
    	}
    	return $result;
    }
}
