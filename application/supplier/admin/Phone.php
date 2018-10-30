<?php
namespace app\supplier\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\admin\model\Access as AccessModel;
use app\supplier\model\Type as TypeModel;
use app\supplier\model\Supplier as SupplierModel;
use app\supplier\model\Phone as PhoneModel;
use app\supplier\model\Res as ResModel;
use app\user\model\Organization as OrganizationModel;
use think\Db;
/**
 *  设计变更
 */
class Phone extends Admin
{

	public function index()
	{

        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('supplier_phone.id desc');

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
				$btn_out = [
				    'title' => '导出',
				    'icon'  => 'fa fa-sign-out',
				    'class' => 'btn btn-primary ajax-get',
				    'href'  => url('OutExcel',http_build_query($this->request->param())),
				    
				];


		$data_list = PhoneModel::getList($map,$order);
        return ZBuilder::make('table')
			->addTopButton('add') // 添加顶部按钮
			->addTopButton('OutExcel',$btn_out)
			->setSearch(['supplier_list.name'=>'供应商','admin_user.nickname'=>'我方联络人','supplier_phone.susername'=>'供应商联络人'],'','',true) // 设置搜索框
            ->addFilter('supplier_phone.type',PhoneModel::phoneType()) // 添加筛选
			->addFilter('supplier_phone.cause',PhoneModel::phoneCause()) // 添加筛选
			->addTimeFilter('supplier_phone.stime') // 添加时间段筛选
			->addOrder('supplier_phone.number,supplier_phone.stime') // 添加排序
			->hideCheckbox()
			->addColumns([ // 批量添加列
				['number', '编号'],
				['name', '主题'],
				['sname', '供应商名称'],
				['susername', '供应商联络人'],
				['stime', '联络时间','date'],
				['cause', '联系原因','text','',PhoneModel::phoneCause()],
				['type', '联系方式','text','',PhoneModel::phoneType()],
				['nickname', '我方联络人'],
				['right_button','操作']
			])
			->setRowList($data_list) // 设置表格数据
			->addRightButton('btn', $btn_detail,true) // 添加授权按钮
			->addRightButton('btn', $btn_update) // 添加授权按钮
			->addRightButton('delete') //添加删除按钮
			->setTableName('supplier_list') // 指定数据表名
	         ->fetch();	        	
	}

	public function add(){

        if ($this->request->isPost()) {
            $data = $this->request->post();
            //验证

            $result = $this->validate($data, 'Phone');
            $data['number'] = "GYSLL".date("YmdHis",time());
            //验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            $data['stime'] = strtotime($data['stime']);

            if ($res = PhoneModel::create($data)) {
                // 记录行为
                action_log('supplier_phone_add', 'supplier_phone', $res['id'], UID, $res['id']);
                $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }
        }
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('联络记录添加')           
            ->addFormItems([
                ['linkage:3','tid', '供应商类型', '', TypeModel::column('id,name'), '', url('get_sname'),'sid'],
                ['select:3','sid', '供应商名称'],
                ['text:6', 'name', '主题'],
                ['text:6', 'susername', '供应商联络人'],
                ['date:6', 'stime', '联络时间'],
                ['select:6','cause', '联络事由','',PhoneModel::phoneCause()],
                ['select:6','type', '联络方式','',PhoneModel::phoneType()],
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
            if (PhoneModel::update($data)) {

                action_log('supplier_phone_edit', 'supplier_phone', $data['id'], UID, $data['id']);
                $this->success('更新成功',url('index'));
            } else {
                $this->error('更新失败');
            }
        }

        $supplier = PhoneModel::getOne($id);
        $suppliertype = SupplierModel::where('id',$supplier['sid'])->value('type');
        $supplierlist = SupplierModel::where('type',$suppliertype)->column('id,name');
        $organization = UserModel::where('id',$supplier['uid'])->value('organization');
        $userlist = UserModel::where('organization',$organization)->column('id,nickname');
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('联络记录修改') 
            ->addHidden('id')          
            ->addFormItems([
                ['linkage:3','tid', '供应商类型', '', TypeModel::column('id,name'), $suppliertype, url('get_sname'),'sid'],
                ['select:3','sid', '供应商名称','',$supplierlist,$supplier['sid']],
                ['text:6', 'name', '主题'],
                ['text:6', 'susername', '供应商联络人'],
                ['date:6', 'stime', '联络时间'],
                ['select:6','cause', '联络事由','',PhoneModel::phoneCause()],
                ['select:6','type', '联络方式','',PhoneModel::phoneType()],
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
		if($model = PhoneModel::where($map)->delete()){	
			//记录行为
        	action_log('supplier_phone_delete', 'supplier_phone', $map['id'], UID,$map['id']);			
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}		
	}
    //详情
	public function detail($id=null){
		if($id==null)$this->error('参数错误');
		$supplier = PhoneModel::getOne($id);
	        return ZBuilder::make('form')
            ->setPageTitle('联络记录详情')           
            ->addFormItems([
                ['static:6','sname', '供应商名称'],
                ['static:6', 'name', '主题'],
                ['static:6', 'susername', '供应商联络人'],
                ['static:6', 'lltime', '联络时间','',date('Y-m-d',$supplier['stime'])],
                ['select:6','cause', '联络事由','',PhoneModel::phoneCause(),'','disabled'],
                ['select:6','type', '联络方式','',PhoneModel::phoneType(),'','disabled'],
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
        $ht = SupplierModel::where('type',$tid)->column('id,name');
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
    
    
    
    public function OutExcel(){							
		$map = $this->getMap();
        // 排序
        $order = $this->getOrder();
        // 查询数据               
        $data = PhoneModel::exportData($map,$order);
        if($data == null) return $this->error('暂无数据！');              
		// 设置表头信息（对应字段名,宽度，显示表头名称）      
		$cellName = [
			['number', '10', '编号'],
			['name', '10', '主题'],
			['sname', '15', '供应商名称'],
			['susername', '15','供应商联络人'],
			['stime', '20','联络时间'],
			['cause', '20','联络原因'],
			['type','20', '联系方式'],
			['nickname', '10','我方联络人'],
		];                 
		// 调用插件（传入插件名，[导出文件名、表头信息、具体数据]）
		plugin_action('Excel/Excel/export', ['phone', $cellName, $data]);			
	}
		

}   
