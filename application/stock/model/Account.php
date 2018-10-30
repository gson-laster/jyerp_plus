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
class Account extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__STOCK_ACCOUNT__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
   
   //获取仓库基础物资明细
   public static function getList($map = [], $order = []){
		$data_list = self::view('stock_account',true)
		->view('stock_material',['name'=>'material_name','code'=>'material_code','version'=>'material_version','unit'=>'material_unit'],'stock_material.id=stock_account.materialid','left')		
		->where($map)
		->order($order)
		->paginate();
		return $data_list;
   }
}