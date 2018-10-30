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
class Otherout extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__STOCK_OTHEROUT__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
   
   public function getIntimeAttr($value){
		return $value ? date('Y-m-d',$value) : '';
	}
    /**
     * 获取仓库列表
     * @param array $map 筛选条件
     * @param array $order 排序
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getList($map = [], $order = [])
    {
    	$data_list = self::view('stock_otherout', true)    	
		->view('tender_obj', ['name'=>'sid'], 'tender_obj.id=stock_otherout.sid', 'left')    		
    	//->view("admin_user", ['nickname'=>'zrid'], 'admin_user.id=stock_otherout.zrid', 'left')
    	//->view("admin_user b", ['nickname'=>'ckid'], 'b.id=stock_otherout.ckid', 'left')
    	//->view("admin_organization", ['title'=>'ckbm'], 'admin_organization.id=stock_otherout.ckbm', 'left')
		->view('stock_house',['name'=>'house_id'],'stock_house.id=stock_otherout.house_id','left')
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
    	$data_list = self::view('stock_otherout', true)
		->view('tender_obj', ['name'=>'sid'], 'tender_obj.id=stock_otherout.sid', 'left')  
		->view('stock_house',['name'=>'house_id'],'stock_house.id=stock_otherout.house_id','left')
    	//->view("admin_user", ['nickname'=>'zrid'], 'admin_user.id=stock_otherout.zrid', 'left')
    	//->view("admin_user b", ['nickname'=>'ckid'], 'b.id=stock_otherout.ckid', 'left')
    	//->view("admin_user c", ['nickname'=>'zdid'], 'c.id=stock_otherout.zdid', 'left')
    	//->view("admin_organization", ['title'=>'ckbm'], 'admin_organization.id=stock_otherout.ckbm', 'left')
    	->where(['stock_otherout.id'=>$id]) 
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
		$data_list = self::view('stock_otherout_detail', true)
    	->view("stock_material", ['name','version','unit'], 'stock_material.id=stock_otherout_detail.itemsid', 'left') 
    	->where($map)
    	->paginate();
    	return $data_list;  	
	} 
	//取物品id
    public static function getMaterials($id){		
		return db::name('stock_otherout_detail')->where('pid',$id)->column('itemsid');
	}
	public static function getMaterialout($map=[],$order=[]){
            $data_list = self::view('stock_otherout','id,intime,code')                     
                      ->view('stock_otherout_detail',['cksl','dj','je'],'stock_otherout_detail.pid=stock_otherout.id')
                      ->view('stock_material',['code','version','unit','name'=>'material_name'],'stock_material.id=stock_otherout_detail.itemsid')
                      ->view('stock_material_type',['title'=>'material_type_name'],'stock_material_type.id=stock_material.type')
                      ->view('stock_house',['name'=>'house_name'],'stock_house.id=stock_otherout.house_id')
                     ->where('stock_otherout.status=1')
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
		  $data_list = self::view('stock_otherout_detail')
					->view('stock_otherout',['intime'],'stock_otherout.id=stock_otherout_detail.pid','left')
					 ->view('stock_material',['code','version','unit','name'=>'material_name'],'stock_material.id=stock_otherout_detail.itemsid')
					->view('stock_material_type',['title'=>'material_type_name'],'stock_material_type.id=stock_material.type')
					->view('stock_house',['name'=>'house_name'],'stock_house.id=stock_otherout.house_id')
					->view('tender_obj', ['name'=>'sid'], 'tender_obj.id=stock_otherout.sid', 'left')    	
					->where('stock_otherout_detail.pid',$id)
					->find();
			return $data_list;		
  }
}