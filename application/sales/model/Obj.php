<?php
	//合同
namespace app\sales\model;
use think\Model as ThinkModel;
use think\Db;
class Obj extends ThinkModel
{
	protected $table = '__TENDER_OBJ__';
	protected $autoWriteTimestamp = true;
	//获取项目名称
	public static function get_nameid($pre_status=""){
	
		$where['status'] = 1;
		$where['pre_status'] = $pre_status ? $pre_status : '';
		if(empty($where['pre_status']))unset($where['pre_status']);
		return self::where($where)->column('id,name');
		// dump(self::where($where)->column('id,name'));die;
	}
	public static function getList($map = [],$order = []){
		$data_list = self::view('tender_obj')
					->view('sales_contract',['name'=>'sale'],'sales_contract.id=tender_obj.sale','left')
					->view('admin_user',['nickname'=>'zrid'],'admin_user.id=tender_obj.zrid','left')
					->view('admin_user a',['nickname'=>'lxid'],'a.id=tender_obj.zrid','left')
					->view("admin_organization", ['title'=>'bmid'], 'admin_organization.id=tender_obj.bmid', 'left')
					->view('tender_type',['name'=>'type'],'tender_type.id=tender_obj.type','left')
					->where($map)
					->order($order)
					->paginate();
		return $data_list;
	}
	public static function getOne($id){
		$data_list = self::view('tender_obj')
					->view('sales_contract',['name'=>'sale'],'sales_contract.id=tender_obj.sale','left')
					->view('admin_user',['nickname'=>'zrid'],'admin_user.id=tender_obj.zrid','left')
					->view('admin_user a',['nickname'=>'lxid'],'a.id=tender_obj.zrid','left')
					->view("admin_organization", ['title'=>'bmid'], 'admin_organization.id=tender_obj.bmid', 'left')
					->view('tender_type',['name'=>'type'],'tender_type.id=tender_obj.type','left')
					->where('tender_obj.id',$id)
					->find();
		return $data_list;
	}
	public static function get_unit(){
		$result = array();
		$where['status'] = ['egt',1];
		$nameid = self::where($where)->select();
		foreach($nameid as $v){
			$result[$v['id']] = $v['unit'];
		}
		return $result;
	}
	public static function get_typeid(){
		$result = array();
		$where['status'] = ['egt',1];
		$nameid = self::where($where)->select();
		foreach($nameid as $v){
			$result[$v['id']] = $v['type'];
		}
		return $result;
	}
	public static function getname($id){
		$data_list  = Db::name('tender_obj')->where('id',$id)->column('id,name');
		return $data_list;
		}
  	public static function getSname($id){
	    $data = Db::name('tender_obj')->where('id',$id)->value('name');
	    return $data; 
	}
	public static function getaname(){
		$data_list  = Db::name('tender_obj')->where('status',1)->column('id,name');
		return $data_list;
		}

/*
 
 * 尾款*/
	public static function balance_payment($map = [], $order = []){
		$list = Db::name('constructionsite_finish')  // 结束项目
		->alias('t')
		->field('t.item as id,b.money,tender_obj.contact,tender_obj.phone,tender_obj.account_status,b.name as contrack_name,tender_obj.name')
		->join('tender_obj','tender_obj.id=t.item','left')  // 项目
		//->join('contract_income d', 'd.attach_item=t.item','left')
		->join('sales_contract b','b.id=tender_obj.sale','left')
		-> where($map)
		-> order($order)
		->group('t.item')
		-> paginate();
		foreach($list as $k => &$value){
			$result = Db::name('finance_gather') -> field('money')-> where(['item_id' => $value['id']]) -> select();
			$total = 0;
			foreach($result as $r) {
				$total += $r['money'];
			}			
			$value['gather'] = $total;
			$value['final_payment'] = $value['money'] - $total;
			$list[$k] = $value;
		}
		return $list;
	}
	public static function balance_payment_id($map = [], $order = []){
		$list = Db::name('constructionsite_finish')  // 结束项目
		->alias('t')
		->field('t.item as id,b.money,tender_obj.contact,tender_obj.phone,b.name as contrack_name,tender_obj.name,t.file')
		->join('tender_obj','tender_obj.id=t.item','left')  // 项目
		//->join('contract_income d', 'd.attach_item=t.item','left')
		->join('sales_contract b','b.id=tender_obj.sale','left')
		-> where($map)
		-> order($order)
		->	group('t.item')
		-> find();
			$result = Db::name('finance_gather') -> field('money')-> where(['item_id' => $list['id']]) -> select();
			$total = 0;
			foreach($result as $r) {
				$total += $r['money'];
			}
			$list['final_payment'] = $list['money'] - $total;
			
			$list['gather'] = $total;
		return $list;
	}
}
