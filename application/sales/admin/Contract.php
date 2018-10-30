<?php
	
namespace app\sales\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\sales\model\Opport as OpportModel;
use app\sales\model\Offer as OfferModel;
use app\sales\model\Plan as PlanModel;
use app\sales\model\Contract as ContractModel;
use app\user\model\Organization as OrganizationModel;
use app\task\model\Task_detail as Task_detailModel;
use app\admin\model\Module as ModuleModel;
use app\supplier\model\Client as ClientModel;
use app\supplier\model\Clienttype as ClienttypeModel;
use app\supplier\model\Clientphone as ClientphoneModel;
use app\admin\model\Access as AccessModel;
use think\Db;
/**
 * 任务控制器
 * @author HJP
 */
class Contract extends Admin
{	
	//销售机会
	public function index(){
		// 获取查询条件
		$map = $this->getMap();
		// 排序
		$order = $this->getOrder('sales_contract.create_time desc');
		// 数据列表
		$data_list = ContractModel::getList($map,$order);
		foreach($data_list as $key => &$value){
			$value['money'] = '￥ '.number_format($value['money'],2);
		}
		// 分页数据
		$page = $data_list->render();
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-search',
			'href' => url('task_list',['id'=>'__id__'])
		];
		return ZBuilder::make('table')
		->setPageTitle('合同列表')
		->setSearch(['sales_contract.name' => '合同名称','customer_name'=>'客户名称'], '', '', true)
		->addOrder(['code','document_time']) // 添加排序
		->addColumns([
			['code','编号'],
			['name','合同名称'],
			['customer_name','客户名称'],
			['phone','客户联系方式'],		
			['money','合同金额'],	
			['document_time','开始时间','date'],
			['adderss','签约地点'],
			['zrid','业务员'],	
			['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],			
			['right_button','操作','btn'],
		])
		->addOrder(['code','document_time']) // 添加排序
		->addTopButtons(['delete'])//添加顶部按钮
		->addRightButtons(['delete' => ['data-tips' => '删除报价将无法恢复。']])
		->addRightButton('task_list',$task_list,true) // 查看右侧按钮 
		->setRowList($data_list)//设置表格数据
		->fetch();
	}
	//添加销售机会
	public function add(){
		if($this->request->isPost()){
			$data = $this->request->post();
			// 验证
			$result = $this->validate($data, 'Contract');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			$data['code'] = 'XSHT'.date('YmdHis',time());
            $data['document_time'] = strtotime($data['document_time']);
            $data['end_time'] = strtotime($data['end_time']);
            $data['zdname'] = UID;

            //dump($data);die;
			if($model = ContractModel::create($data)){
				flow_detail($data['name'],'sales_contract','sales_contract','sales/contract/task_list',$model['id']);
				//记入行为
				message_log('sales_add',1,'1,3','admin/messageaction/index','昌隆钢构');
				$this->success('新增成功！',url('index'));
			}else{
				$this->error('新增失败！');
			}
		}				
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
				$('input[name="end_time"]').change(function(){
					if (new Date($(this).val()).getTime() < new Date($('input[name="document_time"]').val()).getTime()) {
						layer.msg('结束日期不得早于开始日期', {time: 2500})
						$(this).val('')
					}
				});
				$('input[name="document_time"]').change(function(){
					if (new Date($(this).val()).getTime() > new Date($('input[name="end_time"]').val()).getTime()) {
						layer.msg('开始日期不得晚于结束日期', {time: 2500})
						$(this).val('')
					}
				});		
            </script>
EOF;
		
		$date = date('Y-m-d');
		if(UID == 1){
			$helpuid = ','.UID.',';
		}else{
			$helpuid = ','.'1'.','.UID.',';
		}		
		return Zbuilder::make('form')
		->addFormItems([
			['hidden','zrid'],
			['hidden','oid'],
			['hidden','zdid',UID],
			['hidden','helpid',$helpuid],
			['text:4','name','合同名称'],
			//['select:4','monophycode','销售计划单','',PlanModel::getName()],
			['select:4','customer_name','客户名称','',ClientModel::getName()],
			['text:4','phone','客户电话'],
			['select:4','paytype','支付方式','',[-2=>'转账',-1=>'支付宝',0=>'微信',1=>'支票',2=>'现金']],
			['select:4','goodtype','交货方式','',[0=>'一次性交货',1=>'分批交货']],			
			['select:4','currency','币种','',[-1=>'美元',0=>'人民币',1=>'欧元']],
			['number:4','parities','汇率%'],
			['number:4','money','合同金额'],
			['text:4','big_money','金额大写'],
			['text:4','zrname','业务员'],
			['date:4','document_time','开始日期','',$date],
			['text:4','adderss','签约地址'],	
			['date:4','end_time','截止日期'],					
			['static:4','zdname','制单人','',get_nickname(UID)],	
			['static:4','create_time','制单时间','',$date],	

			['textarea:6','helpname','可查看人员(不填只自己和超级管理员可见)'],
			['files','file','附件'],
			['textarea','note','备注'],						
		])
		->setExtraHtml(outhtml2())
		->setExtraJs($js.outjs2())
		->js('chineseNumber,Contract,Opport')
		->fetch();
	}
	public function get_Detail($customer_name = ''){
	$data = ContractModel::get_Detail($customer_name);		
		return $data;
	}	
	public function edit($id = null){
		if($id == null) $this->error('参数错误');
		if($this->request->isPost()){
			$data = $this->request->post();
			// 验证
			$result = $this->validate($data, 'Contract');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			if($model = ContractModel::update($data)){
				//记录行为				
				return $this->success('修改成功',url('index'));
			}else{
				return $this->error('修改失败');
			}
		}
		$info = ContractModel::where('id',$id)->find();
		//获取昵称
			$nickname = Task_detailModel::get_nickname();
			$zrid = $info['zrid'];
			$helpid = $info['helpid'];
			$helpmane = Task_detailModel::get_helpname($helpid);
			$customer_name = OfferModel::customer_name();
		$phone = OfferModel::get_phone();
		$date = date('Y-m-d');
		return ZBuilder::make('form')
		->addFormItems([
			['hidden', 'id'],
			['hidden','zrid'],
			['hidden','helpid'],
			['text:4','name','合同名称'],
			['select:4','monophyletic','合同来源','',OfferModel::get_monophyletic()],
			['text:4','customer_name','客户名称'],
			['select:4','customer_name1','客户名称1','',$customer_name,'','','hidden'],
			['number:4','phone','客户联系方式'],
			['select:4','phone1','客户联系方式1','',$phone,'','','hidden'],
			['select:4','paytype','支付方式','',[-1=>'支付宝',0=>'微信',1=>'银行卡']],
			['select:4','goodtype','交货方式','',GoodModel::get_good()],
			['select:4','transport','运送方式','',TransportModel::get_Transport()],
			['date:4','document_time','开始日期','',$date],
			['text:4','zrname','业务员','',$nickname[$zrid]],
			['number:4','money','总金额'],
			['number:4','tax','总税'],
			['select:4','document_type','合同状态','',[-1=>'执行中',0=>'手工结单',1=>'制单',2=>'终止']],
			['select:4','department','所属部门','', OrganizationModel::getMenuTree2()],		
			['date:4','end_time','截止日期'],				
			['static:4','zdname','制单人','',get_nickname(UID)],	
			['static:4','create_time','制单时间','',$date],	
			['files','file','附件'],				
			['textarea','note','备注'],
			['radio','status','状态', '', ['禁用', '启用'], 1],	
		])
		->setExtraHtml(outhtml2())
		->setExtraJs(outjs2())
		->setFormData($info)
		->js('test')
		->fetch();
	}
	public function delete($record = [])
    {
   		$ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    	// 删除节点
    	if (ContractModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		//$details = '生产任务ID('.$ids.'),操作人ID('.UID.')';
    		//action_log('produce_plan_delete', 'produce_plan', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }

    //查看
	public function task_list($id = null){
		if($id == null) $this->error('参数错误');
		$info = ContractModel::getOne($id);
        $info->document_time = date('Y-m-d',$info['document_time']);
        $info->create_time = date('Y-m-d',$info['create_time']);
        $info->end_time = date('Y-m-d',$info['end_time']);
		$info->helpid = get_helpname($info['helpid']);
		$info['money'] = '￥'.number_format($info['money'],2);
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addFormItems([
			['hidden', 'id'],
			['static:4','name','合同名称'],

			['static:4','customer_name','客户名称'],
			['static:4','phone','客户联系方式(手机)'],
			['select:4','paytype','支付方式','',[-2=>'转账',-1=>'支付宝',0=>'微信',1=>'支票',2=>'现金']],
			['select:4','goodtype','交货方式','',[0=>'一次性交货',1=>'分批交货']],

			['select:4','currency','币种','',[-1=>'美元',0=>'人民币',1=>'欧元']],
			['static:4','parities','汇率%'],
			['static:4','money','合同金额'],
			['static:4','zrid','业务员'],

			['static:4','document_time','开始日期'],
			['static:4','adderss','签约地址'],	
			['static:4','end_time','截止日期'],					
			['static:4','zrid','制单人'],	
			['static:4','create_time','制单时间'],	
			['static','helpid','可查看人员'],
		])
		->setFormData($info)
		->fetch();
	}
}
