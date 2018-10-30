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
 * 基础物资模型
 * @package app\cms\model
 */
class Material extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__STOCK_MATERIAL__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    /**
     * 获取基础物资列表
     * @param array $map 筛选条件
     * @param array $order 排序
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
	  public static function getList($map = [], $order = [])
    {
    	$data_list = self::where($map)
		->order($order)
		->paginate();
    	return $data_list;
    }
    
    /**
     * 获取基础物资
     * @param array $map 筛选条件
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getOne($id = '')
    {
    	$data_list = self::get($id);
    	return $data_list;
    }
	public static function getMobone($id = ''){
		 $data_list = self::view('stock_material', true)        
        ->view("stock_material_type", ['title'=>'type_name'], 'stock_material_type.id=stock_material.type', 'left')
		->view('stock_house',['name'=>'house_name1'],'stock_house.id=stock_material.house_id','left')
        ->where(['stock_material.id'=>$id])
        ->find();		
        return $data_list;
	}
    public static function exportData($map=[],$order=[]){
        $data_list = self::view('stock_material', true)        
        ->view("stock_material_type", ['title'=>'type_name'], 'stock_material_type.id=stock_material.type', 'left')
        ->where($map)
        ->order($order)
        ->paginate();
        foreach ($data_list as $key => &$value) {
            $value['status'] = $value['status']==1 ? '启用' : '禁用';
            $value['create_time'] = date('Y-m-d',$value['create_time']);
        }
        return $data_list;
    }
    	
}