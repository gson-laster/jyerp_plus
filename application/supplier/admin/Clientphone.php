<?php
namespace app\supplier\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\admin\model\Access as AccessModel;
use app\supplier\model\Clienttype as ClienttypeModel;
use app\supplier\model\Client as ClientModel;
use app\supplier\model\Clientphone as ClientphoneModel;
use app\supplier\model\Res as ResModel;
use app\user\model\Organization as OrganizationModel;
use think\Db;
/**
 *  设计变更
 */
class Clientphone extends Admin
{
	public function index()
	{
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('supplier_clientphone.id desc');

        $btn_detail = [
		    'title' => '详情',
		    'icon'  => 'fa fa-fw fa-search',
		    'href'  => url('detail', ['id' => '__id__'])
		];
		 $btn_update = [
		    'title' => '修改',
		    'icon'  => 'fa fa-fw fa-pencil',
		    'href'  => url('edit', ['id' => '__id__'])
		];
		$data_list = ClientphoneModel::getList($map,$order);
        return ZBuilder::make('table')
        			->addTopButton('add') // 添加顶部按钮
	        	 	->setSearch(['supplier_client.name'=>'客户','admin_user.nickname'=>'我方联络人'],'','',true) // 设置搜索框
                    ->addFilter('supplier_clientphone.type',ClientphoneModel::phoneType()) // 添加筛选
	        	 	->addFilter('supplier_clientphone.cause',ClientphoneModel::phoneCause()) // 添加筛选
	        	 	->addTimeFilter('supplier_clientphone.stime') // 添加时间段筛选
	        	 	->addOrder('supplier_clientphone.number,supplier_clientphone.stime') // 添加排序
	        		->hideCheckbox()
                    ->addColumns([ // 批量添加列
				        ['number', '编号'],
                        ['name', '主题'],
                        ['sname', '客户名称'],
                        ['stime', '联络时间','date'],
                        ['cause', '联系原因','text','',ClientphoneModel::phoneCause()],
                        ['type', '联系方式','text','',ClientphoneModel::phoneType()],
                        ['nickname', '我方联络人'],
				        ['right_button','操作']
				    ])
				    ->setRowList($data_list) // 设置表格数据
				    ->addRightButton('btn', $btn_detail,true) // 添加授权按钮
					->addRightButton('btn', $btn_update) // 添加授权按钮
				    ->addRightButton('delete') //添加删除按钮
					->addTopButton('export', [
						'title' => '导出',
						'icon' => 'fa fa-sign-out',
						'class' => 'btn btn-primary ajax-get',
						'href' => url('export', http_build_query($this->request->param()))
					])
					->addTopButton('import', [
						'title' => '导入',
						'icon' => 'fa fa-fw fa-sign-in',
						'class' => 'btn btn-primary',
						'href' => url('import')
					])
				    ->setTableName('supplier_client') // 指定数据表名
	                ->fetch();
	        	
	}

	public function add(){

        if ($this->request->isPost()) {
            $data = $this->request->post();
            //验证

            $result = $this->validate($data, 'Phone');
            $data['number'] = "KHLL".date("YmdHis",time());
            //验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            $data['stime'] = strtotime($data['stime']);

            if ($res = ClientphoneModel::create($data)) {
                // 记录行为
                //action_log('supplier_phone_add', 'supplier_phone', $res['id'], UID, $res['id']);
                $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }
        }
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('联络记录添加')           
            ->addFormItems([
                ['linkage:3','tid', '客户类型', '', ClienttypeModel::column('id,name'), '', url('get_sname'),'sid'],
                ['select:3','sid', '客户名称'],
                ['text:6', 'name', '主题'],
                ['date:6', 'stime', '联络时间'],
                ['select:6','cause', '联络事由','',ClientphoneModel::phoneCause()],
                ['select:6','type', '联络方式','',ClientphoneModel::phoneType()],
                ['linkage:6','oid', '我方联络人所在部门', '', OrganizationModel::column('id,title'),'', url('get_tj'),'uid'],
                ['select:6','uid', '我方联络人'],
                ['files:6', 'file', '附件'],
                ['wangeditor', 'content', '联络内容'],
            ])           
            ->fetch();

	}


	public function edit($id=""){

        if($id == null) $this->error('参数错误');
        if ($this->request->isPost()) {
            $data = $this->request->post();
            //验证

            $result = $this->validate($data, 'Phone');
            //验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            $data['stime'] = strtotime($data['stime']);
            if (ClientphoneModel::update($data)) {

                //action_log('supplier_phone_edit', 'supplier_phone', $data['id'], UID, $data['id']);
                $this->success('更新成功',url('index'));
            } else {
                $this->error('更新失败');
            }
        }

        $supplier = ClientphoneModel::getOne($id);
        $suppliertype = ClientModel::where('id',$supplier['sid'])->value('type');
        $supplierlist = ClientModel::where('type',$suppliertype)->column('id,name');
        $organization = UserModel::where('id',$supplier['uid'])->value('organization');
        $userlist = UserModel::where('organization',$organization)->column('id,nickname');
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('联络记录修改') 
            ->addHidden('id')          
            ->addFormItems([
                ['linkage:3','tid', '客户类型', '', ClienttypeModel::column('id,name'), $suppliertype, url('get_sname'),'sid'],
                ['select:3','sid', '客户名称','',$supplierlist,$supplier['sid']],
                ['text:6', 'name', '主题'],
                ['date:6', 'stime', '联络时间'],
                ['select:6','cause', '联络事由','',ClientphoneModel::phoneCause()],
                ['select:6','type', '联络方式','',ClientphoneModel::phoneType()],
                ['linkage:6','oid', '我方联络人所在部门', '', OrganizationModel::column('id,title'),$organization, url('get_tj'),'uid'],
                ['select:6','uid', '我方联络人','',$userlist,$supplier['uid']],
                ['files:6', 'file', '附件'],
                ['wangeditor', 'content', '联络内容'],
            ])     
            ->setFormData($supplier)       
            ->fetch();

	}

	//删除
	public function delete($ids = null){		
		if($ids == null) $this->error('参数错误');
		$map['id'] = $ids;
		if($model = ClientphoneModel::where($map)->delete()){	
			//记录行为
        	//action_log('supplier_phone_delete', 'supplier_phone', $map['id'], UID,$map['id']);			
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}		
	}
    //详情
	public function detail($id=null){
		if($id==null)$this->error('参数错误');
		$supplier = ClientphoneModel::getOne($id);
	        return ZBuilder::make('form')
            ->setPageTitle('联络记录详情')           
            ->addFormItems([
                ['static:6','sname', '客户名称'],
                ['static:6', 'name', '主题'],
                ['static:6', 'lltime', '联络时间','',date('Y-m-d',$supplier['stime'])],
                ['select:6','cause', '联络事由','',ClientphoneModel::phoneCause(),'','disabled'],
                ['select:6','type', '联络方式','',ClientphoneModel::phoneType(),'','disabled'],
                ['static:6','nickname', '我方联络人'],
                ['archives:6', 'file', '附件'],
                ['wangeditor', 'content', '联络内容'],
            ])           
 			->setFormData($supplier) 
			->hideBtn('submit') 
            ->fetch();

	}
    //供应商
    public function get_sname($tid = '')
    {
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        $ht = ClientModel::where('type',$tid)->column('id,name');
        foreach ($ht as $key => $value) {
        	 $arr['list'][] = ['key'=>$key,'value'=>$value];
        }
        return json($arr);
    }
    //推荐人
    public function get_tj($oid = '')
    {
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        $ht = UserModel::where('organization',$oid)->column('id,nickname');
        foreach ($ht as $key => $value) {
             $arr['list'][] = ['key'=>$key,'value'=>$value];
        }
        return json($arr);
    }
	//导出
   public function export()
    {
        $map = $this->getMap();        
        $order = $this->getOrder();
        $data = ClientphoneModel::exportData($map,$order);
		if($data == null) $this->error('暂无数据！');
        $cellName = [
            ['number', 'auto', '编号'],
            ['name', 'auto', '主题'],
            ['sname', 'auto', '客户名称'],
			['stime', 'auto', "联络时间"],
            ['is_cause', 'auto', "联系原因"],
            ['is_type', 'auto', "联系方式"],
            ['nickname', 'auto', "我方联络人"],
			['content', 'auto', '联络内容'],
        ];
        plugin_action('Excel/Excel/export', ['clientphonelist', $cellName, $data]);
    }
}   
