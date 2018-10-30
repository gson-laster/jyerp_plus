<?php
namespace app\constructionsite\model;
use app\user\model\User as UserModel;
use think\Model;
use think\Db;


class Finish extends Model
{
    // 设置当前模型对应的完整数据表名称
     
    protected $autoWriteTimestamp = true;
    protected $table = '__CONSTRUCTIONSITE_FINISH__';

    //日志列表
    public static function getList($map=[], $order = []){
        $data_list = self::view('constructionsite_finish', true)
            ->view('tender_obj', ['name'=>'item'], 'tender_obj.id=constructionsite_finish.item', 'left')//完工项目
            ->view('admin_user', ['nickname'=>'maker'], 'admin_user.id=constructionsite_finish.maker', 'left')//申请人
            ->where($map)
            ->order($order)
            ->paginate();
            //DUMP($data_list);die;
        return $data_list;
    }

    public static function getName($id){
    	$data_list = Db::name('tender_obj')->where('id',$id)->value('name');
    	return $data_list;
    	
    	}
    public static function getItem(){
    	
    	$data_list = Db::name('tender_obj')->where('status',1)->column('id,name');
      //dump($data_list);die;
    	return $data_list;
    	
    	}
    	
    public static function getOne($id){
    	//dump($id);die;
    	 $data_list = self::view('constructionsite_finish', true)
            ->view('tender_obj', ['name'=>'item'], 'tender_obj.id=constructionsite_finish.item', 'left')//完工项目
            ->view('admin_user', ['nickname'=>'maker'], 'admin_user.id=constructionsite_finish.maker', 'left')//申请人
            ->where('constructionsite_finish.id',$id)
            ->find();
            //DUMP($data_list);die;
        return $data_list;
    	
    	
    	}
 

}


