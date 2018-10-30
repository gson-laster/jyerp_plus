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

namespace app\produce\model;

use think\Model as ThinkModel;

/**
 * 物料需求计划模型
 * @package app\produce\model
 */
class Mateget extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__PRODUCE_MATEGET__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
   
   
    /**
     * 物料需求计划列表
     * @param array $map 筛选条件
     * @param array $order 排序
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getList($map = [], $order = [])
    {
    	$data_list = self::view('produce_mateget', true)    	
        ->view("admin_user", ['nickname'=>'get_username'], 'admin_user.id=produce_mateget.get_user_id', 'left')   
    	->view("admin_user c", ['nickname'=>'out_username'], 'c.id=produce_mateget.out_user_id', 'left')   
    	->view("admin_user b", ['nickname'=>'bname'], 'b.id=produce_mateget.wid', 'left')
        ->view("admin_organization", ['title'=>'org_name'], 'admin_organization.id=produce_mateget.organization_id', 'left')
    	->view("produce_plan", ['name'=>'plan_name'], 'produce_plan.id=produce_mateget.plan_id', 'left')
    	->where($map)
    	->order($order)
    	->paginate();
    	return $data_list;
    }
    
  

    /**
     * 获取物料
     * @param array $map 筛选条件
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getOne($id = '')
    {
        $data_list = self::view('produce_mateget', true)        
        ->view("admin_user", ['nickname'=>'get_username'], 'admin_user.id=produce_mateget.get_user_id', 'left')   
        ->view("admin_user c", ['nickname'=>'out_username'], 'c.id=produce_mateget.out_user_id', 'left')   
        ->view("admin_user b", ['nickname'=>'bname'], 'b.id=produce_mateget.wid', 'left')
        ->view("admin_organization", ['title'=>'org_name'], 'admin_organization.id=produce_mateget.organization_id', 'left')
        ->view("produce_plan", ['name'=>'plan_name'], 'produce_plan.id=produce_mateget.plan_id', 'left')
        ->where(['produce_mateget.id'=>$id])
        ->find();
        return $data_list;
    }  

    //获取物资
    public static function getmdetail($map = [])
    {
        $data_list = self::view('produce_mateget_list', true)        
        ->view("stock_material", ['name','version','unit'], 'stock_material.id=produce_mateget_list.mid', 'left')
        ->view('stock_house',['name'=>'ckname'],'stock_house.id=produce_mateget_list.ckid')
        ->view('stock_stock',['number'],'stock_stock.materialid=produce_mateget_list.mid and stock_stock.ckid=produce_mateget_list.ckid','left')
        ->where($map)
        ->select();
        return $data_list;
    }  

    public static function getMaterialout($map=[],$order=[]){
            $map['produce_mateget.status'] = 1;
            $data_list = self::view('produce_mateget','id,create_time')
                      ->view("admin_user b", ['nickname'=>'get_user_id'], 'b.id=produce_mateget.get_user_id ', 'left')//领料人   
                      ->view("admin_user c", ['nickname'=>'out_user_id'], 'c.id=produce_mateget.out_user_id', 'left') //发料人
                      ->view('produce_mateget_list',['lysl','dj','price_sum'],'produce_mateget_list.pid=produce_mateget.id')
                      ->view('stock_material',['code','version','unit','name'=>'material_name'],'stock_material.id=produce_mateget_list.mid')
                      ->view('stock_material_type',['title'=>'material_type_name'],'stock_material_type.id=stock_material.type')
                      ->view('stock_house',['name'=>'house_name'],'stock_house.id=produce_mateget_list.ckid')
                      ->where($map)
                      ->order($order)
                      ->paginate();
			
                      
            $tem_time = 0;
            foreach ($data_list as $key => $value) {
                $day0 = strtotime(date('Y-m-d',$value['create_time']));
                $day24 = strtotime(date('Y-m-d',$value['create_time']))+86400;
                if($key!=0 && ($tem_time>$day0 && $tem_time<$day24)){
                    $tem_time = $value['create_time'];
                    $value['create_time'] = '';
                }else{
                    $tem_time = $value['create_time'];
                }
				$value['dj'] = '￥'.number_format($value['dj'],2);
				$value['price_sum'] = '￥'.number_format($value['price_sum'],2);
            }
			
            return $data_list;

      }
}