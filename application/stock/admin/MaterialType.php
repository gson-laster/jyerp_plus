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
use app\stock\model\MaterialType as MaterialTypeModel;
use util\Tree;
use think\Db;

/**
 * 物资类型控制器
 * @package app\stock\admin;
 */
class MaterialType extends Admin
{
    /**
     * 物资类型首页    
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {       
        // 配置分组信息
        $list_group = MaterialTypeModel::getGroup();
        $tab_list = array();
        foreach ($list_group as $key => $value) {
            $tab_list[$key]['title'] = $value;    
        }
        // 获取节点数据
        $data_list = MaterialTypeModel::getMenusByGroup();

        $max_level = $this->request->get('max', 0);

        $this->assign('menus', $this->getNestMenu($data_list, $max_level));
       

        $this->assign('tab_nav', ['tab_list' => $tab_list]);
        $this->assign('page_title', '类型管理');
        return $this->fetch();
    }

    /**
     * 新增
     * @param string $pid 所属节点id
      * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function add($pid = 0)
    {
        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post('', null, 'trim');
            // 验证
            $result = $this->validate($data, 'MaterialType');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if ($menu = MaterialTypeModel::create($data)) {
                // 记录行为
                $details = '类型ID('.$menu['id'].'),类型标题('.$menu['title'].')';
                action_log('stock_material_type_add', 'stock_material_type', $menu['id'], UID, $details);
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }

        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('新增类型')           
            ->addFormItems([
                ['select', 'pid', '父类型', '所属父类型', MaterialTypeModel::getMenuTree(), $pid],
                ['text', 'title', '类型名称','<span class="text-danger">必填</span>'],
            	['text', 'code', '类型编号'],
            	['number', 'sort', '排序', '', 100],
            ])           
            ->fetch();
    }

    /**
     * 编辑
     * @param int $id 节点ID
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function edit($id = 0)
    {
        if ($id === 0) $this->error('缺少参数');

        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post('', null, 'trim');

            // 验证
            $result = $this->validate($data, 'MaterialType');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if (MaterialTypeModel::update($data)) {               
                // 记录行为
                $details = '类型ID('.$id.')';
                action_log('stock_material_type_edit', 'stock_material_type', $id, UID, $details);
                $this->success('编辑成功','index');
            } else {
                $this->error('编辑失败');
            }
        }

        // 获取数据
        $info = MaterialTypeModel::get($id);


        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('编辑类型')
            ->addFormItems([
            		['hidden','id'],
            		['select', 'pid', '父类型', '所属父类型', MaterialTypeModel::getMenuTree()],
	                ['text', 'title', '类型名称','<span class="text-danger">必填</span>'],
	            	['text', 'code', '类型编号'],
	            	['number', 'sort', '排序', '', 100],
            ])
            ->setFormData($info)
            ->fetch();
    }
    
    /**
     * 删除
     * @param array $record 行为日志内容
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function delete($record = [])
    {
    	$id = $this->request->param('id');
    	$menu = MaterialTypeModel::where('id', $id)->find();
    
    	// 获取该节点的所有后辈节点id
    	$menu_childs = MaterialTypeModel::getChildsId($id);
    
    	// 要删除的所有节点id
    	$all_ids = array_merge([(int)$id], $menu_childs);   	
    	// 删除节点
    	if (MaterialTypeModel::destroy($all_ids)) {
    		// 记录行为
    		$details = '部门ID('.$id.'),部门标题('.$menu['title'].')';
    		action_log('organization_delete', 'admin_organization', $id, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }
      
    /**
     * 获取嵌套式节点
     * @param array $lists 原始节点数组
     * @param int $pid 父级id
     * @param int $max_level 最多返回多少层，0为不限制
     * @param int $curr_level 当前层数
     * @author 黄远东 <641435071@qq.com>
     * @return string
     */
	private function getNestMenu($lists = [], $max_level = 0, $pid = 0, $curr_level = 1)
    {
        $result = '';
        foreach ($lists as $key => $value) {
            if ($value['pid'] == $pid) {
                $disable  = $value['status'] == 0 ? 'dd-disable' : '';

                // 组合节点
                $result .= '<li class="dd-item dd3-item '.$disable.'" data-id="'.$value['id'].'">';
                $result .= '<div class="dd-handle dd3-handle">拖拽</div><div class="dd3-content">'.$value['title'];
                
                $result .= '<div class="action">';
                $result .= '<a href="'.url('add', ['pid' => $value['id']]).'" data-toggle="tooltip" data-original-title="新增子类型"><i class="list-icon fa fa-plus fa-fw"></i></a><a href="'.url('edit', ['id' => $value['id']]).'" data-toggle="tooltip" data-original-title="编辑"><i class="list-icon fa fa-pencil fa-fw"></i></a>';

                $result .= '<a href="'.url('delete', ['id' => $value['id'], 'table' => 'stock_material_type']).'" data-toggle="tooltip" data-original-title="删除" class="ajax-get confirm"><i class="list-icon fa fa-times fa-fw"></i></a></div>';
               
                $result .= '</div>';

                if ($max_level == 0 || $curr_level != $max_level) {
                    unset($lists[$key]);
                    // 下级节点
                    $children = $this->getNestMenu($lists, $max_level, $value['id'], $curr_level + 1);
                    if ($children != '') {
                        $result .= '<ol class="dd-list">'.$children.'</ol>';
                    }
                }

                $result .= '</li>';
            }
        }
        return $result;
    }
}
