<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5 0005
 * Time: 15:26
 */

namespace app\finance\model;
use think\Model;
use think\Db;
class Profits extends Model
{
    public static function getList($map,$tax = ''){

        $map['status'] = 1;

        //项目
        $data_list = self::view('tender_obj',['id','name','sale'])->where($map)->select();
		//dump($data_list);die;
        
           foreach($data_list as $key=>&$value){
							
              //合同总收款  //如果为0 不统计这条数据的利润
              $obj_sum = db::name('finance_receipts')->where(['item'=>$value['id'],'status'=>1])->field('sum(gathering) as obj_sum')->find();
              if(empty($obj_sum['obj_sum'])){
                  unset($data_list[$key]);
                  continue;
              }
              
              $value['obj_sum'] = $obj_sum['obj_sum'];

  

              // 材料
              
              //$cost = implode(db::name('tender_obj')->where('status',1)->column('id'),','); //成本id
              $materials_sum =db::name('stock_otherout_detail')
              ->alias('t')
              ->field('sid')
              ->field('sum(je)')
              ->join('stock_otherout b','t.pid=b.id','left')
              ->join('tender_obj a','a.id=b.sid','left')
              ->group('b.sid')
            	->where(['is_jz'=>1])
            	//->where(['b.sid'=>['in','a.id']])
            	//->buildSql();    
              ->select();
              $a = [];
              
             
      				foreach($materials_sum as $k=>$v){
      					
	     					if($v['sid']==$value['id']){
	     						$value['materials_sum'] = $v['sum(je)'];
	      					$value['sid'] = $v['sid'];
	     					}
      					
      					
      				}    
      			
             
              
							
               //工资
              $facts_sum = db::name('tender_fact_salary')->where('obj_id',$value['id'])->field('sum(fact) as facts')->find();
              $value['facts_sum'] = $facts_sum['facts'] ? $facts_sum['facts'] : 0;
              //其他付款
              $others_sum = db::name('finance_info')->where(['item'=>$value['id'],'status'=>1])->field('sum(money) as others_sum')->find();
              $value['others_sum'] = $others_sum['others_sum'] ? $others_sum['others_sum'] : 0;
              //税率
              $value['tax'] = $tax;

              //毛利润
              $tax2 = $value['tax'] ? (1-$value['tax']) : 1;
              $value['mlr'] = ($value['obj_sum']-$value['materials_sum']-$value['facts_sum']-$value['others_sum'])*$tax2;

              //毛利率
              $value['mll'] = round($value['mlr']/$value['obj_sum'],2);
					
              $value['obj_sum'] = '￥ '.number_format($value['obj_sum'],2);
             
              $value['materials_sum'] = '￥ '.number_format($value['materials_sum'],2);
              $value['facts_sum'] = '￥ '.number_format($value['facts_sum'],2);
              $value['others_sum'] = '￥ '.number_format($value['others_sum'],2);
              $value['mlr'] = '￥ '.number_format($value['mlr'],2);
           	}
           	
           	
           return $data_list;
       
            }
        
    }