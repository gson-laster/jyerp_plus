<?php
	
namespace app\sales\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\sales\model\Plan as PlanModel;
use app\user\model\Organization as OrganizationModel;
use app\task\model\Task_detail as Task_detailModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use think\Db;
use app\tender\model\Prebudget as PrebudgetModel;
/**
 * 任务控制器
 * @author HJP
 */
class Index extends Admin
{
	//销售计划列表
	public function index()
	{
		// 获取查询条件
		$map = $this->getMap();
		// 排序
		$order = $this->getOrder('sales_plan.create_time desc');
		// 数据列表
		$data_list = PlanModel::getList($map,$order);
		foreach($data_list as $key => &$value){
			$value['low_money'] = '￥ '.number_format($value['low_money'],2);
			$value['total_money'] = '￥ '.number_format($value['total_money'],2);
		}
		// 分页数据
		$page = $data_list->render();
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-search',
			'href' => url('task_list',['id'=>'__id__'])
		];
		return ZBuilder::make('table')
		->setPageTitle('计划列表')
		->setSearch(['tender_prebudget.item'=>'销售机会','sales_plan.name' => '计划名称'], '', '', true) // 设置搜索参数
		->addFilter() // 添加筛选
		->addTimeFilter('sales_plan.create_time') // 添加时间段筛选
		->addColumns([
			['code',' 编号'],
			['name','计划名称'],
			['item','销售预算'],
			['low_money','参考价格(元)'],
			['total_money','计划总额(元)'],
			['start_time','开始时间','date'],
			['end_time','结束时间','date'],
			['zrid','业务员'],
			['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
			['right_button','操作','btn'],
		])
		->addOrder(['code','end_time','start_time']) // 添加排序
		->addTopButtons(['delete'])//添加顶部按钮
		->addRightButtons(['delete' => ['data-tips' => '删除计划将无法恢复。']])
		->addRightButton('task_list',$task_list,true) // 查看右侧按钮 
		->setRowList($data_list)//设置表格数据
		->fetch();
	}
	//查看
	public function task_list($id = null){
		if($id == null) $this->error('参数错误');
		$info = PlanModel::getOne($id);
        $info->create_time = date('Y-m-d',$info['create_time']);
        $info->start_time = date('Y-m-d',$info['start_time']);
        $info->end_time = date('Y-m-d',$info['end_time']);
		$info['helpname'] = Task_detailModel::get_helpname($info['helpid']);
		$info['low_money'] = '￥'.number_format($info['low_money'],2);
		$info['total_money'] = '￥'.number_format($info['total_money'],2);
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addFormItems([
			['hidden', 'id'],
			['static:4','name','计划名称'],
			['static:4','item','销售预算'],
			['static:4','customer_name','客户名称'],	
			['static:4','phone','客户电话'],	
			['static:4','low_money','参考报价(元)'],
			['static:4','total_money','计划报价(元)'],			
			['static:4','start_time','开始时间'],			
			['static:4','end_time','结束时间'],			
			['static:4','zrid','业务员'],
			['static:4','zdid','制单人'],
			['static:4','create_time','制单时间'],
			['static:4','department','所属部门'],		
			['archives','file','附件'],		
			['static','note','备注'],			
		])
		->setFormData($info)
		->fetch();
	}
	//添加销售计划
	public function add(){
		if($this->request->isPost()){
			$data = $this->request->post();
			// 验证
			$result = $this->validate($data, 'Plan');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			$data['code'] = 'CGXJ'.date('YmdHis',time());
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);
            $data['zdid'] = UID;
			//查看人员，隔开
			$data['helpid'] = ','.$data['helpid'];
			if($model = PlanModel::create($data)){
				flow_detail($data['name'],'sales_plan','sales_plan','sales/index/task_list',$model['id']);
				//记入行为				
				$this->success('新增成功！',url('index'));
			}else{
				$this->error('新增失败！');
			}
		}
		$js = <<<EOF
            <script type="text/javascript">
				$('input[name="end_time"]').change(function(){
					fn($(this));
				});
				$('input[name="start_time"]').change(function(){
					fn($(this));
				});
				function fn(o) {
					var e_t = new Date($('input[name="end_time"]').val()).getTime();
					var s_t = new Date($('input[name="start_time"]').val()).getTime();
					if (s_t > e_t) {
						layer.msg('结束日期不得早于开始日期', {time: 3000})
						o.val('')
					}
				}
				 $(function(){
                   $('#total_money').attr('oninput','return Edit1Change();');				   					
                });
				var j=chineseNumber(document.getElementById("total_money").value);
				document.getElementById("big_money").value=j;		
				function Edit1Change(){			
					document.getElementById("big_money").value=chineseNumber(document.getElementById("total_money").value);
				}
                		
			</script>
EOF;
		return Zbuilder::make('form')
		->addFormItems([
			['hidden','zrid'],
			['hidden','helpid'],
			['hidden','zdid',UID],
			['hidden','department'],
			['text:4','name','计划名称'],
			['select:4','item','销售预算','',PrebudgetModel::getOnesale()],
			['text:4','customer_name','客户名称'],	
			['text:4','phone','客户电话'],			
			['text:4','low_money','参考报价(元)'],
			['number:4','total_money','计划报价(元)'],
			['text:4','big_money','金额大写'],
			['text:4','zrname','业务员','','','','disabled'],
			['text:4','oid','所属部门','','','','disabled'],
			['date:4','start_time','开始时间'],
			['date:4','end_time','结束时间'],		
			['static:4','zdname','制单人','',get_nickname(UID)],
			['static:4','create_time','制单时间','',date('Y-m-d')],			
			['textarea:8','helpname','可查看人员'],	
			['files','file','附件'],		
			['textarea','note','备注'],							
		])
		->setExtraHtml(outhtml2())
		->setExtraJs($js.outjs2())
		->js('chineseNumber,Plan')
		->fetch();
	}
	public function get_Detail($customer_name = ''){
			$data = PlanModel::get_Detail($customer_name);
		return $data;
	}					
	//编辑计划
	public function edit($id = null){
		if($id == null) $this->error('参数错误');
		if($this->request->isPost()){
			$data = $this->request->post();
			// 验证
			$result = $this->validate($data, 'Plan');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			$data['code'] = 'XSJH'.date('YmdHis',time());
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);
            $data['zdid'] = UID;
			if($model = PlanModel::update($data)){
				//记录行为
				
				return $this->success('修改成功',url('index'));
			}else{
				return $this->error('修改失败');
			}
		}
		$info = PlanModel::where('id',$id)->find();
        $info->create = date('Y-m-d',$info['start_time']);
		
		//获取昵称
			$nickname = Task_detailModel::get_nickname();
			$zrid = $info['zrid'];
			$helpid = $info['helpid'];
			$helpmane = Task_detailModel::get_helpname($helpid);
		return ZBuilder::make('form')
		->addFormItems([
			['hidden', 'id'],
			['hidden','zrid'],
			['hidden','helpid'],
			['text:4','name','计划名称'],
			['select:4','type','计划类型','',[1=>'日',2=>'月',3=>'年']],
			['date:4','start_time','开始时间'],
			['date:4','end_time','结束时间'],
			['number:4','low_money','最低金额(元)'],
			['number:4','total_money','计划总额(元)'],	
			['text:4','zrname','业务员','',$nickname[$zrid]],
			['static:4','zdname','制单人','',get_nickname(UID)],
			['static:4','create','制单时间'],
			['select:4','department','所属部门','', OrganizationModel::getMenuTree2()],
			['textarea:8','helpname','可查看人员','',$helpmane],													
			['files','file','附件'],		
			['textarea','note','备注'],
		])
		->setExtraHtml(outhtml2())
		->setExtraJs(outjs2())
		->setFormData($info)
		->fetch();
	}
	//删除计划
	public function delete($record = [])
    {
   		$ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    	// 删除节点
    	if (PlanModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		//$details = '生产任务ID('.$ids.'),操作人ID('.UID.')';
    		//action_log('produce_plan_delete', 'produce_plan', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }

}
