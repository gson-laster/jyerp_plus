<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5 0005
 * Time: 17:39
 */

namespace app\tender\model;


use think\Model;
use think\db;
class Costall extends Model
{
    protected $table = '__TENDER_OBJ__';   
     public static function getList($map){
        $map['status'] = 1;
        //ЮĿ
        $data_list = self::view('tender_obj','id,name,sale')->where($map)->paginate();        
           foreach($data_list as $key=>&$value){
              $obj_sum = db::name('finance_receipts')->where(['item'=>$value['id'],'status'=>1])->field('sum(gathering) as obj_sum')->find();
              $value['obj_sum'] = $obj_sum['obj_sum'];           
              $hires = db::name('finance_hire')->where(['item'=>$value['id'],'status'=>1])->field('sum(money) as hires')->find();
              $value['hires_sum'] = $hires['hires'] ? $hires['hires'] : 0;       
              $cost = implode(db::name('produce_cost')->where(['obj_id'=>$value['id'],'status'=>1])->column('id'),','); //³ɱ¾id
              $lists = db::name('produce_cost_list')->where(['pmid'=>['in',$cost]])->column('id,smid,snum');//³ɱ¾ķϸid ϯ؊id  ϯ؊˽
              $cost_materials = implode(db::name('produce_cost_list')->where(['pmid'=>['in',$cost]])->column('smid'),',');
              $price = db::name('stock_stock')->where(['materialid'=>['in',$cost_materials]])->column('materialid,price');//物资价格            
              $materials = 0;
              foreach ($lists as $k => $v) {
                  $materials += ($v['snum']*$price[$v['smid']]);
               } 
              $value['materials_sum'] = $materials ? $materials : 0;      
              $facts_sum = db::name('tender_fact_salary')->where('obj_id',$value['id'])->field('sum(fact) as facts')->find();
              $value['facts_sum'] = $facts_sum['facts'] ? $facts_sum['facts'] : 0;
              $others_sum = db::name('finance_other')->where(['item'=>$value['id'],'status'=>1])->field('sum(money) as others_sum')->find();
              $value['others_sum'] = $others_sum['others_sum'] ? $others_sum['others_sum'] : 0;
              $value['obj_sum'] = '￥'.number_format($value['obj_sum'],2);
              $value['hires_sum'] = '￥ '.number_format($value['hires_sum'],2);
              $value['materials_sum'] = '￥ '.number_format($value['materials_sum'],2);
              $value['facts_sum'] = '￥ '.number_format($value['facts_sum'],2);
              $value['others_sum'] = '￥ '.number_format($value['others_sum'],2);
           	}
           	//dump($data_list);die;
           return $data_list;
            }
}