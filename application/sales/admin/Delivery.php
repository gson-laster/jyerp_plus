<?php
	
namespace app\sales\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\sales\model\Opport as OpportModel;
use app\sales\model\Offer as OfferModel;
use app\stock\model\House as HouseModel;
use app\sales\model\Order as OrderModel;
use app\sales\model\Delivery as DeliveryModel;
use app\sales\model\Deliverydetail as DeliverydetailModel;
use app\sales\model\Contract as ContractModel;
use app\user\model\Organization as OrganizationModel;
use app\task\model\Task_detail as Task_detailModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use think\Db;
/**
 * 任务控制器
 * @author HJP
 */
class Delivery extends Admin
{	
	
	//销售机会
	public function index(){
		
		// 获取查询条件
		$map = $this->getMap();
		// 排序
		$order = $this->getOrder('sales_delivery.create_time desc');
		// 数据列表
		$data_list = DeliveryModel::getList($map,$order);
		// 分页数据
		$page = $data_list->render();
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-search',
			'href' => url('task_list',['id'=>'__id__'])
		];
		return ZBuilder::make('table')
		->setPageTitle('订单列表')
		->setSearch(['code' => '编号', 'sales_delivery.name' => '订单名称'], '', '', true) // 设置搜索参数
		->addColumns([
			['code','编号'],
			['name','发货名称'],
			['monophyletic','源单类型',[3=>'销售合同']],
			['customer_name','客户名称'],
			['phone','客户联系方式'],						
			['money','运费金额(元)'],
			['zrid','业务员'],
			['deliveryman','收货人'],
			['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
			['right_button','操作','btn'],
		])
		->addOrder(['code']) // 添加排序
		->addTopButtons(['delete'])//添加顶部按钮
		->addRightButtons(['delete' => ['data-tips' => '删除报价将无法恢复。']])
		->addRightButton('task_list',$task_list,true) // 查看右侧按钮 
		->setRowList($data_list)//设置表格数据
		->fetch();
	}
	//添加销售机会
	public function add(){
		if($this->request->isPost()){
			$data = $this->request->post();
			// 验证
			$result = $this->validate($data, 'delivery');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			$data['code'] = 'XSFH'.date('YmdHis',time());
            $data['zdid'] = UID;
			//查看人员，隔开
			$data['helpid'] = ','.$data['helpid'];
			if($model = DeliveryModel::create($data)){
				flow_detail($data['name'],'sales_delivery','sales_delivery','sales/delivery/task_list',$model['id']);
//				//记入行为
					foreach($data['mid'] as $k => $v){
            		$info = array();
            		$info = [
            				'pid'=>$model['id'],
            				'itemsid'=>$v,            				
            				'fhsl'=>$data['fhsl'][$k],
            				'je'=>$data['je'][$k],
							'bzxq'=>$data['bzxq'][$k],
            				'bz'=>$data['bz'][$k],
            		];    
          		DeliverydetailModel::create($info);         		      	
          }           	      
				$this->success('新增成功！',url('index'));
			}else{
				$this->error('新增失败！');
			}
	}
		$date = date('Y-m-d');
		return Zbuilder::make('form')
		 ->addGroup(
        [
          '新增发货' =>[
            ['hidden','zrid'],
			['hidden','helpid'],
			['hidden','oid'],
			['hidden','uid',UID],
			['hidden','zdid',UID],
			['text:4','name','发货名称'],
			['linkage:4','monophyletic','源单类型','',[3=>'销售合同'],'',url('get_yd'),'monophycode'],
			['select:4','monophycode','源单号'],
			['text:4','customer_name','客户名称'],
			['text:4','phone','客户联系方式'],
			['select:4','paytype','支付方式','',[-2=>'转账',-1=>'支付宝',0=>'微信',1=>'支票',2=>'现金']],
			['select:4','goodtype','交货方式','',[0=>'一次性交货',1=>'分批交货']],
			['select:4','transport','运送方式','',[-1=>'空运',0=>'海运',1=>'快递']],
			['select:4','currency','币种','',[-1=>'美元',0=>'人民币',1=>'欧元']],
			['number:4','parities','汇率%'],
			['text:4','zrname','业务员','','','','disabled'],
			['text:4','department','所属部门','','','','disabled'],
			['text:4','deliveryman','收货人'],
			['text:4','deliveryphone','收货人电话(手机)'],
			['text:4','addrss','收货地址'],
			['text:4','goodaddrss','发货地址'],
			['text:4','zrname','发货人'],
			['number:4','money','运费金额(元)'],
			['static:4','zdname','制单人','',get_nickname(UID)],	
			['static:4','create_time','制单时间','',$date],							
			['textarea:6','helpname','可查看人员'],			
			['files','file','附件'],
			['textarea','note','备注'],	
          ],
          '新增物品' =>[
            ['hidden', 'materials_list'],
          ]
        ]
      )
		
		->setExtraHtml(outhtml2())
		->setExtraJs(outjs2())
		->js('Delivery')
		->fetch();
	}

	/**
     * 弹出工艺列表
     * @author 黄远东 <641435071@qq.com>
     */
    public function choose_materials($materials = '',$pid = null)
    {    	
	$map['status'] = 1;
	if($pid!==null){
		$map['type'] = $pid;
		$map['id'] = ['not in',$materials];		
		$data = MaterialModel::where($map)->select();			
		$html = '';	 
		if($data){									
				foreach($data as $k => $v){								
				$html .='<tr>                                    	
			                <td class="text-center">
			                    <label class="css-input css-checkbox css-checkbox-primary">
			                        <input class="ids" onclick="che(this)" type="checkbox" name="ids[]" value="'.$v['id'].'"><span></span>
			                    </label>
			                </td>			             
		                    <td>'.$v['id'].'</td>
		                    <td>'.$v['name'].'</td>
			                <td>'.$v['code'].'</td>
		                    <td>'.$v['unit'].'</td>
		                    <td>'.$v['version'].'</td>
		                    <td>'.$v['price'].'</td>
		                    <td>'.$v['color'].'</td>		                   
		                    <td>'.$v['brand'].'</td>
		                    <td>'.$v['status'].'</td>	                                                                                                    		                                                         
	          			</tr>';
			}				
		}else{
			$html .='<tr class="table-empty">
                        <td class="text-center empty-info" colspan="10">
                            <i class="fa fa-database"></i> 暂无数据<br>
                        </td>
                    </tr>';
		}  
		return $html;		
	}
	 	$data = MaterialModel::where($map)->select();
		$this->assign('data',$data);
		$this->assign('resulet',MaterialTypeModel::getOrganization());    
    	// 查询
    	$map = $this->getMap();
    	$map['id'] = ['not in',$materials];
    	// 排序
    	$order = $this->getOrder('create_time desc');
    	// 数据列表
    	$data_list = MaterialModel::getList($map,$order);    
    	$btn_pick = [
    			'title' => '选择',
    			'icon'  => 'fa fa-plus-circle',
    			'class' => 'btn btn-xs btn-success',
    			'id' => 'pick'
    	];   
    	    $js = <<<EOF
            <script type="text/javascript">
                $('#pick').after('<input id="pickinp" type="hidden" name="materialsid">');
                	$('#pickinp').val({$materials});
            </script>
EOF;
$ck = HouseModel::column('id,name');
		$html = ' <script type="text/javascript">
            var house_select = \'<select name="ck[]">';
        foreach ($ck as $key => $value) {
            $html.='<option value="'.$key.'">'.$value.'</option>';
            
        }
        $html.='</select>\';
        </script>';
    	// 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['name' => '物品名称']) // 设置搜索框
            ->addOrder('id,create_time') // 添加排序
            ->setPageTitle('选择物品')
            ->addColumns([ // 批量添加数据列
                ['id', '编号'], 
            	['name', '物品名称'],           	
            	['version', '规格型号',],
            	['unit', '单位'],
				['price', '单价'],
            	['status', '启用状态', 'status'],
            ])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js.$html)
    	->js('Delivery')
    	->addTopButton('pick', $btn_pick)
    	->assign('empty_tips', '暂无数据')
    	->fetch('admin@choose/choose'); // 渲染页面
    }

	public function edit($id = null){
		if($id == null) $this->error('参数错误');
		if($this->request->isPost()){
			$data = $this->request->post();
			// 验证
			$result = $this->validate($data, 'delivery');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			if($model = DeliveryModel::update($data)){				
				$mlist = explode(',',$data['materials_list']);
            	$oldmlist = explode(',',$data['old_plan_list']);
            	$dif = array_diff($oldmlist,$mlist);
            	ItemsModel::where(['itemsid'=>['in',$dif],'pid'=>$id])->delete();
            	foreach($data['mid'] as $k => $v){
            		$info = array();
            		if($data['mlid'][$k]){
            			$info = [
            					'id'=>$data['mlid'][$k],
            					'pid'=>$model['id'],
	            				'itemsid'=>$v,
	            				'number'=>$data['number'][$k],
	            				'note'=>$data['mnote'][$k]
            			];
            			ItemsModel::update($info);
            		}else{         			
            			$info = [
            					'pid'=>$model['id'],
	            				'itemsid'=>$v,
	            				'number'=>$data['number'][$k],
	            				'note'=>$data['mnote'][$k]
            			];
            			ItemsModel::create($info);
            		}		
            	}
            	//记录行为
				return $this->success('修改成功',url('index'));
			}else{
				return $this->error('修改失败');
			}
		}
		$info = DeliveryModel::getOne($id);
		//获取昵称
			$nickname = Task_detailModel::get_nickname();			
			$zrid = $info['zrid'];
			$helpid = $info['helpid'];
			$helpmane = Task_detailModel::get_helpname($helpid);
			$customer_name = OrderModel::customer_name();
			$info->zrname = $nickname[$zrid];
			$info->helpname = $helpmane;
		$phone = OrderModel::get_phone();
		$date = date('Y-m-d');
		return ZBuilder::make('form')
		->addGroup([
		'编辑发货'=>[
			['hidden', 'id'],
			['hidden','zrid'],
			['hidden','helpid'],
			['text:4','name','发货名称'],
			['select:4','monophyletic','发货来源','',OrderModel::get_monophyletic()],
			['text:4','customer_name','客户名称'],
			['select:4','customer_name1','客户名称1','',$customer_name,'','','hidden'],
			['number:4','phone','客户联系方式'],
			['select:4','phone1','客户联系方式1','',$phone,'','','hidden'],
			['select:4','paytype','支付方式','',[-1=>'支付宝',0=>'微信',1=>'银行卡',2=>'现金']],
			['select:4','goodtype','交货方式','',GoodModel::get_good()],
			['select:4','transport','运送方式','',TransportModel::get_Transport()],
			['date:4','document_time','预计发货时间','',$date],
			['text:4','deliveryphone','收货人电话(手机)'],
			['text:4','addrss','收货地址'],
			['text:4','goodaddrss','发货地址'],
			['text:4','zrname','发货人','',$nickname[$zrid]],
			['number:4','money','运费金额(元)'],
			['select:4','department','所属部门','', OrganizationModel::getMenuTree2()],						
			['textarea:8','helpname','可查看人员','',$helpmane],					
			['textarea','note','备注'],
			['radio','status','状态', '', ['禁用', '启用'], 1],	
		],
          '新增物品' =>[
            ['hidden', 'materials_list'],
            ['hidden', 'old_plan_list'],
          ]			
		])
		->setExtraHtml(outhtml2())
		->setExtraJs(outjs2())
		->setFormData($info)
		->js('test')
		->fetch();
	}
	/**
     * 编辑生成物品表格
     * @param array $record 行为日志
     * @author 黄远东<641435071@qq.com>
     * @return mixed
     *///明细
    public function tech($pid = '',$materials_list = '')
    {
    	if($materials_list == '' || $materials_list == 'undefined') {
    		$html = '';	
    	}else{
    		$map = ['sales_delivery_detail.pid'=>$pid,'stock_material.id'=>['in',($materials_list)]];
    		$order = '';
    		$data = $data_list = DeliveryModel::getDetail($map);
    		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称1</td><td>单位</td><td>规格</td><td>售价</td><td>发货数量</td><td>包装需求</td><td>备注</td></tr>';
    		foreach ($data as $k => $v){ 
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['itemsid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['price'].'</td><td>'.$v['fhsl'].'</td><td>'.$v['bzxq'].'</td><td>'.$v['bz'].'</td></tr>';
    		}   		
    		$html .= '</tbody></table></div>';
    
    	}
    	return $html;
    }
	//获取单源明细
	//$monophyletic 源单类型
	//$monophycode 源单号
	public function getDetail($monophyletic = '',$monophycode = ''){
		if($monophyletic == 1){
			$data = OpportModel::getDetail($monophycode);
			$data['zrid'] = OpportModel::where('id',$monophycode)->value('zrid');
			$data['oid'] = OpportModel::where('id',$monophycode)->value('department');
		}elseif($monophyletic == 2){
			$data = OfferModel::getDetail($monophycode);
			$data['zrid'] = OfferModel::where('id',$monophycode)->value('zrid');
			$data['oid'] = OfferModel::where('id',$monophycode)->value('department');
		}elseif($monophyletic == 3){
			$data = ContractModel::getDetail($monophycode);
			$data['zrid'] = ContractModel::where('id',$monophycode)->value('zrid');
			$data['oid'] = ContractModel::where('id',$monophycode)->value('oid');	
		}elseif($monophyletic == 4){
			$data = OrderModel::getDetail($monophycode);
			$data['zrid'] = OrderModel::where('id',$monophycode)->value('zrid');
			$data['oid'] = OrderModel::where('id',$monophycode)->value('oid');	
		}elseif($monophyletic == 0){
			$data = '';
		}
		return $data;
	}
	public function delete($record = [])
    {
   		$ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    	// 删除节点
    	if (DeliveryModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		//$details = '生产任务ID('.$ids.'),操作人ID('.UID.')';
    		//action_log('produce_plan_delete', 'produce_plan', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }
    public function get_yd($monophyletic = '')
    {
        if($monophyletic==0){
            $list= ['0'=>'无']; 
        }elseif($monophyletic==1){
            $list = OpportModel::getName();
        }elseif($monophyletic==2){
            $list = OfferModel::getName();
        }elseif($monophyletic==3){
        	$list = ContractModel::getName();
        }elseif($monophyletic==4){
        	$list = OrderModel::getName();
        }
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        foreach ($list as $key => $value) {
             $arr['list'][] = ['key'=>$key,'value'=>$value];
        }
        return json($arr);
    }
    //查看
	public function task_list($id = null){
		if($id == null) $this->error('参数错误');
		$info = DeliveryModel::getOne($id);
		$info['materials_list'] = implode(DeliveryModel::getMaterials($id),',');
		//$info['helpname'] = Task_detailModel::get_helpname($info['helpid']);
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addGroup([
		'销售出库'=>[
			['hidden', 'id'],
			['static:4','name','合同名称'],
			['select:4','monophyletic','源单类型','',[3=>'销售合同']],
			['static:4','monophycode','源单号'],
			['static:4','customer_name','客户名称'],
			['static:6','phone','客户联系方式(手机)'],
			['select:4','paytype','支付方式','',[-2=>'转账',-1=>'支付宝',0=>'微信',1=>'支票',2=>'现金']],
			['select:4','goodtype','交货方式','',[0=>'一次性交货',1=>'分批交货']],
			['select:4','transport','运送方式','',[-1=>'空运',0=>'海运',1=>'快递']],
			['select:4','currency','币种','',[-1=>'美元',0=>'人民币',1=>'欧元']],
			['static:4','parities','汇率%'],
			['static:4','zrname','业务员'],
			['static:4','bm','所属部门'],	
			['static:4','deliveryman','收货人'],
			['static:4','deliveryphone','收货人电话(手机)'],
			['static:4','addrss','收货地址'],
			['static:4','goodaddrss','发货地址'],
			['static:4','zrname','发货人'],
			['number:4','money','运费金额(元)'],
			['static:4','zdname','制单人'],	
			['static:4','create_time','制单时间'],
			['static:6','helpname','可查看人员'],	
			['static','note','备注'],									
		],
          '销售出库明细' =>[
            ['hidden', 'materials_list'],
            ['hidden', 'old_plan_list'],
          ]			
		])
		->setFormData($info)
		->js('Delivery')
		->fetch();
	}
}
