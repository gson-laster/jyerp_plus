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

class Hire extends Model
{
    protected $table = '__TENDER_HIRE__';
    protected $autoWriteTimestamp = true;
    public static function getList($map = [], $order = [])
    {
        $data_list = self::view('tender_hire',true)
            ->view("admin_user", ['nickname'=>'authorizedname'], 'admin_user.id=tender_hire.authorized', 'left')
            ->view('tender_obj',['name'=>'obj_id'],'tender_obj.id=tender_hire.obj_id','left')
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }
    public static function getOne($id = '')
    {
        $data_list = self::view('tender_hire h', true)
            ->view("admin_user u", ['nickname'=>'authorized'], 'u.id=h.authorized', 'left')
            ->view('tender_obj o',['name'=>'obj_id'],'o.id=h.obj_id','left')
            ->where(['h.id'=>$id])
            ->find();
        return $data_list;
    }
    //查看
    public static function getDetail($map = [])
    {
        $data_list = self::view('tender_hire_detail ', true)
            ->view("stock_material", ['name','version','unit'], 'stock_material.id=tender_hire_detail.itemsid', 'left')
            ->where($map)
            ->paginate();
        return $data_list;
    }
    public static function getMaterials($id){
        return db::name('tender_hire_detail')->where('pid',$id)->column('itemsid');
    }
    
    public static function getPlan(){
    	return	$data_list = Db::name('tender_hire')->column('id,name');
    	}
    	
    public static function HireList(){
    	$data_list = Db::name('tender_hire')->where('id>0')->column('id,name');
    	return $data_list;
    	}
    	
    
}