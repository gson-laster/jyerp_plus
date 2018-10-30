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

namespace app\stock\validate;

use think\Validate;

/**
 * 仓库验证器
 * @package app\produce\validate
 * @author 黄远东<64143571@qq.com>
 */
class Produce extends Validate
{
 //定义验证规则
	protected $rule = [
		//'name|入库主题' => 'require',
		//'order_id|生产任务单' => 'require',
		//'mlid|需用明细' => 'require',
		//'zrid|验收人' => 'require',
		//'putinid|入库部门' => 'require',
		//'deliverer|交货人' => 'require',
		'code|入库单号'=>'require',
		'intime|入库时间'=>'require',
		'sid|项目'=>'require',
		'house_id|仓库'=>'require',
		'mid|成品'=>'require',
	];
	// 验证提示
	protected $message = [
		//'obj_id.require' => '请选择生产任务单',
		//'mlid.require' => '请选择需用明细',
		//'putinid.require' => '请选择入库部门',
	];
	
}
