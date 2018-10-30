<?php
namespace app\sales\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\sales\model\Clear as ClearModel;
/*
 *材料付款
 * */
 class Clear extends Admin {

 	public function index(){
		$map = $this->getMap();
		$data_list = ClearModel::getList($map);
		
   	$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('edit',['id'=>'__id__'])
		];
		return ZBuilder::make('table')
			->hideCheckbox()
			->setSearch(['constructionsite_finish.item'=>'项目'],'','',true) // 设置搜索框
      ->addTimeFilter('finance_stuff.date') // 添加时间段筛选
      ->addOrder('e_time') // 添加排序
		  ->addColumns([ // 批量添加列
			[ 'item', '项目'],
			[ 'e_time', '竣工日期','date'],
			[ 'final_payment','未结算金额'],
			 ['file', '竣工图（点击下载）','callback',
				        function($value, $data){
				        	if(!$value== null){
				        		 return'<a href="'.get_file_path($value).'">'.get_file_name($value).'</a>';
				        		 }else{
				        		 	return '无附件';
				        		 	}
				        		 	},'__data__'],
			[ 'maker','提交人'],					
	    ]) // 添加顶部按钮
		->setPageTitle('尾款结算')
		->setRowList($data_list)
		->fetch();
	
 	}
}