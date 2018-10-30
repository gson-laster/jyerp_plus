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
 * 生产任务明细模型
 * @package app\produce\model
 */
class PlanList extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__PRODUCE_PLAN_LIST__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
   
   
    /**
     * 生产任务明细列表
     * @param array $map 筛选条件
     * @param array $order 排序
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getList($map = [], $order = [])
    {
    	$data_list = self::view('produce_plan_list', true)    	
    	->view("stock_material", ['code','name','version','unit'], 'stock_material.id=produce_plan_list.smid', 'left')   	
    	->where($map)
    	->order($order)
    	->paginate();
    	return $data_list;
    }
    
    /**
     * 获取生产任务明细
     * @param array $map 筛选条件
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getOne($id = '',$map = [])
    {
    	$data_list = self::view('produce_plan_list', true)
    	->view("stock_material", ['code','name','version','unit'], 'stock_material.id=produce_plan_list.smid', 'left')   	
    	->where(['produce_plan_list.id'=>$id]) 
    	->where($map)
    	->paginate();
    	return $data_list;
    }   
}