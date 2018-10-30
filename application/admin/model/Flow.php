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

namespace app\admin\model;

use think\Model;

/**
 * 流程类型
 * @package app\admin\model
 */
class Flow extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__ADMIN_FLOW__';

    /**
     * 获取流程列表
     * @param array $map 筛选条件
     * @param array $order 排序
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public static function getList($map=[])
    {
    	$order = ['admin_flow_type.sort'=>'asc','admin_flow.sort'=>'asc'];
        $data_list = self::view('admin_flow', "id,title,doc_no_format,status,sort")
            ->view("admin_flow_type", ['title' => 'type_title'], 'admin_flow.tid=admin_flow_type.id', 'left')
            ->where($map)
            ->order($order)
            ->paginate(20);
        return $data_list;
    }
}