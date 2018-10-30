<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/6 0006
 * Time: 09:12
 */

namespace app\tender\admin;


use app\admin\controller\Admin;
use app\tender\model\Manage as ManageModel;
class Manage extends Admin
{
    public function index(){
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder();
        // 数据列表
        $arr = array();
        $data_list = ManageModel::getList($map,$order);
        foreach($data_list as $key=>$val){
            $array[] = ['id'=>$val['id'],
                'name'=>$val['name'],
                'income'=>number_format($val['income'],2),
                'material_money'=>number_format($val['material_money'],2),
                'hire_money'=>number_format($val['hire_money'],2),
                'other_money'=>number_format($val['other_money'],2),
                'profit'=>number_format($val['income']-$val['material_money']-$val['hire_money']-$val['other_money'],2),
                'per_profit'=>round(($val['income']-$val['material_money']-$val['hire_money']-$val['other_money'])/$val['income']*100,2)
            ];
        }
        //dump($array);die;


        //dump($data_list);die;
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['tender_obj.name' => '项目名称']) // 设置搜索框
            ->addColumns([ // 批量添加数据列)
                ['__INDEX__','序号'],
                ['name','项目名称'],
                ['contract_money', '合同总额'],
                ['income_all', '收入结算金额'],
                ['in_all','收款累计'],
                ['out_contract','支出合同总额'],
                ['out_all', '支出结算总额'],
                ['pay_all', '付款累计'],
                ['contract_diff', '合同差额'],
                ['js_diff', '结算差额'],
                ['diff', '收支差额'],

            ])
            ->addTopButtons('back') // 批量添加顶部按钮
            ->setRowList($array) // 设置表格数据
            ->fetch(); // 渲染模板





    }
}