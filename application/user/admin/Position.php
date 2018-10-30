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

namespace app\user\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\Position as PositionModel;
use app\user\model\Organization as OrganizationModel;
use util\Tree;
use think\Db;

/**
 * 职位控制器
 * @package app\admin\controller
 */
class Position extends Admin
{
    /**
     * 节点首页
     * @param string $group 分组
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index($oid = null)
    {
    	if($oid == null) $this->error("缺少参数");
        // 保存模块排序
        if ($this->request->isPost()) {
            $modules = $this->request->post('sort/a');
            if ($modules) {
                $data = [];
                foreach ($modules as $key => $module) {
                    $data[] = [
                        'id'   => $module,
                        'sort' => $key + 1
                    ];
                }
                $MenuModel = new PositionModel();
                if (false !== $MenuModel->saveAll($data)) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            }
        }

        cookie('__forward__', $_SERVER['REQUEST_URI']);
        // 配置分组信息
        $list_group = PositionModel::getGroup();
        if(empty($list_group)) $this->error('请先添加部门',url('user/organization/index'));
        foreach ($list_group as $key => $value) {
            $tab_list[$key]['title'] = $value; 
            $tab_list[$key]['url']  = url('index', ['oid' => $key]);
        }
  		$map = $oid ? ['oid'=>$oid]:[];
        // 获取节点数据
        $data_list = PositionModel::getMenusByGroup($oid);
        $max_level = $this->request->get('max', 0);
        $this->assign('menus', $this->getNestMenu($data_list, $max_level));       
        $this->assign('tab_nav', ['tab_list' => $tab_list ,'curr_tab' => $oid]);
        $this->assign('page_title', '职位管理');
        return $this->fetch();
    }

    /**
     * 新增节点
     * @param string $oid 所属部门
     * @param string $pid 所属节点id
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function add($oid = '',$pid = '')
    {
    	if($oid == '') $this->error("缺少参数");
        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post('', null, 'trim');
            // 验证
            $result = $this->validate($data, 'Position');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            $data['oid'] = $oid;
            if ($menu = PositionModel::create($data)) {
                // 记录行为
                $details = '所属部门ID('.$data['oid'].'),职位标题('.$data['title'].')';
                action_log('position_add', 'admin_position', $menu['id'], UID, $details);
                $this->success('新增成功', cookie('__forward__'));
            } else {
                $this->error('新增失败');
            }
        }

        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('新增职位')           
            ->addFormItems([
                ['select', 'pid', '所属上级', '所属上级节点', PositionModel::getMenuTree2(0, '',true), $pid],
                ['text', 'title', '职位名称','<span class="text-danger">必选</span>'],
            	['text', 'pos_rank', '职位等级'],
            	['number', 'num', '编制人数','<span class="text-danger">选填，0为不做人数限制</span>',0],            	
            	['textarea', 'pos_res', '岗位职责'],
            	['textarea', 'pos_req', '岗位要求'],
            	['textarea', 'pos_des', '岗位描述'],
            	['number', 'sort', '排序', '', 100],
            ])           
            ->fetch();
    }

    /**
     * 编辑节点
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
            $result = $this->validate($data, 'Position');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if (PositionModel::update($data)) {               
                // 记录行为
                $details = '职位ID('.$id.')';
                action_log('position_edit', 'admin_position', $id, UID, $details);
                $this->success('编辑成功', cookie('__forward__'));
            } else {
                $this->error('编辑失败');
            }
        }

        // 获取数据
        $info = PositionModel::get($id);


        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('编辑部门')
            ->addFormItems([
            		['hidden','id'],
            		['select', 'pid', '所属上级', '所属上级节点', PositionModel::getMenuTree2(0, '',true)],
            		['text', 'title', '职位名称','<span class="text-danger">必选</span>'],
            		['text', 'pos_rank', '职位等级'],
            		['number', 'num', '编制人数','<span class="text-danger">选填，0为不做人数限制</span>',0],            	
            		['textarea', 'pos_res', '岗位职责'],
            		['textarea', 'pos_req', '岗位要求'],
            		['textarea', 'pos_des', '岗位描述'],
            		['number', 'sort', '排序', '', 100],
            ])
            ->setFormData($info)
            ->fetch();
    }
    
    /**
     * 删除节点
     * @param array $record 行为日志内容
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function delete($record = [])
    {
    	$id = $this->request->param('id');
    	if($id == 1) $this->error('系统模型，无法删除，只能修改');
    	$menu = PositionModel::where('id', $id)->find();
    	
    	// 获取该节点的所有后辈节点id
    	$menu_childs = PositionModel::getChildsId($id);
    
    	// 要删除的所有节点id
    	$all_ids = array_merge([(int)$id], $menu_childs);   	
    	// 删除节点
    	if (PositionModel::destroy($all_ids)) {
    		// 记录行为
    		$details = '职位ID('.$id.'),职位标题('.$menu['title'].')';
    		action_log('position_delete', 'admin_position', $id, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }
    
    /**
     * 保存节点排序
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
	public function save()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (!empty($data)) {
                $menus = $this->parseMenu($data['menus']);
                foreach ($menus as $menu) {
//                     if ($menu['pid'] == 0) {
//                         continue;
//                     }                                   
                    PositionModel::update($menu);
                }
                $this->success('保存成功');
            } else {
                $this->error('没有需要保存的节点');
            }
        }
        $this->error('非法请求');
    }
    
    /**
     * 添加子节点
     * @param array $data 节点数据
     * @param string $pid 父节点id
     * @author 蔡伟明 <314013107@qq.com>
     */
    private function createChildNode($data = [], $pid = '')
    {
    	$url_value  = substr($data['url_value'], 0, strrpos($data['url_value'], '/')).'/';
    	$child_node = [];
    	$data['pid'] = $pid;
    
    	foreach ($data['child_node'] as $item) {
    		switch ($item) {
    			case 'add':
    				$data['title'] = '新增';
    				break;
    			case 'edit':
    				$data['title'] = '编辑';
    				break;
    			case 'delete':
    				$data['title'] = '删除';
    				break;
    			case 'enable':
    				$data['title'] = '启用';
    				break;
    			case 'disable':
    				$data['title'] = '禁用';
    				break;
    			case 'quickedit':
    				$data['title'] = '快速编辑';
    				break;
    		}
    		$data['url_value']   = $url_value.$item;
    		$data['create_time'] = $this->request->time();
    		$data['update_time'] = $this->request->time();
    		$child_node[] = $data;
    	}
    
    	if ($child_node) {
    		$MenuModel = new PositionModel();
    		$MenuModel->insertAll($child_node);
    	}
    }
    
    /**
     * 递归解析节点
     * @param array $menus 节点数据
     * @param int $pid 上级节点id
     * @author 蔡伟明 <314013107@qq.com>
     * @return array 解析成可以写入数据库的格式
     */
    private function parseMenu($menus = [], $pid = 0)
    {
    	$sort   = 1;
    	$result = [];
    	foreach ($menus as $menu) {
    		$result[] = [
    				'id'   => (int)$menu['id'],
    				'pid'  => (int)$pid,
    				'sort' => $sort,
    		];
    		if (isset($menu['children'])) {
    			$result = array_merge($result, $this->parseMenu($menu['children'], $menu['id']));
    		}
    		$sort ++;
    	}
    	return $result;
    }
    
    /**
     * 获取嵌套式节点
     * @param array $lists 原始节点数组
     * @param int $pid 父级id
     * @param int $max_level 最多返回多少层，0为不限制
     * @param int $curr_level 当前层数
     * @author 蔡伟明 <314013107@qq.com>
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
                $result .= '<a href="'.url('add', ['oid' => $value['oid'],'pid' => $value['id']]).'" data-toggle="tooltip" data-original-title="新增子节点"><i class="list-icon fa fa-plus fa-fw"></i></a><a href="'.url('edit', ['id' => $value['id']]).'" data-toggle="tooltip" data-original-title="编辑"><i class="list-icon fa fa-pencil fa-fw"></i></a>';
                if ($value['status'] == 0) {
                    // 启用
                    $result .= '<a href="javascript:void(0);" data-ids="'.$value['id'].'" class="enable" data-toggle="tooltip" data-original-title="启用"><i class="list-icon fa fa-check-circle-o fa-fw"></i></a>';
                } else {
                    // 禁用
                    $result .= '<a href="javascript:void(0);" data-ids="'.$value['id'].'" class="disable" data-toggle="tooltip" data-original-title="禁用"><i class="list-icon fa fa-ban fa-fw"></i></a>';
                }                               
                $result .= '<a href="'.url('delete', ['id' => $value['id'], 'table' => 'admin_organization']).'" data-toggle="tooltip" data-original-title="删除" class="ajax-get confirm"><i class="list-icon fa fa-times fa-fw"></i></a></div>';
               
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
    
    /**
     * 启用节点
     * @param array $record 行为日志
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function enable($record = [])
    {
        $id      = input('param.ids');
        if($category = PositionModel::where('id', $id)->find()){
        	$details = '部门ID('.$id.'),部门标题('.$category['title'].')';
        	if(PositionModel::where('id', $id)->update(['status'=>1])){       		
        		action_log('position_enable', 'admin_position', $id, UID, $details);
        		$this->success('操作成功');
        	}else{
        		$this->error('操作失败');        	
        	}        	
        }else{
        	$this->error('找不到该条记录');
        }     
    }

    /**
     * 禁用节点
     * @param array $record 行为日志
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function disable($record = [])
    {
    	$id      = input('param.ids');
    	if($category = PositionModel::where('id', $id)->find()){
    		$details = '职位ID('.$id.'),职位标题('.$category['title'].')';
    		if(PositionModel::where('id', $id)->update(['status'=>0])){
    			action_log('position_disable', 'admin_position', $id, UID, $details);
    			$this->success('操作成功');
    		}else{
    			$this->error('操作失败');
    		}
    	}else{
    		$this->error('找不到该条记录');
    	}    	       
    }
}
