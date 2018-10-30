<?php
namespace app\supplier\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\admin\model\Access as AccessModel;
use app\supplier\model\Clienttype as ClienttypeModel;
use app\supplier\model\Client as ClientModel;
use app\user\model\Organization as OrganizationModel;
use think\Db;
use app\supplier\validate\Client as ClientValidate;
/**
 *  设计变更
 */
class Client extends Admin
{
	//
	public function index()
	{
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('supplier_client.id desc');

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

		$data_list = ClientModel::getList($map,$order);
        return ZBuilder::make('table')
        			->addTopButton('add') // 添加顶部按钮
	        	 	->setSearch(['supplier_client.name'=>'客户名称','phone'=>'手机','wechat'=>'微信','qq'=>'QQ'],'','',true) // 设置搜索框
	        	 	->addFilter(['tname'=>'supplier_clienttype.name']) // 添加筛选
	        	 	->addTimeFilter('supplier_client.create_time') // 添加时间段筛选
	        	 	->addOrder('supplier_client.number,supplier_client.create_time') // 添加排序
	        		->hideCheckbox()
                    ->addColumns([ // 批量添加列
				        ['number', '编号'],
				        ['name', '客户名称'],
				        ['tname', '客户类别'],
				        ['create_time', '建档时间','date'],
                        ['phone', '手机'],
                        ['wechat', '微信'],
				        ['qq', 'QQ'],
				        ['stime', '成立时间','date'],
				        ['right_button','操作']
				    ])
				    ->setRowList($data_list) // 设置表格数据
				    ->addRightButton('btn', $btn_detail,true) // 添加授权按钮
				    ->addRightButton('btn', $btn_update) // 添加授权按钮
				    ->addRightButton('delete') //添加删除按钮
					->addTopButton('export', [
                        'title' => '导出',
                        'icon'  => 'fa fa-sign-out',
                        'class' => 'btn btn-primary',
						'href' => url('export', http_build_query($this->request->param()))
					])
					->addTopButton('import', [
						'title' => '导入',
						'icon' => 'fa fa-fw fa-sign-in',
						'class' => 'btn btn-primary',
						'href' => url('import') 
					],true)
				    ->setTableName('supplier_client') // 指定数据表名
	                ->fetch();
	        	
	}

	public function add(){

        if ($this->request->isPost()) {
            $data = $this->request->post();
            //验证

            $result = $this->validate($data, 'Client');

            $data['create_time'] = time();
            $data['stime'] = strtotime($data['stime']);
            $data['number'] = "KH".date("YmdHis",time());
            $data['wid'] = UID;
            //验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if ($res = ClientModel::create($data)) {
                // 记录行为
                //action_log('supplier_add', 'supplier_list', $res['id'], UID, $res['id']);
                $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }
        }
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('客户添加')           
            ->addFormItems([
                ['text:6', 'name', '客户名称'],
                ['select:6','type', '客户类型', '', ClienttypeModel::column('id,name')],
                ['text:6', 'phone', '手机号'],
                ['text:6', 'tel', '电话'],
                ['text:6', 'wechat', '微信'],
                ['text:6', 'qq', 'QQ'],
                ['text:6', 'email', '邮箱'],
                ['text:6', 'chuanzhen', '传真'],
                ['textarea', 'address', '公司地址'],
                ['textarea', 'content', '经营范围'],
                ['date:6', 'stime', '公司建立时间'],
                ['text:6', 'yingye', '营业执照'],
                ['text:6', 'bankname', '开户行'],
                ['text:6', 'bankuser', '户名'],
                ['text:6', 'banknumber', '卡号'],
                ['files:6', 'file', '附件'],
                ['wangeditor', 'remark', '备注'],
            ])           
            ->fetch();

	}


	public function edit($id=""){

        if($id == null) $this->error('参数错误');
        if ($this->request->isPost()) {
            $data = $this->request->post();
            //验证

            $result = $this->validate($data, 'Client');

            $data['create_time'] = time();
            $data['stime'] = strtotime($data['stime']);
            $data['wid'] = UID;
            //验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if (ClientModel::update($data)) {
                // 记录行为
                //action_log('supplier_edit', 'supplier_list', $data['id'], UID, $data['id']);
                $this->success('更新成功',url('index'));
            } else {
                $this->error('更新失败');
            }
        }

        $supplier = ClientModel::getOne($id);
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('供应商添加') 
            ->addHidden('id')          
            ->addFormItems([
                ['text:6', 'name', '客户名称'],
                ['select:6','type', '客户类型', '', ClienttypeModel::column('id,name')],
                ['text:6', 'phone', '手机号'],
                ['text:6', 'tel', '电话'],
                ['text:6', 'wechat', '微信'],
                ['text:6', 'qq', 'QQ'],
                ['text:6', 'email', '邮箱'],
                ['text:6', 'chuanzhen', '传真'],
                ['textarea', 'address', '公司地址'],
                ['textarea', 'content', '经营范围'],
                ['date:6', 'stime', '公司建立时间'],
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
		if($model = ClientModel::where($map)->delete()){	
			//记录行为
        	//action_log('supplier_delete', 'supplier_list', $map['id'], UID,$map['id']);			
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}		
	}

	public function detail($id=null){
		if($id==null)$this->error('参数错误');
		$supplier = ClientModel::getOne($id);
	        return ZBuilder::make('form')
            ->setPageTitle('客户详情')           
            ->addFormItems([
            	['static:6','number','编号'],
            	['static:6','createtime','创建时间','',date('Y-m-d',$supplier['create_time'])],
                ['static:6', 'name', '客户名称'],
                ['static:6','tname', '客户类型'],
                ['static:6', 'phone', '手机号'],
                ['static:6', 'tel', '电话'],
                ['static:6', 'wechat', '微信'],
                ['static:6', 'qq', 'QQ'],
                ['static:6', 'email', '邮箱'],
                ['static:6', 'chuanzhen', '传真'],
                ['textarea', 'address', '公司地址'],
                ['textarea', 'content', '经营范围'],
                ['date:6', 'stime', '公司建立时间'],
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
	//导出
   public function export()
    {
        $map = $this->getMap();        
        $order = $this->getOrder('supplier_client.id desc');
        $data = ClientModel::exportData($map,$order);
		if($data == null) $this->error('暂无数据！');
        $cellName = [
            ['number', '15', '编号'],
            ['name', '15', '客户名称'],
            ['tname', '15', '客户类型'],
            ['phone', '15', "手机"],
			['tel', '15', "电话"],
            ['wechat', '15', "微信"],
            ['qq', '15', "qq"],
			['email', '15', "邮箱"],
			['chuanzhen', '15', '传真'],
			['address', '20', '公司地址'],
            ['create_time', '15', "建档时间"],
			['remark', '20', '备注'],
        ];
        plugin_action('Excel/Excel/export', ['client_list', $cellName, $data]);
    }

    public function import()
    {
        // 提交数据
        if ($this->request->isPost()) {
            // 接收附件 ID
            $excel_file = $this->request->post('excel');
            // 获取附件 ID 完整路径
            $full_path = getcwd().get_file_path($excel_file);
            // 只导入的字段列表
            $fields = [
                'name'=>'客户名称', //
                'type'=>'客户类型',//
                'phone'=>"手机",
                'tel'=>"电话",
                'wechat'=>"微信",
                'qq'=>"qq",
                'email'=>"邮箱",
                'chuanzhen'=>"传真",
                'address'=>"公司地址",
                'remark'=>"备注",
                'create_time'=>"建档时间"
            ];
            // 调用插件('插件',[路径,导入表名,字段限制,类型,条件,重复数据检测字段])
            $import = plugin_action('Excel/Excel/import', [$full_path, 'supplier_client', $fields, $type = 0, $where = null, $main_field = 'name']);
            // 失败或无数据导入
            if ($import['error']){
                $this->error($import['message']);
            }

            // 导入成功
            $this->success($import['message']);
        }

        // 创建演示用表单
        return ZBuilder::make('form')
            ->setPageTips('导入客户规则：<br><br>&nbsp;&nbsp;&nbsp;&nbsp;客户类型：必须与系统中客户类型名称一致，如果没有类型，请先添加<br>&nbsp;&nbsp;&nbsp;&nbsp;建档时间：格式 2018-01-01，不填默认获取当前时间')
            ->setPageTitle('导入Excel')
            ->addFormItems([ // 添加上传 Excel
                ['archive', 'excel', '示例','',36],
                ['file', 'excel', '上传文件'],
            ])
            ->fetch();
    }

}   
