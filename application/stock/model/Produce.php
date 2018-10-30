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

namespace app\stock\model;

use think\Model as ThinkModel;
use think\Db;
/**
 * 生产入库模型
 * @package app\produce\model
 */
class Produce extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__STOCK_PRODUCE__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
   
   
    /**
     * 获取仓库列表
     * @param array $map 筛选条件
     * @param array $order 排序
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getList($map = [], $order = [])
    {
    	$data_list = self::view('stock_produce', true)    
		->view('tender_obj',['name'=>'sid'],'tender_obj.id=stock_produce.sid','left')
    	->view("produce_production", ['name'=>'order_id'], 'produce_production.id=stock_produce.order_id', 'left')
    	->view("admin_user", ['nickname'=>'header'], 'admin_user.id=produce_production.header', 'left')    	   	
    	->view("admin_user f", ['nickname'=>'warehouses'], 'f.id=stock_produce.warehouses', 'left')    	
        ->view('admin_organization', ['title' => 'org_id'], 'admin_organization.id=produce_production.org_id', 'left')
        ->view('admin_organization c', ['title' => 'putinid'], 'c.id=stock_produce.putinid', 'left')
		->view('stock_house',['name'=>'house_id'],'stock_house.id=stock_produce.house_id','left')
    	->where($map)
    	->order($order)
    	->paginate();
    	return $data_list;
    }
    
    /**
     * 获取仓库
     * @param array $map 筛选条件
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getOne($id = '',$map = [])
    {
    	$data_list = self::view('stock_produce', true)
		->view('tender_obj',['name'=>'sid'],'tender_obj.id=stock_produce.sid','left')
		->view('stock_house',['name'=>'house_id'],'stock_house.id=stock_produce.house_id','left')
    	->where(['stock_produce.id'=>$id]) 
    	->where($map)
    	->find();
    	return $data_list;
    }
    //获取单源明细
	public static function get_Detail($id = ''){
		$getDetail = self::view('produce_production',['id','header','org_id'])
		->view('admin_user',['nickname'=>'header'],'admin_user.id=produce_production.header','left')
    	->view('admin_organization',['title'=>'org_id'],'admin_organization.id=produce_production.org_id','left')
		->where(['produce_production.id'=>$id])
		->find();	
 		return $getDetail;
	}
    /**
     * 获取所有仓库
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getTree()
    {
    	$menus = self::where(['status'=>1])->column('name','id');
    	return $menus;
    }
    //查看
	public static function getDetail($map = [])
	{
		$data_list = self::view('stock_produce_detail', true)
    	->view("stock_material", ['name','version','unit','price'], 'stock_material.id=stock_produce_detail.itemsid', 'left') 
    	->where($map)
    	->paginate();
    	return $data_list;  	
	} 
	//取物品id
    public static function getMaterials($id){		
		return db::name('stock_produce_detail')->where('pid',$id)->column('itemsid');
	}
}