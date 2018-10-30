<?php

namespace app\tender\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\tender\model\Factsalary as FSalaryModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use app\tender\model\Obj as ObjModel;
use think\Db;
use app\tender\model\Factsalary as FValidate;
/**
 * 招标控制器
 * @author HJP
 */
class Factsalary extends Admin
{
    //投标类型列表
    public function index()
    {
        // 获取查询条件
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('tender_fact_salary.create_time desc');
        // 数据列表
        $data_list = FSalaryModel::getList($map,$order);

        // 分页数据
        //$page = $data_list->render();
        $task_list = [
            'title' => '查看详情',
            'icon' => 'fa fa-fw fa-eye',
            'href' => url('task_list',['id'=>'__id__'])
        ];
        return ZBuilder::make('table')
            ->addOrder(['code','create_time']) // 添加排序
            ->setSearch(['obj_id' => '所属项目'], '', '', true)
            ->addTimeFilter('tender_fact_salary.create_time')
            ->addColumns([ // 批量添加数据列
                ['obj_id','所属项目'],
                ['s_time','开始时间'],
                ['e_time','结束时间'],
                ['fact','实发工资(元)'],
                ['zdid','制单人'],
                ['create_time','制单时间','date'],
                ['right_button','操作','btn'],
            ])
            ->addTopButtons('add,delete') // 批量添加顶部按钮
            ->addRightButtons('delete')
            //->addRightButton('task_list',$task_list) // 查看右侧按钮
            ->setRowList($data_list) // 设置表格数据
            ->setTableName('tender_fact_salary')
            ->fetch(); // 渲染模板
    }
    //添加项目
    public function add(){
        if($this->request->isPost()){
            $data = $this->request->post();
            //dump($data);
            // 验证
            $result = $this->validate($data, 'Factsalary');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            //查看人员，隔开

            if($model = FSalaryModel::create($data)){
                //记入行为

                $this->success('新增成功！',url('index'));
            }else{
                $this->error('新增失败！');
            }
        }



        $js = <<<EOF
            <script type="text/javascript">
                $(function(){
                   $('#fact').attr('oninput','return Edit1Change();');				   					
                });
				var j=chineseNumber(document.getElementById("fact").value);
				document.getElementById("big_money").value=j;		
				function Edit1Change(){			
					document.getElementById("big_money").value=chineseNumber(document.getElementById("fact").value);
				}
				$('input[name="e_time"]').change(function(){
					fn($(this));
				});
				$('input[name="s_time"]').change(function(){
					fn($(this));
				});
				function fn(o) {
					var e_t = new Date($('input[name="e_time"]').val()).getTime();
					var s_t = new Date($('input[name="s_time"]').val()).getTime();
					if (s_t > e_t) {
						layer.msg('结束日期不得早于开始日期', {time: 3000})
						o.val('')
					}
				}
            </script>
  
EOF;



        return Zbuilder::make('form')
            ->addFormItems([
                ['hidden','zdid',UID],
                ['select:8','obj_id','所属项目','',ObjModel::get_nameid()],
                ['date:6','s_time','开始时间'],
                ['date:6','e_time','结束时间'],
                ['number:6','fact','实发工资(元)'],
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
        $info = FSalaryModel::getOne($id);
        $info->create_time = date('Y-m-d',$info['create_time']);
        return ZBuilder::make('form')
            ->addFormItems([
                ['static:6','obj_id','所属项目'],
                ['static:6','fact','应发工资(元)'],
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
