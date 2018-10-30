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

namespace app\stock\model;

use think\Model as ThinkModel;

/**
 * 仓库类型模型
 * @package app\produce\model
 */
class HouseType extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__STOCK_HOUSE_TYPE__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
   
    /**
     * 获取所有仓库类型
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getTree()
    {
    	$menus = self::where(['status'=>1])->column('name','id');
    	return $menus;
    }
}