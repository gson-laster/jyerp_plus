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
use app\personnel\model\Contract as ContractModel;

/**
 * 合同控制器
 * @package app\cms\admin
 */
class Contract extends Admin
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
        $order = $this->getOrder('personnel_contract.create_time desc');
        // 数据列表
        $data_list = ContractModel::getList($map,$order);

        //echo '<pre>';var_dump($data_list);exit;
        
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['admin_user.nickname' => '姓名']) // 设置搜索框
            ->hideCheckbox()
            ->addOrder('personnel_contract.id,personnel_contract.contract_time,admin_user.birth') // 添加排序
            //->addTimeFilter('personnel_record.in_time', '入职时间', '开始时间,结束时间')
            ->addTimeFilter('personnel_contract.contract_time', '签约时间', '开始时间,结束时间')
            ->addFilter('admin_user.role', RoleModel::getTree2())
            ->addFilter('admin_user.organization', OrganizationModel::getTree())
            ->addFilter('admin_user.position', PositionModel::getTree())
            ->addFilter('admin_user.is_on', ['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职']) 
            ->addFilter('personnel_contract.status', ['0'=>'过期','1'=>'有效','2'=>'解除'])
            ->addColumns([ // 批量添加数据列
                ['id', 'ID'],            	
            	['nickname', '姓名'],
            	['role', '角色',  RoleModel::getTree2()],
            	['organization', '部门', OrganizationModel::getTree()],
            	['position', '职位', PositionModel::getTree()],
            	['contract_type', '合同类型',  ['1'=>'固定期限劳动合同','2'=>'无固定期限劳动合同','3'=>'劳务派遣合同','4'=>'非全日制用工合同']],
            	['contract_time', '签订时间'],
            	['end_time', '合同到期'],
            	['test_time', '试用结束'],
            	['is_on', '在职状态', ['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职']],
            	['status', '合同状态',['0'=>'过期','1'=>'有效','2'=>'解除']],
            	['right_button', '操作', 'btn']
            ])
            ->addTopButtons('add') // 批量添加顶部按钮
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
            $result = $this->validate($data, 'Contract');
            if (true !== $result) $this->error($result);
            
            $data['contract_code'] = date('Ymd',time()).substr(time(),-4).'u'.$data['uid'];
            
            if ($contract = ContractModel::create($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.$contract['uid'].'),合同ID('.$contract['id'].')';
                action_log('personnel_contract_add', 'personnel_contract', $contract['id'], UID, $details);
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
			  content: '/admin.php/personnel/contract/choose'
			});		 
	});	
});
            </script>
EOF;
      
        // 显示添加页面
        return ZBuilder::make('form')
            ->addGroup(
				[					
					'新增合同' =>[
						['hidden', 'uid'],
						['text', 'nickname','选择用户'],
						['select', 'contract_type', '合同类型', '', ['1'=>'固定期限劳动合同','2'=>'无固定期限劳动合同','3'=>'劳务派遣合同','4'=>'非全日制用工合同'],1],
						['radio','is_fixed','是否期限固定','',['0'=>'否','1'=>'是'],1],								
						['date', 'contract_time', '签约时间'],
						['date', 'start_time', '开始日期'],
						['date', 'end_time', '结束日期'],
						['date', 'test_time', '试用结束日期'],
						['date', 'pend_time', '提前终止日期'],
						['textarea', 'code', '备注'],
						['files', 'enclosure', '附件'],
					]					
				]
			)
			->setTrigger('is_fixed', '1', 'end_time')
			->setExtraJs($js)
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
        $tep = ContractModel::where(['status'=>1])->column('uid');
       	$map['id'] = ['not in',$tep];
       
        $data_list = UserModel::where($map)->order($order)->paginate();

        // 分页数据
        $page = $data_list->render();

        $js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {   
	$('.table-builder input:checkbox').click(function(){
			var uid = $(this).val();
        	var nickname = $.trim($(this).parents('tr').find('td').eq(3).text());
			$("#uid",parent.document).val(uid);
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
                ['role', '角色',  RoleModel::getTree2()],
            	['organization', '部门', OrganizationModel::getTree()],
            	['position', '职位', PositionModel::getTree()],
                ['create_time', '创建时间', 'datetime'],
            	['is_on', '在职状态',['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职']],               
            ])
            ->setRowList($data_list) // 设置表格数据
            ->setExtraJs($js)
            ->assign('empty_tips', '暂无需要建档的用户')
            ->fetch('choose'); // 渲染页面
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
            $result = $this->validate($data, 'Contract');
            if (true !== $result) $this->error($result);

            if (ContractModel::update($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.$data['uid'].'),合同ID('.$data['id'].')';
                action_log('personnel_contract_edit', 'personnel_contract', $id, UID, $details);
                $this->success('编辑成功', 'index');
            } else {
                $this->error('编辑失败');
            }
        }
        

        $data_list = ContractModel::getOne($id);
        // 显示编辑页面
        return ZBuilder::make('form')           
            ->addGroup(
				[					
					'编辑合同' =>[
						['hidden', 'id'],
						['hidden', 'uid'],
						['static', 'nickname','用户'],
						['static', 'contract_code', '合同编号'],
						['select', 'contract_type', '合同类型', '', ['1'=>'固定期限劳动合同','2'=>'无固定期限劳动合同','3'=>'劳务派遣合同','4'=>'非全日制用工合同'],1],
						['number', 'num', '签约次数'],
						['radio','is_fixed','是否期限固定','',['0'=>'否','1'=>'是'],1],								
						['date', 'contract_time', '签约时间'],
						['date', 'start_time', '开始日期'],
						['date', 'end_time', '结束日期'],
						['date', 'test_time', '试用结束日期'],
						['date', 'pend_time', '提前终止日期'],
						['radio', 'status', '合同状态','',['0'=>'过期','1'=>'有效','2'=>'解除'],1],
						['textarea', 'code', '备注'],
						['files', 'enclosure', '附件'],
					]					
				]
			)
			->setTrigger('is_fixed', '1', 'end_time')
            ->setFormData($data_list)
            ->fetch();
    }
    
    /**
     * 删除合同
     * @param array $record 行为日志
     * @author 黄远东<641435071@qq.com>
     * @return mixed
     */
    public function delete($record = [])
    {
   		$id = $this->request->param('ids');
   		$contract = ContractModel::where('id', $id)->find();
   		if($contract['status'] == 1){
   			$this->error('合同仍在有效期，禁止删除');
   		}
    	// 删除节点
    	if (ContractModel::destroy($id)) {
    		// 记录行为
    		$details = '合同ID('.$id.')，用户ID('.$contract['uid'].')';
    		action_log('personnel_contract_delete', 'personnel_contract', $id, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }

}