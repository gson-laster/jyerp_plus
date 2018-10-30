<?php
namespace app\mobile\controller;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\House as HouseModel;
use app\stock\model\Stock as StockModel;
use app\stock\model\Purchase as PurchaseModel;
use app\stock\model\Otherout as OtheroutModel;
use app\admin\model\Config as ConfigModel;
/*
 
 * 联系人控制器*/
class Stock extends Base{
	
	//基础物资库列表
	public function material($id = '') {
		$per_page = ConfigModel::where('name','mobilePage')->value('value');
		$action = request()->action();
		$material_type = MaterialTypeModel::where('status',1)->field('id,title')->select();
		if($this->request->isAjax()){
			if($id == null){
				$lists = MaterialModel::exportData([],'create_time desc');
				return $lists;
			}else{
				$map['type'] = $id;
				$lists = MaterialModel::exportData($map,'create_time desc');
				return $lists;
			}			
		}
		$this->assign('action',$action);
		$this->assign('type',$material_type);
		$this->assign('per_page',$per_page);
		return $this->fetch('apply/cklists');		
	}
	//物品详情
	public function materialdetail($id='') {
		if($id==null)$this->error('没有此材料');
		$info = MaterialModel::getMobone($id);	
		$data_list = detaillist([
				['code','物品编码'],
				['name','物品名称'],
				['type_name','物品类型'],
				['version','规格型号'],
				['size','尺寸'],
				['unit','计量单位'],
				['weight','重量'],
				['house_name1','主放仓库'],
				//['explain','说明'],
			],$info);
		$this->assign('data_list',$data_list);		
		return $this->fetch('apply/details');
	}

	//仓库
	public function house($keywords = '') {
		if($this -> request -> isAjax()) {
			$map = 'locate("'.$keywords.'",`stock_house`.`name`)>0 OR locate("'.$keywords.'",`stock_house_type`.`name`)>0 OR locate("'.$keywords.'",`admin_user`.`nickname`)>0';
			$lists = HouseModel::getList($map,'stock_house.create_time desc');
			$data_list = [];
			foreach ($lists as $key => $value) {
				$data_list[$key] = [					
					'top'	=>	'建档时间：'.date('Y-m-d',$value['create_time']),
					'left'	=>	$value['name'],
					'right'	=>	'仓库类型：'.$value['house_type'],
					'bottom'=>	'仓库管理：'.$value['nickname']
				];
			}
			return $data_list;
		}		
		return $this->fetch('apply/lists');
	}
	//现有库存
	public function stock($id = '') {
		$per_page = ConfigModel::where('name','mobilePage')->value('value');
		$action = request()->action();
		$material_type = MaterialTypeModel::where('status',1)->field('id,title')->select();
		$map = $this -> getMap();
		if($this->request->isAjax()){
			if($id == null){
				$lists = StockModel::getList($map,'create_time desc');
				return $lists;
			}else{
				$map['stock_stock.material_type'] = $id;
				$lists = StockModel::getList($map,'create_time desc');
				return $lists;
			}			
		}
		$this->assign('type',$material_type);
		$this->assign('action',$action);
		$this->assign('per_page',$per_page);
		return $this->fetch('apply/cklists');
	}
	//库存详情
	public function stockdetail($id='') {
		if($id==null)$this->error('没有此库存');
		$info = StockModel::getMobone($id);	
		$data_list = detaillist([
				['material_code','物品编码'],
				['material_name','物品名称'],
				['material_type_name','物品类型'],
				['material_version','规格型号'],
				['material_unit','计量单位'],
				['stock_name','主放仓库'],
				['number','数量'],
				['price','单价','money'],
				['total','合计','money'],
			],$info);
		$this->assign('data_list',$data_list);		
		return $this->fetch('apply/details');
	}
	//入库查询
	public function materialin($keywords = ''){
		if($this -> request -> isAjax()) {
			$map = 'locate("'.$keywords.'",`stock_material`.`name`)>0';
			$lists = PurchaseModel::getMaterialin($map,'stock_purchase.intime desc');
			//dump($lists);die;
			$data_list = [];
			foreach ($lists as $key => $value) {
				if(empty($value['intime'])){
					unset($value['intime']);
				}				
				$data_list[$key] = [
					'url'	=>	url('materialindetail',['id'=>$value['id']]),
					'top'	=>	"入库时间：".isset($value['intime']) ? $value['intime'] : '',
					'left'	=>	$value['material_name'],
					'right'	=>	'仓库：'.$value['house_name'],
					'bottom'=>	'规格：'.$value['version'].' - 入库数量：'.$value['rksl']
				];				
			}
			return $data_list;
			
		}
		return $this->fetch('apply/lists');
	}
	public function materialindetail($id = ''){
		if($id==null)$this->error('没有此库存');
		$info = PurchaseModel::getMobone($id);	
		$data_list = detaillist([
				['code','物品编码'],
				['material_name','物品名称'],
				['material_type_name','物品类型'],
				['version','规格型号'],
				['unit','计量单位'],
				['house_name','主放仓库'],
				['rksl','入库数量'],
				['dj','单价','money'],
				['je','合计','money'],
				['intime','入库时间'],
				['sid','供应商']
			],$info);
		$this->assign('data_list',$data_list);		
		return $this->fetch('apply/details');
	}
	//出库查询
	public function materialout($keywords = ''){
		if($this -> request -> isAjax()) {
			$map = 'locate("'.$keywords.'",`stock_material`.`name`)>0';
			$lists = OtheroutModel::getMaterialout($map,'stock_otherout.intime desc');
			//dump($lists);die;
			$data_list = [];
			foreach ($lists as $key => $value) {			
				$data_list[$key] = [
					'url'	=>	url('materialoutdetail',['id'=>$value['id']]),
					'top'	=>	"入库时间：".isset($value['intime']) ? $value['intime'] : '',
					'left'	=>	$value['material_name'],
					'right'	=>	'仓库：'.$value['house_name'],
					'bottom'=>	'规格：'.$value['version'].' - 出库数量：'.$value['cksl']
				];				
			}
			return $data_list;
			
		}
		return $this->fetch('apply/lists');
	}
	public function materialoutdetail($id = ''){
		if($id==null)$this->error('没有此库存');
		$info = OtheroutModel::getMobone($id);	
		$data_list = detaillist([
				['code','物品编码'],
				['material_name','物品名称'],
				['material_type_name','物品类型'],
				['version','规格型号'],
				['unit','计量单位'],
				['house_name','主放仓库'],
				['cksl','出库数量'],
				['dj','单价','money'],
				['je','合计','money'],
				['intime','入库时间'],
				['sid','所属项目']
			],$info);
		$this->assign('data_list',$data_list);		
		return $this->fetch('apply/details');
	}


}
