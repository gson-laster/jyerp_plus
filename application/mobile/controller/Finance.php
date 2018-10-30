<?php
namespace app\mobile\controller;
use app\finance\model\Finance_receipts as Finance_receiptsmodel;
use app\finance\model\Gather as GModel;
use app\sales\model\Contract as ContractModel;
use app\purchase\model\Hetong as HetongModel;
use app\finance\model\Income as IncomeModel;
use app\finance\model\SectorChart as SectorChartModel;
use app\tender\model\Obj as ObjModel;

/*
 
 * 联系人控制器*/
class Finance extends Base{
	
	//合同收款列表
	public function receipts($keywords = '') {
		if($this -> request -> isAjax()) {
			$map = 'locate("'.$keywords.'",`finance_receipts`.`name`)>0';
			$order = [];
			$lists = Finance_receiptsmodel::getList($map,$order);
			//dump($data_list);die;
			$data_list = [];
			foreach ($lists as $key => $value) {
				$data_list[$key] = [
					'url'	=>	url('receiptsdetail',['id'=>$value['id']]),
					'top'	=>	'制单时间：'.date('Y-m-d',$value['create_time']),
					'left'	=>	$value['name'],
					'right'	=>	$value['number'],
					'bottom'=>	'责任人：'.get_nickname($value['operator'])
				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');
		
	}
	//合同收款详情
	public function receiptsdetail($id='') {
		$type = [1=>'按进度付款',2=>'按合同付款'];
		if($id==null)$this->error('没有此项目');
		$info = Finance_receiptsmodel::one(['id' => $id]);	
		$data_list = detaillist([
			[ 'date', '日期','date'],
			[ 'number', '收款编号'],
			['name','收款名称'],
			[ 'item','项目'],
			[ 'title','合同名称'],
			[ 'money','合同金额','money'],
			[ "big_money", '大写金额'],
			['nail','甲方单位'],			
			['gathering_type', '收款类型',$type],			
			['fine','罚款'],			
			['withhold','扣款'],			
			['gathering','收款金额'],			
			['nickname','填报人'],			
			['note','备注'],
					],$info);

		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details');
	}
	
	//收款单列表
	public function gather($keywords = ''){
			
		if($this -> request -> isAjax()) {
			$map = 'locate("'.$keywords.'",`finance_gather`.`name`)>0 OR locate("'.$keywords.'",`finance_gather`.`number`)>0';
			$order=[];
			$lists =  GModel::getList($map, $order);
			$data_list = [];
			foreach ($lists as $key => $value) {
				$data_list[$key] = [
					'url'	=>	url('gatherDetail',['id'=>$value['id']]),
					'top'	=>	'制单时间：'.date('Y-m-d',$value['date']),
					'left'	=>	$value['name'],
					'right'	=>	$value['number'],
					'bottom'=>	'签订人：'.get_nickname($value['maker'])
				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');
		
		
	}
	
	//收款单详情	
	public function gatherDetail($id = ''){
		if($id==null)$this->error('没有此合同');
		$info = GModel::getOne($id);
		$arr = [1=>'工程收款',2=>'销售收款',3=>'其他收款'];
		$data_list = detaillist([
				['date', '日期','date'],
				['number', '付款编号'],
				['pact','合同名称'],
				['name', '收款人'],
				['money','收款金额','money'],
				["big_money", '大写金额'],
				['sname','供应商'],
				['gtype','收款类型',$arr],					
				['maccount','公司账户'],			
				['maker', '经办人'],			
				['remark','备注'],			
				['file','附件']	
			],$info);

		$this->assign('data_list',isset($data_list) ? $data_list :'');
		
		return $this->fetch('apply/details');
	}
		
	//销售列表
	public function sales($keywords = ''){
		if($this -> request -> isAjax()){
			$map = 'locate("'.$keywords.'",`sales_contract`.`name`)>0 OR locate("'.$keywords.'",`sales_contract`.`code`)>0';
			$order = [];
			$lists = ContractModel::getList($map,$order);
			foreach($lists as $key => &$value){
				$value['money'] = '￥ '.number_format($value['money'],2);
			}
			$data_list = [];
				foreach ($lists as $key => $value) {
				$data_list[$key] = [
					'url'	=>	url('salesdetail',['id'=>$value['id']]),
					'top'	=>	'制单时间：'.date('Y-m-d',$value['create_time']),
					'left'	=>	$value['name'],
					'right'	=> $value['code'],
					'bottom'=>	'制单人：'.get_nickname($value['zdid'])
				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');	
	}

	//销售合同列表
	public function salesdetail($id=''){
		if($id==null)$this->error('没有此合同');
		$info = ContractModel::getOne($id);
		$data_list = detaillist([
			['name','合同名称'],
		
			['customer_name','客户名称'],
			['paytype','支付方式',[-2=>'转账',-1=>'支付宝',0=>'微信',1=>'支票',2=>'现金']],
			['goodtype','交货方式',[0=>'一次性交货',1=>'分批交货']],
			['transport','运送方式',[-1=>'空运',0=>'海运',1=>'快递']],
			['currency','币种',[-1=>'美元',0=>'人民币',1=>'欧元']],
			['parities','汇率%'],
			['money','合同金额','money'],
			//['zrid','业务员'],
			//['oid','所属部门'],	
			['document_time','开始日期','date'],
			['adderss','签约地址'],	
			['end_time','截止日期','date'],					
			['zdid','制单人','user'],	
			['create_time','制单时间','date'],	
		
			],$info);

		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details');
		}
		
		//采购合同
		public function purchase($keywords = ''){
			if($this -> request -> isAjax()){
			   $map = 'locate("'.$keywords.'",`purchase_hetong`.`name`)>0 OR locate("'.$keywords.'",`purchase_hetong`.`number`)>0';
		       $order = [];
		       $data_list = [];
					$lists = HetongModel::getList($map,$order);
					foreach ($lists as $key => $value) {
					$data_list[$key] = [
						'url'	=>	url('purchasedetail',['id'=>$value['id']]),
						'top'	=>	'制单时间：'.date('Y-m-d',$value['create_time']),
						'left'	=>	$value['name'],
						'right'	=> $value['number'],
						'bottom'=>	'制单人：'.get_nickname($value['create_uid'])
					];
				}
				return $data_list;
			}
		return $this->fetch('apply/lists');			
			}
		
	
		//采购合同
		public function purchasedetail($id = ''){	
		if($id==null)$this->error('没有此单据');
		$info = HetongModel::getOne($id);
		//dump($info);die;
		$data_list = detaillist([
												['name', '主题'],
                        ['purchase_type_name','采购类型'],
                        ['create_uid','制单人','user'], 
                        ['purchase_nickname','采购员'],
                        
           
                        ['create_time', '制单日期','date'],
                      
                
                        ['file',' 附件'],
                        ['remark','备注']
			],$info);

		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details');
			
			}

		//收入合同列表
		
		public function income($keywords = ''){
			if($this -> request -> isAjax()){
				$map = 'locate("'.$keywords.'",`contract_income`.`title`)>0 OR locate("'.$keywords.'",`contract_income`.`number`)>0';
				$order = [];
				$lists = IncomeModel::getList($map,$order);
				$data_list = [];
					foreach ($lists as $key => $value) {
					$data_list[$key] = [
						'url'	=>	url('incomedetail',['id'=>$value['id']]),
						'top'	=>	'制单时间：'.date('Y-m-d',$value['date']),
						'left'	=>	$value['title'],
						'right'	=> $value['number'],
						'bottom'=>	'制单人：'.get_nickname($value['operator'])
					];
				}
				return $data_list;
			}
		return $this->fetch('apply/lists');			
			
		}
		
	
		//收入合同详情
		
		
		public function incomedetail($id=''){
		if($id==null)$this->error('没有此计划');
		$info = IncomeModel::getOne($id);
		$pay_type = [1=>'按进度付款',2=>'按合同付款'];
		$balance = [1=>'按月结算',2=>'分段结算',3=>'目标结算',4=>'竣工后一次结算',5=>'其他'];	
		$data_list = detaillist([
                    ["date", '日期','date'],
                    ["number", '合同编号'],
                    ["title", '合同标题'],
                    ["objname", '所属项目'],
                    ["type", '合同类型'],
                    ["begin_date", '开始日期','date'],
                    ["end_date", '结束日期','date'],
                    ["money", '合同金额','money'],
                    ["big_money", '大写金额'],
                    ["nail", '甲方单位'],
                    ["second_party", '乙方单位'],
                    ["authorizedname", '签订人'],
                    ["pay_type",'付款方式',$pay_type],
                    ["balance", '结算方式',$balance],
                    ["advances_received",'预付款'],
                    ["bail", '保证金','money'],
                    ["collection_terms", '收款条件'],
                    ["main_requirements", '主要条款'],
                   	['file','附件'],         
			],$info);
		//dump($data_list);die;
		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details');
			
			
			}
			//尾款结算列表
		
		public function balance_payment($keywords = ''){
			if($this -> request -> isAjax()){
				$map = 'locate("'.$keywords.'",`tender_obj`.`name`)>0';
				$order = [];
				$lists = ObjModel::balance_payment($map,$order);
				$data_list = [];
			foreach ($lists as $key => $value) {
			$data_list[$key] = [
				'url'	=>	url('balance_paymentdetail',['id'=>$value['id']]),
				'top'	=>	'',
				'left'	=> $value['name'],
				'right'	=> '',			
				'bottom' =>'',
			];
		}
		return $data_list;
			}
			
		return $this->fetch('apply/lists');			
			
		}
		
		//尾款结算详情
		
		public function balance_paymentdetail($id=''){
		if($id==null)$this->error('没有此计划');
		$info =  ObjModel::balance_payment_id(['t.item' => $id]);
		$pay_type = [1=>'按进度付款',2=>'按合同付款'];
		$balance = [1=>'按月结算',2=>'分段结算',3=>'目标结算',4=>'竣工后一次结算',5=>'其他'];	
		$data_list = detaillist([
      ['name', '项目名称'],
			['contact','联系人'],
			['phone','联系电话'],
			['contrack_name','合同名称'],
			['money','合同金额','money'],
			['gather','已收款','money'],
			['final_payment','尾款金额','money'],
			['file','竣工图'],
			],$info);
		//dump($data_list);die;
		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details');
			
			
			}
			

			
		
	}   
