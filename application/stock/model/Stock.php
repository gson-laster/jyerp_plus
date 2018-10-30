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
 * 仓库模型
 * @package app\produce\model
 */
class Stock extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__STOCK_STOCK__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
   
   //获取仓库基础物资明细
   public static function getList($map = [], $order = []){
		$data_list = self::view('stock_stock',true)
		->view('stock_house',['name'=>'stock_name'],'stock_house.id=stock_stock.ckid','left')
		->view('stock_material',['name'=>'material_name','code'=>'material_code','version'=>'material_version','unit'=>'material_unit'],'stock_material.id=stock_stock.materialid','left')
		->view('stock_material_type',['title'=>'material_type_name'],'stock_material_type.id=stock_stock.material_type','left')
		->where($map)
		->order($order)
		->paginate();
		return $data_list;
   }
   public static function getFlow($map = []){
	   
   }
    public static function exportData($map=[], $order = []){
		$data_list = self::view('stock_stock', true) 
		->view('stock_house',['name'=>'ckid'],'stock_house.id=stock_stock.ckid','left')
		->view('stock_material',['name'=>'materialid'],'stock_material.id=stock_stock.materialid','left')
		->where($map)
		->where($order)
		->select();
		foreach ($data_list as $key => &$value) {
			$value['update_time'] = date('Y-m-d',$value['update_time']);
		}
	   return $data_list;
	}
	public static function getMobone($id = ''){
		$data_list = self::view('stock_stock',true)
		->view('stock_house',['name'=>'stock_name'],'stock_house.id=stock_stock.ckid','left')
		->view('stock_material',['name'=>'material_name','code'=>'material_code','version'=>'material_version','unit'=>'material_unit'],'stock_material.id=stock_stock.materialid','left')
		->view('stock_material_type',['title'=>'material_type_name'],'stock_material_type.id=stock_stock.material_type','left')
		->where('stock_stock.id',$id)
		->find();
		return $data_list;
	}
}