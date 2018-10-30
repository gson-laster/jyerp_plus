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

namespace app\stock\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;


/**
 * 奖惩控制器
 * @package app\cms\admin
 */
class Instock extends Admin
{ 
    public function index()
    {
    	return ZBuilder::make('table')
    	->addColumns([ // 批量添加数据列
    			['id', 'ID'],
    			['right_button', '操作', 'btn']
    	])
    	->setRowList() // 设置表格数据
    	->fetch(); // 渲染模板
    }    
}