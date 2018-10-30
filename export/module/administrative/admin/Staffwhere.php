<?php
namespace app\administrative\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\administrative\model\Staffwhere as StaffwhereModel;
use app\user\model\Organization as OrganizationModel;
use app\user\model\User as UserModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use think\Db;
/**
 * 员工去向
 */
class Staffwhere extends Admin
{
	// 去向主页
	public function index(){
			//时间段
			$map = $this->getMap();

			if($this->request->isGet()){
				$time_in = $this->request->get();
				if(!empty($time_in['_filter_time_from']) && !empty($time_in['_filter_time'])){
					$time_in = strtotime($time_in['_filter_time_from']);
				}else{
					$time_in = time();
				}
				$map['start_time'] = ['<',$time_in];
				$map['end_time'] = ['>',$time_in];
			}
			$time_in = date('Y-m-d',$time_in);
        $html = <<<EOF
<div class="form-inline time-filter">
    <div class="time-filter">
        <div class="input-daterange input-group" data-date-format="yyyy-mm-dd">
            <input class="form-control" type="text" id="_filter_time_from" name="_filter_time_from" value="{$time_in}" placeholder="时间">
        </div>
        <input type="hidden" id="_filter_time" name="search_field" value="time_in">
        <button type="button" id="btn-filter-time" class="btn btn-default">确定</button>
    </div>
</div>
EOF;
		$js = <<<EOF
<script src="__LIBS__/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<script src="__LIBS__/bootstrap-datepicker/locales/bootstrap-datepicker.zh-CN.min.js"></script>
<script>
jQuery(function () {
    App.initHelpers(["datepicker"]);
    
});
</script>
EOF;

        // 排序
	        $order = $this->getOrder('administrative_staffwhere.id desc');
	        // 数据列表
	        $data_list = StaffwhereModel::getIndexlist($map,$order);
			return ZBuilder::make('table')
			// ->addTimeFilter('start_time') // 添加时间段筛选
			->setSearch(['user_name' => '人员']) // 设置搜索框
	 		 ->addOrder('start_time,end_time') // 添加排序
	 		 ->addFilter('admin_organization.title')
			 ->addColumns([ // 批量添加数据列
	            ['user_name', '人员'],
	            ['title','部门'],
	            ['start_time', '开始时间','datetime','', 'Y-m-d H:i'],
	            ['end_time', '结束时间','datetime','', 'Y-m-d H:i'],
	            ['staff_where', '去向'],
	        ])
			->setExtraHtml($html,'toolbar_top')
			->setExtraHtml($js)
			->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板
	}

	// 去向管理
	public function manage(){

        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('id desc');
        // 数据列表
        $data_list = StaffwhereModel::getList($map,$order);

		return ZBuilder::make('table')
		 ->setSearchArea([
			    ['daterange', 'time_in', '时间'],
		 ])
 		 ->setSearch(['user_name' => '人员']) // 设置搜索框
 		 ->addOrder('start_time,end_time') // 添加排序
 		 ->addFilter('admin_organization.title')
		 ->addColumns([ // 批量添加数据列
            ['user_name', '人员'],
            ['title','部门'],
            ['start_time', '开始时间','datetime','', 'Y-m-d H:i'],
            ['end_time', '结束时间','datetime','', 'Y-m-d H:i'],
            ['staff_where', '去向'],
            ['right_button', '操作', 'btn']
        ])
	   	->addTopButton('add',true) // 添加顶部按钮
	   	->addTopButton('delete') // 添加顶部按钮
	   	->addRightButtons('edit,delete') // 添加编辑和删除按钮
	   	->setRowList($data_list) // 设置表格数据
        ->fetch(); // 渲染模板
	}

	//添加去向
	public function add(){

		if($this->request->isPost()){
			$data2 = $this->request->post();
			if($data2['is_open']==0){
				$data['open_user'] = '-'.implode('-',explode(',',$data2['helpid'])).'-';
			}
			$data['is_open'] = $data2['is_open'];
			$data['user_id'] = $data2['user_id'];
			$data['oid'] = $data2['oid'];
			$data['user_name'] = get_nickname($data['user_id']);
			$data['start_time'] = strtotime($data2['start_time']);
			$data['end_time'] = strtotime($data2['end_time']);
			$data['staff_where'] = $data2['staff_where'];
			$result = $this->validate($data, 'Staffwhere');

			if (true !== $result) $this->error($result);

			if ($StaffwhereModel = StaffwhereModel::create($data)) {
                $this->success('新增成功', 'manage');
            } else {
                $this->error('新增失败');
            }
		}

		return ZBuilder::make('form')
            ->setPageTitle('新增员工去向') // 设置页面标题
             ->addFormItems([
             	['hidden','helpid'],
                ['linkage:6','oid', '员工部门', '', OrganizationModel::getMenuTree2(), '', url('get_user'), 'user_id', 'organization'],
                ['select:6','user_id', '员工姓名'],
                ['datetime:6', 'start_time', '开始时间'],
                ['datetime:6', 'end_time', '开始时间'],
                ['textarea','staff_where','去向'],
                ['radio', 'is_open', '是否开放','',[0=>'否',1=>'是'],1],
                ['textarea','helpname','可查看人员'],
            ])
            ->setTrigger('is_open', 0, 'helpname')
            ->setExtraHtml(outhtml2())
			->setExtraJs(outjs2())
            ->fetch();
	}


	//添加去向
	public function edit($id=null){
		if($id==null)$this->error('编辑失败');
		if($this->request->isPost()){
			$data2 = $this->request->post();
			if($data2['is_open']==0){
				$data['open_user'] = '-'.implode('-',explode(',',$data2['helpid'])).'-';
			}
			$data['id'] = $data2['id'];
			$data['is_open'] = $data2['is_open'];
			$data['user_id'] = $data2['user_id'];
			$data['oid'] = $data2['oid'];
			$data['user_name'] = get_nickname($data['user_id']);
			$data['start_time'] = strtotime($data2['start_time']);
			$data['end_time'] = strtotime($data2['end_time']);
			$data['staff_where'] = $data2['staff_where'];
			$result = $this->validate($data, 'Staffwhere');

			if (true !== $result) $this->error($result);

			if ($StaffwhereModel = StaffwhereModel::update($data)) {
                $this->success('新增成功', 'manage');
            } else {
                $this->error('新增失败');
            }
		}


		$where_list = StaffwhereModel::getOne($id);
		return ZBuilder::make('form')
            ->setPageTitle('新增员工去向') // 设置页面标题
             ->addFormItems([
             	['hidden','id'],
             	['hidden','helpid'],
                ['linkage:6','oid', '员工部门', '', OrganizationModel::getMenuTree2(), '', url('get_user'), 'user_id', 'organization'],
                ['select:6','user_id', '员工姓名','',UserModel::where('organization',$where_list['oid'])->column('nickname','id')],
                ['datetime:6', 'start_time', '开始时间'],
                ['datetime:6', 'end_time', '开始时间'],
                ['textarea','staff_where','去向'],
                ['radio', 'is_open', '是否开放','',[0=>'否',1=>'是']],
                ['textarea','helpname','可查看人员'],
            ])
            ->setTrigger('is_open', 0, 'helpname')
            ->setExtraHtml(outhtml2())
			->setExtraJs(outjs2())
            ->setFormData($where_list)
            ->fetch();
	}


	//获取用户
	public function get_user($organization=''){
		 $arr['code'] = '1'; //判断状态
		 $arr['msg'] = '请求成功'; //回传信息
		 $list = UserModel::field('id,nickname')->where(['status'=>1,'organization'=>$organization])->select();
		 foreach ($list as $key => $value) {
		 	$arr['list'][] = ['key'=>$value['id'],'value'=>$value['nickname']]; 
		 }
        return json($arr);
	}
	
	   

}   
