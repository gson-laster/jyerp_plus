<?php
namespace app\mobile\controller;
use app\tender\model\Materials as MaterialsModel;
use app\purchase\model\Plan as PlanModel;
use think\Db;
use app\purchase\model\Informmoney as InformmoneyModel;
use app\purchase\model\HetongMaterial as HetongMaterialModel;
use app\purchase\model\Hetong as HetongModel;
/*
 
 * 联系人控制器*/
class Purchase extends Base{
	
	
		
		//采购通知
		public function inform($keywords = ''){
			if($this -> request -> isAjax()){
			$map='locate("'.$keywords.'", `name`)>0';
				
			$lists = MaterialsModel::where($map)->order('create_time')->paginate(config('mobilePage'),false,['query' => request()->param()]);
			$data_list = [];
			foreach ($lists as $key => $value) {
			$data_list[$key] = [
				'url'	=>	url('informdetail',['id'=>$value['id']]),
				'top'	=>	'通知时间：'.date('Y-m-d',$value['create_time']),
				'left'	=>	$value['name'],
				'right'	=>  get_status($value['status']),
				'bottom'=>	'制单人：'.get_nickname($value['authorized'])
			];
		}
		return $data_list;
			}
		return $this->fetch('apply/lists');			
			}	
	
	
	
		//采购通知详情
		
		public function informdetail($id=''){
		if($id==null)$this->error('没有此计划');
		$info = MaterialsModel::getOne($id);
		$info->wu = '<a href="javascript:;" id="examine">点击查看</a>';
		$data_list = detaillist([
				['code','单据编号'],
				['name','单据名称'],
				['obj_id','项目'],
				['authorizedname','制单人','user'],
				//['money','合同金额','money'],
				['create_time','制单时间','date'],
				['wu','查看详情']
			],$info);
		//dump($data_list);die;
		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details',['url' => url('informdetail_tech', ['id' => $id])]);
	
			}

		public function informdetail_tech($id = null){
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
			//dump($list);die;
			$data = [
				'title' => $title,
				'list' => $list,
				'name' => 'name'
			];
			return $data;
		}
			
			
		//采购申请列表
	    public function ask($keywords = ''){
	    	if($this -> request -> isAjax()) {
	    		
	    	$lists = PlanModel::where('locate("'.$keywords.'",`name`)>0')->order('create_time')->paginate(config('mobilePage'),false,['query' => request()->param()]);
			  $data_list = [];
					foreach ($lists as $key => $value) {
					$data_list[$key] = [
						'url'	=>	url('askdetail',['id'=>$value['id']]),
						'top'	=>	'申请时间：'.date('Y-m-d',$value['create_time']),
						'left'	=>	$value['name'],
						'right'	=> '审核状态：'.get_status($value['status']),
						'bottom'=>	'申请人：'.get_nickname($value['wid'])
					];
				}
				return $data_list;
	    	}
		return $this->fetch('apply/lists');			    	
	  }
	    
	    //采购申请详情
	    public function askdetail($id=''){
	    	
	    if($id==null)$this->error('没有此申请');
			$info = PlanModel::getOne($id);
			$info->wu = '<a href="javascript:;" id="examine">点击查看</a>';
			$data_list = detaillist([
				['name', '主题'],
				['tname','采购类型'],
				['cnickname','采购员'],
				['prate','备料单'],
				['wnickname','制单人','user'],
				['create_time', '制单日期','date'],
				['wu','查看详情']
			  ],$info);
		
			$this->assign('data_list',$data_list);
		
			return $this->fetch('apply/details',['url' => url('ask_tech', ['id' => $id])]);
	    	}
	    


	    public function ask_tech($id = null){
			if($id == null) $this->error('没有此清单');
			$title = [
				'plan_money' => '小计',
				'plan_num' => '需用数量',
				'bj_money' => '参考价格',
				//'supplier' => '供应商',
				'name' => '物品名称',
				'version' => '规格',
				'unit' => '单位'
			];
			$list = Db::name('purchase_plan_material') -> alias('t') -> field('s.name,s.version,s.unit,t.plan_num,t.bj_money,t.plan_money') -> join('stock_material s', 't.wid=s.id', 'left') -> where(['aid' => $id]) -> select();
			//dump($list);die;
			$data = [
				'title' => $title,
				'list' => $list,
				'name' => 'name'
			];
			return $data;
		}
	    




	    public function informmoney($keywords=''){
	    	if($this -> request -> isAjax()) {
	    		//$map = $this->getMap();
        // 排序
        $map = 'locate("'.$keywords.'", `admin_user`.`nickname`)>0 OR locate("'.$keywords.'", `purchase_informmoney`.`name`)>0';
	    	$lists = InformmoneyModel::getList($map);
			  $data_list = [];
					foreach ($lists as $key => $value) {
					$data_list[$key] = [
						'url'	=>	url('informmoneydetail',['id'=>$value['id']]),
						'top'	=>	'申请时间：'.date('Y-m-d',$value['create_time']),
						'left'	=>	$value['name'],
						'right'	=> '审核状态：'.get_status($value['status']),
						'bottom'=>	'申请人：'.get_nickname($value['maker'])
					];
				}
				return $data_list;
	    	}
		return $this->fetch('apply/lists');			    	
	  }
	    
	    //采购申请详情
	    public function informmoneydetail($id=''){
	    	
	    if($id==null)$this->error('没有此申请');
			$info = InformmoneyModel::getOne($id);
			$data_list = detaillist([
			[ 'date', '日期','date' ],
        	['name','主题'],
        	['contract','采购合同'],
            ['supplier','供应商'],
            ['nickname','申请人'],
            ['money','金额','money'],
            ['big_money','金额大写'],
            ['note','备注'],
            ['file','文件']  
			  ],$info);
		
			$this->assign('data_list',$data_list);
		
			return $this->fetch('apply/details',['url' => url('ask_tech', ['id' => $id])]);
	    	}



	    //采购合同


	    	public function hetong($keywords = ''){
	    	if($this -> request -> isAjax()) {
       			 $map = 'locate("'.$keywords.'", `purchase_hetong`.`name`)>0';
	    		//$map = $this->getMap();
        // 排序
	    	$lists = HetongModel::getList($map);
			  $data_list = [];
					foreach ($lists as $key => $value) {
					$data_list[$key] = [
						'url'	=>	url('hetongdetail',['id'=>$value['id']]),
						'top'	=>	'申请时间：'.date('Y-m-d',$value['create_time']),
						'left'	=>	$value['name'],
						'right'	=> '审核状态：'.get_status($value['status']),
						'bottom'=>	'申请人：'.get_nickname($value['create_uid'])
					];
				}
				return $data_list;
	    	}
		return $this->fetch('apply/lists');			    	
	  }
	    
	    //采购申请详情
	    public function hetongdetail($id=''){
	    	
	    if($id==null)$this->error('没有此申请');
			$info = HetongModel::getOne($id);
			$info->wu = '<a href="javascript:;" id="examine">点击查看</a>';
			$data_list = detaillist([
						['name', '主题'],
                        ['purchase_type_name','采购类型'],
                        ['create_uid','制单人','user'],                   
                        ['source_id','询价单号'],
                        ['purchase_organization_name', '采购部门'],
                        ['purchase_nickname','采购员'],              
                        ['hetong_time','签约时间','date'],                   
                        [ 'create_time', '制单日期','date'],
                        ['file',' 附件'],
                        ['wu','查看详情']
                       
			  ],$info);
		
			$this->assign('data_list',$data_list);
		
			return $this->fetch('apply/details',['url' => url('hetong_tech', ['id' => $id])]);
	    	}


	     public function hetong_tech($id = null){
			if($id == null) $this->error('没有此清单');
			$title = [
				'bj_money' => '采购询价',
				'plan_num' => '采购数量',
				'plan_money'=>'小计',
				//'supplier' => '供应商',
				'name' => '物品名称',
				'version' => '规格',
				'unit' => '单位'
			];
			$list = Db::name('purchase_hetong_material') -> alias('t') -> field('s.name,s.version,s.unit,t.plan_num,t.bj_money,t.plan_money') -> join('stock_material s', 't.wid=s.id', 'left') -> where(['aid' => $id]) -> select();
			//dump($list);die;
			$data = [
				'title' => $title,
				'list' => $list,
				'name' => 'name'
			];
			return $data;
		}
	    



	    
}
