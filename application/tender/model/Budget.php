<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/30 0030
 * Time: 16:19
 */

namespace app\tender\model;


use think\Model;
use think\Db;

class Budget extends Model
{
    protected $table = '__TENDER_BUDGET__';
    protected $autoWriteTimestamp = true;
	public static function getList($map = [], $order = ''){
		$list = self::view('tender_budget') -> view('tender_obj', 'name', 'tender_budget.obj_id = tender_obj.id', 'left') -> view('admin_user', 'nickname', 'tender_budget.recorded=admin_user.id') -> where($map) -> order($order) -> paginate();
//		foreach($list as $v){
//			$v['file1'] = $v['file1'] == 0 ? '无': get_file_path($v['file1']);
//			$v['file2'] = $v['file2'] == 0 ? '无': get_file_path($v['file2']);
//		}
		return $list;
	}    
	public static function getOne($map = [], $order = '') {
		return $list = self::view('tender_budget') -> view('tender_obj', 'name', 'tender_budget.obj_id = tender_obj.id', 'left') -> where($map) -> order($order) -> find();
	}
	
	public static function get_pre_obj($map) {
		return Db::view('tender_obj') -> view('sales_contract', 'money,document_time,end_time', 'tender_obj.sale=sales_contract.id', 'left') -> where($map) -> paginate();
	}
	public static function get_pre_obj_id($map) {
		return Db::view('tender_obj') -> view('sales_contract', 'money,document_time,end_time', 'tender_obj.sale=sales_contract.id', 'left') -> where($map) -> find();
	}
}