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

namespace app\tender\model;

use think\Model;
use think\Db;
/**
 * 生产任务模型
 * @package app\produce\model
 */
class Factpic extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__TENDER_FACTPIC__';
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
    	$data_list = self::view('tender_factpic', true)    	
    	->view("admin_user", ['nickname'], 'admin_user.id=tender_factpic.uid', 'left')   
		->view("tender_obj",['name'=>'obj_id'],'tender_obj.id=tender_factpic.obj_id','left')
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
    public static function getOne($id = '',$map = [])
    {
    	$data_list = self::view('tender_factpic', true)
    	->view("admin_user", ['nickname'=>'uid','organization'], 'admin_user.id=tender_factpic.uid', 'left')   
    	->view('tender_obj',['name'=>'obj_id'],'tender_obj.id=tender_factpic.obj_id','left')
    	->where(['tender_factpic.id'=>$id]) 
    	->where($map)
    	->find();
    	return $data_list;
    }  
//查看
	public static function getDetail($map = [])
	{
		$data_list = self::view('produce_plan_list', true)
    	->view("stock_material", ['name','version','unit'], 'stock_material.id=produce_plan_list.smid', 'left') 
    	->where($map)
    	->paginate();
    	return $data_list;  	
	} 
	//取物品id
    public static function getMaterials($id){		
		return db::name('produce_plan_list')->where('ppid',$id)->column('smid');
	}	
}