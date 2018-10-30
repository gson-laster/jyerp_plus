<?php
namespace app\constructionsite\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\admin\model\Access as AccessModel;
use think\Db;
use app\constructionsite\model\Page as PageModel;
/**
 *  施工日志
 */
class Page extends Admin
{
	//
	public function index()
	{

        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('create_time desc');

        $btn_detail = [
		    'title' => '查看详情',
		    'icon'  => 'fa fa-fw fa-search',
		    'href'  => url('detail', ['lid' => '__id__'])
		];
$css = <<<EOF
	<style>
	th,td{text-align:center;}
		
	</style>
EOF;
		
		$data_list = PageModel::getList($map,$order);
        return ZBuilder::make('table')
	        	 	
	        	 	->addTimeFilter('tender_budget.start_time') // 添加时间段筛选
	        	 	->addFilter(['item'=>'tender_obj.name']) // 添加筛选
	        		->hideCheckbox()
	        		->addOrder('tender_budget.start_time,tender_budget.end_time,tender_budget.obj_time') // 添加排序
                    ->addColumns([ // 批量添加列
				        ['__INDEX__','序号'],
				        ['item', '项目'],
				        ['start_time','预计开始日期'],
				        ['end_time','预计竣工日期'],
				        ['obj_time', '预计工期（天）'],
				        ['file3', '工程图纸（点击下载）','callback',
				        function($value, $data){
				        	if(!$value== null){
				        		 return'<a href="'.get_file_path($value).'">'.get_file_name($value).'</a>';
				        		 }else{
				        		 	return '无附件';
				        		 	}
				        		 	},'__data__'],
				    ])
				    ->setExtraCss($css)
				    ->setRowList($data_list) // 设置表格数据
				    ->addRightButton('btn', $btn_detail,true) // 添加授权按钮
				    ->addRightButton('delete') //添加删除按钮
	                ->fetch();
	        	
	}

	

}   
