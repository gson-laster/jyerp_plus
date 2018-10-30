<?php
namespace app\supplier\model;
use app\user\model\User as UserModel;
use think\Model;


class Clientphone extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__SUPPLIER_CLIENTPHONE__';

    //日志列表
    public static function getList($map=[], $order = []){
        $data_list = self::view('supplier_clientphone', true)
            ->view('admin_user', 'nickname', 'admin_user.id=supplier_clientphone.uid', 'left')
            ->view('supplier_client', ['name' => 'sname'], 'supplier_client.id=supplier_clientphone.sid')
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }

    //获取单个日志
    public static function getOne($lid){
        $log = self::view('supplier_clientphone', true)
            ->view('admin_user', 'nickname', 'admin_user.id=supplier_clientphone.uid', 'left')
            ->view('supplier_client', ['name' => 'sname'], 'supplier_client.id=supplier_clientphone.sid')
            ->where(['supplier_clientphone.id'=>$lid])
            ->find();
        return $log;
    }
 
     public static function phoneType(){
        return [1=>'手机',2=>'电话',3=>'微信',4=>'qq',5=>'邮件',6=>'传真'];
     }

     public static function phoneCause(){
        return [1=>'寻找新客户',2=>'老客户跟踪',3=>'电话回访'];
     }
	  public static function exportData($map=[], $order = []){
		$data_list = self::view('supplier_clientphone', true) 
		->view('admin_user', 'nickname', 'admin_user.id=supplier_clientphone.uid', 'left')
        ->view('supplier_client', ['name' => 'sname'], 'supplier_client.id=supplier_clientphone.sid')
		->where($map)
		->where($order)
		->select();
		foreach ($data_list as $key => &$value) {
			switch ($value['cause']) {              
                case 1:
                    $value['is_cause'] = "寻找新客户";
                    break;    
                case 2:
                    $value['is_cause'] = "老客户跟踪";
                    break; 
				case 3:
                    $value['is_cause'] = "电话回访";
                    break;  					
                default:
                    $value['is_cause'] = "暂无";
                    break;
            }
			switch ($value['type']) {              
                case 1:
                    $value['is_type'] = "手机";
                    break;    
                case 2:
                    $value['is_type'] = "电话";
                    break; 
				case 3:
                    $value['is_type'] = "微信";
                    break;  	
				case 4:
                    $value['is_type'] = "qq";
                    break;    
                case 5:
                    $value['is_type'] = "邮件";
                    break; 
				case 6:
                    $value['is_type'] = "传真";
                    break;  					
                default:
                    $value['is_type'] = "暂无";
                    break;
            }
			$value['stime'] = date('Y-m-d',$value['stime']);
		}
	   return $data_list;
	}
}


