<?php
namespace app\stock\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\stock\model\Account as AccountModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\House as HouseModel;
use app\stock\model\Bad	as BadModel;
use app\stock\model\MaterialType as MaterialTypeModel;
/**
 * 其他入库控制器
 */
class Account extends Admin
{	
	// 库存主页
	public function index(){
		$map = $this->getMap();
		$order = $this->getOrder('stock_account.update_time desc');
		$data_list = AccountModel::getList($map,$order);
		return ZBuilder::make('table')
		->addTimeFilter('stock_account.update_time') // 添加时间段筛选
		->hideCheckbox()
		->addColumns([
				['material_code', '编号'],
				['material_name','名称',],
				['material_version','规格'],
				['material_unit','计量单位'],
				['qnum','初期数量'],
				['qprice','单价'],
				['qtotal', '金额'],
				['rnum','入库数量'],
				['rprice','单价'],
				['rtotal', '金额'],
				['cnum','出库数量'],
				['cprice','单价'],
				['ctotal', '金额'],
				['ynum','结存数量'],
				['yprice','单价'],
				['ytotal', '金额'],
			])                      
            ->setRowList($data_list) // 设置表格数据            
            ->fetch(); // 渲染模板
	}

	
}

