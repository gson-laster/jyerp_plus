<?php
namespace app\supplier\model;
use app\user\model\User as UserModel;
use think\Model;


class Client extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__SUPPLIER_CLIENT__';

    //日志列表
    public static function getList($map=[], $order = []){
        $data_list = self::view('supplier_client', true)
            ->view('supplier_clienttype', ['name' => 'tname'], 'supplier_clienttype.id=supplier_client.type')
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }

    //获取单个日志
    public static function getOne($lid){
        $log = self::view('supplier_client', true)
            ->view('supplier_clienttype', ['name' => 'tname'], 'supplier_clienttype.id=supplier_client.type')
            ->where(['supplier_client.id'=>$lid])
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
 		$getType = self::view('supplier_client',['id','type'])
        ->view('supplier_clienttype', ['name' => 'supplier_clienttype'], 'supplier_clienttype.id=supplier_client.type')
 		->paginate();
 		foreach($getType as $v){
 			$result[$v['id']] = $v['supplier_clienttype'];
 		}
 		return $result;
 	}
    public static function getOBJ(){
        $data_list = self::where('status=1')->column('id,name');
        return $data_list;
    }
    
    public static function get_Detail($id = ''){
		$getDetail = self::view('supplier_client',['phone','type'])
		->view('supplier_clienttype',['name'=>'supplier_type'],'supplier_clienttype.id=supplier_client.type','left')
		->where(['supplier_client.id'=>$id])
		->find();	
 		return $getDetail;
	}
    
    
    
    
    
	 public static function exportData($map=[], $order = []){
		$data_list = self::view('supplier_client', true) 
		->view('supplier_clienttype', ['name' => 'tname'], 'supplier_clienttype.id=supplier_client.type')
		->where($map)
		->order($order)
		->paginate();
		foreach ($data_list as $key => &$value) {
			$value['create_time'] = date('Y-m-d',$value['create_time']);
			$value['stime'] = date('Y-m-d',$value['stime']);
		}
	   return $data_list;
	}
}


