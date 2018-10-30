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

namespace app\produce\model;

use think\Model as ThinkModel;

/**
 * 工作中心模型
 * @package app\produce\model
 */
class Workcenter extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__PRODUCE_WORKCENTER__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
   
   
    /**
     * 获取工作中心列表
     * @param array $map 筛选条件
     * @param array $order 排序
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getList($map = [], $order = [])
    {
   		return $data = self::view('produce_workcenter')
		->view('admin_user',['nickname'=>'header'],'admin_user.id=produce_workcenter.header','left')
		->view('admin_user a',['nickname'=>'uid'],'a.id=produce_workcenter.uid','left')
		-> where($map) -> order($order) -> paginate();
   		
    }
    
    /**
     * 获取工作中心
     * @param array $map 筛选条件
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getOne($id = '',$map = [])
    {
    	$data_list = self::view('produce_workcenter', true)
    	->view("admin_user", ['nickname'=>'zrname'], 'admin_user.id=produce_workcenter.header', 'left')
    	->view("admin_organization", ['title'], 'admin_organization.id=produce_workcenter.org_id', 'left')
    	->where(['produce_workcenter.id'=>$id]) 
    	->where($map)
    	->find();
    	return $data_list;
    }
    
    /**
     * 获取所有工作中心
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getTree()
    {
    	$menus = self::where(['status'=>1])->column('name','id');
    	return $menus;
    }
}