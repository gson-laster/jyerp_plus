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

namespace app\logs\model;

use think\Model as ThinkModel;
use think\Db;

/**
 * 日志模型
 * @package app\cms\model
 */
class Plan extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__PERSONNEL_PLAN__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
   
    // 定义修改器
    public function setPlanTimeAttr($value)
    {
        return $value != '' ? strtotime($value) : '';
    }    
    public function getPlanTimeAttr($value)
    {
    	return $value != 0 ? date('Y-m-d', $value) : '';
    }

    
    /**
     * 获取档案列表
     * @param array $map 筛选条件
     * @param array $order 排序
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getList($map = [], $order = [])
    {
    	$data_list = self::view('personnel_plan', true)    	
    	->view("admin_user", ['username','nickname','avatar','sex','birth','role','organization','position','is_on'], 'admin_user.id=personnel_plan.uid', 'left')
    	->view('admin_position', 'title as positions', 'admin_user.position=admin_position.id', 'left')
    	
    	->where($map)
    	->order($order)
    	->paginate();
    	return $data_list;
    }
    
    /**
     * 获取日志
     * @param array $map 筛选条件
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getOne($id = '',$map = [])
    {
    	$data_list = self::view('personnel_plan', true)
	    	->view("admin_user", ['username','nickname','avatar','sex','birth','role','organization','position','is_on','email','mobile'], 'admin_user.id=personnel_plan.uid', 'left')
	    	->view('admin_position', 'title as positions', 'admin_user.position=admin_position.id', 'left')
	    	->view('admin_organization', 'title as organizations', 'admin_organization.id=admin_user.organization', 'left')
	    
	    	->where(['personnel_plan.id'=>$id]) 
	    	
	    	->where($map)
	    	->find();
    	return $data_list;
    }
    
    //获取最后一条计划数据
        public static function getLastOne($map = [])
    {		

    	$data_list = Db::name('personnel_plan')
	    	->field('title')
	    	->where($map) 
	    	->order('plan_time desc')
	    	->limit(1)
	    	->find();
    	return $data_list;
    }
    
}