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
use think\Db;
/**
 * 生产任务模型
 * @package app\produce\model
 */
class Page extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__PRODUCE_PLAN__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
   
   
    /**
     * 生产任务列表
     * @param array $map 筛选条件
     * @param array $order 排序
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getList($map = [], $order = [])
    {
    	$data_list = self::view('produce_plan', true)    	
    	->view("admin_user", ['nickname'], 'admin_user.id=produce_plan.uid', 'left')   
    	->view("admin_user b", ['nickname'=>'bname'], 'b.id=produce_plan.header', 'left')
		->view("tender_obj",['name'=>'obj_name'],'tender_obj.id=produce_plan.obj_id','left')
    	->where($map)
    	->order($order)
    	->paginate();
    	return $data_list;
    }
    
    /**
     * 获取生产任务
     * @param array $map 筛选条件
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
}