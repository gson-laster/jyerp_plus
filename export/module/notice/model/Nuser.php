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

namespace app\notice\model;

use think\Model as ThinkModel;

/**
 * 人事档案模型
 * @package app\cms\model
 */
class Nuser extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__NOTICE_USER__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
     
    /**
     * 获取档案列表
     * @param array $map 筛选条件
     * @param array $order 排序
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getList($map = [], $order = [])
    {
    	$data_list = self::view('notice_user', true)    	
    	->view("notice_list", ['title','description','info','note','enclosure'], 'notice_user.lid=notice_list.id', 'left')
    	->where($map)
    	->order($order)
    	->paginate();
    	return $data_list;
    }
    
    /**
     * 获取档案
     * @param array $map 筛选条件
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getOne($id = '',$map = [])
    {
    	$data_list = self::view('notice_user', true)
    	->view("notice_list",['title','description','info','note','enclosure'], 'notice_user.lid=notice_list.id', 'left')
    	->where(['notice_user.id'=>$id]) 
    	->where($map)
    	->find();
    	return $data_list;
    }
}