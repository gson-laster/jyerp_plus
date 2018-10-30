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

namespace app\notice\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\user\model\Role as RoleModel;
use app\user\model\Organization as OrganizationModel;
use app\user\model\Position as PositionModel;
use app\notice\model\Nlist as NlistModel;
use app\notice\model\Cate as CateModel;
use app\notice\model\Nuser as NuserModel;
/**
 * 公告列表
 * @package app\shop\admin
 */
class Nlist extends Admin
{
    /**
     * 公告列表
     * @author 黄远东 <641435071@qq.com>
     */
    public function index($cate = null)
    {    	
    	$tab = 'tab0';
    	// 查询
    	$map = $this->getMap();
        if($cate != null){
        	$map['cate'] = $cate;
        	$tab = 'tab'.$cate;
        }
             
        // 排序
        $order = $this->getOrder('update_time desc');
        // 数据列表
        $data_list = NlistModel::where($map)->order($order)->paginate();
		// 选择分类
        $list_tab = [];
		$cate = CateModel::getTree();
		foreach ($cate as $k => $v){
			$list_tab['tab'.$k] = ['title' => $v, 'url' => url('index', ['cate' => $k])];
		
		}	

		//撤销按钮
		$btn_release = [
				'title' => '发布',
				'icon'  => 'fa fa-fw fa-share',
				'class' => 'btn btn-xs btn-default ajax-get',
				'href'  => url('release',['id'=>'__id__'])
		];
		//撤销按钮
		$btn_cancel = [
    			'title' => '撤销',
    			'icon'  => 'fa fa-fw fa-mail-reply',
				'class' => 'btn btn-xs btn-default ajax-get',
    			'href'  => url('cancel',['id'=>'__id__'])
    	];
		
        return ZBuilder::make('table')
            ->setSearch(['title' => '公告标题']) // 设置搜索框
            ->addOrder('id,update_time') // 添加排序
            ->addFilter('status',['0'=>'待发布','1'=>'已发布','2'=>'撤销'])
            ->addColumns([ // 批量添加数据列
                ['id', 'ID'],            	
            	['title', '公告标题'],
            	['cate', '公告类型', CateModel::getTree()],        	
            	['update_time', '发布时间','datetime'],
            	['status', '发布状态','status', '',['待发布','已发布','撤销']],
            	['right_button', '操作', 'btn']
            ])
            ->addTopButton('add',['title' => '添加']) // 批量添加顶部按钮
            ->addRightButton('btn_release',$btn_release)
            ->addRightButton('btn_cancel',$btn_cancel)
            ->addRightButtons('edit,delete')
            ->replaceRightButton(['status' => ['in', '0,2']], '', ['btn_cancel'])
            ->replaceRightButton(['status' => '1'], '', ['btn_release','delete'])
            ->setRowList($data_list) // 设置表格数据            
			->setTabNav($list_tab,  $tab)
			->setTableName('notice_list')
            ->fetch(); // 渲染模板
    }	

    /**
     * 添加公告
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function add()
    {
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            if($data['cate'] !=1){
            	if(!$data['to_user']) $this->error('通知单位不能为空！');	            
            } 
            $data['uid'] = UID; 
            // 验证
            $result = $this->validate($data, 'Nlist');
            if (true !== $result) $this->error($result);
			
            
            if ($notice = NlistModel::create($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.UID.'),公告ID('.$notice['id'].')';
                action_log('notice_list_add', 'notice_list', $notice['id'], UID, $details);
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }
        
        $js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
	$('#noticer').click(function(){
        	var cate = $("#cate option:selected").val();
        	var to_user = $("#to_user").val();	 
	
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择通知单位',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/notice/nlist/choose/cate/'+cate+'/to_user/'+to_user+'/noticer/'+noticer 
			});
	});
        		
    $("#cate").change(function(){   	        	
    			$("#to_user").val('');
        		$("#noticer").val('');
     })
});
            </script>
EOF;
  
        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([	
            			['hidden', 'to_user'],
            			['select', 'cate','公告类型','',CateModel::getTree(),1],
						['text', 'noticer','选择通知单位','','','','readonly'],
						['text', 'title','标题'],	
            			['textarea', 'description', '公告描述'],
            			['ueditor', 'info', '公告详情'],
            			['files', 'enclosure', '附件'],
						['textarea', 'note', '备注'],
				])				
			->setTrigger('cate', CateModel::getId(), 'to_user,noticer')
			->setExtraJs($js)
            ->fetch();
    }

    /**
     * 编辑
     * @param null $id 公告id
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function edit($id = null)
    {
        if ($id === null) $this->error('缺少参数');
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post(); 
            if($data['cate'] !=1){
            	if(!$data['to_user']) $this->error('通知单位不能为空！');
            }
            $data['uid'] = UID;
            // 验证
            $result = $this->validate($data, 'Nlist');
            if (true !== $result) $this->error($result);

            if (NlistModel::update($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.UID.'),公告ID('.$data['id'].')';
                action_log('notice_list_edit', 'notice_list', $id, UID, $details);
                $this->success('编辑成功', 'index');
            } else {
                $this->error('编辑失败');
            }
        }
        
        $js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
	$('#noticer').click(function(){
        	var cate = $("#cate option:selected").val();
        	var to_user = $("#to_user").val();
        
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择通知单位',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/notice/nlist/choose/cate/'+cate+'/to_user/'+to_user+'/noticer/'+noticer
			});
	});
        
    $("#cate").change(function(){
    			$("#to_user").val('');
        		$("#noticer").val('');
     })
});
            </script>
EOF;
        
        $data_list = NlistModel::get($id);
        // 显示编辑页面
        return ZBuilder::make('form')           
            ->addFormItems([
						['hidden', 'id'],
            			['hidden', 'to_user'],
						['select', 'cate','公告类型','',CateModel::getTree(),1],
						['text', 'noticer','选择通知单位','','','','readonly'],
						['text', 'title','标题'],	
            			['textarea', 'description', '公告描述'],
            			['ueditor', 'info', '公告详情'],
            			['files', 'enclosure', '附件'],
						['textarea', 'note', '备注'],						
				])
            ->setFormData($data_list)
            ->setTrigger('cate', CateModel::getId(), 'to_user,noticer')
            ->setExtraJs($js)
            ->fetch();
    }
    
    /**
     * 弹出选择列表
     * @author 黄远东 <641435071@qq.com>
     */
    public function choose($cate = null,$to_user = '')
    {
    	if($cate == null) $this->error("缺少参数"); 

    	// 分页数据
    	$js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
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
    			
			$("#to_user",parent.document).val(ids);
        	$("#noticer",parent.document).val(titles);
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
    	$map = ['id'=>['not in',$to_user]];
    	$order = $this->getOrder();
    	
    	$btn_pick = [
    			'title' => '选择',
    			'icon'  => 'fa fa-plus-circle',
    			'class' => 'btn btn-xs btn-success',
    			'id' => 'pick'
    	];
    	// 数据列表
    	if($cate == 2){
    		$data_list = OrganizationModel::where($map)->order($order)->paginate('50');
    		// 使用ZBuilder快速创建数据表格
    		return ZBuilder::make('table')
    		->setTableName('admin_organization') // 设置数据表名   		
    		->addColumns([ // 批量添加列
    				['id', 'ID'],
    				['title', '部门'],    				    				
    		])
    		->setRowList($data_list) // 设置表格数据
    		->setExtraJs($js)
    		->addTopButton('pick', $btn_pick)
    		->assign('empty_tips', '暂无需要添加证件的用户')
    		->fetch('choose'); // 渲染页面
    	}else if($cate == 3){
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
    	}else{
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
    } 

    /**
     * 发布
     * @param null $ids
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function release($id = null)
    {
    	if($id == null) $this->error('缺少参数');
		$notice =  NlistModel::get($id); 

		if($notice['cate'] == 1){
			$user = UserModel::where(['status'=>1])->select();			
			foreach ($user as $k => $v){
				$data = [
						'lid'=>$id,
						'uid'=>$v['id'],
						'cate'=>$notice['cate']
				];
				NuserModel::create($data);
			}
		}else if($notice['cate'] == 2){
			$user = UserModel::where(['status'=>1,'organization'=>['in',$notice['to_user']]])->select();
			foreach ($user as $k => $v){
				$data = [
						'lid'=>$id,
						'uid'=>$v['id'],
						'cate'=>$notice['cate']
				];
				NuserModel::create($data);
			}
		}else if($notice['cate'] == 3){
			$user = explode(',', $notice['to_user']);
			foreach ($user as $k => $v){
				$data = [
						'lid'=>$id,
						'uid'=>$v,
						'cate'=>$notice['cate']
				];
				NuserModel::create($data);
			}
		}else{
			$user = explode(',', $notice['to_user']);
			foreach ($user as $k => $v){
				$data = [
						'lid'=>$id,
						'uid'=>$v,
						'cate'=>$notice['cate']
				];
				NuserModel::create($data);
			}			
		}
		
		if (NlistModel::update(['id'=>$id,'status'=>1])) {
			// 记录行为
			$details = '发布公告，公告ID('.$id.')';
    		action_log('notice_list_release', 'notice_list', $id, UID, $details);
    		$this->success('发布成功');
		} else {
			$this->error('发布失败');
		}
		
    	
    }
    
    /**
     * 撤销
     * @param null $ids
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function cancel($id = null)
    {
    	if($id == null) $this->error('缺少参数');
    	// 删除节点
    	if (NuserModel::where(['lid'=>$id])->delete()) {
    		NlistModel::update(['id'=>$id,'status'=>2]);
    		// 记录行为
    		$details = '撤销公告，公告ID('.$id.')';
    		action_log('notice_list_cancel', 'notice_list', $id, UID, $details);
    		$this->success('撤销成功');
    	} else {
    		$this->error('撤销失败');
    	}     	 
    }

    /**
     * 删除
     * @param null $ids 
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function delete($ids = null)
    {    	
    	$id = $this->request->param('ids');
   		if(!$daily = NlistModel::where('id', $id)->find()){
    	   	$this->error('你存在该条公告');	
   		}  		
    	// 删除节点
    	if (NlistModel::destroy($id)) {
    		// 记录行为
    		$details = '公告ID('.$id.')';
    		action_log('notice_list_delete', 'notice_list', $id, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    } 
}