<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/25 0025
 * Time: 14:51
 */

namespace app\finance\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\finance\model\Finance as FinanceModel;
use app\tender\model\Obj as IModel;
use app\user\model\Organization as OrganizationModel;
use app\finance\validate\Finance;
use think\config;
use app\user\model\Position;
/*
 
 * 报销管理*/
class Reimburse extends Admin
    {

    /*
     * 首页
     */
    public function index(){
        $map = $this->getMap();
        $order = $this->getOrder('finance_info.id desc');
        $data_list = FinanceModel::getList($map,$order);
        $task_list = [
						'title' => '查看详情',
						'icon' => 'fa fa-fw fa-eye',
						'href' => url('edit',['id'=>'__id__'])
						];

        //dump($data_list);die;
        //dump($data_list);die;
        //使用ZBuilder构建表格展示数据
        return ZBuilder::make('table')
            ->setSearch(['finance_info.title'=>'报销名称','obj.name'=>'项目'],'','',true) // 设置搜索框
            ->addTimeFilter('finance_info.bx_time') // 添加时间段筛选
            ->addOrder('bx_time,objname') // 添加排序
            ->addFilter(['detitle'=>'admin_organization.title'])
            ->addColumns([ // 批量添加列
                ['number', '报销编号'],
                ['title', '报销名称'],
                ['detitle', '部门'],
                ['unickname', '报销人'],
                ['bx_time', '日期'],
                ['item', '所属项目'],
                ['money', '报销金额'],
                ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                ['right_button','操作']
            ])
            ->setRowList($data_list) // 设置表格数据
         //   ->addTopButton('add') //添加删除按钮
            ->addTopButton('delete') //添加删除按钮
            ->addRightButton('edit',$task_list,true) // 添加授权按钮
            ->addRightButton('delete') //添加删除按钮
            ->setTableName('finance_info')
            ->fetch();





    }

    /*
     * 添加
     */
    public function add(){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            // 验证

//            dump($data);die;
            $result = $this->validate($data, 'receipts');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            $data['number'] = 'FYBX'.date('YmdHis',time());
            if ($res = FinanceModel::create($data)) {
            	flow_detail($data['title'],'finace_reimburse','finance_info','finance/reimburse/edit',$res['id']);
               $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }
        }
        // 使用ZBuilder快速创建表单
        $js = <<<EOF
            <script type="text/javascript">
                $(function(){
                   $('#money').attr('oninput','return Edit1Change();');				   					
                });
				var j=chineseNumber(document.getElementById("money").value);
				document.getElementById("big_money").value=j;		
				function Edit1Change(){			
					document.getElementById("big_money").value=chineseNumber(document.getElementById("money").value);
				}
					
            </script>
EOF;
        return ZBuilder::make('form')
            ->setPageTitle('费用报销')
            ->addFormItems([
            		['hidden','zrid'],
								['hidden:6', 'maker', UID],
                ['date:3','time','日期','',date('Y-m-d')],
                ['text:3','title', '报销名称',''],
                ['select:3','item','所属项目','',IModel::get_nameid()],
                ['select:3','project', '报销科目','',Config::get('apply_subject')],
                ['number:3','money','报销金额'],
                ['text:3', 'big_money','金额大写'],	
                ['select:3','depot','部门','', OrganizationModel::getMenuTree2()],
                ['select:3','work','职位','',Position::getTree()],
                ['text:3','zrname','报销人'],
                ['date:3','bx_time','报销日期'],
                ['number:3', 'sum', '累计报销'],
                ['static:3','makername','填表人','',get_nickname(UID)],
                ['textarea','remark','备注'],
                ['file','file','附件'],

            ])
            ->setExtraHtml(outhtml2())
			      ->setExtraJs($js.outjs2())
						->js('chineseNumber')
			   
            ->fetch();

    }

    /*
     * 修改
     */
    public function edit($id = '')
    {

        if ($id === null) $this->error('缺少参数');

        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $res = FinanceModel::update($data);
            if ($res) {
                $this->success('编辑成功', url('index'));
            } else {
                $this->error('编辑失败');
            }
        }
			

    
        $data_list = FinanceModel::getOne($id);
				//dump($data_list);die;
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('修改报销单')// 设置页面标题
            ->addFormItems([ // 批量添加表单项
                ['hidden:3','id'],
                ['static:3', 'number', '报销单号',''],
                ['static:3','time','日期'],
                ['static:3','title', '报销名称',''],
                ['static:3','oname','所属项目'],
                ['static:3','pname', '报销科目'],
                ['static:3','money','报销金额'],
                ['static:3', "big_money", '大写金额'],
                ['static:3','ortitle','部门'],
                ['static:3','potitle','职位'],
                ['static:3','unickname','报销人'],
                ['static:3','bx_time','报销日期'],
                ['static:3', 'sum', '累计报销'],
                ['static:3','maker','填表人'],
                ['static','remark','备注'],
                ['static','file','附件'],
            ])
            ->hideBtn('submit')
            ->setFormData($data_list[0])// 设置表单数据
            ->fetch();




    }

    public function delete($record = [])
    {
        return $this->setStatus('delete');
    }

}