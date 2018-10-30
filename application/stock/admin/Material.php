<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\stock\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\stock\model\House as HouseModel;
use app\stock\model\Stock as StockModel;

/**
 * 基础物资控制器
 * @package app\cms\admin
 */
class Material extends Admin
{
    /**
     * 文档列表
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('create_time desc');
        // 数据列表
        $data_list = MaterialModel::getList($map,$order);

        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['name' => '物品名称']) // 设置搜索框
            ->addOrder('id,create_time') // 添加排序
            ->addFilter('type',MaterialTypeModel::getTree())
            ->addColumns([ // 批量添加数据列
                ['code', '物品编号'],
            	['name', '物品名称'],           	
            	['type', '物品类型',MaterialTypeModel::getTree()],
            	['version', '规格型号',],
				['unit','计量单位'],            	
            	['status', '启用状态', 'status'],
            	['right_button', '操作', 'btn']
            ])
            ->addTopButton('export', [
                'title' => '导出',
                'icon' => 'fa fa-sign-out',
                'class' => 'btn btn-primary',
                'href' => url('export', http_build_query($this->request->param()))
            ])
            ->addTopButton('import', [
                'title' => '导入',
                'icon' => 'fa fa-fw fa-sign-in',
                'class' => 'btn btn-primary',
                'href' => url('import')
            ],true)
            ->addTopButtons('add,delete') // 批量添加顶部按钮
            ->addRightButtons('edit,delete')
            ->setRowList($data_list) // 设置表格数据           
            ->fetch(); // 渲染模板
    }

    /**
     * 新增
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function add()
    {
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            //dump($data);die;
            // 验证
            $result = $this->validate($data, 'Material');
            if (true !== $result) $this->error($result);
			
            if ($materia = MaterialModel::create($data)) {				
                // 记录行为
            	$details    = '详情：物资ID('.$materia['id'].')';
                action_log('stock_material_add', 'stock_material', $materia['id'], UID, $details);
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }
        
        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([	
				['text','code','编号'],
				['text:3', 'name','物品名称'],						
				['select:3', 'type', '物品类型', '', MaterialTypeModel::getMenuTree()],
				['text:3', 'version','规格型号'],
				['text:3','unit','计量单位'],
				['text:3','funit','辅计量单位'],
				['number:3', 'weight', '重量'],
				['text:3', 'size', '尺寸'],
				['select:4', 'house_id', '主放仓库', '',HouseModel::getTree(),35],				
				['files', 'enclosure', '附件'],
				['textarea', 'explain', '说明'],
				['radio', 'status', '启用状态','',['0'=>'关闭','1'=>'启用'],1],				
			])
            ->fetch();
    }
    
    
    /**
     * 编辑
     * @param null $id id
     * @author 黄远东<6414350717@qq.com>
     * @return mixed
     */
    public function edit($id = null)
    {
        if ($id === null) $this->error('缺少参数');

        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            // 验证
            $result = $this->validate($data, 'Material');
            if (true !== $result) $this->error($result);

            if (MaterialModel::update($data)) {
                // 记录行为
            	$details    = '详情：物资ID('.$data['id'].')';
                action_log('stock_material_edit', 'stock_material', $id, UID, $details);
                $this->success('编辑成功', 'index');
            } else {
                $this->error('编辑失败');
            }
        }
        

        $data_list = MaterialModel::getOne($id);
        // 显示编辑页面
        return ZBuilder::make('form')
			->addFormItems([
				['hidden','id'],
				['text:3', 'name','物品名称'],						
				['select:3', 'type', '物品类型', '', MaterialTypeModel::getMenuTree()],
				['text:3', 'version','规格型号'],
				['text:3','unit','计量单位'],
				['text:3','funit','辅计量单位'],
				['number:3', 'weight', '重量'],
				['text:3', 'size', '尺寸'],
				['select:4', 'house_id', '主放仓库', '',HouseModel::getTree()],				
				['files', 'enclosure', '附件'],
				['textarea', 'explain', '说明'],
				['radio', 'status', '启用状态','',['0'=>'关闭','1'=>'启用']],					
			])		            
            ->setFormData($data_list)
            ->fetch();
    }
    /**
     * 删除
     * @param array $record 行为日志
     * @author 黄远东<641435071@qq.com>
     * @return mixed
     */
    public function delete($record = [])
    {
    	$ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    	// 删除节点
    	if (MaterialModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		$details = '物资ID('.$ids.'),操作人ID('.UID.')';
    		action_log('stock_material_delete', 'stock_material', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }

       //导出
   public function export()
    {
        $map = $this->getMap();
        //dump($map);die;
        
        $order = $this->getOrder();
        $data = MaterialModel::exportData($map,$order);
        $cellName = [
                ['name', 'auto','物品名称'],
                ['code', 'auto','物品编号'],
                ['type_name', 'auto','物品类型'],
                ['unit', 'auto','计量单位'],
				['funit','auto','辅计量单位'],
                ['version', 'auto','规格型号',],
				['weight', 'auto','重量'],
				['size', 'auto','尺寸'],
				['house_id', 'auto','主放仓库'],
				['explain', 'auto','说明'],
                ['status', 'auto','启用状态'],
                ['create_time', 'auto', "创建时间"]							
        ];
        plugin_action('Excel/Excel/export', ['materiallist', $cellName, $data]);
    }

    // //导入

    public function import()
    {
        // 提交数据
        if ($this->request->isPost()) {
            // 接收附件 ID
            $excel_file = $this->request->post('excel');
            // 获取附件 ID 完整路径
            $full_path = getcwd().get_file_path($excel_file);
            // 只导入的字段列表
            $fields = [
                'name'=>'物品名称',
                'code'=>'物品编号',
                'type'=>'物品类型',
                'unit'=>'单位',
                'version'=>'规格型号',
                'status'=>'启用状态',
                'create_time'=>"创建时间"
            ];
            // 调用插件('插件',[路径,导入表名,字段限制,类型,条件,重复数据检测字段])
            $import = plugin_action('Excel/Excel/import', [$full_path, 'stock_material', $fields, $type = 0, $where = null, $main_field = 'name']);
            // 失败或无数据导入
            if ($import['error']){
                $this->error($import['message']);
            }
            // 导入成功
            $this->success($import['message']);
        }
        // 创建演示用表单
        return ZBuilder::make('form')
            ->setPageTips('导入基础物资规则：<br><br>&nbsp;&nbsp;&nbsp;&nbsp;物品类型：必须与系统中物品类型一致<br>&nbsp;&nbsp;&nbsp;&nbsp;创建时间：格式 2018-01-01，不填默认获取当前时间')
            ->setPageTitle('导入Excel')
            ->addFormItems([ // 添加上传 Excel
                ['archive', 'excel', '示例','',29],
                ['file', 'excel', '上传文件'],
            ])
            ->fetch();
    }
}