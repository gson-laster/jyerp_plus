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
use app\personnel\model\Record as RecordModel;

/**
 * 文档控制器
 * @package app\cms\admin
 */
class Record extends Admin
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
        $order = $this->getOrder('personnel_record.create_time desc');
        // 数据列表
        $data_list = RecordModel::getList($map,$order);

        //echo '<pre>';var_dump($data_list);exit;
        
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['admin_user.nickname' => '姓名']) // 设置搜索框
            ->hideCheckbox()
            ->addOrder('personnel_record.id,personnel_record.in_time,admin_user.birth') // 添加排序
            //->addTimeFilter('personnel_record.in_time', '入职时间', '开始时间,结束时间')
            ->addTimeFilter('personnel_record.zz_time', '转正时间', '开始时间,结束时间')
            ->addFilter('admin_user.sex', ['0'=>'保密','1'=>'男','2'=>'女'])
            ->addFilter('admin_user.role', RoleModel::getTree2())
            ->addFilter('admin_user.organization', OrganizationModel::getTree())
            ->addFilter('admin_user.position', PositionModel::getTree())
            ->addFilter('personnel_record.con_type', ['1'=>'合同工','2'=>'正式员工','3'=>'临时工'])
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
            	['con_type', '员工类型',  ['1'=>'合同工','2'=>'正式员工','3'=>'临时工']],
            	['in_time', '入职时间'],
            	['is_on', '在职状态', ['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职']],
            	['status', '账号状态', 'switch'],
            	['right_button', '操作', 'btn']
            ])
            ->addTopButtons('add') // 批量添加顶部按钮
            ->addRightButtons('edit')
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
            $result = $this->validate($data, 'Record');
            if (true !== $result) $this->error($result);
            
            $data['record_code'] = date('Ymd',time()).substr(time(),-4).'u'.$data['uid'];
            
            if ($record = RecordModel::create($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.$record['uid'].'),档案ID('.$record['id'].')';
                action_log('personnel_record_add', 'personnel_record', $record['id'], UID, $details);
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
			  content: '/admin.php/personnel/record/choose'
			});		 
	});	
});
		
		$('input[name="in_time"]').change(function(){
			fn($(this));
		})
		$('input[name="zz_time"]').change(function(){
			fn($(this));
		})
		$('input[name="lz_time"]').change(function(){
			fn($(this));
		})
		function fn(o){
			if(new Date($('input[name="in_time"]').val()).getTime() > new Date($('input[name="zz_time"]').val()).getTime() || new Date($('input[name="zz_time"]').val()).getTime() > new Date($('input[name="lz_time"]').val()).getTime()|| new Date($('input[name="in_time"]')).getTime() > new Date($('input[name="lz_time"]')).getTime()) {
				layer.msg('入职时间小于转正时间小于离职时间', {
				  icon: 2,
				  time: 2000 
				});
				o.val('');
			}
		}
            </script>
EOF;
      
        // 显示添加页面
        return ZBuilder::make('form')
            ->addGroup(
				[					
					'档案信息' =>[
						['hidden', 'uid'],
						['text', 'nickname','选择用户'],
						['select', 'con_type', '员工类型', '', ['1'=> '合同工', '2'=> '正式员工', '3'=> '临时工'],1],
						['date', 'in_time', '入职时间'],
						['date', 'zz_time', '转正时间'],
						['date', 'lz_time', '离职时间'],
						['text', 'person_card', '身份证号码'],
						['select', 'hukou_type', '户口类型', '', ['1'=> '本地城镇', '2'=> ' 外地城镇', '3'=> '本地农村', '4'=> '外地农村'],1],
						['text', 'hukou_address', '户口所在地'],
						['text', 'old_name', '曾用名'],
						['text', 'old_name', '曾用名'],
						['text', 'nation', '民族'],
						['text', 'birth_address', '籍贯'],
						['text', 'english_name', '英文名'],
						['text', 'nationality', '国籍'],
						['text', 'passport', '护照'],
						['select', 'marriage', '婚姻状况', '', ['0'=> '未婚', '1'=> '已婚 ', '2'=> '离异', '3'=> '丧偶'],0],
						['select', 'political', '政治面貌', '', ['1'=> '共青团员 ', '2'=> '预备党员', '3'=> '党员','4'=>'民主党派','5'=> '群众'],5],
						['date', 'political_time', '政治面貌取得时间'],
						['date', 'work_time', '参加工作时间'],
						['text', 'professor_title', '职称'],
						['date', 'professor_time', '职称取得时间'],
						['select', 'education', '学历', '', ['1'=> '小学 ', '2'=> '初中', '3'=> '中专','4'=>'高中','5'=> '大专', '6'=> '本科','7'=>'硕士','8'=> '博士','9'=> '博士后'],6],
						['select', 'degree', '学位', '', ['1'=> '学士','2'=>'硕士','3'=> '博士'],1],
						['text', 'school', '毕业院校'],
						['text', 'major', '所学专业'],
						['date', 'graduation_time', '毕业时间'],
						['text', 'hobby', '业余爱好'],
						['text', 'health', '健康状况'],
						['number', 'height', '身高'],
						['number', 'weight', '体重'],
						['text', 'bank', '银行'],
						['text', 'bank_code', '银行卡号'],
						['text', 'social_security', '社保卡号'],
						['text', 'social_security_pc', '社保电脑号'],
						['text', 'accumulation_fund', '公积金账号'],
						['text', 'emergency_contact', '紧急联系人'],
						['text', 'emergency_phone', '紧急联系人电话'],
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
        $tep = RecordModel::column('uid');
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
            $result = $this->validate($data, 'Record');
            if (true !== $result) $this->error($result);

            if (RecordModel::update($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.$data['uid'].'),档案ID('.$data['id'].')';
                action_log('personnel_record_edit', 'personnel_record', $id, UID, $details);
                $this->success('编辑成功', 'index');
            } else {
                $this->error('编辑失败');
            }
        }
        

        $data_list = RecordModel::getOne($id);
		
        // 显示编辑页面
        return ZBuilder::make('form')           
            ->addGroup(
				[
					'基本信息' =>[	
						['hidden', 'id'],
						['hidden', 'uid'],
						['static', 'username', '用户名', '不可更改'],
               			['text', 'nickname', '昵称', '可以是中文'],
               			['select', 'role', '角色', '', RoleModel::getTree2(null, false)],
            			['select', 'organization', '部门', '', OrganizationModel::getMenuTree(0, '')],
            			['select', 'position', '职位', '', PositionModel::getMenuTree(0, '')],
               			['text', 'email', '邮箱', ''],		               
		                ['text', 'mobile', '手机号'],
		                ['image', 'avatar', '头像'],
		            	['radio', 'is_on', '在职状态', '', ['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职'], '1'],
		                ['radio', 'status', '状态', '', ['禁用', '启用']]
					],
					'档案信息' =>[
						['static', 'record_code', '档案编号'],
						['select', 'con_type', '员工类型', '', ['1'=> '合同工', '2'=> '正式员工', '3'=> '临时工']],
						['date', 'in_time', '入职时间'],
						['date', 'zz_time', '转正时间'],
						['date', 'lz_time', '离职时间'],
						['text', 'person_card', '身份证号码'],
						['select', 'hukou_type', '户口类型', '', ['1'=> '本地城镇', '2'=> ' 外地城镇', '3'=> '本地农村', '4'=> '外地农村']],
						['text', 'hukou_address', '户口所在地'],
						['text', 'old_name', '曾用名'],
						['text', 'old_name', '曾用名'],
						['text', 'nation', '民族'],
						['text', 'birth_address', '籍贯'],
						['text', 'english_name', '英文名'],
						['text', 'nationality', '国籍'],
						['text', 'passport', '护照'],
						['select', 'marriage', '婚姻状况', '', ['0'=> '未婚', '1'=> '已婚 ', '2'=> '离异', '3'=> '丧偶']],
						['select', 'political', '政治面貌', '', ['1'=> '共青团员 ', '2'=> '预备党员', '3'=> '党员','4'=>'民主党派','5'=> '群众']],
						['date', 'political_time', '政治面貌取得时间'],
						['date', 'work_time', '参加工作时间'],
						['text', 'professor_title', '职称'],
						['date', 'professor_time', '职称取得时间'],
						['select', 'education', '学历', '', ['1'=> '小学 ', '2'=> '初中', '3'=> '中专','4'=>'高中','5'=> '大专', '6'=> '本科','7'=>'硕士','8'=> '博士','9'=> '博士后']],
						['select', 'degree', '学位', '', ['1'=> '学士','2'=>'硕士','3'=> '博士']],
						['text', 'school', '毕业院校'],
						['text', 'major', '所学专业'],
						['date', 'graduation_time', '毕业时间'],
						['text', 'hobby', '业余爱好'],
						['text', 'health', '健康状况'],
						['number', 'height', '身高'],
						['number', 'weight', '体重'],
						['text', 'bank', '银行'],
						['text', 'bank_code', '银行卡号'],
						['text', 'social_security', '社保卡号'],
						['text', 'social_security_pc', '社保电脑号'],
						['text', 'accumulation_fund', '公积金账号'],
						['text', 'emergency_contact', '紧急联系人'],
						['text', 'emergency_phone', '紧急联系人电话'],
						['textarea', 'code', '备注'],
						['files', 'enclosure', '附件'],
					]					
				]
			)
            ->setFormData($data_list)
            ->fetch();
    }

}