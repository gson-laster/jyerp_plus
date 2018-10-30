<?php

namespace app\tender\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\tender\model\Alreadysalary as AModel;
use app\admin\model\Module as ModuleModeL;
use app\admin\model\Access as AccessModel;
use app\tender\model\Obj as ObjModel;
use think\Db;
use app\tender\model\Alreadysalary as AValidate;
use app\tender\model\Salary as SModel;
/**
 * 招标控制器
 * @author HJP
 */
class Alreadysalary extends Admin
{
    //投标类型列表
    public function index()
    {
        // 获取查询条件
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('tender_already_salary.create_time desc');
        // 数据列表
        $data_list = AModel::getList($map,$order);

        // 分页数据

        $task_list = [
            'title' => '查看详情',
            'icon' => 'fa fa-fw fa-eye',
            'href' => url('task_list',['id'=>'__id__'])
        ];
        
     
        return ZBuilder::make('table')
            ->setSearch(['obj_id' => '所属项目'], '', '', true)
            ->addOrder(['create_time']) // 添加排序
            ->addTimeFilter('tender_already_salary.create_time') // 添加时间段筛选
            ->addColumns([
                ['obj_id','所属项目'],
                ['already','应发工资(元)'],
                ['s_time','开始时间'],
                ['e_time','结束时间'],
                ['zdid','制单人'],
                ['create_time','制单时间','date'],
                ['right_button','操作','btn'],
            ])
            ->addRightButtons(['delete' => ['data-tips' => '删除类型将无法恢复。']])
            ->addTopButtons('add,delete')
   
            ->setRowList($data_list)//设置表格数据
            //->addRightButton('task_list',$task_list,true) // 查看右侧按钮
            ->setTableName('tender_already_salary')
            ->fetch();
    }
    //添加项目
    public function add(){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['maker'] = UID;
            $result = $this->validate($data,'Alreadysalary');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            if ($res = AModel::create($data)) {
                //flow_detail(OModel::getSname($data['item']).'项目的付款','finance_other','finance_other','finance/other/edit',$res['id']);
                $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }
        }
        $js = <<<EOF
            <script type="text/javascript">
                $(function(){
                   $('#already').attr('oninput','return Edit1Change();');				   					
                });
				var j=chineseNumber(document.getElementById("already").value);
				document.getElementById("big_money").value=j;		
				function Edit1Change(){			
					document.getElementById("big_money").value=chineseNumber(document.getElementById("already").value);
				}
					
            </script>
EOF;



        return Zbuilder::make('form')
            ->addFormItems([
                ['hidden','zdid',UID],
                ['select:8','obj_id','所属项目','',ObjModel::get_nameid()],
                ['date:6','s_time','开始时间'],
                ['date:6','e_time','结束时间'],
                ['number:6','already','应发工资(元)'],
                ['text:6','big_money','金额大写'],
                ['static:6','zdname','制单人','',get_nickname(UID)],
                ['files','file','附件'],
                ['textarea','note','备注'],
            ])
            ->setExtraJs($js.outjs2())
            ->js('chineseNumber')
            ->fetch();
    }
    //查看
    public function task_list($id = null){
        if($id == null) $this->error('参数错误');
        $info = AModel::getOne($id);
        //dump($info);die;
        return ZBuilder::make('form')
            ->addFormItems([
                ['static:6','obj_id','所属项目'],
                ['static:6','already','应发工资(元)'],
                ['static:6','practical','实发工资(元)'],
                ['static:6','zdid','制单人'],
                ['static:6','create_time','制单时间'],
                ['archives','file','附件'],
                ['static','note','备注'],
            ])
            ->setFormData($info)
            ->fetch();
    }

    //删除计划
    public function delete($ids = null){
        if($ids == null) $this->error('参数错误');
        return $this->setStatus('delete');
    }

}
