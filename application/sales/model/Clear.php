<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/30 0030
 * Time: 15:02
 */

namespace app\sales\model;
use think\Model as ThinkModel;

use think\Model;
use think\Db;
class Clear extends Model
{
		//项目竣工结算
    public static function getList($map = '', $order = ''){
        $data_list = self::view('constructionsite_finish')
            ->view('tender_obj',['id'=>'obj_id','name'=>'item'],'tender_obj.id=constructionsite_finish.item','left')//竣工项目	
            ->view('admin_user',['nickname'=>'maker'],'admin_user.id=constructionsite_finish.maker','left')//提交人
			->view('sales_contract',['money'],'sales_contract.id=tender_obj.sale')
            ->where('constructionsite_finish.status',1)
            ->where($map)
            ->order($order)
            ->paginate();
			
			foreach($data_list as $k => &$value){
			$result = Db::name('finance_gather') -> field('money')-> where(['item_id' => $value['obj_id']]) -> select();
			$total = 0;
			foreach($result as $r) {
				$total += $r['money'];
			}			
			$value['gather'] = $total;
			$value['final_payment'] = $value['money'] - $total;
			$data_list[$k] = $value;
		}
        return $data_list;
    }
}