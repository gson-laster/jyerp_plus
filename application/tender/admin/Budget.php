<?php
	
namespace app\tender\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\tender\model\Budget as Bmodel;
use think\Db;
use app\tender\model\Obj;
use app\task\model\Task_detail as Task_detailModel;
use app\tender\model\Type as TypeModel;
use app\user\model\Organization as OrganizationModel;
use app\sales\model\Contract;
/**
 * 项目预算控制器
 * @author HJP
 */
class Budget extends Admin
{
	/*
	 
	 * 第一次预算*/
	public function index(){
		
        // 查询
       $map= $this->getMap();
       $order = $this->getOrder('tender_budget.create_time desc');
        // 排序
        // 数据列表
        $data_list = Bmodel::getList($map, $order);
        // 使用ZBuilder快速创建数据表格
        
        $edit = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('edit',['id'=>'__id__'])
		];
       
        return ZBuilder::make('table')
        ->addTimeFilter('tender_budget.create_time') // 添加时间段筛选
        	->setSearch(['title' => '项目预算主题', 'name' => '预算项目']) // 设置搜索参数

            ->addColumns([ // 批量添加数据列)
                ['id','编号'],
                ['title','项目预算主题'],
                ['name','预算项目'],
                ['budget', '预算成本'],
                ['pre_profit', '预计利润'],
                ['start_time', '开始日期'],
                ['end_time', '结束日期'],
                ['obj_time', '预算工期(天)'],
                ['nickname', '预算人'],
				['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
               
                ['create_time','创建时间', 'datetime'],
                ['right_button', '操作', 'btn']
                
            ])
        	->addOrder('tender_budget.id,tender_budget.create_time') // 添加排序
            ->addFilter('tender_budget.status', [0 =>'进行中', 2=>'否决', 1=>'同意']) // 只获取status大于等于10的id字段
			->addRightButton('edit',$edit,true)
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板
            
	}
	
//	粗预算添加

	public function add(){
		if($this -> request -> ispost()) {
			$data = $this -> request -> post();
			$V = $this -> validate($data, 'Budget');
			if(true !== $V) {
				$this -> error($V);
			}
			
			$result = Bmodel::create($data);
			if($result) {

				
				
				//记入行为
				flow_detail($data['title'],'tender_budget','tender_budget','tender/budget/edit',$result['id']);
				//Db::name('sales_opport')->where('id',$data['item'])->setField('status_pre',1);
				//Db::name('tender_prebudget')->where('id',$data['item'])->setField('status_pre',1);
				
				$this -> success('添加成功', 'index');
			} else {
				$this -> error('添加失败');
			}
			
		}
		$js = <<<EOF
            <script type="text/javascript">
                $(function(){
                   $('#budget').attr('oninput','return Edit1Change();');				   					
                });
				var j=chineseNumber(document.getElementById("budget").value);
				document.getElementById("big_budget").value=j;		
				function Edit1Change(){			
					document.getElementById("big_budget").value=chineseNumber(document.getElementById("budget").value);
				}
				
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
						$('input[name="obj_time"]').val('')
						
					}
                      else{
      						var tianshu = (e_t - s_t) / (24 * 60 * 60 * 1000);
      						if(!isNaN(tianshu)){
								$('input[name="obj_time"]').val(tianshu)
							}
                      }
				
				}
            </script>
EOF;
		return Zbuilder::make('form')
			->addFormItems([
				['hidden', 'recorded', UID],
				['text:6','title','项目预算主题'],
				['select:6','obj_id','预算项目', '', Obj::get_nameid(0)],
				['number:6','budget','预算成本'],
				['text:6','big_budget','预算成本大写'],			
				['date:6','start_time','预计开始日期'],			
				['date:6','end_time','预计结束日期'],			
				['number:6','obj_time','预算工期(天)'],			
				['text:6','','预算人', '', get_nickname(UID), '', 'disabled'],			
				['file:12','file1','预算附件'],
				['file:12','file2','生产部图纸附件'],
				['file:12','file3','工程部图纸附件'],
											
			])
			->setExtraJs($js)
			->js('chineseNumber')
			->fetch();
	
	}
	
	public function edit($id = null){
		if(is_null($id)) $this -> error('参数错误');
		$data = Bmodel::getOne(['tender_budget.id' => $id]);
		$data['recorded'] = get_nickname($data['recorded']);
		
		return Zbuilder::make('form')
			->addFormItems([
				['static:6','title','项目预算主题'],
				['static:6','name','预算项目'],
				['static:6','budget','预算成本'],
				['static:6','big_budget','预算成本大写'],			
				['static:6','start_time','开始日期'],			
				['static:6','end_time','结束日期'],			
				['static:6','obj_time','预算工期(天)'],			
				['static:6','recorded','预算人'],			
				['archives:12','file1','预算附件'],
				['archives:12','file2','生产部图纸附件'],
				['archives:12','file3','工程部图纸附件'],
											
			])
		->setFormData($data)
			
		->fetch();
	}
	
	public function pre_obj(){
		// 获取查询条件
		$map = $this->getMap();
		$map['tender_obj.pre_status'] = 0;
		$map['tender_obj.status'] = 1;
		// 数据列表
		$data_list = Bmodel::get_pre_obj($map);     
		//获取昵称
		$nickname = Task_detailModel::get_nickname();
		// 分页数据
		$page = $data_list->render();
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
		return ZBuilder::make('table')
		//->setSearch(['code' => '编号', 'name' => '项目名称'], '', '', true) // 设置搜索参数
		->setPageTitle('预算项目列表')
		->addColumns([
			['code','编号'],
			['name','项目名称'],
			['money','合同金额'],
			['document_time','合同开始日期', 'date'],
			['end_time','合同结束日期', 'date'],
		//	['zrid','责任人','','',$nickname],
			//['tender_time','投标日期','date'],
			['address','项目地址'],
			['unit','建设单位'],
			['contact','联系人'],
			['phone','联系人电话'],
			['right_button','操作','btn'],
		])
		->addOrder(['code']) // 添加排序
		->setRowList($data_list)//设置表格数据
		->addRightButton('task_list',$task_list) // 查看右侧按钮 
		->setTableName('tender_obj')
		->fetch();
	
	
	}
	
	
	public function task_list($id = null){
		if($id == null) $this->error('参数错误');
		
		$get_type = TypeModel::get_type();
		$map = $this->getMap();
		
		$map['tender_obj.id'] = $id;
		// 数据列表
		$info = Bmodel::get_pre_obj_id($map);   
			
		$get_sale = Contract::getCname();
		//dump($id);die;
		
		//获取昵称
			$nickname = Task_detailModel::get_nickname();
			$zrid = $info['zrid'];	
			$lxid = $info['lxid'];				
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addFormItems([
			['hidden', 'id'],
			['text:6','name','项目名称','','','','disabled'],
			['select:4','sale','销售合同','',$get_sale,'','disabled'],
			['text:4','money','合同金额','','','','disabled'],
			['date:4','document_time','合同开始日期','','','','disabled'],
			['date:4','end_time','合同结束日期','','','','disabled'],
			['text:6','address','项目地址','','','','disabled'],
			['textarea','info','项目简介','','','disabled'],
			//['text:4','obj_time','工程工期(天)','','','','disabled'],
			//['text:4','estimate','工程量估算(元)','','','','disabled'],
			//['text:4','cost','工程造价(元)','','','','disabled'],
			['select:4','type','项目类型','',$get_type,'','disabled'],
			['text:4','zrname','项目追踪人','',$nickname[$zrid],'','disabled'],
			['select:4','bmid','所属部门','',OrganizationModel::getMenuTree2(),'','disabled'],						
			
			['text:4','unit','建设单位','','','','disabled'],
			['text:4','contact','联系人','','','','disabled'],
			['number:4','phone','联系电话','','','','','','disabled'],
			['text:4','lxaddrss','联系地址','','','','disabled'],
			['static:4','lxname','立项人','',$nickname[$lxid]],
			['archives','file','附件'],		
			['textarea','note','备注','','','disabled'],		
		])
		->setPageTitle('详情')
		->setFormData($info)
		->fetch();

	}
}
