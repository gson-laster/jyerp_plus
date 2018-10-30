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
use app\user\model\User as UserModel;
use app\user\model\Role as RoleModel;
use app\user\model\Organization as OrganizationModel;
use app\task\model\Task_detail as Task_detailModel;
use app\user\model\Position as PositionModel;
use app\stock\model\House as HouseModel;
use app\stock\model\HouseType as HouseTypeModel;


/**
 * 仓库控制器
 * @package app\produce\admin
 */
class House extends Admin
{
    /**
     * 仓库列表
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('stock_house.create_time desc');
        // 数据列表
        $data_list = HouseModel::getList($map,$order);
		//获取昵称
		$nickname = Task_detailModel::get_nickname();
        // 类型按钮
        $btn_type = [
        		'title' => '仓库类型',
        		'icon'  => 'fa fa-fw fa-th-list',
        		'class' => 'btn btn-info',
        		'href'  => url('house_type/index')
        ];
        
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['stock_house.name' => '仓库名称']) // 设置搜索框
            ->addOrder('stock_house.create_time') // 添加排序                     
            ->addFilter('stock_house.status',['0'=>'关闭','1'=>'启用'])
            ->addColumns([ // 批量添加数据列
                ['__INDEX__', '序号'], 
            	['name', '仓库名称'],
            	['zrid','责任人','','',$nickname],
            	['type','仓库类型',HouseTypeModel::getTree()],
            	['create_time', '建档时间','datetime'],
            	['status', '启用状态状态','switch','',['0'=>'关闭','1'=>'启用']],
            	['right_button', '操作', 'btn']
            ])
            ->addTopButton('custom', $btn_type)
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
            $result = $this->validate($data, 'House');
            if (true !== $result) $this->error($result);
			$data['uid']=UID;
            if ($result = HouseModel::create($data)) {
                // 记录行为
            	$details    = '详情：仓库ID('.$result['id'].'),建档人ID('.$result['uid'].'),责任人ID('.$result['zrid'].')';
                action_log('stock_house_add', 'stock_house', $result['id'], UID, $details);
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }
        
        // 显示添加页面
        return ZBuilder::make('form')
        	->setPageTitle('新建仓库')
            ->addFormItems([
						['hidden','zrid',UID],  
            			['hidden','helpid'],
            			['text', 'name','仓库名称'],
            			['select', 'type', '仓库类型', '',HouseTypeModel::getTree()],	
            			['text', 'zrname','选择中心责任人'],
            			['text', 'helpname','可查看该仓库的人员名单'],
            			['wangeditor', 'description', '仓库描述'],
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
            $result = $this->validate($data, 'House');
            if (true !== $result) $this->error($result);

            if (HouseModel::update($data)) {
                // 记录行为
            	$details    = '详情：仓库ID('.$data['id'].'),修改人ID('.UID.')';
                action_log('stock_house_edit', 'stock_house', $id, UID, $details);
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }

        //$data_list = HouseModel::getOne($id);
        $data_list = HouseModel::where('id', $id)->find();
			//获取昵称
			$nickname = Task_detailModel::get_nickname();
			$zrid = $data_list['zrid'];
			$helpid = $data_list['helpid'];
			$helpmane = Task_detailModel::get_helpname($helpid);
        // 显示编辑页面
        return ZBuilder::make('form')   
        	->setPageTitle('修改仓库')
            ->addFormItems([
						['hidden', 'id'],
						['hidden','zrid'],  
            			['hidden','helpid'],
            			['text', 'name','仓库名称'],
            			['select', 'type', '仓库类型', '',HouseTypeModel::getTree()],
            			['text', 'zrname','选择中心责任人','',$nickname[$zrid]],
            			['text', 'helpname','可查看该仓库的人员名单','',$helpmane],	
            			['wangeditor', 'description', '仓库描述'],
						['radio', 'status', '启用状态','',['0'=>'关闭','1'=>'启用'],1],								
				])
            ->setFormData($data_list)
            ->setExtraHtml(outhtml2())
			->setExtraJs(outjs2())
            ->fetch();
    }
    
    /**
     * 选择责任人
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
     * 选择可查看该仓库的人员名单
     * @author 黄远东 <641435071@qq.com>
     */
	public function chooses($access = '')
    {
    	// 分页数据
    	$js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
    if($('tbody tr:first').hasClass('table-empty')){
    	$('#pick').hide();
    }
	$('#pick').click(function(){
			var chk = $('tbody .active');
    		var ids = '';
    		var titles = '';
    		chk.each(function(){
    			ids += $(this).find('.ids').val()+','; 
    			titles += $.trim($(this).find('td').eq(2).text())+',';       			
   			});	
    		ids = ids.slice(0,-1);	
    		titles = titles.slice(0,-1);

    		var to_user = $("#to_user",parent.document).val();
    		if(to_user){
    			ids = to_user+','+ids;
    		}
    			    		    			
    		var noticer = $("#noticer",parent.document).val();
    		if(noticer){
    			titles = noticer+','+titles;
    		}
		
    		var idsArr=ids.split(",");   
   			idsArr.sort();
    		idsArr = $.unique(idsArr);
   			ids = idsArr.join(",");	
    			
    		var titlesArr=titles.split(",");   
   			titlesArr.sort();
    		titlesArr = $.unique(titlesArr);
   			titles = titlesArr.join(",");	
    			
			$("#access",parent.document).val(ids);
        	$("#accessname",parent.document).val(titles);
			//当你在iframe页面关闭自身时
			var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
			parent.layer.close(index); //再执行关闭
	});
});
            </script>
EOF;
    	
    	// 获取查询条件
    	$map = $this->getMap();
    	$map = ['status'=>1];
    	//$map = ['id'=>['not in',$access]];
    	$order = $this->getOrder();
    	
    	$btn_pick = [
    			'title' => '选择',
    			'icon'  => 'fa fa-plus-circle',
    			'class' => 'btn btn-xs btn-success',
    			'id' => 'pick'
    	];
    	
    	$data_list = UserModel::where($map)->order($order)->paginate('50');
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
    			['nickname', '姓名'],
    			['role', '角色', RoleModel::getTree2()],
   				['organization', '部门', OrganizationModel::getTree()],
   				['position', '职位', PositionModel::getTree()],
   				['create_time', '创建时间', 'datetime'],
   				['is_on', '在职状态',['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职']],
    	])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js)
    	->addTopButton('pick', $btn_pick)
    	->fetch('choose'); // 渲染页面 	
    } 
    
    /**
     * 编辑显示可查看人
     * @param array $access 可查看人ids
     * @author 黄远东<641435071@qq.com>
     * @return mixed
     */
    public function showaccess($access = '')
    {
    	$names = '';
    	if($access){
    		$result = UserModel::where(['id'=>['in',$access]])->column('nickname');
    		$names = implode(',',$result);	
    	}
    	return $names;
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
    	if (HouseModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		$details = '仓库ID('.$ids.'),操作人ID('.UID.')';
    		action_log('stock_house_delete', 'stock_house', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }

}