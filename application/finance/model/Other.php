<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28 0028
 * Time: 14:45
 */

namespace app\finance\model;


use think\Model;
use Think\Db;

class Other extends Model
{
    protected $table = '__FINANCE_OTHER__';
    public static function getList($map=[], $order=[]){
            $data_list = self::view('finance_other o')
                ->view('tender_obj obj',['name'=>'objname'],'obj.id=o.item','left')//项目
                ->view('admin_user u',['nickname'=>'unickname'],'u.id=o.payer','left')//支付人
                ->view('admin_organization',['title'=>'ortitle'],'admin_organization.id=o.part','left')//部门
                ->view('supplier_list o1',['name'=>'o1rtitle'],'o1.id=o.supplier','left')//供应商
                ->view('finance_manager m',['accmount'=>'macc'],'m.id=o.account','left')//账号
                ->view('finance_ptype',['name'=>'ptype'],'finance_ptype.id=o.ptype','left')//支付类型
                ->view('finance_pway pw',['name'=>'pwname'],'pw.id=o.pway','left')//支付方式
                ->where($map)
                ->where('o.status',1)
                ->order($order)
                ->paginate();
            return $data_list;
        }       
    public static function getOne($id = ''){
    			 $data_list = self::view('finance_other o')
    			 
    			 		
                ->view('tender_obj obj',['name'=>'objname'],'obj.id=o.item','left')//项目
                ->view('admin_user u',['nickname'=>'unickname'],'u.id=o.payer','left')//支付人
                ->view('admin_organization or',['title'=>'ortitle'],'or.id=o.part','left')//部门
                ->view('supplier_list o1',['name'=>'o1rtitle'],'o1.id=o.supplier','left')//供应商
                ->view('finance_manager m',['accmount'=>'macc'],'m.id=o.account','left')//账号
                ->view('finance_ptype p',['name'=>'pname'],'p.id=o.ptype','left')//支付类型
                ->view('finance_pway pw',['name'=>'pwname'],'pw.id=o.pway','left')//支付方式
               	->view('admin_user',['nickname'=>'usnickname'],'u.id=o.maker','left')
                ->where('o.id',$id)
                ->paginate();
            return $data_list;
          }
    public static function getP(){
    			return Db::name('finance_ptype')->where('status=1')->column('id,name');
    	}
    
    public static function getSname($id){
    			$data = Db::name('tender_obj')->where('id',$id)->value('name');
    			return $data; 
    	}
    	
    	
   
    	
    	
    	
}