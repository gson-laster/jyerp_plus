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
 * 公告类型模型
 * @package app\notice\model
 */
class Cate extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__NOTICE_CATE__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
          
    /**
     * 获取所有公告类型
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getTree()
    {
    	$menus = self::where(['status'=>1])->column('title','id');
    	return $menus;
    }
    
    /**
     * 获取所有公告类型 id
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getId()
    {
    	$menus = implode(',',self::where(['status'=>1,'id'=>['neq',1]])->column('id'));
    	return $menus;
    }
}