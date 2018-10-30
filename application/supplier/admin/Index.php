<?php
namespace app\supplier\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\admin\model\Access as AccessModel;
use app\supplier\model\Type as TypeModel;
use app\supplier\model\Supplier as SupplierModel;
use app\user\model\Organization as OrganizationModel;
use think\Db;
/**
 *  设计变更
 */
class Index extends Admin
{
	//
	public function index()
	{
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('supplier_list.id desc');

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
				    'class' => 'btn btn-primary',
				    'href'  => url('OutExcel',http_build_query($this->request->param())),
				    
				];
				$btn_in = [
				    'title' => '导入',
				    'icon'  => 'fa fa-sign-in',
				    'class' => 'btn btn-primary',
				    'href'  => url('InExcel',http_build_query($this->request->param())),
				    
				];

		$data_list = SupplierModel::getList($map,$order);
		$type = TypeModel::column('id,name');
        return ZBuilder::make('table')
        			->addTopButton('add') // 添加顶部按钮
        			->addTopButton('InExcel',$btn_in,true)
        			->addTopButton('OutExcel',$btn_out)
	        	 	->setSearch(['supplier_list.name'=>'供应商','admin_user.nickname'=>'采购员','phone'=>'手机','wechat'=>'微信','qq'=>'QQ'],'','',true) // 设置搜索框
	        	 	->addFilter('type',$type) // 添加筛选
	        	 	->addTimeFilter('supplier_list.create_time') // 添加时间段筛选
	        	 	->addOrder('supplier_list.number,supplier_list.create_time') // 添加排序
	        		->hideCheckbox()
                    ->addColumns([ // 批量添加列
				        ['number', '编号'],
				        ['name', '供应商'],
				        ['type', '供应商类别',$type],
				        ['create_time', '建档时间','date'],
				        ['susername', '供应商联系人'],
						['phone', '手机'],
						['wechat', '微信'],
				        ['qq', 'QQ'],
				        ['nickname', '采购员'],
				        ['stime', '成立时间','date'],
				        ['suser', '法人代表'],
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

            $result = $this->validate($data, 'Supplier');

            $data['create_time'] = time();
            $data['stime'] = strtotime($data['stime']);
            $data['number'] = "GYS".date("YmdHis",time());
            $data['wid'] = UID;
            //验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if ($res = SupplierModel::create($data)) {
                // 记录行为
                action_log('supplier_add', 'supplier_list', $res['id'], UID, $res['id']);
                $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }
        }
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('供应商添加')           
            ->addFormItems([
                ['text:6', 'name', '供应商名称'],
                ['select:6','type', '供应商类型', '', TypeModel::column('id,name')],
                ['linkage:6','cid', '采购员所在部门', '', OrganizationModel::column('id,title'), '', url('get_cg'),'purchas_user'],
                ['select:6','purchas_user', '采购员'],
                ['text:6', 'susername', '供应商联系人'],
                ['text:6', 'phone', '手机号'],
                ['text:6', 'tel', '电话'],
                ['text:6', 'wechat', '微信'],
                ['text:6', 'qq', 'QQ'],
                ['text:6', 'email', '邮箱'],
                ['text:6', 'chuanzhen', '传真'],
                ['textarea', 'address', '公司地址'],
                ['textarea', 'content', '经营范围'],
                ['date:6', 'stime', '公司建立时间'],
                ['text:6', 'suser', '法人代表'],
                ['text:6', 'yingye', '营业执照'],
                ['text:6', 'bankname', '开户行'],
                ['text:6', 'bankuser', '户名'],
                ['text:6', 'banknumber', '卡号'],
                ['files:6', 'file', '附件'],
                ['wangeditor', 'remark', '备注'],
            ])           
            ->fetch();

	}
	
	//导出供应商信息
	public function OutExcel(){
				
				
		$map = $this->getMap();
        // 排序
        //dump($map);die;
        $order = $this->getOrder('supplier_list.id desc');
        // 查询数据
        $data = SupplierModel::exportData($map,$order);
        // 设置表头信息（对应字段名,宽度，显示表头名称）
        $cellName = [
            ['id', '10', '编号'],
            ['name', '20', '供应商'],
            ['tname', '15', '供应商类别'],
            ['susername', '15','供应商联系人'],
            ['phone', '20','手机'],
            ['wechat', '20','微信'],
		    ['qq','20', 'QQ'],
		    ['nickname', '10','采购员'],
        ];
        // 调用插件（传入插件名，[导出文件名、表头信息、具体数据]）
        plugin_action('Excel/Excel/export', ['supplier', $cellName, $data]);		
		}


	public function edit($id=""){

        if($id == null) $this->error('参数错误');
        if ($this->request->isPost()) {
            $data = $this->request->post();
            //验证

            $result = $this->validate($data, 'Supplier');

            $data['create_time'] = time();
            $data['stime'] = strtotime($data['stime']);
            $data['wid'] = UID;
            //验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if (SupplierModel::update($data)) {
                // 记录行为
                action_log('supplier_edit', 'supplier_list', $data['id'], UID, $data['id']);
                $this->success('更新成功',url('index'));
            } else {
                $this->error('更新失败');
            }
        }

        $supplier = SupplierModel::getOne($id);
        $organization = UserModel::where('id',$supplier['purchas_user'])->value('organization');
        $userlist = UserModel::where('organization',$organization)->column('id,nickname');
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('供应商添加') 
            ->addHidden('id')          
            ->addFormItems([
                ['text:6', 'name', '供应商名称'],
                ['select:6','type', '供应商类型', '', TypeModel::column('id,name')],
                ['linkage:6','cid', '采购员所在部门', '', OrganizationModel::column('id,title'), $organization, url('get_cg'),'purchas_user'],
                ['select:6','purchas_user', '采购员','',$userlist,$supplier['purchas_user']],
                ['text:6', 'susername', '供应商联系人'],
                ['text:6', 'phone', '手机号'],
                ['text:6', 'tel', '电话'],
                ['text:6', 'wechat', '微信'],
                ['text:6', 'qq', 'QQ'],
                ['text:6', 'email', '邮箱'],
                ['text:6', 'chuanzhen', '传真'],
                ['textarea', 'address', '公司地址'],
                ['textarea', 'content', '经营范围'],
                ['date:6', 'stime', '公司建立时间'],
                ['text:6', 'suser', '法人代表'],
                ['text:6', 'yingye', '营业执照'],
                ['text:6', 'bankname', '开户行'],
                ['text:6', 'bankuser', '户名'],
                ['text:6', 'banknumber', '卡号'],
                ['files:6', 'file', '附件'],
                ['wangeditor', 'remark', '备注'],
            ])     
            ->setFormData($supplier)       
            ->fetch();

	}

	//删除
	public function delete($ids = null){		
		if($ids == null) $this->error('参数错误');
		$map['id'] = $ids;
		if($model = SupplierModel::where($map)->delete()){	
			//记录行为
        	action_log('supplier_delete', 'supplier_list', $map['id'], UID,$map['id']);			
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}		
	}

	public function detail($id=null){
		if($id==null)$this->error('参数错误');
		$supplier = SupplierModel::getOne($id);
	        return ZBuilder::make('form')
            ->setPageTitle('供应商详情')           
            ->addFormItems([
            	['static:6','number','编号'],
            	['static:6','createtime','创建时间','',date('Y-m-d',$supplier['create_time'])],
                ['static:6', 'name', '供应商名称'],
                ['static:6','tname', '供应商类型'],
                ['static:6','nickname', '采购员'],
                ['static:6', 'susername', '供应商联系人'],
                ['static:6', 'phone', '手机号'],
                ['static:6', 'tel', '电话'],
                ['static:6', 'wechat', '微信'],
                ['static:6', 'qq', 'QQ'],
                ['static:6', 'email', '邮箱'],
                ['static:6', 'chuanzhen', '传真'],
                ['textarea', 'address', '公司地址'],
                ['textarea', 'content', '经营范围'],
                ['date:6', 'stime', '公司建立时间'],
                ['static:6', 'suser', '法人代表'],
                ['static:6', 'yingye', '营业执照'],
                ['static:6', 'bankname', '开户行'],
                ['static:6', 'bankuser', '户名'],
                ['static:6', 'banknumber', '卡号'],
                ['archives:6', 'file', '附件'],
                ['wangeditor', 'remark', '备注'],
            ])           
 			->setFormData($supplier) 
			->hideBtn('submit') 
            ->fetch();
	}

    public function get_cg($cid = '')
    {
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        $ht = db::name('admin_user')->where('organization',$cid)->column('id,nickname');
        foreach ($ht as $key => $value) {
        	 $arr['list'][] = ['key'=>$key,'value'=>$value];
        }
        return json($arr);
    }
    
    
    public function InExcel()
    {
        // 提交数据
        if ($this->request->isPost()) {
            // 接收附件 ID
            $excel_file = $this->request->post('excel');
            // 获取附件 ID 完整路径
            $full_path = getcwd().get_file_path($excel_file);
            // 只导入的字段列表
            $fields = [
                'name'=>'供应商', //
                'type'=>'供应商类别',//
                'susername'=>"供应商联系人",
                'purchas_user'=>"我方联络人",
                'phone'=>"手机",
                'tel'=>"座机",
                'wechat'=>"微信",
                'qq'=>"qq",
                'email'=>"email",
                'chuanzhen'=>"传真",
                'address'=>"地址",
                'remark'=>"备注",
                'create_time'=>"建档时间"
            ];
            // 调用插件('插件',[路径,导入表名,字段限制,类型,条件,重复数据检测字段])
            $import = plugin_action('Excel/Excel/import', [$full_path, 'supplier_list', $fields, $type = 0, $where = null, $main_field = 'name']);
            // 失败或无数据导入
            if ($import['error']){
                $this->error($import['message']);
            }

            // 导入成功
            $this->success($import['message']);
        }

        // 创建演示用表单
        return ZBuilder::make('form')
            ->setPageTips('导入供应商规则：<br><br>&nbsp;&nbsp;&nbsp;&nbsp;供应商类别：必须与系统中供应商类别名称一致，如果没有类别，请先添加<br>&nbsp;&nbsp;&nbsp;&nbsp;我方联络人：请确保用户列表中此人的信息存在<br>&nbsp;&nbsp;&nbsp;&nbsp;建档时间：格式 2018-01-01，不填默认获取当前时间')
            ->setPageTitle('导入Excel')
            ->addFormItems([ // 添加上传 Excel
                ['archive', 'excel', '示例','',31],
                ['file', 'excel', '上传文件'],
            ])
            ->fetch();
    }
    

}   
