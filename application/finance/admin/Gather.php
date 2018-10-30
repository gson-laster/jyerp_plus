<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/29 0029
 * Time: 10:44
 */

namespace app\finance\admin;


use app\admin\controller\Admin;
use think\Db;
use app\common\builder\ZBuilder;
use app\finance\validate\Gather as GatherValidate;
use app\finance\model\Gather as GModel;
use app\tender\model\Obj as IModel;
use app\finance\model\User as UModel;
use app\finance\model\Bank as BModel;
use app\supplier\model\Supplier;
use app\contract\model\Hire;
use think\config;
use app\contract\model\Income;
use app\finance\model\Income as IncomeModel;
class Gather extends Admin
{
    public function index()
    {
        $map = $this->getMap();
        $order = $this->getOrder('finance_gather.id desc');
        $data_list = GModel::getList($map, $order);       
   			
   			
   			$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('edit',['id'=>'__id__'])
		];
        return ZBuilder::make('table')
            ->setSearch(['finance_gather.number' => '收款编号', 'finance_gather.name' => '收款人'], '', '', true)// 设置搜索框
            ->addTimeFilter('finance_gather.date')// 添加时间段筛选
            ->addOrder('number,date,uname,maker,money,sname,maccount')// 添加排序
            ->addFilter('gtype',[1=>'工程收款',2=>'销售收款',3=>'其他收款'])
            ->addColumns([ // 批量添加列
                ['number','收款编号'],
                ['date','收款日期','date'],
                ['name','收款人'],
                ['maker','录入人'],
                ['money','收款金额'],
                ['gtype','收款类型',[1=>'工程收款',2=>'销售收款',3=>'其他收款']],
                ['maccount', '公司账户'],
                ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                ['right_button', '操作']
            ])
            ->setExtraHtml()
            ->setRowList($data_list)// 设置表格数据
            ->addTopButton('add')//添加删除按钮
            ->addTopButton('delete')//添加删除按钮
            ->addRightButton('delete')//添加删除按钮
						->addRightButton('edit',$task_list,true)
      
            ->setTableName('finance_gather')
            ->fetch();


    }

    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            // 验证
      			//dump($data['pact']);die;
      			
            $result = $this->validate($data, 'Gather');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            $data['number'] = 'SKD'.date('YmdHis',time());
            $data['date'] = strtotime($data['date']);
            $data['item_id'] = IncomeModel::where(['id' => $data['pact']]) -> field('attach_item') -> find()['attach_item'];
            if ($res = GModel::create($data)) {
            	
            	//Income::getname($data['pact'])
            	//dump();die;
            	flow_detail(Income::getname($data['pact']).'的收款','finance_gather','finance_gather','finance/gather/edit',$res['id']);
            	
            	
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
            ->setPageTitle('收款单')
            ->addFormItems([
                ['date:3','date','日期','',date('Y-m-d')],
                ['select:4','pact','合同名称','',Income::name('contract_income')->where('id','>',0)->column('id,title')],
                ['text:4','name' ,'收款人'],
                ['number:2','money','收款金额'],
                ['text:3', 'big_money','金额大写'],					
                ['select:3','gtype','收款类型','',[1=>'工程收款',2=>'销售收款',3=>'其他收款']],
                ['select:3','supplier','供应商','',Supplier::getOBJ()],
                ['select:3','account','公司账户','',BModel::where('id','>','0')->column('id,name')],
                ['text:3','maker','录入人','',get_nickname(UID)],
                ['textarea','remark','备注'],
                ['file','file','附件'],
            ])
            ->setExtraHtml(outhtml2())
            ->setExtraJs($js.outjs2())
						->js('chineseNumber')
            ->fetch();
    }
    
    public function edit($id=''){	
    	$data_list = GModel::getOne($id);
    	$arr = [1=>'工程收款',2=>'销售收款',3=>'其他收款'];
    	$data_list['gtype'] = $arr[$data_list['gtype']];
        $data_list['date'] = date('Y-m-d',$data_list['date']);
    	return ZBuilder::make('form')
		  ->addFormItems([
		// 批量添加表单项
			['static:3', 'date', '日期'],
			['static:3', 'number', '付款编号'],
			['static:3', 'pact','合同名称'],
			['static:3', 'name', '收款人'],
			['static:3', 'money','收款金额'],
			['static:3', "big_money", '大写金额'],
			['static:3', 'sname','供应商'],
			['static:3', 'gtype','收款类型','',$arr],					
			['static:3', 'maccount','公司账户'],			
			['static:3', 'maker', '经办人'],			
			['static:3', 'remark','备注'],			
			['static:3', 'file','附件']	
		])
		-> setPageTitle('详情')
		->hideBtn('submit')
		->setFormData($data_list)
		->fetch();
    	
    	
    	}
}