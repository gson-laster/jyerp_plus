<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\flow\model;

use think\Model;

/**
 * 模型
 * @package app\admin\model
 */
class Itemdetail extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__FLOW_ITEMDETAIL__';


   
    /**
     * 项目审批列表
     * @param array $map 筛选条件
     * @param array $order 排序
     * @return mixed
     */
    public static function getList($map = [], $order = [])
    {
    	$data_list = self::view('flow_itemdetail', true)    	
    	->view("admin_user", 'nickname', 'admin_user.id=flow_itemdetail.uid', 'left')   
    	->view("admin_itemflow", ['module','title'=>'flow_title'], 'admin_itemflow.id=flow_itemdetail.action_id', 'left')
    	->where($map)
    	->order($order)
    	->paginate();
       foreach ($data_list  as $key => &$value) {
       		$url = url($value['url'],['id'=>$value['trigger_id']]);
            $value['url'] = '<a href="'.$url.'?_pop=1" class="pop" data-toggle="tooltip">点击查看</a>';
        }
    	return $data_list;
    }

    //代办流程  已办流程
    public static function getMyflow($map = [], $order = []){

    	$data_list = self::view('flow_itemdetail_step',['id'=>'lid']) 
    		->view("flow_itemdetail",["id",'title'=>'wtitle','create_time'=>'ctime','update_time'=>'utime','url','trigger_id','step'],'flow_itemdetail.id=flow_itemdetail_step.itemdetail_id','left')
    		->view("admin_itemflow",["title"=>"ftitle",'module'],'admin_itemflow.id=flow_itemdetail.action_id','left')
    		->view("admin_user",["nickname"=>"fnickname"],'admin_user.id=flow_itemdetail.uid','left')
            ->where($map)
            ->order($order)
            ->paginate();
       foreach ($data_list  as $key => &$value) {
       		$url = url($value['url'],['id'=>$value['trigger_id']]);
            $value['url'] = '<a href="'.$url.'?_pop=1" class="pop" data-toggle="tooltip">点击查看</a>';
        }
        return $data_list;
    }


    public static function getOne($id)
    {
        $data_list = self::view('flow_itemdetail', true)        
        ->view("admin_user", 'nickname', 'admin_user.id=flow_itemdetail.uid', 'left')   
        ->view("admin_itemflow", ['module','title'=>'flow_title'], 'admin_itemflow.id=flow_itemdetail.action_id', 'left')
        ->where('flow_itemdetail.id',$id)
        ->find();
        return $data_list;
    }
}