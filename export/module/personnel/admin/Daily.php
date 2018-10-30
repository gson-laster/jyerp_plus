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
use app\personnel\model\Daily as DailyModel;

/**
 * 日志控制器
 * @package app\shop\admin
 */
class Daily extends Admin
{
    /**
     * 店铺列表
     * @author 黄远东 <641435071@qq.com>
     */
    public function index($type = null)
    {    	
    	$tab = 'tab0';
    	// 查询
    	$map = $this->getMap();
        if($type != null){
        	$map['personnel_daily.type'] = $type;
        	$tab = 'tab'.$type;
        }
             
        // 排序
        $order = $this->getOrder('personnel_daily.update_time desc');
        // 数据列表
        $data_list = DailyModel::getList($map, $order);
		// 选择分类

		$list_tab = [
			'tab0' => ['title' => '日报', 'url' => url('index', ['type' => '0'])],
			'tab1' => ['title' => '周报', 'url' => url('index', ['type' => '1'])],
			'tab2' => ['title' => '月报', 'url' => url('index', ['type' => '2'])],			
		];

		//查看订单按钮
		$btn_view = [
    			'title' => '详情',
    			'icon'  => 'fa fa-fw fa-search',
    			'href'  => url('view',['id'=>'__id__'])
    	];
		
        return ZBuilder::make('table')
            ->setSearch(['admin_user.nickname' => '姓名']) // 设置搜索框
            ->addOrder('personnel_daily.id,personnel_daily.daily_time,personnel_daily.create_time') // 添加排序
            ->addFilter('admin_user.organization', OrganizationModel::getTree())
            ->addColumns([ // 批量添加数据列
                ['id', 'ID'],            	
            	['nickname', '姓名'],
            	['organization', '部门', OrganizationModel::getTree()],
            	['position', '职位', PositionModel::getTree()],            	
            	['title', '标题'],
            	['daily_time', '日志时间'],  
            	['create_time', '创建时间','datetime'],
            	['status', '查阅状态',['1'=>'已查阅','0'=>'待查阅']],
            	['right_button', '操作', 'btn']
            ])
            ->addTopButton('add',['title' => '添加']) // 批量添加顶部按钮
            ->addRightButton('view',$btn_view,true)
            ->addRightButton('edit')
            ->addRightButtons('delete')    
            ->setRowList($data_list) // 设置表格数据            
			->setTabNav($list_tab,  $tab)
            ->fetch(); // 渲染模板
    }	

    /**
     * 添加日志
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
            $data['oid'] = get_user_value(UID,'organization');
            // 验证
            $result = $this->validate($data, 'Daily');
            if (true !== $result) $this->error($result);

            if ($daily = DailyModel::create($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.UID.'),日志ID('.$daily['id'].')';
                action_log('personnel_daily_add', 'personnel_daily', $daily['id'], UID, $details);
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }
  
        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([	
            			['select', 'type','日志类型','',['0'=>'日报','1'=>'周报','2'=>'月报'],0],
						['text', 'title','标题'],	
            			['date', 'daily_time', '报告时间'],
            			['ueditor', 'info', '报告详情'],
            			['files', 'enclosure', '附件'],
						['textarea', 'note', '备注'],
				])
            ->fetch();
    }

    /**
     * 编辑
     * @param null $id 日志id
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
            // 验证
            $result = $this->validate($data, 'Daily');
            if (true !== $result) $this->error($result);

            if (DailyModel::update($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.UID.'),日志ID('.$data['id'].')';
                action_log('personnel_daily_edit', 'personnel_daily', $id, UID, $details);
                $this->success('编辑成功', 'index');
            } else {
                $this->error('编辑失败');
            }
        }
        
        $data_list = DailyModel::getOne($id);
        // 显示编辑页面
        return ZBuilder::make('form')           
            ->addFormItems([
						['hidden', 'id'],
						['select', 'type','日志类型','',['0'=>'日报','1'=>'周报','2'=>'月报']],
						['text', 'title','标题'],	
            			['date', 'daily_time', '报告时间'],
            			['ueditor', 'info', '报告详情'],
            			['files', 'enclosure', '附件'],
						['textarea', 'note', '备注'],							
				])
            ->setFormData($data_list)
            ->fetch();
    }
    
    /**
     * 查看
     * @param null $id 日志id
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function view($id = null)
    {
    	if ($id === null) $this->error('缺少参数');
    	DailyModel::update(['id'=>$id,'status'=>1]);
    	$data_list = DailyModel::getOne($id);
    	// 显示编辑页面
    	return ZBuilder::make('form')
    	->addFormItems([
    			['hidden', 'id'],
    			['select:3', 'type','日志类型','',['0'=>'日报','1'=>'周报','2'=>'月报'],'','disabled'],
    			['static:3', 'nickname','姓名'],
    			['select:3','organization', '部门','', OrganizationModel::getTree(),'','disabled'],
            	['select:3','position', '职位','',PositionModel::getTree(),'','disabled'],        			
    			['text', 'title','标题'],
    			['date', 'daily_time', '报告时间'],
    			['ueditor', 'info', '报告详情'],
    			['files', 'enclosure', '附件'],
    			['textarea', 'note', '备注'],
    	])
    	->setFormData($data_list)
    	->hideBtn('submit')
    	->fetch();
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
   		if(!$daily = DailyModel::where('id', $id)->find()){
    	   	$this->error('缺少参数');	
   		}  		
    	// 删除节点
    	if (DailyModel::destroy($id)) {
    		// 记录行为
    		$details = '日志ID('.$id.')，用户ID('.$daily['uid'].')';
    		action_log('personnel_daily_delete', 'personnel_daily', $id, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    } 
}