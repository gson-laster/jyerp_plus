<?php
namespace app\supplier\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\admin\model\Access as AccessModel;
use app\supplier\model\Type as TypeModel;
use app\supplier\model\Supplier as SupplierModel;
use app\supplier\model\Res as ResModel;
use app\user\model\Organization as OrganizationModel;
use think\Db;
/**
 *  设计变更
 */
class Res extends Admin
{
	//
	public function index()
	{
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('supplier_res.id desc');

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


		$data_list = ResModel::getList($map,$order);
        return ZBuilder::make('table')
        			->addTopButton('add') // 添加顶部按钮
	        	 	->setSearch(['supplier_list.name'=>'供应商','admin_user.nickname'=>'推荐人','supplier_res.res'=>'物品名称'],'','',true) // 设置搜索框
	        	 	->addFilter('supplier_res.type',[1=>'低',2=>'中',3=>'高']) // 添加筛选
	        	 	->addTimeFilter('supplier_res.create_time') // 添加时间段筛选
	        	 	->addOrder('supplier_res.number,supplier_res.create_time') // 添加排序
	        		->hideCheckbox()
                    ->addColumns([ // 批量添加列
				        ['number', '编号'],
                        ['sname', '供应商名称'],
				        ['res', '物品名称'],
                        ['type', '推荐程度','status','',[1=>'低',2=>'中',3=>'高']],
                        ['nickname', '推荐人'],
				        ['create_time', '推荐时间','date'],
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

            $result = $this->validate($data, 'Res');

            if(empty($data['create_time'])){
                $data['create_time'] = time();
            }else{
                $data['create_time'] = strtotime($data['create_time']);
            }
            $data['number'] = "GYSWP".date("YmdHis",time());
            //验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if ($res = ResModel::create($data)) {
                // 记录行为
                action_log('supplier_res_add', 'supplier_res', $res['id'], UID, $res['id']);
                $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }
        }
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('供应商添加')           
            ->addFormItems([
                ['linkage:6','tid', '供应商类型', '', TypeModel::column('id,name'), '', url('get_sname'),'sid'],
                ['select:6','sid', '供应商名称'],
                ['text:6', 'res', '物品名称'],
                ['date:6', 'create_time', '推荐时间'],
                ['linkage:6','oid', '推荐人所在部门', '', OrganizationModel::column('id,title'),'', url('get_tj'),'uid'],
                ['select:6','uid', '推荐人'],
                ['select:6','type', '推荐程度','',[1=>'低',2=>'中',3=>'高']],
                ['files:6', 'file', '附件'],
                ['wangeditor', 'cause', '推荐理由'],
            ])           
            ->fetch();

	}


	public function edit($id=""){

        if($id == null) $this->error('参数错误');
        if ($this->request->isPost()) {
            $data = $this->request->post();
            //验证

            $result = $this->validate($data, 'Res');
            //验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if (ResModel::update($data)) {
                // 记录行为
                action_log('supplier_res_edit', 'supplier_res', $data['id'], UID, $data['id']);
                $this->success('更新成功',url('index'));
            } else {
                $this->error('更新失败');
            }
        }

        $supplier = ResModel::getOne($id);
        $suppliertype = SupplierModel::where('id',$supplier['sid'])->value('type');
        $supplierlist = SupplierModel::where('type',$suppliertype)->column('id,name');
        $organization = UserModel::where('id',$supplier['uid'])->value('organization');
        $userlist = UserModel::where('organization',$organization)->column('id,nickname');
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('供应商物品添加') 
            ->addHidden('id')          
            ->addFormItems([
                ['linkage:6','tid', '供应商类型', '', TypeModel::column('id,name'), $suppliertype, url('get_sname'),'sid'],
                ['select:6','sid', '供应商名称','',$supplierlist,$supplier['sid']],
                ['text:6', 'res', '物品名称'],
                ['static:6','createtime','创建时间','',date('Y-m-d',$supplier['create_time'])],
                ['linkage:6','oid', '推荐人所在部门', '', OrganizationModel::column('id,title'),$organization, url('get_tj'),'uid'],
                ['select:6','uid', '推荐人','',$userlist,$supplier['uid']],
                ['select:6','type', '推荐程度','',[1=>'低',2=>'中',3=>'高']],
                ['files:6', 'file', '附件'],
                ['wangeditor', 'cause', '推荐理由'],
            ])     
            ->setFormData($supplier)       
            ->fetch();

	}

	//删除
	public function delete($ids = null){		
		if($ids == null) $this->error('参数错误');
		$map['id'] = $ids;
		if($model = ResModel::where($map)->delete()){	
			//记录行为
        	action_log('supplier_res_delete', 'supplier_res', $map['id'], UID,$map['id']);			
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}		
	}
    //详情
	public function detail($id=null){
		if($id==null)$this->error('参数错误');
		$supplier = ResModel::getOne($id);
	        return ZBuilder::make('form')
            ->setPageTitle('供应商物品详情')           
            ->addFormItems([
                ['static:6','number', '编号'],
                ['static:6','createtime','创建时间','',date('Y-m-d',$supplier['create_time'])],
                ['static:6', 'sname', '供应商'],
                ['static:6', 'res', '推荐物品'],
                ['static:6','nickname', '推荐人'],
                ['static:6','types', '推荐程度','',self::res_type($supplier['type'])],
                ['archives:6', 'file', '附件'],
                ['wangeditor', 'cause', '推荐理由'],
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
    //判断高中低 物品
    public function res_type($type){
       switch ($type) {
            case 1:
                return '低';
                break;
            case 2:
                return '中';
            case 3:
                return '高';
            default:
                return '未填写';
                break;
        }
    }

}   
