<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/30 0030
 * Time: 16:19
 */

namespace app\finance\model;


use think\Model;
use think\Db;

class Hire extends Model
{
    protected $table = '__TENDER_CONTRACT_HIRE__';
    protected $autoWriteTimestamp = true;
    public static function getList($map = [], $order = [])
    {
        $data_list = self::view('tender_contract_hire')
            ->view("admin_user", ['nickname'=>'authorizedname'], 'admin_user.id=tender_contract_hire.create_uid', 'left')
            ->view("supplier_list", ['name'=>'objname'], 'supplier_list.id=tender_contract_hire.supplier', 'left')//��Ӧ��
            ->view('tender_obj',['name'=>'obname'],'tender_obj.id=tender_contract_hire.obj_id','left')//��Ŀ
            ->where($map)
            ->order($order)
            ->paginate();
        return $data_list;
    }
    public static function getOne($id = '')
    {
        $data_list = self::view('tender_contract_hire h', true)
            ->view("admin_user u", ['nickname'=>'authorizedname'], 'u.id=h.create_uid', 'left')
            ->view('tender_obj o',['name'=>'objname'],'o.id=h.obj_id','left')
            ->view('tender_obj ob',['name'=>'obname'],'ob.id=h.supplier','left')
            ->view('tender_hire t', ['name' => 'hireName'], 'h.plan=t.id')
            ->where(['h.id'=>$id])
            ->find();
        return $data_list;
    }
    //�鿴
    public static function getDetail($map = [])
    {
        $data_list = self::view('tender_contract_hire_detail', true)
            ->view("stock_material", ['name','version','unit'], 'stock_material.id=tender_contract_hire_detail.itemsid', 'left')
            ->where($map)
            ->paginate();
        return $data_list;
    }
    public static function getMaterials($id){
        return db::name('tender_contract_hire_detail')->where('pid',$id)->column('itemsid');
    }
    
    
    
    
    
    
    
    
}