<?php
	
namespace app\tender\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\tender\model\Type as TypeModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use think\Db;
/**
 * 招标控制器
 * @author HJP
 */
class Type extends Admin
{
	//投标类型列表
	public function index()
	{
		// 获取查询条件
		$map = $this->getMap();
		// 数据列表
		$data_list = TypeModel::where($map)->paginate();
		// 分页数据
		$page = $data_list->render();
		return ZBuilder::make('table')
		->setPageTitle('项目类型列表')
		->addColumns([
			['id','编号'],
			['name','类型名称'],			
			['status','状态','switch'],
			['right_button','操作','btn'],
		])
		->addOrder(['id']) // 添加排序
		->addTopButtons(['delete'])//添加顶部按钮
		->addRightButtons(['edit','delete' => ['data-tips' => '删除类型将无法恢复。']])
		->setRowList($data_list)//设置表格数据
		->setTableName('tender_type')
		->fetch();
	}
	//添加项目
	public function add(){
		if($this->request->isPost()){
			$data = $this->request->post();
			// 验证
			$result = $this->validate($data, 'Type');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			
			//查看人员，隔开
			
			if($model = TypeModel::create($data)){
				//记入行为
				
				$this->success('新增成功！',url('index'));
			}else{
				$this->error('新增失败！');
			}
		}
		return Zbuilder::make('form')
		->addFormItems([
			['text','name','类型名称'],
			['radio','status','状态','', ['禁用', '启用'], 1],						
		])
		->fetch();
	}
	//编辑项目
	public function edit($id = null){
		if($id == null) $this->error('参数错误');
		if($this->request->isPost()){
			$data = $this->request->post();
			// 验证
			$result = $this->validate($data, 'Type');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			if($model = TypeModel::update($data)){
				//记录行为
				
				return $this->success('修改成功',url('index'));
			}else{
				return $this->error('修改失败');
			}
		}
		$info = TypeModel::where('id',$id)->find();
		return ZBuilder::make('form')
		->addFormItems([
			['hidden', 'id'],
			['text','name','类型名称'],
			['radio','status','状态', '', ['禁用', '启用'], 1],
		])
		->setFormData($info)
		->fetch();
	}
	//删除计划
	public function delete($ids = null){
		if($ids == null) $this->error('参数错误');
		return $this->setStatus('delete');
	}

}
