<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/1 0001
 * Time: 14:15
 */

namespace app\finance\model;

use think\db;
use think\Model;

class Income extends Model
{
    protected $table = '__CONTRACT_INCOME__';
    protected $autoWriteTimestamp = true;
    public static function getList($map = [], $order = [])
    {
        $data_list = self::view('contract_income',true)
            ->view("tender_obj", ['name'=>'sname'], 'tender_obj.id=contract_income.attach_item', 'left')//所属项目
            -> view('admin_user', ['nickname'], 'contract_income.operator=admin_user.id', 'left') // 签订人
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }
//  public static function getOne($id = '')
//  {
//      $data_list = self::view('contract_income h', true)
//          ->view("admin_user u", ['nickname'=>'authorizedname'], 'u.id=h.operator', 'left')
//          ->view('tender_obj o',['name'=>'objname'],'o.id=h.attach_item','left')
//          ->view('tender_obj ob',['name'=>'obname'],'ob.id=h.supplier','left')
//          ->where(['h.id'=>$id])
//          ->find();
//      return $data_list;
//  }
public static function getOne($id = '')
    {
        $data_list = self::view('contract_income c', true)
            ->view("admin_user u", ['nickname'=>'authorizedname'], 'u.id=c.operator','left')//签订人
            ->view('tender_obj o',['name'=>'objname'],'o.id=c.attach_item','left')//项目
          
            ->where('c.id',$id)
            ->find();
        return $data_list;
    }
    //查看
    public static function getDetail($map = [])
    {
        $data_list = self::view('contract_income_detail', true)
            ->view("stock_material", ['name','version','unit'], 'stock_material.id=contract_hire_detail.itemsid', 'left')
            ->where($map)
            ->paginate();
        return $data_list;
    }
    public static function getMaterials($id){
        return db::name('contract_income_detail')->where('pid',$id)->column('itemsid');
    }
    
    public static function getname($id){
    	
    	$data_list = Db::name('contract_income')->where('id',$id)->value('title');
    	//dump($data_list);die;
    	return $data_list;
    	}
    
 
}