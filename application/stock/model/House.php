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

/**
 * 仓库模型
 * @package app\produce\model
 */
class House extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__STOCK_HOUSE__';

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
    	$data_list = self::view('stock_house', true)    	
    	->view("admin_user", ['nickname'], 'admin_user.id=stock_house.zrid', 'left')
    	->view("admin_user b", ['nickname'=>'bname'], 'b.id=stock_house.uid', 'left')
		->view('stock_house_type',['name'=>'house_type'],'stock_house_type.id=stock_house.type','left')
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
    	$data_list = self::view('stock_house', true)
    	->view("admin_user", ['nickname'], 'admin_user.id=stock_house.zrid', 'left')
    	->where(['stock_house.id'=>$id]) 
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
    //获取仓库名称
    public static function getName(){
    	$result = array();
    	$map['status'] = 1;
    	$getName = self::where($map)->select();
    	foreach($getName as $v){
			$result[$v['id']] = $v['name'];
		}
		return $result;
    }
	public static function getCk($id = ''){    	
    	$map['status'] = 1;
		$map['id'] = $id;
    	$getCk = self::where($map)->value('name');    	
		return $getCk;
    }
}