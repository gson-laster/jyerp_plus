<?php
namespace app\administrative\model;
use app\user\model\User as UserModel;
use think\Model;


class Staffwhere extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__ADMINISTRATIVE_STAFFWHERE__';
    //去向列表
    public static function getIndexlist($map=[], $order = []){
    	
    	$data_list = self::view('administrative_staffwhere', 'id,user_name,start_time,end_time,staff_where,is_open')    	
    	->view("admin_organization", 'title', 'admin_organization.id=administrative_staffwhere.oid', 'left')
    	->where($map)
    	->where(function ($query) {
			    $query->where('is_open', 1 )
			    	  ->whereor("locate('-".UID."-',`open_user`)>0")
			          ->whereor("user_id",UID);
			})
    	->order($order)
    	->paginate();
    	return $data_list;
    }

    //去向列表 可管理  所有项
    public static function getList($map = [], $order = [])
    {

    	if(!empty($map['time_in'])){
    		$time_in = $map['time_in'];
			@$map['start_time|end_time']=['between',[strtotime($time_in[1][0]),strtotime($time_in[1][1])]];	
    	}
    	unset($map['time_in']);
    	$data_list = self::view('administrative_staffwhere', 'id,user_name,start_time,end_time,staff_where,is_open')    	
    	->view("admin_organization", 'title', 'admin_organization.id=administrative_staffwhere.oid', 'left')
    	->where($map)
    	->order($order)
    	->paginate();
    	return $data_list;
    }


    //获取单个去向
    public static function getOne($id){
		$one = self::get($id)->toArray();
		if($one['is_open']==0){
			$one['helpid'] = trim(implode(',',explode('-', $one['open_user'])),',');
			if(!empty($one['helpid'])){
				$one['helpname'] = implode(',',UserModel::where('id','in',$one['helpid'])->column('nickname'));
				unset($one['open_user']);
			}
		}
		return $one;
    }
    

}


