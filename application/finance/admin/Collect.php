<?php
namespace app\finance\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\finance\model\Finance_receipts as Finance_receiptsmodel;
use think\Db;
/*
 *应收款汇总
 * */
 class Collect extends Admin {
	protected $model;
	protected $manager;
	protected $bankInfo;
	protected $bankMinInfo;
    protected function _initialize(){
        parent::_initialize();
        $this -> model = new Finance_receiptsmodel();
    }
 	public function index(){
		$map = $this->getMap();
		$data_list = Finance_receiptsmodel::getList($map);
		return ZBuilder::make('table')
		  ->addColumns([ // 批量添加列
			[ 'id', '序号'],
			[ '', '供应商'],
			[ '','合同名称'],
			[ '','项目名称'],
			[ '','日期'],
			[ '','期初金额'],
			[ '','合同金额'],			
			[ '','结算金额'],			
			[ '','开票金额'],			
			[ '','已收款'],			
	        ['right_button', '操作','btn'],
	    ])
      //  ->addTopButton('add') // 添加顶部按钮
		->setPageTitle('应收款汇总表')
	->addRightButton('edit',[],true)
	//	->addTopButton('delete') // 添加顶部按钮
		->setTableName('Finance_receipts') // 指定数据表名
		->setSearch([ 'operator' => '填报人','item'=>'项目名称','contract_title' => '合同名称',  'name' => '收款名称', 'number' => '收款编号', 'nail' => '甲方单位'], '', '', true) // 设置搜索参数
		->addTimeFilter('date') // 添加时间段筛选
		->setRowList($data_list)
		->fetch();
	
 	}
 	
	public function edit($id = null){
		if($this -> request -> ispost()){
			$data = $this -> request ->post();
			$r = $this -> Validate($data, 'receipts');
			if(true !== $r) $this -> error($r);
			$data['date'] = strtotime($data['date']);
			$this-> model -> where('id', $id) -> update($data);
            $this->success('添加成功', 'index');
		}
		if (null == $id) $this -> error('参数错误');
		$data_list = $this -> model -> where('id', $id) -> find();
		$data_list['date'] = date('Y-m-d', $data_list['date']);
		
		return ZBuilder::make('form')
		->addFormItems([
		// 批量添加表单项
			['static:3', 'date', '日期'],
			['static:3', 'number', '收款编号'],
			['static:3', 'name','收款名称'],
			['static:3', 'item','项目'],
			['static:3', 'contract_title','合同名称'],
			['static:3', 'contract_money','合同金额'],
			['static:3', 'type','收款类型'],			
			['static:3', 'nail','甲方单位'],			
			['static:3', 'gathering_type','收款类型'],			
			['static:3', 'fine','罚款'],			
			['static:3', 'withhold','扣款'],			
			['static:3', 'gathering','收款金额'],			
			['static:3', 'big','金额大写'],			
			['static:3', 'operator','填报人'],			
			['static:12', 'note','备注'],
		])
		-> setPageTitle('详情')
		->hideBtn('submit')
		-> setFormData($data_list)
		->fetch();
	
	}
	public function delete($ids = null){
		if(null == $ids) $this -> error('参数错误');
     	return $this->setStatus('delete');
	}
}