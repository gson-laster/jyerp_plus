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
use think\Db;

/**
 * 流程类型
 * @package app\admin\model
 */
class FlowType extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__ADMIN_FLOW_TYPE__';
    
	public static function getList()
    {
    	$flow_type = array();
    	$list = db::name('admin_flow_type')->field('id,title')->select();
    	foreach ($list as $key => $value) {
    		$flow_type[$value['id']] = $value['title'];
    	}
    	return $flow_type;
    }
}