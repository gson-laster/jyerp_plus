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
use app\personnel\model\Papers as PapersModel;
use app\personnel\model\Papercat as PapercatModel;

/**
 * 合同控制器
 * @package app\cms\admin
 */
class Papers extends Admin
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
        $order = $this->getOrder('personnel_papers.create_time desc');
        // 数据列表
        $data_list = PapersModel::getList($map,$order);
        //echo '<pre>';var_dump($data_list);exit;
        
        $btn_cat = [
        		'title' => '证件类型',
        		'icon'  => 'fa fa-fw fa-list',
        		'class' => 'btn btn-success',
        		'href'  => url('papercat/index')
        ];
        
              
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['admin_user.nickname' => '姓名']) // 设置搜索框
            ->hideCheckbox()
            ->addOrder('personnel_papers.id,personnel_papers.end_time') // 添加排序
            //->addTimeFilter('personnel_record.in_time', '入职时间', '开始时间,结束时间')
            ->addTimeFilter('personnel_papers.paper_time', '取证时间', '开始时间,结束时间')
            ->addFilter('admin_user.role', RoleModel::getTree2())
            ->addFilter('admin_user.organization', OrganizationModel::getTree())
            ->addFilter('admin_user.position', PositionModel::getTree())
            ->addFilter('personnel_papers.paper_type', PapercatModel::getTree())
            ->addFilter('admin_user.is_on', ['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职']) 
            ->addColumns([ // 批量添加数据列
                ['id', 'ID'],            	
            	['nickname', '姓名'],
            	['role', '角色',  RoleModel::getTree2()],
            	['organization', '部门', OrganizationModel::getTree()],
            	['position', '职位', PositionModel::getTree()],
            	['paper_type', '证件类型', PapercatModel::getTree()],
            	['paper_code', '证件编号'],
            	['start_time', '生效日期'],
            	['end_time', '到期日期'],
            	['is_on', '在职状态', ['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职']],            	
            	['right_button', '操作', 'btn']
            ])
            ->addTopButtons('add') // 批量添加顶部按钮
            ->addTopButton('custom', $btn_cat,true)
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
            $result = $this->validate($data, 'Papers');
            if (true !== $result) $this->error($result);

            if ($paper = PapersModel::create($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.$paper['uid'].'),证件ID('.$paper['id'].')';
                action_log('personnel_papers_add', 'personnel_papers', $paper['id'], UID, $details);
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
            ->addGroup(
				[					
					'新增证件' =>[
						['hidden', 'uid'],
						['text', 'nickname','选择用户'],
						['select', 'paper_type', '证件类型', '',PapercatModel::getMenuTree()],	
						['text', 'paper_organization','发证机构'],
						['text', 'paper_code','证件编号'],
						['date', 'paper_time', '取证时间'],
						['date', 'start_time', '生效日期'],
						['date', 'end_time', '到期日期'],
						['textarea', 'code', '备注'],
						['files', 'enclosure', '附件'],
					]					
				]
			)
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
            $result = $this->validate($data, 'Papers');
            if (true !== $result) $this->error($result);

            if (PapersModel::update($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.$data['uid'].'),证件ID('.$data['id'].')';
                action_log('personnel_papers_edit', 'personnel_papers', $id, UID, $details);
                $this->success('编辑成功', 'index');
            } else {
                $this->error('编辑失败');
            }
        }
        

        $data_list = PapersModel::getOne($id);
        // 显示编辑页面
        return ZBuilder::make('form')           
            ->addGroup(
				[					
					'编辑证件' =>[
						['hidden', 'id'],
						['hidden', 'uid'],
						['static', 'nickname','用户'],
						['select', 'paper_type', '证件类型', '',PapercatModel::getMenuTree()],							
						['text', 'paper_organization','发证机构'],
						['text', 'paper_code','证件编号'],
						['date', 'paper_time', '取证时间'],
						['date', 'start_time', '生效日期'],
						['date', 'end_time', '到期日期'],
						['textarea', 'code', '备注'],
						['files', 'enclosure', '附件'],
					]					
				]
			)
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
   		if(!$paper = PapersModel::where('id', $id)->find()){
    	   	$this->error('缺少参数');	
   		}  		
    	// 删除节点
    	if (PapersModel::destroy($id)) {
    		// 记录行为
    		$details = '证件ID('.$id.')，用户ID('.$paper['uid'].')';
    		action_log('personnel_papers_delete', 'personnel_papers', $id, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }

}