<?php
namespace app\supplier\model;
use app\user\model\User as UserModel;
use think\Model;


class Supplier extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__SUPPLIER_LIST__';

    //日志列表
    public static function getList($map=[], $order = []){
        $data_list = self::view('supplier_list', true)
            ->view('admin_user', 'nickname', 'admin_user.id=supplier_list.purchas_user', 'left')
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }

    //获取单个日志
    public static function getOne($lid){
        $log = self::view('supplier_list', true)
            ->view('admin_user', 'nickname', 'admin_user.id=supplier_list.purchas_user', 'left')
            ->view('supplier_type', ['name' => 'tname'], 'supplier_type.id=supplier_list.type')
            ->where(['supplier_list.id'=>$lid])
            ->find();
        return $log;
    }
 //获取供应商名称
 	public static function getName(){		
 		$result = self::column('id,name');
 		return $result;
 	}
	//获取供应商电话
	public static function getPhone(){		
 		$result = self::column('id,phone');
 		return $result;
 	}
 	//获取供应商类型
	public static function getType(){
		$result = array();		
 		$getType = self::view('supplier_list',['id','type'])
        ->view('supplier_type', ['name' => 'supplier_type'], 'supplier_type.id=supplier_list.type')
 		->paginate();
 		foreach($getType as $v){
 			$result[$v['id']] = $v['supplier_type'];
 		}
 		return $result;
 	}
    public static function getOBJ(){
        $data_list = self::where('status=1')->column('id,name');
        return $data_list;
    }
    
    
     public static function exportData($map=[], $order = []){
        $data_list = self::view('supplier_list', true)
            ->view('admin_user', 'nickname', 'admin_user.id=supplier_list.purchas_user', 'left')
            ->view('supplier_type', ['name' => 'tname'], 'supplier_type.id=supplier_list.type')
            ->where($map)
            ->order($order)
           	->paginate();
        return $data_list;
    }
    
    
}


