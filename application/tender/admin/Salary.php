<?php

namespace app\tender\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\tender\model\Salary as SModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use app\tender\model\Obj as ObjModel;
use think\Db;
/**
 * 招标控制器
 * @author HJP
 */
class Salary extends Admin
{
    //投标类型列表
    public function index()
    {
        // 获取查询条件
        $map = $this->getMap();
        // 排序
        // 数据列表
        $data_list = SModel::getList($map);

        // 分页数据
        //$page = $data_list->render();
        $task_list = [
            'title' => '查看详情',
            'icon' => 'fa fa-fw fa-eye',
            'href' => url('task_list',['id'=>'__id__'])
        ];
        return ZBuilder::make('table')
            ->addOrder(['code','create_time']) // 添加排序
            ->hideCheckbox()
            ->addColumns([ // 批量添加数据列
                ['__INDEX__', '编号'],
                ['name', '项目名称'],
                ['alreadys','计划应发总工资（元）','link', url('already_list',['id'=>'__id__']), '_blank', 'pop'],
                ['facts','实际发放总工资（元）','link', url('fact_list',['id'=>'__id__']), '_blank', 'pop'],
            ])
            ->addTopButtons('') // 批量添加顶部按钮
            ->addRightButton('task_list',$task_list,true)
            //->addRightButton('task_list',$task_list) // 查看右侧按钮
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板
    }
    
    
    public function already_list($id){
    	 
  		 //$data_list = array();
    	 $data_list = SModel::getAlready($id);
    	
    	 
    	 
    	 return ZBuilder::make('table')
            ->addOrder(['code','create_time']) // 添加排序
            ->hideCheckbox()
            ->addColumns([ // 批量添加数据列
                ['__INDEX__', '编号'],
                ['s_time','开始时间'],
                ['e_time','结束时间'],
                ['already','计划应发工资（元）'],
            ])         
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板
    	}
    	
    	
    	
    public function fact_list($id){
   	   //$data_list = array();
    	 $data_list = SModel::getFact($id);   	 
    	 return ZBuilder::make('table')
            ->addOrder(['code','create_time']) // 添加排序
            ->hideCheckbox()
            ->addColumns([ // 批量添加数据列
                ['__INDEX__', '编号'],
                ['s_time','开始时间'],
                ['e_time','结束时间'],
                ['fact','实际发放工资（元）'],
            ])         
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板
    	
    	
    	
    	}
    
    
  
 
 
 
 
 
 
 
 
 
 
 
    //删除计划
    public function delete($ids = null){
        if($ids == null) $this->error('参数错误');
        return $this->setStatus('delete');
    }

}
