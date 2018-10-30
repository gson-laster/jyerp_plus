<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5 0005
 * Time: 17:37
 */

namespace app\tender\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\tender\model\Costall as CostAllModel;
use plugins\chart\chart;
use think\Hook;
class Costall extends Admin
{
    public function index()
    {
        // 查询
       $map= $this->getMap();
        // 排序
        // 数据列表
        $array = array();
        $data_list = CostAllModel::getList($map);
        // 使用ZBuilder快速创建数据表格
       
        return ZBuilder::make('table')
            ->setSearch(['name' => '项目名称']) // 设置搜索框
            ->addColumns([ // 批量添加数据列)
                ['__INDEX__','序号'],
                ['name','项目名称'],
                ['materials_sum', '材料付款 （元）'],
                ['hires_sum','租赁付款 （元）'],
                ['others_sum','间接费 （元）'],
                ['facts_sum','工资 （元）'],
            ])
            ->addTopButtons('back') // 批量添加顶部按钮
            ->setRowList($data_list) // 设置表格数据
            ->hideCheckbox()
            ->fetch(); // 渲染模板
    }


    public function show(){
        hook('tender_cost_show');
    }
}