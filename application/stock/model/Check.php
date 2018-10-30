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
 * 借货模型
 * @package app\produce\model
 */
class Check extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__STOCK_CHECK__';

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
    	$data_list = self::view('stock_check', true)    	
    	->view("admin_user", ['nickname'=>'zrid'], 'admin_user.id=stock_check.zrid', 'left')
    	->view("admin_user b", ['nickname'=>'tzid'], 'b.id=stock_check.tzid', 'left')
    	->view('admin_organization',['title'=>'pdbm'],'admin_organization.id=stock_check.pdbm','left')
    	->view('stock_house',['name'=>'pdck'],'stock_house.id=stock_check.pdck','left')
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
    	$data_list = self::view('stock_check', true)
    	->view("admin_user", ['nickname'=>'zrid'], 'admin_user.id=stock_check.zrid', 'left')
    	->view("admin_user b", ['nickname'=>'tzid'], 'b.id=stock_check.tzid', 'left')
    	->view("admin_user c", ['nickname'=>'zdid'], 'c.id=stock_check.zdid', 'left')
    	->view('admin_organization',['title'=>'pdbm'],'admin_organization.id=stock_check.pdbm','left')
    	->view('stock_house',['name'=>'pdck'],'stock_house.id=stock_check.pdck','left')
    	->where(['stock_check.id'=>$id]) 
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
		$data_list = self::view('stock_check_detail', true)
    	->view("stock_material", ['name','version','unit'], 'stock_material.id=stock_check_detail.itemsid', 'left') 
    	->where($map)
    	->paginate();
    	return $data_list;  	
	} 
	//取物品id
    public static function getMaterials($id){		
		return db::name('stock_check_detail')->where('pid',$id)->column('itemsid');
	}
}