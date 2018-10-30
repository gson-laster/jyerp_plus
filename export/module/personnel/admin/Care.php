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
use app\personnel\model\Care as CareModel;

/**
 * 员工关怀控制器
 * @package app\cms\admin
 */
class Care extends Admin
{
    /**
     * 员工关怀列表
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('personnel_care.create_time desc');
        // 数据列表
        $data_list = CareModel::getList($map,$order);
        //echo '<pre>';var_dump($data_list);exit;
              
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['admin_user.nickname' => '姓名']) // 设置搜索框
            ->addOrder('personnel_care.id,personnel_care.care_time') // 添加排序
            ->addTimeFilter('personnel_care.care_time', '关怀日期', '开始时间,结束时间')
            ->addFilter('admin_user.role', RoleModel::getTree2())
            ->addFilter('admin_user.organization', OrganizationModel::getTree())
            ->addFilter('admin_user.position', PositionModel::getTree())
            ->addColumns([ // 批量添加数据列
                ['id', 'ID'],            	
            	['nickname', '姓名'],
            	['role', '角色',  RoleModel::getTree2()],
            	['organization', '部门', OrganizationModel::getTree()],
            	['position', '职位', PositionModel::getTree()],            	
            	['care_type', '奖惩类型',['1'=>'节日关怀 ','2'=>'生日关怀','3'=>'其他']],
            	['money', '关怀费用'],
            	['holiday', '假期'],
            	['care_time', '关怀日期'],  
            	['status', '执行状态',['0'=>'待执行','1'=>'已执行']],
            	['right_button', '操作', 'btn']
            ])
            ->addTopButtons('add') // 批量添加顶部按钮
            ->addRightButton('enable',['title'=>'执行'])
            ->addRightButton('disable',['title'=>'取消'])
            ->addRightButtons('edit,delete')    
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
            
            // 验证
            $result = $this->validate($data, 'Care');
            if (true !== $result) $this->error($result);

            if ($care = CareModel::create($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.$care['uid'].'),关怀ID('.$care['id'].')';
                action_log('personnel_care_add', 'personnel_care', $care['id'], UID, $details);
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }
        
        $js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
	$('#nickname').click(function(){
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择用户',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/personnel/papers/choose'
			});		 
	});	
});
            </script>
EOF;
      
        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([
						['hidden', 'uid'],
						['text', 'nickname','选择用户'],
						['select', 'care_type', '奖惩类型', '',['1'=>'节日关怀 ','2'=>'生日关怀','3'=>'其他']],	
						['text', 'money','关怀费用','','0.00'],
            			['text', 'holiday','休假天数'],
						['text', 'good','关怀物品'],
						['date', 'care_time', '奖惩日期'],
						['textarea', 'code', '备注'],
						['radio', 'status', '执行状态','',['0'=>'待执行','1'=>'已完成'],0],
						['files', 'enclosure', '附件'],
				])
			->setExtraJs($js)
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
            $result = $this->validate($data, 'Care');
            if (true !== $result) $this->error($result);

            if (CareModel::update($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.$data['uid'].'),关怀ID('.$data['id'].')';
                action_log('personnel_care_edit', 'personnel_care', $id, UID, $details);
                $this->success('编辑成功', 'index');
            } else {
                $this->error('编辑失败');
            }
        }
        

        $data_list = CareModel::getOne($id);
        // 显示编辑页面
        return ZBuilder::make('form')           
            ->addFormItems([
						['hidden', 'id'],
						['hidden', 'uid'],
						['static', 'nickname','用户'],
						['select', 'care_type', '奖惩类型', '',['1'=>'节日关怀 ','2'=>'生日关怀','3'=>'其他']],	
						['text', 'money','关怀费用','','0.00'],
            			['text', 'holiday','休假天数'],
						['text', 'good','关怀物品'],
						['date', 'care_time', '奖惩日期'],
						['textarea', 'code', '备注'],
						['files', 'enclosure', '附件'],							
				])
            ->setFormData($data_list)
            ->fetch();
    }
    
    /**
     * 删除证件
     * @param array $record 行为日志
     * @author 黄远东<641435071@qq.com>
     * @return mixed
     */
    public function delete($record = [])
    {
   		$id = $this->request->param('ids');
   		if(!$care = CareModel::where('id', $id)->find()){
    	   	$this->error('缺少参数');	
   		}  		
    	// 删除节点
    	if (CareModel::destroy($id)) {
    		// 记录行为
    		$details = '关怀ID('.$id.')，用户ID('.$care['uid'].')';
    		action_log('personnel_care_delete', 'personnel_care', $id, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }

}