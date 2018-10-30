<?php
namespace app\mobile\controller;
use app\sales\model\Contract as ContractModel;
use  app\user\model\User;
use app\sales\model\Clear as ClearModel;
use app\tender\model\Obj as ObjModel;

/*
 
 * 联系人控制器*/
class Sales extends Base{
	
	//合同列表
	public function contract($keywords = '') {

		if($this -> request -> isAjax()) {
			$lists = ContractModel::getMobileList('locate("'.$keywords.'", `admin_user`.`nickname`) OR locate("'.$keywords.'", `sales_contract`.`name`)','sales_contract.create_time desc');
			$data_list = [];
			foreach ($lists as $key => $value) {
				$data_list[$key] = [
					'url'	=>	url('contractdetail',['id'=>$value['id']]),
					'top'	=>	'制单时间：'.date('Y-m-d',$value['create_time']),
					'left'	=>	$value['name'],
					'right'	=>	'业务员：'.$value['zrid'],
					'bottom'=>	'时间：'.date('Y-m-d',$value['document_time']).'——'.date('Y-m-d',$value['end_time'])
				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');
		
	}
	//合同详情
	public function contractdetail($id='') {

		if($id==null)$this->error('没有此合同');
		$info = ContractModel::getOne($id);
	

	//	$info->wu = '<a href="javascript:;" id="examine">点击查看</a>';
		$data_list = detaillist([
				['code','合同编号'],
				['name','合同名称'],
				['zrid','业务员'],
				['customer_name','客户名称'],
				['phone','客户手机'],
				['money','合同金额','money','money'],
				['document_time','开始时间','date'],
				['end_time','结束时间','date'],
				['create_time','制单时间','date'],
			//	['wu','物资清单'],
			],$info);

		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details');
	}



	//尾款结算
	public function clear($keywords = '') {

		if($this -> request -> isAjax()) {
			$lists = ClearModel::getList('locate("'.$keywords.'", `tender_obj`.`name`) OR locate("'.$keywords.'", `admin_user`.`nickname`)','constructionsite_finish.e_time desc');
			//dump($lists);die;
			$data_list = [];
			foreach ($lists as $key => $value) {
				$data_list[$key] = [
					'url'	=>	url('cleardetail',['id'=>$value['id']]),
					'top'	=>'时间：'.date('Y-m-d',$value['s_time']).'——'.date('Y-m-d',$value['e_time']),
					'left'	=>	$value['item'],
					'right'	=>	'业务员：'.$value['maker'],
					'bottom'=>	''


				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');
		
	}

	//尾款结算详情
	public function cleardetail($id='') {

		$map = $this->getMap();
		if($id==null)$this->error('没有此合同');
		$info = ObjModel::balance_payment_one($map,$id);
		//dump($info);die;
	

	//	$info->wu = '<a href="javascript:;" id="examine">点击查看</a>';
		$data_list = detaillist([
				['name','项目'],
				//['e_time','竣工日期'],
				['all_money','合同收入','money'],
				['pay','已支付','money'],
				['final_payment','未结算','money'],
				//['file','竣工图'],
				//['maker','提交人'],
			//	['wu','物资清单'],
			],$info);

		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details');
	}

}
