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
use app\user\model\User as UserModel;
use app\user\model\Role as RoleModel;
use app\user\model\Organization as OrganizationModel;
use app\user\model\Position as PositionModel;
use app\produce\model\Workcenter as WorkcenterModel;


/**
 * 工作中心控制器
 * @package app\produce\admin
 */
class Workcenter extends Admin
{
    /**
     * 工作中心列表
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('produce_workcenter.create_time desc');
        // 数据列表
        $data_list = WorkcenterModel::getList($map,$order);
           
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['produce_workcenter.name' => '工作中心名称','admin_user.nickname' => '建档人']) // 设置搜索框
            ->addOrder('produce_workcenter.create_time') // 添加排序                     
            ->addFilter('admin_organization.title')
            ->addFilter('produce_workcenter.is_key',['0'=>'否','1'=>'是'])
            ->addColumns([ // 批量添加数据列
                ['__INDEX__', '序号'], 
            	['name', '工作中心名称'],
            	['code', '工作中心代码'],
            	['org_id', '所属部门', OrganizationModel::getTree()],
            	['uid', '建档人'],
            	['header', '责任人'],
            	['is_key', '是否为关键中心','status','',['0'=>'否','1'=>'是']],
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
            $result = $this->validate($data, 'Workcenter');
            if (true !== $result) $this->error($result);
			$data['uid']=UID;
			$data['header'] = $data['zrid'];
            if ($result = WorkcenterModel::create($data)) {
                // 记录行为
            	$details    = '详情：中心ID('.$result['id'].'),建档人ID('.$result['uid'].'),责任人ID('.$result['header'].')';
                action_log('produce_workcenter_add', 'produce_workcenter', $result['id'], UID, $details);
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }
        
      
        // 显示添加页面
        return ZBuilder::make('form')
        	->setPageTitle('新建工作中心')
            ->addFormItems([
						['hidden', 'header'],  
						['hidden','zrid'],
            			['text', 'name','工作中心名称'],
            			['text', 'code','工作中心编号'],
            			['text', 'zrname','选择中心责任人'],
						['select', 'org_id','所属部门','',OrganizationModel::getTree()],
            			['wangeditor', 'description', '车间描述'],
            			['textarea', 'note', '备注'],
            			['radio', 'is_key', '是否为关键中心','',['0'=>'否','1'=>'是'],0],
						['radio', 'status', '启用状态','',['0'=>'关闭','1'=>'启用'],1],
				])
			->setExtraHtml(outhtml2())
            ->setExtraJs(outjs2())
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
            $result = $this->validate($data, 'Workcenter');
            if (true !== $result) $this->error($result);

            if (WorkcenterModel::update($data)) {
                // 记录行为
            	$details    = '详情：中心ID('.$data['id'].'),修改人ID('.UID.')';
                action_log('produce_workcenter_edit', 'produce_workcenter', $id, UID, $details);
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }
        

        $data_list = WorkcenterModel::getOne($id);
        // 显示编辑页面
        return ZBuilder::make('form')   
        	->setPageTitle('修改工作中心')
            ->addFormItems([
						['hidden', 'id'],
						['hidden','zrid'],
						['hidden', 'header'],            		
            			['text', 'name','工作中心名称'],
            			['text', 'code','工作中心编号'],
            			['text', 'zrname','选择中心责任人'],
						['select', 'org_id','所属部门','',OrganizationModel::getTree()],
            			['wangeditor', 'description', '车间描述'],
            			['textarea', 'note', '备注'],
            			['radio', 'is_key', '是否为关键中心','',['0'=>'否','1'=>'是'],0],
						['radio', 'status', '启用状态','',['0'=>'关闭','1'=>'启用'],1],								
				])
            ->setFormData($data_list)
            ->setExtraHtml(outhtml2())
            ->setExtraJs(outjs2())
            ->fetch();
    }
    
    /**
     * 弹出用户列表
     * @author 黄远东 <641435071@qq.com>
     */
    public function choose()
    {
    	// 获取查询条件
    	$map = $this->getMap();
    	$order = $this->getOrder();
    	// 数据列表   	 
    	$data_list = UserModel::where($map)->order($order)->paginate();    
    	// 分页数据
    	$page = $data_list->render();
    
    	$js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
	$('.table-builder input:checkbox').click(function(){
			var uid = $(this).val();
        	var nickname = $.trim($(this).parents('tr').find('td').eq(3).text());
			$("#header",parent.document).val(uid);
        	$("#nickname",parent.document).val(nickname);
			//当你在iframe页面关闭自身时
			var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
			parent.layer.close(index); //再执行关闭
	});
});
            </script>
EOF;
    
    	// 使用ZBuilder快速创建数据表格
    	return ZBuilder::make('table')
    	->setTableName('admin_user') // 设置数据表名
    	->setSearch(['id' => 'ID', 'username' => '用户名', 'nickname' => '姓名']) // 设置搜索参数
    	->addOrder('id,role,organization,position,is_on')
    	->addFilter('role', RoleModel::getTree2())
    	->addFilter('organization', OrganizationModel::getTree(null, false))
    	->addFilter('position', PositionModel::getTree(null, false))
    	->addFilter('is_on', ['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职'])
    	->addColumns([ // 批量添加列
    			['id', 'ID'],
    			['username', '用户名'],
    			['nickname', '姓名'],
    			['role', '角色', RoleModel::getTree2()],
    			['organization', '部门', OrganizationModel::getTree()],
    			['position', '职位', PositionModel::getTree()],
    			['create_time', '创建时间', 'datetime'],
    			['is_on', '在职状态',['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职']],
    	])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js)
    	->assign('empty_tips', '暂无需要添加证件的用户')
    	->fetch('choose'); // 渲染页面
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
    	if (WorkcenterModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		$details = '中心ID('.$ids.'),操作人ID('.UID.')';
    		action_log('produce_workcenter_delete', 'produce_workcenter', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }

}