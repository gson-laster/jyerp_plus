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
 * 销售出库模型
 * @package app\produce\model
 */
class Sell extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__STOCK_SELL__';

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
    	$data_list = self::view('stock_sell', true)    	    	
    	->view("sales_delivery", ['name'=>'deliveryid','customer_name','oid','uid'], 'sales_delivery.id=stock_sell.deliveryid', 'left')
    	->view("admin_user", ['nickname'=>'zrid'], 'admin_user.id=stock_sell.zrid', 'left')
    	->view("admin_user b", ['nickname'=>'ckid'], 'b.id=stock_sell.ckid', 'left')
    	->view("admin_user c", ['nickname'=>'uid'], 'c.id=sales_delivery.uid', 'left')
    	->view("admin_organization", ['title'=>'ckbm'], 'admin_organization.id=stock_sell.ckbm', 'left')
    	->view("admin_organization d", ['title'=>'department'], 'd.id=sales_delivery.oid', 'left')
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
    	$data_list = self::view('stock_sell', true)
    	->view("sales_delivery", ['name'=>'deliveryid','customer_name','oid','uid','goodaddrss','addrss'], 'sales_delivery.id=stock_sell.deliveryid', 'left')
    	->view("admin_user", ['nickname'], 'admin_user.id=stock_sell.zrid', 'left')
    	->view("admin_user b", ['nickname'=>'uid'], 'b.id=sales_delivery.uid', 'left')
    	->view("admin_user c", ['nickname'=>'ckid'], 'c.id=stock_sell.ckid', 'left')
    	->view("admin_user d", ['nickname'=>'zdid'], 'd.id=stock_sell.zdid', 'left')
    	->view("admin_organization", ['title'=>'ckbm'], 'admin_organization.id=stock_sell.ckbm', 'left')
    	->view("admin_organization e", ['title'=>'department'], 'e.id=sales_delivery.oid', 'left')
    	->where(['stock_sell.id'=>$id]) 
    	->where($map)
    	->find();
    	return $data_list;
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
		$data_list = self::view('stock_sell_detail', true)
    	->view("stock_material", ['name','version','unit','price'], 'stock_material.id=stock_sell_detail.itemsid', 'left') 
    	->where($map)
    	->paginate();
    	return $data_list;  	
	} 
	
	//取物品id
    public static function getMaterials($id){		
		return db::name('stock_sell_detail')->where('pid',$id)->column('itemsid');
	}
}