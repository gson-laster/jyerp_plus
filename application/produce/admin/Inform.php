<?php
namespace app\produce\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use think\Db;
use app\produce\model\Inform as InformModel;
/**
 *  施工日志
 */
class Inform extends Admin
{
	//
	public function index()
	{
		// 获取查询条件
		$map = $this->getMap();
		// 数据列表
		$data_list = InformModel::getList($map);
		//dump($data_list);die;               
		//获取昵称
		// 分页数据
		$page = $data_list->render();
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
			$confirm = [
		    'title' => '确认生产计划已经完成吗？',
		    'icon'  => 'fa fa-fw fa-key',
		    'class' => 'btn btn-xs btn-default ajax-get confirm',
		    'data-title' => '确认生产计划已经完成吗？',
		    'href'  => url('confirm', ['id' => '__id__', 'name' => '__name__'])
		];
		
		return ZBuilder::make('table')
		->addTimeFilter('produce_plan.date')
		->addFilter(['obj_id'=>'tender_obj.name']) // 添加筛选
		->addFilter(['nickname'=>'admin_user.nickname'])
		->hideCheckbox()
		->setPageTitle('生产计划列表')
		->addColumns([
			['__INDEX__','序号'],
			['date','日期','date'],
			['name','计划主题'],
			['obj_id','项目名称'],	
			['nickname','制单人'],
			['status', '进度','status','',[0 =>'生产中:info',1=>'完工:success']],
			['right_button', '操作', 'btn']
		])
		->addOrder(['id','time']) // 添加排序
		->addRightButton('task_list',$task_list,true)
		->addRightButton('confirm',$confirm)
		->setRowList($data_list)//设置表格数据
		->replaceRightButton(['status' => 1], '', 'confirm')
		->setTableName('produce_plan')
		->fetch();
	}
	
	
		public function task_list($id = null){
		if($id == null) $this->error('参数错误');
	
		$info = InformModel::getOne($id);
		$info['date'] = date('Y-m-d',$info['date']);
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addFormItems([
			['static:4','date','日期'],
			['static:4','name','生产主题'],
			['static:4','obj_id','项目'],
			['static:4','uid','制单人'],
			['static','note','备注'],
			['archives','enclosure','生产图纸']								
		])
		->setFormData($info)
		->fetch();

	}
	
	
	
	public function confirm($id = null){
		if(is_null($id)) $this -> error('参数错误');
		Db::name('produce_plan') -> where('id',$id) -> setField('status',1);
		$this -> success('操作成功');
	}
	
	
	
	

}   
