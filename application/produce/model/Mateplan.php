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
 * 物料需求计划模型
 * @package app\produce\model
 */
class Mateplan extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__PRODUCE_MATEPLAN__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
   
   
    /**
     * 物料需求计划列表
     * @param array $map 筛选条件
     * @param array $order 排序
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getList($map = [], $order = [])
    {
    	$data_list = self::view('produce_mateplan', true)    	
    	->view("admin_user", ['nickname'], 'admin_user.id=produce_mateplan.uid', 'left')   
    	->view("admin_user b", ['nickname'=>'bname'], 'b.id=produce_mateplan.header', 'left')
    	->view("produce_plan", ['name'=>'plan_name'], 'produce_plan.id=produce_mateplan.plan_id', 'left')
    	->where($map)
    	->order($order)
    	->paginate();
    	return $data_list;
    }
    
    /**
     * 获取物料需求计划
     * @param array $map 筛选条件
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getOne($id = '',$map = [])
    {
    	$data_list = self::view('produce_mateplan', true)
    	->view("admin_user", ['nickname'=>'header_name','organization'], 'admin_user.id=produce_mateplan.uid', 'left') 
    	->view("produce_plan", ['name'=>'plan_name'], 'produce_plan.id=produce_mateplan.plan_id', 'left')
    	->view("admin_organization", ['title'=>'org_name'], 'admin_organization.id=produce_mateplan.org_id', 'left')
    	->where(['produce_mateplan.id'=>$id]) 
    	->where($map)
    	->find();
    	return $data_list;
    }  
//查看
	public static function getDetail($map = [])
	{
		$data_list = self::view('produce_mateplan_list', true)
    	->view("stock_material", ['name','version','unit','price'], 'stock_material.id=produce_mateplan_list.smid', 'left') 
    	->where($map)
    	->paginate();
    	return $data_list;  	
	} 
	//取物品id
    public static function getMaterials($id){		
		return db::name('produce_mateplan_list')->where('pmid',$id)->column('smid');
	}		
}