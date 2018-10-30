<?php
namespace app\purchase\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\purchase\model\Ask as AskModel;
use app\purchase\model\Plan as PlanModel;
use app\purchase\model\Type as TypeModel;
use app\admin\model\Access as AccessModel;
use app\user\model\Organization as OrganizationModel;
use app\sales\model\Order as OrderModel;
use think\Db;
/**
 *  施工日志
 */
class Cancel extends Admin
{
	//
	public function lists()
	{

  //       $map = $this->getMap();
  //       $order = $this->getOrder('purchase_ask.id desc');

     	$btn_detail = [
		    'title' => '查看详情',
		    'icon'  => 'fa fa-fw fa-search',
		    'href'  => url('detail', ['lid' => '__id__'])
		];

		// $data_list = PlanModel::getList($map,$order);
        return ZBuilder::make('table')
	        	 	// ->setSearch(['purchase_ask.name'=>'主题','admin_user.nickname'=>'询价员'],'','',true) // 设置搜索框
	        	 	->addTimeFilter('purchase_ask.atime') // 添加时间段筛选
	        	 	->addFilter('purchase_ask.tid',TypeModel::where('status=1')->column('id,name')) // 添加筛选
	        		->hideCheckbox()
	        		->addOrder('purchase_ask.number,purchase_ask.atime') // 添加排序
                    ->addColumns([ // 批量添加列
				        ['number', '单据编号'],
				        ['name', '主题'],
				        ['tid', '采购类型','text','',TypeModel::where('status=1')->column('id,name')],
                        ['nickname', '供应商'],
                        ['nickname23', '部门'],
                        ['nickname2', '交货方式'],
                        ['nickname2', '运货方式'],
                        ['nickname2', '收货人'],
                        ['nickname2', '联系电话'],
                        ['nickname2', '采购员'],
                        ['nickname2', '退货时间'],
                        ['nickname2', '发货地址'],
                        ['nickname2', '收货地址'],
				        // ['oname', '采购部门'],
				        // ['address','制单人'],
				        ['right_button','操作']
				    ])
				    // ->setRowList($data_list) // 设置表格数据
				    ->addRightButton('btn', $btn_detail,true) // 添加授权按钮
	                ->fetch();
	        	
	        	
	}

	public function index()
	{

  //       $map = $this->getMap();
  //       $order = $this->getOrder('purchase_ask.id desc');

     	$btn_detail = [
		    'title' => '查看详情',
		    'icon'  => 'fa fa-fw fa-search',
		    'href'  => url('detail', ['lid' => '__id__'])
		];

		// $data_list = AskModel::getList($map,$order);
        return ZBuilder::make('table')
	        	 	// ->setSearch(['purchase_ask.name'=>'主题','admin_user.nickname'=>'询价员'],'','',true) // 设置搜索框
	        	 	->addTimeFilter('purchase_ask.atime') // 添加时间段筛选
	        	 	->addFilter('purchase_ask.tid',TypeModel::where('status=1')->column('id,name')) // 添加筛选
	        		->hideCheckbox()
	        		->addOrder('purchase_ask.number,purchase_ask.atime') // 添加排序
                    ->addColumns([ // 批量添加列
                        ['number', '单据编号'],
                        ['name', '主题'],
                        ['tid', '采购类型','text','',TypeModel::where('status=1')->column('id,name')],
                        ['nickname', '供应商'],
                        ['nickname23', '部门'],
                        ['nickname2', '交货方式'],
                        ['nickname2', '运货方式'],
                        ['nickname2', '收货人'],
                        ['nickname2', '联系电话'],
                        ['nickname2', '采购员'],
                        ['nickname2', '退货时间'],
                        ['nickname2', '发货地址'],
                        ['nickname2', '收货地址'],
                        // ['oname', '采购部门'],
                        // ['address','制单人'],
                        ['right_button','操作']
				    ])
				    // ->setRowList($data_list) // 设置表格数据
				    ->addRightButton('btn', $btn_detail,true) // 添加授权按钮
				    ->addRightButton('delete') //添加删除按钮
				    ->addTopButton('add') //添加删除按钮
	                ->fetch();
	        	
	}

	public function add(){

        if ($this->request->isPost()) {
            $data = $this->request->post();
            // 验证
            $result = $this->validate($data, 'Ask');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            $data['number'] = 'CGSQ'.date('YmdHis',time());
            $data['atime'] = strtotime($data['atime']);
            if ($res = AskModel::create($data)) {
                // 记录行为
                action_log('purchase_ask_add', 'purchase_ask', $res['id'], UID, $res['id']);
                $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }
        }
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('添加退货')           
            ->addFormItems([
                ['text:6', 'name', '主题'],
            //     ['date:6', 'atime', '计划时间'],
            //     ['select:6','tid','采购类型','',TypeModel::where('status=1')->column('id,name')],
            // 	['linkage:3','oid', '采购部门', '', OrganizationModel::column('id,title'),'', url('get_tj'),'aid'],
            // 	['select:3','aid','申请人'],
            // 	['linkage:6','yd','源单类型','',yd('ask'),'',url('get_ydh'),'ydnumber'],
            // 	['select:6','ydnumber','源单号'],
            // 	['textarea','address','到货地址'],
            // 	['files:6','file','附件'],
            // 	['wangeditor', 'remark','备注'],
            ])           
            ->fetch();

	}
	//详情 
	public function detail($lid=null){

		if($lid==null)return $this->error('缺少参数');
		
		$detail = AskModel::getOne($lid);
		if($detail['yd']=='order'){
			$ydnumber = OrderModel::where('status=1')->column('id,name');
		}
	        return ZBuilder::make('form')
            ->setPageTitle('详情')           
            ->addFormItems([
                ['static:6', 'name', '主题'],
                ['static:6', 'ctime', '申请时间','',date('Y-m-d',$detail['atime'])],
                ['static:6','tname','采购类型'],
            	['static:3','oname', '申请部门'],
            	['static:3','nickname','申请人'],
            	['linkage:6','yd','源单类型','',yd('ask'),$detail['yd'],url('get_ydh'),'ydnumber'],
            	['select:6','ydnumber','源单号','',$ydnumber],
            	['textarea','address','到货地址'],
            	['archives:6','file','附件'],
            	['wangeditor', 'remark','备注'],
            ])      
            ->setFormData($detail)    
            ->hideBtn('submit') 
            ->fetch();

	}

	//删除
	public function delete($ids = null){		
		if($ids == null) $this->error('参数错误');
		$map['id'] = $ids;
		if($model = AskModel::where($map)->delete()){	
			//记录行为
        	action_log('purchase_ask_delete', 'purchase_ask', $map['id'], UID,$map['id']);			
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}		
	}

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

    public function get_ydh($yd = '')
    {
    	if($yd=='null'){
    		$list= ['0'=>'无']; 
    	}elseif($yd=='order'){
    		$list = OrderModel::where('status=1')->column('id,name');
    	}elseif($yd=='res'){
    		$list= ['0'=>'无']; 
    	}
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        foreach ($list as $key => $value) {
             $arr['list'][] = ['key'=>$key,'value'=>$value];
        }
        return json($arr);
    }
}   
