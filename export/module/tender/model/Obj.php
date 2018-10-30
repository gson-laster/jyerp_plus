<?php
	//合同
namespace app\tender\model;
use think\Model as ThinkModel;
use think\Db;
class Obj extends ThinkModel
{
	protected $table = '__TENDER_OBJ__';
	protected $autoWriteTimestamp = true;
	//获取项目名称
	public static function get_nameid(){
		$result = array();
		$where['status'] = ['egt',1];
		$nameid = self::where($where)->select();
		foreach($nameid as $v){
			$result[$v['id']] = $v['name'];
		}
		return $result;
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
}
