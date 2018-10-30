<?php
namespace app\finance\model;

use think\Model as ThinkModel;
use think\Db;

class Finance_manager extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__FINANCE_MANAGER__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    /*
     
     * 账户信息*/
    public static function getList($map = [], $order = []) {
    	return self::view('finance_manager')
    			-> view('admin_user', 'nickname', 'admin_user.id = finance_manager.operator', 'left')
    	 		-> where($map) -> order($order) -> paginate();
    }
    public static function getAll() {
    	return self::select();
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
}
