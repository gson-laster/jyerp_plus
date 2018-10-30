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

namespace app\purchase\model;

use think\Model;
use think\Db;
/**
 * 生产任务模型
 * @package app\produce\model
 */
class Informmoney extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__PURCHASE_INFORMMONEY__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
   
   
    /**
     * 生产任务列表
     * @param array $map 筛选条件
     * @param array $order 排序
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
     
     
    public static function getc(){
    	$data_list = Db::name('purchase_hetong')->where('status',1)->column('id,name');
    	return $data_list;
    	
    	
    	
    	
    	}
    public static function getList($map = [], $order = [])
    {
    	$data_list = self::view('purchase_informmoney', true)    	
    	->view("admin_user", ['nickname'], 'admin_user.id=purchase_informmoney.maker', 'left')   
			->view("purchase_hetong",['name'=>'contract'],'purchase_hetong.id=purchase_informmoney.contract','left')
			->view('supplier_list',['name'=>'supplier'],'supplier_list.id=purchase_informmoney.supplier')
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
    	$data_list = self::view('purchase_informmoney', true)
    	->view("admin_user", ['nickname'], 'admin_user.id=purchase_informmoney.maker', 'left')   
			->view("purchase_hetong",['name'=>'contract'],'purchase_hetong.id=purchase_informmoney.contract','left')
			->view('supplier_list',['name'=>'supplier'],'supplier_list.id=purchase_informmoney.supplier')
    	->where(['purchase_informmoney.id'=>$id]) 
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
	
	
	
	
	
	public static function getCa($id){
		
		$data_list = self::view('purchase_hetong_material', true)
		->view('supplier_list','name','supplier_list.id=purchase_hetong_material.supplier_id')
		->where('purchase_hetong_material.aid',$id)
		->select();
		return $data_list;
		}

    public static function getAll($id){
        //dump($id);die;
        $map ['status']=1;
        
        $data_list = Db::name('purchase_hetong_material')->where('aid',$id)->value('SUM(plan_money) as all_money');
        //dump($data_list);die;
        return $data_list;



        }
       



    
}