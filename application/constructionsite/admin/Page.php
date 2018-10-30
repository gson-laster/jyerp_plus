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
        $order = $this->getOrder('tender_factpic.date desc');

        $task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
$css = <<<EOF
	<style>
	th,td{text-align:center;}
		
	</style>
EOF;
		
		$data_list = PageModel::getList($map,$order);
        return ZBuilder::make('table')
	        	 	
	        	 	->addTimeFilter('tender_factpic.date') // 添加时间段筛选
	        	 	->addFilter(['obj_id'=>'tender_obj.name']) // 添加筛选
	        		->hideCheckbox()
	        		->addOrder('tender_factpic.date') // 添加排序
                    ->addColumns([ // 批量添加列
				        ['__INDEX__','序号'],
				        ['obj_id', '项目'],
				       	['date','下发时间','date'],
				       	['right_button', '操作', 'btn'],
				    ])
				    ->setExtraCss($css)
				    ->setRowList($data_list) // 设置表格数据
				    ->addRightButton('btn', $task_list,true) // 添加授权按钮
				    ->addRightButton('delete') //添加删除按钮
	                ->fetch();
	        	
	}
	public function task_list($id = null){
		if($id == null) $this->error('参数错误');
	
		$info = PageModel::getOne($id);
		$info['date'] = date('Y-m-d',$info['date']);
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addFormItems([
			['static:4','date','日期'],
			['static:4','name','生产主题'],
			['static:4','obj_id','项目'],
			['static:4','uid','制单人'],
			['static','note','备注'],
			['archives','file','生产图纸']								
		])
		->setFormData($info)
		->fetch();
	}

	

}   
