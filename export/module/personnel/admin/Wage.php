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
use app\personnel\model\Wage as WageModel;
use app\personnel\model\Wagecate as WagecateModel;
use app\personnel\model\Wagelist as WagelistModel;

/**
 * 薪资控制器
 * @package app\cms\admin
 */
class Wage extends Admin
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
        $order = $this->getOrder('personnel_wage.create_time desc');
        // 数据列表
        $data_list = WageModel::getList($map,$order);

        $btn_wage = [
        		'title' => '薪资类型',
        		'icon'  => 'fa fa-fw fa-th-list',
        		'href'  => url('wagecate/index')
        ];
        
        $btn_view = [
					    'title' => '薪资详情',
					    'icon'  => 'fa fa-fw fa-search',
					    'href'  => url('view', ['uid' => '__uid__','type'=>'__wage_type__'])
					  ];
        
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['admin_user.nickname' => '姓名']) // 设置搜索框
            ->hideCheckbox()
            ->addOrder('personnel_wage.id,') // 添加排序
            ->addFilter('admin_user.sex', ['0'=>'保密','1'=>'男','2'=>'女'])
            ->addFilter('admin_user.role', RoleModel::getTree2())
            ->addFilter('admin_user.organization', OrganizationModel::getTree())
            ->addFilter('admin_user.position', PositionModel::getTree())
            ->addFilter('personnel_wage.wage_type', WagecateModel::getTree())
            ->addFilter('admin_user.is_on', ['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职'])
            ->addColumns([ // 批量添加数据列
                ['id', 'ID'],
            	['username', '用户名'],
            	['nickname', '姓名'],
            	['sex', '性别',['0'=>'保密','1'=>'男','2'=>'女']],
            	['birth', ' 出生日期', 'date'],
            	['role', '角色',  RoleModel::getTree2()],
            	['organization', '部门', OrganizationModel::getTree()],
            	['position', '职位', PositionModel::getTree()],
            	['wage_type', '工资类型',  WagecateModel::getTree()],           	
            	['is_on', '在职状态', ['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职']],
            	['right_button', '操作', 'btn']
            ])
            ->addTopButtons('add') // 批量添加顶部按钮
            ->addTopButton('wage',$btn_wage,true) // 批量添加顶部按钮
            ->addRightButton('edit',['title' => '薪资标准设定'])
            ->addRightButton('view',$btn_view)
            ->setRowList($data_list) // 设置表格数据
            
            ->fetch(); // 渲染模板
    }

    /**
     * 文档列表
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function view($uid = null,$type = 1)
    {
    	if($uid == null) $this->error('缺少参数');
    	// 查询
    	$map = $this->getMap();
    	$map['personnel_wagelist.uid'] = $uid;
    	// 排序
    	$order = $this->getOrder('personnel_wagelist.create_time desc');
    	// 数据列表    	
    	$data_list = WagelistModel::getList($map,$order);
   
    	$btn_view = [
    			'title' => '薪资详情',
    			'icon'  => 'fa fa-fw fa-search',
    			'href'  => url('wagelist/edit', ['id' => '__id__'])
    	];
    	
    	$btn_sign_right = [
    			'title' => '签到详情',
    			'icon'  => 'fa fa-fw fa-edit',
    			'href'  => url('sign/view', ['uid' => '__uid__','date'=>'__wage_time__'])
    	];
    	
    	$btn_add = [
    			'title' => '增加',
    			'icon'  => 'fa fa-plus-circle',
    			'class' => 'btn btn-primary',
    			'href'  => url('wagelist/add', ['uid' => $uid,'type'=>$type])
    	];
    	
    	$btn_sign_top = [
    			'title' => '签到详情',
    			'icon'  => 'fa fa-fw fa-edit',
    			'class' => 'btn btn-success',
    			'href'  => url('sign/view', ['uid' => $uid])
    	];
    	
    	if($type == 1){
    		$data = [
    				['id', 'ID'],
	    			['username', '用户名'],
	    			['nickname', '姓名'],
	    			['organization', '部门', OrganizationModel::getTree()],
	    			['position', '职位', PositionModel::getTree()],
	    			['base_pay', '基础工资'],
	    			['merit_pay', '绩效工资'],
    				['extro_pay','其他工资'],
	    			['total_pay', '总工资'],
    				['wage_time', '月份'],  
	    			['right_button', '操作', 'btn']    				
    		 ] ;  		
    	}
    	else if($type == 2){
    		$data = [
    				['id', 'ID'],
	    			['username', '用户名'],
	    			['nickname', '姓名'],
	    			['organization', '部门', OrganizationModel::getTree()],
	    			['position', '职位', PositionModel::getTree()],
	    			['base_pay', '基础工资'],
	    			['piece_pay', '计件工资'],
    				['extro_pay','其他工资'],
	    			['total_pay', '总工资'],
    				['wage_time', '月份'],  
	    			['right_button', '操作', 'btn']    				
    		 ] ; 
    		
    	}    	    
    	// 使用ZBuilder快速创建数据表格
    	return ZBuilder::make('table')
    	->setSearch(['admin_user.nickname' => '姓名']) // 设置搜索框
    	->hideCheckbox()
    	->addOrder('personnel_wagelist.id,') // 添加排序
    	->addFilter('admin_user.sex', ['0'=>'保密','1'=>'男','2'=>'女'])
    	->addFilter('admin_user.organization', OrganizationModel::getTree())
    	->addFilter('admin_user.position', PositionModel::getTree())
    	->addFilter('admin_user.is_on', ['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职'])
    	->addColumns($data)
    	->addTopButton('list_add',$btn_add) // 批量添加顶部按钮
    	->addTopButton('sign_top',$btn_sign_top,true)
    	->addRightButton('view',$btn_view,true)
    	->addRightButton('sign_right',$btn_sign_right,true)
    	->addRightButton('delete')
    	->setRowList($data_list) // 设置表格数据    
    	->setTableName('personnel_wagelist')
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
            $result = $this->validate($data, 'Wage');
            if (true !== $result) $this->error($result);
 
            if ($wage = WageModel::create($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.$wage['uid'].'),档案ID('.$wage['id'].')';
                action_log('personnel_wage_add', 'personnel_wage', $wage['id'], UID, $details);
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
			  content: '/admin.php/personnel/wage/choose'
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
						['select', 'wage_type', '工资类型', '', WagecateModel::getTree()],
           				['text', 'base_pay','基本工资','单位/元','0.00'],
           				['text', 'merit_pay','绩效工资','单位/元','0.00'],
           				['text', 'piece_pay','计件工资','每单位工资','0.00'],
           				['text', 'unit','数量单位'],
			])
			->setTrigger('wage_type', '1', 'merit_pay')
			->setTrigger('wage_type', '2', 'piece_pay,unit')
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
        $temp = WageModel::column('uid');
       	$map['id'] = ['not in',$temp];
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
            ->addFilter('role', RoleModel::getTree(null, false))
            ->addFilter('organization', OrganizationModel::getTree(null, false))
            ->addFilter('position', PositionModel::getTree(null, false))
            ->addFilter('is_on', ['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职'])
            ->addColumns([ // 批量添加列
                ['id', 'ID'],
                ['username', '用户名'],
                ['nickname', '姓名'],
                ['role', '角色',  RoleModel::getTree(null, false)],
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
            $result = $this->validate($data, 'Wage');
            if (true !== $result) $this->error($result);

            if (WageModel::update($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.$data['uid'].'),档案ID('.$data['id'].')';
                action_log('personnel_wage_edit', 'personnel_wage', $id, UID, $details);
                $this->success('编辑成功', 'index');
            } else {
                $this->error('编辑失败');
            }
        }
        

        $data_list = WageModel::getOne($id);
        // 显示编辑页面
        return ZBuilder::make('form')           
           ->addFormItems([
						['hidden', 'id'],
						['hidden', 'uid'],
						['static', 'username', '用户名'],
               			['static', 'nickname', '姓名'],
               			['select', 'role', '角色', '', RoleModel::getTree(null, false)],
            			['select', 'organization', '部门', '', OrganizationModel::getMenuTree(0, '')],
            			['select', 'position', '职位', '', PositionModel::getMenuTree(0, '')], 
           				['select', 'wage_type', '工资类型', '', WagecateModel::getTree()],
           				['text', 'base_pay','基本工资','单位/元','0.00'],
           				['text', 'merit_pay','绩效工资','单位/元','0.00'],
           				['text', 'piece_pay','计件工资','每单位工资','0.00'],
           				['text', 'unit','数量单位'],
			])
			->setTrigger('wage_type', '1', 'merit_pay')
			->setTrigger('wage_type', '2', 'piece_pay,unit')
            ->setFormData($data_list)
            ->fetch();
    }

}