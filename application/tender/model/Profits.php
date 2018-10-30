<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5 0005
 * Time: 15:26
 */

namespace app\tender\model;
use think\Model;
use think\Db;
class Profits extends Model
{
    public static function getList($map,$tax = ''){

        $map['status'] = 1;

        //项目
        $data_list = self::view('tender_obj','id,name,sale')->where($map)->paginate();
		//dump($data_list);die;
        
           foreach($data_list as $key=>&$value){

              //合同总收款  //如果为0 不统计这条数据的利润
              $obj_sum = db::name('finance_receipts')->where(['item'=>$value['id'],'status'=>1])->field('sum(gathering) as obj_sum')->find();
              if(empty($obj_sum['obj_sum'])){
                  unset($data_list[$key]);
                  continue;
              }
              $value['obj_sum'] = $obj_sum['obj_sum'];

              //租赁
              $hires = db::name('finance_hire')->where(['item'=>$value['id'],'status'=>1])->field('sum(money) as hires')->find();
              $value['hires_sum'] = $hires['hires'] ? $hires['hires'] : 0;

              // 材料
              $cost = implode(db::name('produce_cost')->where(['obj_id'=>$value['id'],'status'=>1])->column('id'),','); //成本id
              $lists = db::name('produce_cost_list')->where(['pmid'=>['in',$cost]])->column('id,smid,snum');//成本明细id 物资id  物资数量
              $cost_materials = implode(db::name('produce_cost_list')->where(['pmid'=>['in',$cost]])->column('smid'),',');
              $price = db::name('stock_stock')->where(['materialid'=>['in',$cost_materials]])->column('materialid,price');//物资价格
              $materials = 0;
              foreach ($lists as $k => $v) {
                  $materials += ($v['snum']*$price[$v['smid']]);
               } 
              $value['materials_sum'] = $materials ? $materials : 0;

               //工资
              $facts_sum = db::name('tender_fact_salary')->where('obj_id',$value['id'])->field('sum(fact) as facts')->find();
              $value['facts_sum'] = $facts_sum['facts'] ? $facts_sum['facts'] : 0;

              //其他付款
              $others_sum = db::name('finance_other')->where(['item'=>$value['id'],'status'=>1])->field('sum(money) as others_sum')->find();
              $value['others_sum'] = $others_sum['others_sum'] ? $others_sum['others_sum'] : 0;

              //税率
              $value['tax'] = $tax;

              //毛利润
              $tax2 = $value['tax'] ? (1-$value['tax']) : 1;
              $value['mlr'] = ($value['obj_sum']-$value['hires_sum']-$value['materials_sum']-$value['facts_sum']-$value['others_sum'])*$tax2;

              //毛利率
              $value['mll'] = round($value['mlr']/$value['obj_sum'],2);

              $value['obj_sum'] = '￥ '.number_format($value['obj_sum'],2);
              $value['hires_sum'] = '￥ '.number_format($value['hires_sum'],2);
              $value['materials_sum'] = '￥ '.number_format($value['materials_sum'],2);
              $value['facts_sum'] = '￥ '.number_format($value['facts_sum'],2);
              $value['others_sum'] = '￥ '.number_format($value['others_sum'],2);
              $value['mlr'] = '￥ '.number_format($value['mlr'],2);
           	}
           return $data_list;
       
            }
        
    }