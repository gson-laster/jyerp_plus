<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/25 0025
 * Time: 17:41
 */

namespace app\finance\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\finance\model\Standby as StandbyModel;
use app\tender\model\Obj as IModel;
use app\finance\validate\Standby as StandbyValidate;
use app\user\model\Organization as OrganizationModel;
use Think\Db;

class standby extends Admin
{
    public function index(){
        $map = $this->getMap();
        $order = $this->getOrder('s.id desc');
        $data_list = StandbyModel::getList($map,$order);
				$data = OrganizationModel::getMenuTree2();
				$task_list = [
						'title' => '查看详情',
						'icon' => 'fa fa-fw fa-eye',
						'href' => url('edit',['id'=>'__id__'])
						];
        //dump($data);die;
        //dump($data_list);die;
        //使用ZBuilder构建表格展示数据
        return ZBuilder::make('table')
            ->setSearch(['nickname'=>'领用人','item'=>'项目'],'','',true) // 设置搜索框
            ->addTimeFilter('s.time') // 添加时间段筛选
            ->addFilter(['depot'=>'admin_organization.title'])
            ->addOrder('number,money,time') // 添加排序
            ->addColumns([ // 批量添加列
                ['number', '领用单号'],
                ['nanickname', '领用人'],
                ['depot', '部门'],
                ['item', '项目'],
                ['money', '金额'],
                ['time', '领用日期'],
                ['case', '用途'],
                ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                ['right_button','操作']
            ])
            ->setRowList($data_list) // 设置表格数据
            ->addTopButton('delete') //添加删除按钮
           ->addRightButton('edit',$task_list,true) // 添加授权按钮
            ->addRightButton('delete') //添加删除按钮
            ->setTableName('standby_info')
            ->fetch();
    }
    public function add(){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            // 验证

            //dump($data);die;
            $result = $this->validate($data, 'Standby');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            $data['number'] = 'BYJ'.date('YmdHis',time());
            if ($res = StandbyModel::create($data)) {
            	
            	flow_detail(StandbyModel::getname($data['zrid']).'的备用金发放','finance_standby','standby_info','finance/standby/edit',$res['id']);            	      	
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
            ->setPageTitle('备用金发放')
            ->addFormItems([
            		['hidden','zrid'],
                ['date:3','time','日期','',date('Y-m-d')],
                ['text:3','zrname', '领用人'],
                ['select:3','part', '领用部门','',OrganizationModel::getMenuTree2()],
                ['number:3','year_money','本年领取金额'],
                ['number:3','money','金额'],
                ['text:3', 'big_money','金额大写'],		
                ['select:3','item','所属项目','',IModel::get_nameid()],
            		['text:3','maker', '经办人','',get_nickname(UID)],
                ['textarea','case','用途'],
                ['textarea','remark','备注'],
                ['file','file','附件'],

            ])
    		->setExtraHtml(outhtml2())
			  ->setExtraJs($js.outjs2())
				->js('chineseNumber')
        ->fetch();

    }

    public function edit($id = '')
    {

       
     
        $data_list = StandbyModel::getOne($id);

        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('详情')// 设置页面标题
            ->addFormItems([ // 批量添加表单项
                ['hidden:3','id'],
                ['static:3', 'number', '报销单号'],
                ['static:3','time','日期','',date('Y-m-d')],
                ['static:3','nanickname', '领用人'],
                ['static:3','detitle', '领用部门'],
                ['static:3','year_money','本年领取金额'],
                ['static:3','money','金额'],
                ['static:3', "big_money", '大写金额'],
                ['static:3','obj_name','所属项目'],
                ['static:3','maker','经办人'],
                ['static','case','用途'],
                ['static','remark','备注'],
                ['static','file','附件'],
            ])
            ->setFormData($data_list[0])// 设置表单数据
            ->hideBtn('submit')
            ->fetch();
    }

    public function delete($record = [])
    {
        return $this->setStatus('delete');
    }

}