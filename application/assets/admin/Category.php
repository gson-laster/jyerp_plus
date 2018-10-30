<?php
namespace app\assets\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\assets\model\Assets_category as Assets_categoryModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use think\Db;
/**
 * 资产类别
 * @author HJP
 */
class Category extends Admin
{
	public function index()
    {    	
        // 获取查询条件
        $map = $this->getMap();                
		// 数据列表
        $data_list = Assets_categoryModel::where($map)->paginate();
        // 分页数据
        $page = $data_list->render();
        // 使用ZBuilder快速创建数据表格
		return ZBuilder::make('table')
		->setPageTitle('类别列表') // 设置页面标题
		->addColumns([
			['id', 'ID'],
			['category','类别名称'],			
			['status','状态', 'switch'],			
			['right_button', '操作','btn'],
		])
		->addTopButtons('add,delete') // 批量添加顶部按钮
        ->addRightButtons(['edit', 'delete' => ['data-tips' => '删除资产类别无法恢复。']]) // 批量添加右侧按钮   
        ->setRowList($data_list) // 设置表格数据
		->fetch(); // 渲染页面
    }
    public function add()
    {
    	if($this->request->isPost()){
			$data = $this->request->post();
			// 验证
			//$result = $this->validate($data, 'Category');
			// 验证失败 输出错误信息
			//if(true !== $result) $this->error($result);
			if ($model = Assets_categoryModel::create($data)) {	
				//记录行为
            	action_log('category_add', 'asstes_category', $model['id'], UID);			              
                $this->success('新增成功', url('index'));
            } else {
                $this->error('新增失败');
            }
		}		
		// 使用ZBuilder快速创建表单
		return ZBuilder::make('form')
		->addFormItems([// 批量添加表单项			
			['text', 'category', '类别名称'],			
			['radio', 'status', '状态', '', ['禁用', '启用'], 1]
		])
		->fetch();
    }
    public function edit($id = null){
    	if($this->request->isPost()){
			$data = $this->request->post();
			// 验证失败 输出错误信息
			//if(true !== $result) $this->error($result);
			if ($model = Assets_categoryModel::update($data)) {	
				//记录行为
            	action_log('category_edit', 'asstes_category', $model['id'], UID);			              
                $this->success('修改成功', url('index'));
            } else {
                $this->error('修改失败');
            }
		}	
		 //查哪条
		$info = Assets_categoryModel::where('id', $id)->find();	
		// 使用ZBuilder快速创建表单
		return ZBuilder::make('form')
		->addFormItems([// 批量添加表单项
			['hidden', 'id'],			
			['text', 'category', '类别名称'],			
			['radio', 'status', '状态', '', ['禁用', '启用'], 1]
		])
		->setFormData($info)
		->fetch();
    }
    public function delete($ids = null){
		if ($ids === null) $this->error('参数错误');             
        return $this->setStatus('delete');
	}
}
 