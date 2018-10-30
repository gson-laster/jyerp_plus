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
 * 采购入库模型
 * @package app\produce\model
 */
class Purchase extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__STOCK_PURCHASE__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
   /*
    * 采购入库列表
    * @author HJP
    */
	public function getIntimeAttr($value){
		return $value ? date('Y-m-d',$value) : '';
	}
   public static function getList($map = [], $order = [])
   {
   		$data_list = self::view('stock_purchase', true)
   		//->view("purchase_arrival", ['cid','sid','oid','name'=>'order_id'], 'purchase_arrival.id=stock_purchase.order_id', 'left')
   		->view('supplier_list', ['name'=>'sid'], 'supplier_list.id=stock_purchase.sid', 'left')    	
    	//->view("admin_user", ['nickname'=>'deliverer'], 'admin_user.id=stock_purchase.deliverer', 'left')   
    	//->view("admin_user b", ['nickname'=>'zrid'], 'b.id=stock_purchase.zrid', 'left')   
    	//->view("admin_user c", ['nickname'=>'warehouses'], 'c.id=stock_purchase.warehouses', 'left')     
    	//->view("admin_user e", ['nickname'=>'cid'], 'e.id=purchase_arrival.cid', 'left')   
        //->view('admin_organization', ['title' => 'putinid'], 'admin_organization.id=stock_purchase.putinid', 'left')       	
        //->view('admin_organization f', ['title' => 'oid'], 'f.id=purchase_arrival.oid', 'left')
		->view('stock_house',['name'=>'house_id'],'stock_house.id=stock_purchase.house_id','left')
   		->where($map)
   		->order($order)
   		->paginate();
   		return $data_list;
   }
   public static function getOne($id = '',$map = [])
    {
    	$data_list = self::view('stock_purchase', true)
      //->view('purchase_arrival', ['name'=>'order_id','sid','cid','oid'], 'purchase_arrival.id=stock_purchase.order_id', 'left')       	
     ->view('supplier_list', ['name'=>'sid'], 'supplier_list.id=stock_purchase.sid', 'left')    	
      //->view("admin_user", ['nickname'=>'deliverer'], 'admin_user.id=stock_purchase.deliverer', 'left')   
      //->view("admin_user b", ['nickname'=>'zrid'], 'b.id=stock_purchase.zrid', 'left')   
      //->view("admin_user c", ['nickname'=>'warehouses'], 'c.id=stock_purchase.warehouses', 'left')   
     // ->view("admin_user d", ['nickname'=>'zdid'], 'd.id=stock_purchase.zdid', 'left')   
     // ->view("admin_user e", ['nickname'=>'cid'], 'e.id=purchase_arrival.cid', 'left')   
     // ->view('admin_organization', ['title' => 'putinid'], 'admin_organization.id=stock_purchase.putinid', 'left')       	
     // ->view('admin_organization f', ['title' => 'oid'], 'f.id=purchase_arrival.oid', 'left')
		->view('stock_house',['name'=>'house_id'],'stock_house.id=stock_purchase.house_id','left')	 
      ->where(['stock_purchase.id'=>$id]) 
    	->where($map)
    	->find();
    	return $data_list;
    }   
	
    //查看
	public static function getDetail($map = [])
	{
		$data_list = self::view('stock_purchase_detail', true)
    	->view("stock_material", ['name','version','unit','price'], 'stock_material.id=stock_purchase_detail.itemsid', 'left') 
    	->where($map)
    	->paginate();
    	return $data_list;  	
	} 
	//获取单源明细
	public static function get_Detail($id = ''){
		$getDetail = self::view('purchase_arrival',['id','sid','cid','oid'])
		->view('admin_user',['nickname'=>'cid'],'admin_user.id=purchase_arrival.cid','left')
    	->view('admin_organization',['title'=>'oid'],'admin_organization.id=purchase_arrival.oid','left')
		->view('supplier_list',['name'=>'sid'],'supplier_list.id=purchase_arrival.sid','left')
		->where(['purchase_arrival.id'=>$id])
		->find();	
 		return $getDetail;
	}
	//取物品id
  public static function getMaterials($id){		
		return db::name('stock_purchase_detail')->where('pid',$id)->column('itemsid');
	}

  public static function getMaterialin($map=[],$order=[]){
    $data_list = self::view('stock_purchase','id,intime,code')
              //->view("admin_user b", ['nickname'=>'zrid'], 'b.id=stock_purchase.zrid', 'left')   
              //->view("admin_user c", ['nickname'=>'warehouses'], 'c.id=stock_purchase.warehouses', 'left') 
              ->view('stock_purchase_detail',['rksl','dj','je'],'stock_purchase_detail.pid=stock_purchase.id')
              ->view('stock_material',['version','unit','name'=>'material_name'],'stock_material.id=stock_purchase_detail.itemsid')
              ->view('stock_material_type',['title'=>'material_type_name'],'stock_material_type.id=stock_material.type')
              ->view('stock_house',['name'=>'house_name'],'stock_house.id=stock_purchase.house_id')
              ->where('stock_purchase.status=1')
              ->where($map)
              ->order($order)
              ->paginate();
              
    $tem_time = 0;
    foreach ($data_list as $key => $value) {
        if($key!=0 && $tem_time == $value['intime']){
            $value['intime'] = '';
        }else{
            $tem_time = $value['intime'];
        }
		$value['dj'] = '￥'.number_format($value['dj'],2);
		$value['je'] = '￥'.number_format($value['je'],2);
    }
    return $data_list;

  }
  public static function getMobone($id = ''){
	  $data_list = self::view('stock_purchase_detail')
				->view('stock_purchase',['intime'],'stock_purchase.id=stock_purchase_detail.pid','left')
				 ->view('stock_material',['code','version','unit','name'=>'material_name'],'stock_material.id=stock_purchase_detail.itemsid')
				->view('stock_material_type',['title'=>'material_type_name'],'stock_material_type.id=stock_material.type')
                ->view('stock_house',['name'=>'house_name'],'stock_house.id=stock_purchase.house_id')
				->view('supplier_list', ['name'=>'sid'], 'supplier_list.id=stock_purchase.sid', 'left')    	
				->where('stock_purchase_detail.pid',$id)
				->find();
				//dump($data_list);die;
		return $data_list;		
  }


}