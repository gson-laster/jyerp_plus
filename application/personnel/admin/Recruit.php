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

namespace app\personnel\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\user\model\Role as RoleModel;
use app\user\model\Organization as OrganizationModel;
use app\user\model\Position as PositionModel;
use app\personnel\model\Recruit as RecruitModel;

/**
 * 招聘控制器
 * @package app\cms\admin
 */
class Recruit extends Admin
{
    /**
     * 奖惩列表
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('personnel_recruit.create_time desc');
        // 数据列表
        $data_list = RecruitModel::getList($map,$order);
        //echo '<pre>';var_dump($data_list);exit;
        
       
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['admin_user.nickname' => '姓名']) // 设置搜索框
            ->addOrder('personnel_recruit.id,personnel_recruit.recruit_time') // 添加排序
            ->addFilter('admin_user.organization', OrganizationModel::getTree())
            ->addColumns([ // 批量添加数据列
                ['id', 'ID'],            	
            	['nickname', '申请人'],
            	['organization', '部门', OrganizationModel::getTree()],
            	['position', '职位', PositionModel::getTree()],            	
            	['title', '标题'],
            	['recruit_time', '到岗时间'],  
            	['status', '审核状态',['-1'=>'待审核','1'=>'已通过','0'=>'已拒绝']],
            	['right_button', '操作', 'btn']
            ])
            ->addTopButton('add',['title' => '添加申请']) // 批量添加顶部按钮
            ->addTopButton('enable',['title' => '通过'])
            ->addTopButton('disable',['title' => '拒绝'])
            ->addRightButton('enable',['title'=>'通过'])
            ->addRightButton('disable',['title'=>'拒绝'])
            ->addRightButton('edit',['icon'=>'fa fa-fw fa-search','title' => '详情'])
            ->addRightButtons('delete')    
            ->replaceRightButton(['status' => '1'], '', ['enable'])
            ->replaceRightButton(['status' => '0'], '', ['disable'])
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
            $data['uid'] = UID;
            // 验证
            $result = $this->validate($data, 'Recruit');
            if (true !== $result) $this->error($result);

            if ($recruit = RecruitModel::create($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.UID.'),招聘ID('.$recruit['id'].')';
                action_log('personnel_recruit_add', 'personnel_recruit', $recruit['id'], UID, $details);
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }
  
        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([					
						['text', 'title','招聘标题'],					
						['textarea', 'description', '描述'],
            			['date', 'recruit_time', '期望到岗时间'],   
	            		['ueditor', 'info', '招聘详情'],
            			['textarea', 'note', '备注'],
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
            $result = $this->validate($data, 'Recruit');
            if (true !== $result) $this->error($result);

            if (RecruitModel::update($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.UID.'),招聘ID('.$data['id'].')';
                action_log('personnel_recruit_edit', 'personnel_recruit', $id, UID, $details);
                $this->success('编辑成功', 'index');
            } else {
                $this->error('编辑失败');
            }
        }
        

        $data_list = RecruitModel::getOne($id);
        // 显示编辑页面
        return ZBuilder::make('form')           
            ->addFormItems([
						['hidden', 'id'],
						['text', 'title','招聘标题'],					
						['textarea', 'description', '描述'],
            			['date', 'recruit_time', '期望到岗时间'],   
	            		['ueditor', 'info', '招聘详情'],
            			['textarea', 'note', '备注'],							
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
   		$id = $this->request->param('ids');
   		if(!$recruit = RecruitModel::where('id', $id)->find()){
    	   	$this->error('缺少参数');	
   		}  		
    	// 删除节点
    	if (RecruitModel::destroy($id)) {
    		// 记录行为
    		$details = '招聘ID('.$id.')，用户ID('.$recruit['uid'].')';
    		action_log('personnel_recruit_delete', 'personnel_recruit', $id, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }

}