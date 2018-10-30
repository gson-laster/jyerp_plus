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
use think\Db;
/**
 * 生产任务模型
 * @package app\produce\model
 */
class Cost extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__PRODUCE_COST__';
// 自动写入时间戳
    protected $autoWriteTimestamp = true;
   
   
    /**
     * 生产任务列表
     * @param array $map 筛选条件
     * @param array $order 排序
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
	 public static function getDataa($map = [], $order = []){
		 $data_list = self::view('produce_cost',true)
		 ->view('admin_user',['nickname'=>'zrid'],'admin_user.id=produce_cost.zrid','left')
		 ->view('tender_obj',['name'=>'obj_id'],'tender_obj.id=produce_cost.obj_id','left')
		 ->view('produce_production',['name'=>'rw_name'],'produce_production.id=produce_cost.rw_id','left')
		 ->where($map)
		 ->order($order)
		 ->paginate();
		 //dump($data_list);die;
		 return $data_list;
	 }
    public static function getList($id)
    {
    	$data_list = Db::name('produce_production_list')
		->alias('t')
		->field('t.BOMid,t.ppid,t.smid cpid,t.produce_num,a.name cpname')
		->join('stock_material a','a.id=t.smid','left')
    	->where('t.ppid',$id)
    	->select();
		$data_rw = Db::name('produce_cost')
		->alias('t')
    	->where('t.rw_id',$id)
    	->value('id');
		$data_cost = Db::name('produce_cost_list')
		->alias('t')
    	->where('t.pmid',$data_rw)
    	->select();
		foreach($data_list as $key => $value){
			$data_list[$key]['cl'] = Db::name('produce_materials_list')
							->alias('t')
							->field('t.smid,t.quota,a.name clname,b.price')
							->join('stock_material a','a.id=t.smid','left')
							->join('stock_stock b','b.materialid=t.smid','left')
							->where('t.pmid',$value['BOMid'])
							->select();

		}
		
		foreach($data_list as $k=>$v){
			$data_list[$k]['cpnum'] = empty($data_list[$k]['cpnum']) ? 0 : $data_list[$k]['cpnum'];
			$tem = array();
			foreach($data_cost as $key=>$value){
				if($value['cpid']==$v['cpid']){
					$tem[] = $value;
					unset($data_cost[$key]);
				}
			}
			foreach($v['cl'] as $kk=>$vv){
				foreach($tem as $kkk =>$vvv){
					if($vv['smid']==$vvv['smid']){
						$data_list[$k]['cl'][$kk]['snum'] = $vvv['snum'];
					}
				}
			}
			$data_list[$k]['cpnum'] += count($data_list[$k]['cl']);
		}
		    	return $data_list;
		//dump($data_list);die;
		/**$obj = array();
        foreach ($data_list  as $key => &$value) {
            $value['cpnum'] = empty($value['cpnum']) ? 0 : $value['cpnum'];
			$obj[$value['obj_id']]['cpnum1'] = empty($obj[$value['obj_id']]['cpnum1']) ? 0 : $obj[$value['obj_id']]['cpnum1'];
			$tem = Db::name('produce_production_list')
				   ->alias('t')
				   ->field('t.id,t.ppid,t.smid,t.produce_num,t.BOMid,a.obj_id,a.name rwname,b.name objname,c.name sname')
				   ->join('produce_production a','t.ppid=a.id','left')
				   ->join('tender_obj b','a.obj_id=b.id','left')
				   ->join('stock_material c','t.smid=c.id','left')
				   ->where('ppid',$value['id'])
                   ->select();

			foreach($tem as $k => $v){
			  $tem[$k]['cl'] = Db::name('produce_materials_list')
							  ->alias('t')
							  ->field('t.id,t.pmid,t.smid,t.quota,a.price,a.name')
							  ->join('stock_material a','t.smid=a.id','left')
							  ->where('t.pmid',$v['BOMid'])
							  ->select();         		 	  
			}

            $value['rw_smid']  = $tem;
            $value['cpnum'] += count($value['rw_smid']);
			$obj[$value['obj_id']]['cpnum1'] += $value['cpnum'];			
            $obj[$value['obj_id']]['production'][] = $value->toArray();
		}**/	
		//dump($obj);die;
    	//return $obj;
    }
    
    /**
     * 获取生产任务
     * @param array $map 筛选条件
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getOne($id = '')
    {
    	$data_list = Db::name('produce_production_list')
		->alias('t')
		->field('t.BOMid,t.produce_num,t.smid cpid,a.name')
		->join('produce_materials a','a.id=t.BOMid','left')
    	->where(['t.smid'=>$id]) 
    	->find();
		$data = Db::name('produce_materials_list')
				->alias('b')
				->where('b.pmid',$data_list['BOMid'])
				->select();
		dump($data);die;
    	return $data_list;
    }   
	public static function getDetail($id = ''){
		$data_rw = Db::name('produce_production_list')
					->alias('t')
					->field('t.BOMid,t.smid,t.produce_num,a.name cpname,b.name Bname')
					->join('stock_material a','a.id=t.smid','left')
					->join('produce_materials b','b.id=t.BOMid','left')
					->where('ppid',$id)
					->select();
		foreach ($data_rw as $key => &$value){
			$value['cpnum'] = empty($value['cpnum']) ? 0 : $value['cpnum'];
			$value['cl'] = Db::name('produce_materials_list')
							->alias('t')
							->field('t.smid,t.quota,a.name clname,b.price')
							->join('stock_material a','a.id=t.smid','left')
							->join('stock_stock b','b.materialid=t.smid','left')
							->where('pmid',$value['BOMid'])
							->select();
			$value['cpnum'] += count($value['cl']);
		}
		//dump($data_rw);die;
		return $data_rw;
	} 
    public static function getTotal($map = [])
    {
        $total = self::view('produce_cost',['sum(good_num)'=>'good_name_total','sum(zai_good_cl_money)'=>'zai_good_cl_money_total','sum(good_gsxh_money)'=>'good_gsxh_money_total','sum(zai_good_monthcl_money)'=>'zai_good_monthcl_money_total','sum(zai_wages)'=>'zai_wages_total','sum(zai_zhizao)'=>'zai_zhizao_total','sum(zai_good_num)'=>'zai_good_num_total'])       
        ->view("admin_user", 'nickname', 'admin_user.id=produce_cost.wid', 'left')   
        ->view("stock_material", ['name'=>'good_name'], 'stock_material.id=produce_cost.good_id', 'left')
        ->where($map)
        ->find();

        $html = '<tr class="" style="color:#e66763"><td class="text-center">合计</td><td class="text-center">-</td><td class="text-center">-</td><td class="text-center">-</td><td class="text-center">-</td><td class="">'.$total["good_name_total"].'</td><td class="">'.$total["zai_good_cl_money_total"].'</td><td class="">'.$total["good_gsxh_money_total"].'</td><td class="">'.$total["zai_good_monthcl_money_total"].'</td><td class="">'.$total["zai_wages_total"].'</td><td class="">'.$total["zai_zhizao_total"].'</td><td class="">'.$total["zai_good_num_total"].'</td><td class="text-center">-</td><td class="text-center">-</td><td class="text-center">-</td></tr>';
        return $html;
    }
}

