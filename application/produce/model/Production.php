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
 * 生产任务管理模型
 * @package app\produce\model
 */
class Production extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__PRODUCE_PRODUCTION__';

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
    	$data_list = self::view('produce_production', true)    	
    	->view("admin_user", ['nickname'], 'admin_user.id=produce_production.uid', 'left') 
		->view("tender_obj", ['name'=>'obj_name'], 'tender_obj.id=produce_production.obj_id', 'left')		
    	->view("admin_user b", ['nickname'=>'bname'], 'b.id=produce_production.header', 'left')
    	->view("produce_plan", ['name'=>'plan_name'], 'produce_plan.id=produce_production.plan_id', 'left')
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
    	$data_list = self::view('produce_production', true)
    	->view("admin_user", ['nickname'=>'header_name','organization'], 'admin_user.id=produce_production.uid', 'left') 
    	->view("produce_plan", ['name'=>'plan_name'], 'produce_plan.id=produce_production.plan_id', 'left')
		->view("tender_obj",['name'=>'obj_id'],'tender_obj.id=produce_production.obj_id','left')
    	->view("admin_organization", ['title'=>'org_name'], 'admin_organization.id=produce_production.org_id', 'left')
    	->where(['produce_production.id'=>$id]) 
    	->where($map)
    	->find();
    	return $data_list;
    }  
	//查看
	public static function getDetail($map = [])
	{
		$data_list = self::view('produce_production_list', true)
    	->view("stock_material", ['name','version','unit','price','house_id'], 'stock_material.id=produce_production_list.smid', 'left') 
		->view('stock_house',['name'=>'ckname'],'stock_material.house_id=stock_house.id','left')
    	->where($map)
    	->paginate();
    	return $data_list;  	
	} 
	//取物品id
    public static function getMaterials($id){		
		return db::name('produce_production_list')->where('ppid',$id)->column('smid');
	}	
//关联生产入库 HJP
    //获取生产任务主题
    public static function getName(){
    	$result = array();
		$map['status'] = 1;
    	$getName = self::where($map)->select();
    	foreach($getName as $v){
    	$result['0'] = '其他';
			$result[$v['id']] = $v['name'];
		}
		return $result;
    }
    //获取加工类别
    public static function getJgid(){
    	$result = array();
    	$getCid = self::view('produce_production',['id','jg_type'])      	
    	->select();
    	foreach($getCid as $v){
			$result[$v['id']] = $v['jg_type'];
		}
		return $result;
    }
    //获取生产负责人
    public static function getCid(){
    	$result = array();
    	$getCid = self::view('produce_production',['id','header'])
    	->view("admin_user", ['nickname'], 'admin_user.id=produce_production.header', 'left')       	
    	->select();
    	foreach($getCid as $v){
			$result[$v['id']] = $v['nickname'];
		}
		return $result;
    }
    //获取生产部门
    public static function getOid(){
    	$result = array();
    	$getOid = self::view('produce_production',['id','org_id'])
        ->view('admin_organization', ['title' => 'org_id'], 'admin_organization.id=produce_production.org_id', 'left')       	
    	->select();
    	foreach($getOid as $v){
			$result[$v['id']] = $v['org_id'];
		}
		return $result;
    }	
}