<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/6 0006
 * Time: 10:38
 */

namespace plugins\SectorChart\model;


use think\Model;
use think\Db;
class SectorChart extends Model
{
    public static function getList(){
        $map['status'] = 1;

        //项目
       $data_list = self::view('tender_obj',['id','name','sale'])->order('tender_obj.create_time desc')
->where($map)->select();      
      
           foreach($data_list as $key=>&$value){
      
      
      		 $materials_sum =db::name('stock_otherout_detail')
              ->alias('t')
              ->join('stock_otherout b','t.pid=b.id','left')
              ->join('tender_obj a','a.id=b.sid','left')
              ->group('b.sid')
              ->where(['is_jz'=>1])
              ->column('sid,sum(je)');
      
            	//->where(['b.sid'=>['in','a.id']])
            	//->buildSql();
      
      
      //合同总收款  //如果为0 不统计这条数据的利润
              $obj_sum = db::name('finance_receipts')->where(['item'=>$value['id'],'status'=>1])->field('sum(gathering) as obj_sum')->find();
              if(empty($obj_sum['obj_sum'])){
                  unset($data_list[$key]);
                  continue;
              }
              $value['obj_sum'] = $obj_sum['obj_sum'];
    		 $value['materials_sum'] = isset($materials_sum[$value['id']]) ?  $materials_sum[$value['id']] : 0;
               //工资
              $facts_sum = db::name('tender_fact_salary')->where('obj_id',$value['id'])->field('sum(fact) as facts')->find();
              $value['facts_sum'] = $facts_sum['facts'] ? $facts_sum['facts'] : 0;
              //其他付款
              $others_sum = db::name('finance_info')->where(['item'=>$value['id'],'status'=>1])->field('sum(money) as others_sum')->find();
              $value['others_sum'] = $others_sum['others_sum'] ? $others_sum['others_sum'] : 0;        
             
           	}
           	//dump($data_list);
            return $data_list;
            }

}