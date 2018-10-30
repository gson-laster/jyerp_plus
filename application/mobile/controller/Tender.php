<?php
namespace app\mobile\controller;
use app\sales\model\Contract as ContractModel;
use app\tender\model\Materials as MaterialsModel;
use app\tender\model\Obj as ObjModel;
use app\tender\model\Type as TypeModel;
use app\user\model\User;
use app\user\model\Organization as OrganizationModel;
use app\tender\model\Clear as ClearModel;
use app\tender\model\Produceplan as ProduceplanModel;
use app\tender\model\Factpic as FactpicModel;
use think\Db;

/*
 
 * 联系人控制器*/
class Tender extends Base{
	
	//合同列表
	public function tender($keywords = '') {

		if($this -> request -> isAjax()){
			$lists = ObjModel::where("locate('".$keywords."',`name`)>0")->order('create_time desc')->paginate(config('mobilePage'),false,['query' => request()->param()]);
			$data_list = [];
			foreach ($lists as $key => $value) {
				$data_list[$key] = [
					'url'	=>	url('tenderdetail',['id'=>$value['id']]),
					'top'	=>	'制单时间：'.date('Y-m-d',$value['create_time']),
					'left'	=>	$value['name'],
					'right'	=>	'',
					'bottom'=>	'责任人：'.get_nickname($value['zrid'])
				];
			}
			return $data_list; 
		}
		return $this->fetch('apply/lists');
		
	}
	//合同详情
	public function tenderdetail($id='') {

		if($id==null)$this->error('没有此项目');

		$get_type = TypeModel::get_type();
		$info = ObjModel::where('id',$id)->find();
		$info->sale = ContractModel::where('id',$info->sale)->value('name');
		
		$data_list = detaillist([
						['name','项目名称'],
						['sale','销售合同'],
						['address','项目地址'],
						['type','项目类型',$get_type],
						['zrid','项目追踪人','user'],
						['bmid','所属部门',OrganizationModel::column('id,title')],						
						['unit','建设单位'],
						['contact','联系人'],
						['phone','联系电话'],
						['lxaddrss','联系地址'],
						['lxid','立项人','user'],
						['info','项目简介'],
						['note','备注'],	
					],$info);

		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details');
	}
	
	//合同列表
	public function Contract($keywords = ''){
			if($this -> request -> isAjax()){
				$lists = ContractModel::where('locate("'.$keywords.'", `name`)')->order('create_time desc')->paginate(config('mobilePage'),false,['query' => request()->param()]);
				$data_list = [];
				foreach ($lists as $key => $value) {
				$data_list[$key] = [
					'url'	=>	url('Contractdetail',['id'=>$value['id']]),
					'top'	=>	'制单时间：'.date('Y-m-d',$value['create_time']),
					'left'	=>	$value['name'],
					'right'	=>	'',
					'bottom'=>	'签订人：'.get_nickname($value['zdid'])
				];
			}
			return $data_list;
			}
		return $this->fetch('apply/lists');
		
		
	}
	
	//合同详情	
	public function ContractDetail($id = ''){
		if($id==null)$this->error('没有此合同');
		$info = ContractModel::getOne($id);
		$info->wu = '<a href="javascript:;" id="examine">点击查看</a>';
		$data_list = detaillist([
				['code','合同编号'],
				['name','合同名称'],
				['zrid','业务员','user'],
				['customer_name','客户名称'],
				['phone','客户手机'],
				//['money','合同金额','money'],
				['document_time','开始时间','date'],
				['end_time','结束时间','date'],
				['create_time','制单时间','date'],
				['wu','查看详情']
			],$info);

		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details', ['url' => url('contract_tech', ['id' => $id])]);
	}

	public function contract_tech($id = null){
			if($id == null) $this->error('没有此清单');
			$title = [
				//'xj' => '小计',
				'xysl' => '需用数量',
				//'ckjg' => '参考价格',
				'bz' => '备注',
				'name' => '物品名称',
				'version' => '规格',
				'unit' => '单位'
			];
			$list = Db::name('tender_materials_detail') -> alias('t') -> field('s.name,s.version,s.unit,t.xysl,t.bz') -> join('stock_material s', 't.itemsid=s.id', 'left') -> where(['pid' => $id]) -> select();
			$data = [
				'title' => $title,
				'list' => $list,
				'name' => 'name'
			];
			return $data;
		}




		
	//备料单列表
	public function Materials($keywords = ''){
		
		if($this -> request -> isAjax()){
			
			$lists = MaterialsModel::where('locate("'.$keywords.'",`name`)>0')->order('create_time desc')->paginate(config('mobilePage'),false,['query' => request()->param()]);
			$data_list = [];
				foreach ($lists as $key => $value) {
				$data_list[$key] = [
					'url'	=>	url('materialsdetail',['id'=>$value['id']]),
					'top'	=>	'制单时间：'.date('Y-m-d',$value['create_time']),
					'left'	=>	$value['name'],
					'right'	=> '审核状态：'.get_status($value['status']),
					'bottom'=>	'制单人：'.get_nickname($value['authorized'])
				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');
		
		
		
		
	}

	//备料单详情
	public function materialsdetail($id=''){
		if($id==null)$this->error('没有此合同');
		$info = MaterialsModel::getOne($id);
		$info->wu = '<a href="javascript:;" id="examine">点击查看</a>';
		
		$data_list = detaillist([
				['code','单据编号'],
				['name','单据名称'],
				['obj_id','项目'],
				['authorizedname','制单人','user'],
				//['money','合同金额','money'],
				['create_time','制单时间','date'],
				['wu','物资清单'],
				
			],$info);

			$this->assign('data_list',$data_list);
			return $this->fetch('apply/details', ['url' => url('materials_tech', ['id' => $id])]);
		}
		
		//备料单的物资清单
		
		public function materials_tech($id = null){
			if($id == null) $this->error('没有此清单');
			$title = [
				//'xj' => '小计',
				'xysl' => '需用数量',
				//'ckjg' => '参考价格',
				'bz' => '备注',
				'name' => '物品名称',
				'version' => '规格',
				'unit' => '单位'
			];
			$list = Db::name('tender_materials_detail') -> alias('t') -> field('s.name,s.version,s.unit,t.xysl,t.bz') -> join('stock_material s', 't.itemsid=s.id', 'left') -> where(['pid' => $id]) -> select();
			$data = [
				'title' => $title,
				'list' => $list,
				'name' => 'name'
			];
			return $data;
		}
		
		//材料结算单
		public function clear($keywords = ''){
			if($this -> request -> isAjax()) {
				
				$lists = ClearModel::where('locate("'.$keywords.'", `name`)>0')->order('create_time')->paginate();
				$data_list = [];
				foreach ($lists as $key => $value) {
				$data_list[$key] = [
					'url'	=>	url('cleardetail',['id'=>$value['id']]),
					'top'	=>	'制单时间：'.date('Y-m-d',$value['create_time']),
					'left'	=>	$value['name'],
					'right'	=> '',
					'bottom'=>	'制单人：'.get_nickname($value['authorized'])
				];
			}
			return $data_list;
			}
			return $this->fetch('apply/lists');			
		}
		
	
		//材料结算单详情
		public function cleardetail($id = ''){	
		if($id==null)$this->error('没有此单据');
		$info = ClearModel::getOne($id);
		$info->wu = '<a href="javascript:;" id="examine">点击查看</a>';
		$data_list = detaillist([
				['number','单据编号'],
				['name','单据名称'],
				['obj_id','项目'],
				['authorized','制单人','user'],
				//['money','合同金额','money'],
				['create_time','制单时间','date'],
				['wu','物资清单']
			],$info);

		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details',['url' => url('clear_tech', ['id' => $id])]);
			
			}

		public function clear_tech($id = null){
			if($id == null) $this->error('没有此清单');
			$title = [
				//'xj' => '小计',
				'xysl' => '需用数量',
				//'ckjg' => '参考价格',
				'bz' => '备注',
				'name' => '物品名称',
				'version' => '规格',
				'unit' => '单位'
			];
			$list = Db::name('tender_clear_detail') -> alias('t') -> field('s.name,s.version,s.unit,t.xysl') -> join('stock_material s', 't.itemsid=s.id', 'left') -> where(['pid' => $id]) -> select();
			$data = [
				'title' => $title,
				'list' => $list,
				'name' => 'name'
			];
			return $data;
		}
		




		//生产计划列表
		
		public function produceplan($keywords = ''){
			if($this -> request -> isAjax()){
				$lists = ProduceplanModel::where('locate("'.$keywords.'", `name`)>0')->order('create_time')->paginate(config('mobilePage'),false,['query' => request()->param()]);
				$data_list = [];
					foreach ($lists as $key => $value) {
					$data_list[$key] = [
						'url'	=>	url('produceplandetail',['id'=>$value['id']]),
						'top'	=>	'制单时间：'.date('Y-m-d',$value['create_time']),
						'left'	=>	$value['name'],
						'right'	=> '',
						'bottom'=>	'制单人：'.get_nickname($value['uid'])
					];
				}
				return $data_list;
			}
		return $this->fetch('apply/lists');			
			}	
	
	
	
		//生产计划详情
		
		
		public function produceplandetail($id=''){
		if($id==null)$this->error('没有此计划');
		$info = ProduceplanModel::getOne($id);
		$data_list = detaillist([
				['date','日期','date'],
				['name','生产主题'],
				['code','生产编号'],
				['obj_id','所属项目'],
				['uid','制单人','user'],
			],$info);
		//dump($data_list);die;
		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details');
			
			
			}




		//施工图纸详情
		

		public function factpic($keywords = ''){
			if($this -> request -> isAjax()){
					//$map = $this->getMap();
					// 数据列表
					$lists = FactpicModel::getList('locate("'.$keywords.'", `admin_user`.`nickname`)>0 OR locate("'.$keywords.'", `tender_factpic`.`name`)>0');
					$data_list = [];
					foreach ($lists as $key => $value) {
					$data_list[$key] = [
						'url'	=>	url('factpicdetail',['id'=>$value['id']]),
						'top'	=>	'制单时间：'.date('Y-m-d',$value['date']),
						'left'	=>	$value['name'],
						'right'	=> '',
						'bottom'=>	'制单人：'.get_nickname($value['uid'])
					];
				}
				return $data_list;
			}
		return $this->fetch('apply/lists');			
			}	
	

		public function factpicdetail($id){
			if($id==null)$this->error('没有此计划');
			$info = FactpicModel::getOne($id);
			$data_list = detaillist([
			['date','日期','date'],
			['name','生产主题'],
			['obj_id','项目'],
			['uid','制单人'],
			['note','备注'],
			['file','生产图纸'],
		],$info);
		//dump($data_list);die;
		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details');



		}



	    
}
