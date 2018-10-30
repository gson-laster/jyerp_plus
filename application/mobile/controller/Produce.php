<?php
namespace app\mobile\controller;
use app\tender\model\Produceplan as ProduceplanModel;
use app\tender\model\Obj as ObjModel;
use think\Db;
/*
 
 * 联系人控制器*/
class Produce extends Base{
	
	//申请完工入库

	public function confirm($id = null){
		if(is_null($id)) $this -> error('参数错误');
		Db::name('produce_plan') -> where('id',$id) -> setField('status',1);
		$this -> success('操作成功','mobile/produce/inform');
	}






	//生产计划列表
	public function inform($keywords = '') {
		if($this -> request -> isAjax()){
			
			$map = 'locate("'.$keywords.'", `produce_plan`.`name`)>0 OR locate("'.$keywords.'", `admin_user`.`nickname`)>0';
			$lists = ProduceplanModel::getList($map);
			$data_list = [];
			$state = [0 => '生产中', 1 => '完工'];
			foreach ($lists as $key => $value) {
				$data_list[$key] = [
					'url'	=>	url('informdetail',['id'=>$value['id']]),
					'top'	=>	'制单时间：'.date('Y-m-d',$value['create_time']),
					'left'	=>	$value['name'],
					'right'	=>	 $state[$value['status']],
					'bottom'=>	'责任人：'.get_nickname($value['uid'])
				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');
	}
	//合同详情
	public function informdetail($id='') {

		if($id==null)$this->error('没有此项目');


		$info = ProduceplanModel::getOne($id);
		$data_statu = ProduceplanModel::where('id',$id)->find();
		$data_status = [
				'id'=>$data_statu['id'],
				'status'=>$data_statu['status']
		];

		//dump($data_status);die;
		//$info->wu = '<a href="javascript:;" id="examine">申请完工</a>';
		//dump($info);die;
		$data_list = detaillist([
							['date','日期','date'],
							['name','生产主题'],
							['obj_id','项目'],
							['uid','制单人','user'],
							['note','备注'],
							['enclosure','生产图纸'],
											
					],$info);

		$this->assign('data_list',$data_list);
		$this->assign('data_status',$data_status);
		return $this->fetch('apply/details',['url' => url('materials_tech', ['id' => $id])]);
	}
	
	//合同列表
	public function Contract(){
			$lists = ContractModel::where([])->order('create_time')->paginate();
			foreach ($lists as $key => $value) {
			$data_list[$key] = [
				'url'	=>	url('Contractdetail',['id'=>$value['id']]),
				'top'	=>	'制单时间：'.date('Y-m-d',$value['create_time']),
				'left'	=>	$value['name'],
				'right'	=>	'',
				'bottom'=>	'签订人：'.get_nickname($value['zdid'])
			];
		}
		$this->assign('data_list',$data_list);
		return $this->fetch('apply/lists');
		
		
	}

	    
}
