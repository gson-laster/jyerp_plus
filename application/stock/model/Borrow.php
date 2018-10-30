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
class Borrow extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__STOCK_BORROW__';

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
    	$data_list = self::view('stock_borrow', true) 
    	->view('admin_user',['nickname'=>'zrid'],'admin_user.id=stock_borrow.zrid','left') 
    	->view('admin_user b',['nickname'=>'ckid'],'b.id=stock_borrow.ckid','left')
    	->view('admin_organization',['title'=>'jhbm'],'admin_organization.id=stock_borrow.jhbm','left')
    	->view('admin_organization c',['title'=>'jcbm'],'c.id=stock_borrow.jcbm','left')
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
    	$data_list = self::view('stock_borrow', true)
    	->view("admin_user", ['nickname'=>'zrid'], 'admin_user.id=stock_borrow.zrid', 'left')
    	->view("admin_user b", ['nickname'=>'ckid'], 'b.id=stock_borrow.ckid', 'left')
    	->view("admin_user c", ['nickname'=>'zdid'], 'c.id=stock_borrow.zdid', 'left')
    	->view('admin_organization',['title'=>'jhbm'],'admin_organization.id=stock_borrow.jhbm','left')
    	->view('admin_organization d',['title'=>'jcbm'],'d.id=stock_borrow.jcbm','left')
    	->where(['stock_borrow.id'=>$id]) 
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
		$data_list = self::view('stock_borrow_detail', true)
    	->view("stock_material", ['name','version','unit'], 'stock_material.id=stock_borrow_detail.itemsid', 'left') 
    	->where($map)
    	->paginate();
    	return $data_list;  	
	} 
	//取物品id
    public static function getMaterials($id){		
		return db::name('stock_borrow_detail')->where('pid',$id)->column('itemsid');
	}
	//获取借货单名称
	public static function getName(){
		$result = array();
		$getName = self::select();
		foreach($getName as $v){
			$result[$v['id']] = $v['name'];
		}
		return $result;
	}
	//获取借货部门
	public static function getJhbm(){
		$result = array();
		$getJhbm = self::view('stock_borrow',['id','jhbm'])
        ->view('admin_organization', ['title' => 'jhbm'], 'admin_organization.id=stock_borrow.jhbm', 'left')       	
		->select();
		foreach($getJhbm as $v){
			$result[$v['id']] = $v['jhbm'];
		}
		return $result;
	}
	//获取借货人
	public static function getJhname(){
		$result = array();
		$getJhname = self::view('stock_borrow',['id','zrid'])       	
    	->view("admin_user", ['nickname'=>'jhname'], 'admin_user.id=stock_borrow.zrid', 'left')       	
		->select();
		foreach($getJhname as $v){
			$result[$v['id']] = $v['jhname'];
		}
		return $result;
	}
	//获取借货日期
	public static function getJh_time(){
		$result = array();
		$getJh_time = self::select();
		foreach($getJh_time as $v){
			$result[$v['id']] = $v['jh_time'];
		}
		return $result;
	}
	//获取被借部门
	public static function getJcbm(){
		$result = array();
		$getJcbm = self::view('stock_borrow',['id','jcbm'])
        ->view('admin_organization', ['title' => 'jcbm'], 'admin_organization.id=stock_borrow.jcbm', 'left')       	
		->select();
		foreach($getJcbm as $v){
			$result[$v['id']] = $v['jcbm'];
		}
		return $result;
	}
	//获取返还仓库
	public static function getJcck(){
		$result = array();
		$getJcck = self::view('stock_borrow',['id','jcck'])      	
    	->view('stock_house',['name'=>'jcck'],'stock_house.id=stock_borrow.jcck','left')
		->select();
		foreach($getJcck as $v){
			$result[$v['id']] = $v['jcck'];
		}
		return $result;
	}
}