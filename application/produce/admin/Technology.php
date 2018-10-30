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

namespace app\produce\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\produce\model\Technology as TechnologyModel;


/**
 * 工艺档案控制器
 * @package app\produce\admin
 */
class Technology extends Admin
{
    /**
     * 工艺档案列表
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('produce_technology.create_time desc');
        // 数据列表
        $data_list = TechnologyModel::getList($map,$order);
             
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['produce_technology.name' => '工艺名称','admin_user.nickname' => '建档人']) // 设置搜索框
            ->addOrder('produce_technology.create_time') // 添加排序                     
            ->addColumns([ // 批量添加数据列
                ['__INDEX__', '序号'], 
            	['name', '工艺名称'],
            	['code', '工艺代码'],
            	['nickname', '建档人'],
            	['create_time', '建档时间','datetime'],
            	['status', '启用状态状态','switch','',['0'=>'关闭','1'=>'启用']],
            	['right_button', '操作', 'btn']
            ])
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
            
            // 验证
            $result = $this->validate($data, 'Technology');
            if (true !== $result) $this->error($result);
			$data['uid']=UID;
            if ($result = TechnologyModel::create($data)) {
                // 记录行为
            	$details    = '详情：工艺ID('.$result['id'].'),建档人ID('.$result['uid'].')';
                action_log('produce_technology_add', 'produce_technology', $result['id'], UID, $details);
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }

        // 显示添加页面
        return ZBuilder::make('form')
        	->setPageTitle('新建工艺档案')
            ->addFormItems([						       		
            			['text', 'name','工艺名称'],
            			['text', 'code','工艺代码'],
            			['wangeditor', 'description', '工艺描述'],
            			['textarea', 'note', '备注'],
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
            $result = $this->validate($data, 'Technology');
            if (true !== $result) $this->error($result);

            if (TechnologyModel::update($data)) {
                // 记录行为
            	$details    = '详情：工艺ID('.$data['id'].'),修改人ID('.UID.')';
                action_log('produce_technology_edit', 'produce_technology', $id, UID, $details);
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }
       
        $data_list = TechnologyModel::getOne($id);
        // 显示编辑页面
        return ZBuilder::make('form')   
        	->setPageTitle('修改工艺档案')
            ->addFormItems([
						['hidden', 'id'],           		
            			['text', 'name','工艺名称'],
            			['text', 'code','工艺代码'],
            			['wangeditor', 'description', '工艺描述'],
            			['textarea', 'note', '备注'],
						['radio', 'status', '启用状态','',['0'=>'关闭','1'=>'启用'],1],								
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
    	if (TechnologyModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		$details = '工艺ID('.$ids.'),操作人ID('.UID.')';
    		action_log('produce_technology_delete', 'produce_technology', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }

}