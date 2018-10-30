<?php
namespace app\finance\model;

use think\Model as ThinkModel;
use think\Db;

class Finance_accmount extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__FINANCE_ACCMOUNT__';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    /*
     * 
     *
     
     * 账户期初*/
    public static function getList($map = [], $order = []) {
//  	return self::where($map) -> order($order) -> paginate();
    	return self::view('finance_accmount')
    	-> view('admin_user', 'nickname', 'admin_user.id = finance_accmount.operator')
    	-> order($order) -> paginate();
    }
	    /*
     
     * 编辑,添加*/
    public static function white($map = [], $data = []) {
    	if(is_null($map)) {
    		self::create($data);
    	} else {
    		self::where($map) -> update($data);
    	}
    }
//  public function
}
