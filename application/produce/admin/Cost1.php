<?php
namespace app\produce\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;


/**
 * 成本核算
 * @package app\cms\admin
 */
class Cost extends Admin
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