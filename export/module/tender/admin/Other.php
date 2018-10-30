<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5 0005
 * Time: 17:48
 */

namespace app\tender\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\tender\model\Other as OtherModel;
class Other extends Admin
{
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder();
        // 数据列表
        $array = array();
        $data_list = OtherModel::getList($map,$order);
        foreach($data_list as $key=>$val){
            $array[] = [
                'id'=>$val['id'],
                'name'=>$val['name'],
                'material_money'=>number_format($val['material_money'],2),
                'hire_money'=>number_format($val['hire_money'],2),
                'other_money'=>number_format($val['other_money'],2),
            ];
        }
        //dump($array);die;


        //dump($data_list);die;
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['name' => '项目名称']) // 设置搜索框
            ->addColumns([ // 批量添加数据列)

                ['__INDEX__','序号'],
                ['name','项目名称'],
                ['material_money', '材料付款'],
                ['hire_money','租赁付款'],
                ['other_money','间接费'],
            ])
            ->addTopButtons('back') // 批量添加顶部按钮
            ->setRowList($array) // 设置表格数据
            ->fetch(); // 渲染模板


    }
}