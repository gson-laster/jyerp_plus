<?php
namespace app\stock\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\stock\model\Purchase as PurchaseModel;
use app\stock\model\House as HouseModel;
use app\stock\model\Purchasedetail as PurchasedetailModel;
use app\user\model\Organization as OrganizationModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\purchase\model\Arrival as ArrivalModel;
use app\task\model\Task_detail as Task_detailModel;
use app\supplier\model\Supplier as SupplierModel;
use think\Db;
/**
 * 采购入库控制器
 */
class Purchase extends Admin
{
	/*
	 * 采购入库列表
	 * @author HJP<957547207>
	 */
	public function index()
	{
		// 查询
		$map = $this->getMap();
		// 排序
		$order = $this->getOrder('stock_purchase.create_time desc');
		// 数据列表
		$data_list = PurchaseModel::getList($map,$order);
		 $task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
		// 使用ZBuilder快速创建数据表格
		return ZBuilder::make('table')
			->addTimeFilter('stock_purchase.create_time') // 添加时间段筛选
			->setSearch(['stock_purchase.name' => '入库主题'], '', '', true)
			->addOrder(['code']) // 添加排序
			->addFilter(['oid'=>'admin_organization.title','putinid'=>'admin_organization.title']) // 添加筛选
			->addColumns([
				['code', '入库单号'],
				['intime','入库日期','date'],
				['sid','供应商'],
				['intype','入库类别'],
				['house_id','仓库'],
				['note','备注'],
				['right_button', '操作', 'btn']		
			])
			->addTopButtons('delete') // 批量添加顶部按钮
            ->addRightButtons('delete')       
 			->addRightButton('task_list',$task_list,true) // 查看右侧按钮                 
            ->setRowList($data_list) // 设置表格数据            
            ->fetch(); // 渲染模板
	}
	public function add(){
		if($this->request->isPost()){
			$data = $this->request->post();
			//dump($data);die;
			// 验证			
			$result = $this->validate($data, 'purchase');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			$data['intime'] = strtotime($data['intime']);
			//$data['code'] = 'CGRK'.date('YmdHis',time());
			if($model = PurchaseModel::create($data)){
				//flow_detail($data['name'],'stock_purchase','stock_purchase','stock/purchase/task_list',$model['id']);
				//记入行为
				foreach($data['mid'] as $k => $v){
            		$info = array();
					$kcinfo = array();
            		$info = [
            				'pid'=>$model['id'],
            				'itemsid'=>$v,
            				'rksl'=>$data['rksl'][$k],
							'dj'=>$data['dj'][$k],
            				'je'=>$data['je'][$k],
							'type'=>$data['type'][$k],
            		];  
					$kcinfo = [
            				'materialid'=>$v,
            				'number'=>$data['rksl'][$k],
							'price'=>$data['dj'][$k],
							'total'=>$data['rksl'][$k]*$data['dj'][$k],
							'material_type'=>$data['type'][$k],
							'ckid'=>$model['house_id'],
            		]; 
            		PurchasedetailModel::create($info); 
						
					if(empty(db::name('stock_stock')->where(['materialid'=>$v,'ckid'=>$model['house_id']])->find()))
					{
						db::name('stock_stock')->insert($kcinfo);
					}else{												
						$num = db::name('stock_stock')->where(['materialid'=>$v,'ckid'=>$model['house_id']])->field('number,total')->find();						
						$new_num = $num['number']+$kcinfo['number'];
						$total = $kcinfo['total']+$num['total'];
						$price = $total/$new_num;
						db::name('stock_stock')->where(['materialid'=>$v,'ckid'=>$model['house_id']])->update(['number'=>$new_num,'price'=>number_format($price,2),'total'=>$total]);
					}		
            	}  
				$this->success('新增成功！',url('index'));
			}else{
				$this->error('新增失败！');
			}
		}
		return Zbuilder::make('form')
		 ->addGroup(
        [
          '采购入库单' =>[
            //['hidden','zrid'],
            //['hidden', 'helpid'],            
          	//['hidden', 'warehouses',UID],           
          	//['hidden', 'zdid',UID],           
          	//['hidden', 'deliverer',UID],           
			//['text:4','name','入库主题'],
			//['select:4','order_id','采购到货单','',ArrivalModel::getName()],
			//['text:4','cid','采购员','','','','disabled'],
			//['text:4','oid','采购部门','','','','disabled'],
			//['static:4', 'delivname', '交货人','',get_nickname(UID)],
			//['text:4', 'zrname', '验收人'],
			//['static:4', 'warehname', '入库人','',get_nickname(UID)],
			//['select:4', 'putinid', '入库部门','', OrganizationModel::getMenuTree2()],
			//['static:4','zdname','制单人','',get_nickname(UID)],	
			//['files','file','附件'],
			//['textarea:8', 'helpname', '可查看该入库人员'],
			['text:4','code','入库单号'],
			['date:4','intime','入库日期'],
			['select:4','sid','供应商','',SupplierModel::getName()],
			['text:4','intype','入库类别'],
			['select:4','house_id','仓库','',HouseModel::getTree()],
			['text:4','note','备注'],	
          ],
          '入库明细' =>[
            ['hidden', 'materials_list'],
			['hidden', 'controller',request()->controller()],
          ]
        ]
      )		
		->js('stock')
		->setExtraHtml(outhtml2())
		->setExtraJs(outjs2())		
		->fetch();
	}
	public function creatMaterial(){
	    $data = $this->request->Get();
		// 验证
		$result = $this->validate($data, 'Material');
		if (true !== $result) $this->error($result);
		if(MaterialModel::create($data)){
				$msg = '新增成功';
		}else{
				$mag = '新增失败';
		}
		return $msg;
	}
	public function get_Mateplan($mateplan = ''){
		if($mateplan == ''){
			return $html='<span>请选择采购到货单</span>';	
		}
		$map = ['aid'=>$mateplan];
		$data = ArrivalModel::getMaterial($map);			
		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>入库仓库</td><td>到货数量</td><td>入库数量</td><td>单价(元)</td><td>金额(元)</td><td>供应商</td><td>备注</td></tr>';
    		foreach ($data as $k => $v){ 
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['wid'].'"><input type="hidden" name="type[]" value="'.$v['type'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td><input type="hidden" name="ck[]" value="'.$v['house_id'].'">'.$v['ckname'].'</td><td>'.$v['num'].'</td><td><input type="number" class="rksl" oninput="inpu(this)" name="rksl[]" value="'.$v['num'].'"></td><td class="price"><input type="hidden" name="dj[]" value="'.$v['price'].'">'.$v['price'].'</td><td><input type="number" class="je" name="je[]" readonly value="'.$v['plan_money'].'"></td><td>'.$v['sname'].'</td><td><input type="text" name="bz[]" value="'.$v['remarks'].'"></td></tr>';
    		}   		
    		$html .= '</tbody></table></div>';
		return $html;				
	}
	public function get_Detail($order_id = ''){
			$data = PurchaseModel::get_Detail($order_id);
		return $data;
	}
	
	public function delete($record = [])
    {
   		$ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    	// 删除节点
    	if (PurchaseModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		$details = '生产任务ID('.$ids.'),操作人ID('.UID.')';
    		//action_log('produce_plan_delete', 'produce_plan', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }    	
	//查看
    public function task_list($id = null){
    	if($id == null) $this->error('参数错误');		
		$info = PurchaseModel::getOne($id);
		$info['materials_list'] = implode(PurchaseModel::getMaterials($id),',');
		$info['controller'] = request()->controller();
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addGroup([
		'采购入库'=>[
			['hidden','id'],    			
			['static:4','code','入库单号'],
			['static:4','intime','入库日期'],
			['static:4','sid','供应商'],
			['static:4','intype','入库类别'],
			['static:4','house_id','仓库'],
			['static:4','note','备注'],									
		],
          '采购入库明细' =>[
            ['hidden', 'materials_list'],
            ['hidden', 'controller'],
          ]			
		])
		->setExtraJs(outjs2())
		->setFormData($info)
		->js('stock')
		->fetch();
    }
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
				$status = $v['status']?'<span class="label label-success">启用</span>':'<span class="label label-warning">禁用</span>';
				$html .='<tr>                                    	
			                <td class="text-center">
			                    <label class="css-input css-checkbox css-checkbox-primary">
			                        <input class="ids" onclick="che(this)" type="checkbox" name="ids[]" value="'.$v['id'].'"><span></span>
			                    </label>
			                </td>			             
		                    <td>'.$v['id'].'</td>
		                    <td>'.$v['code'].'</td>
		                    <td>'.$v['name'].'</td>
		                    <td>'.$v['version'].'</td>
		                    <td>'.$v['unit'].'</td>
							<td style="display:none;">'.$v['type'].'</td>
		                    <td>'.$status.'</td>		                 		                                                                                                                  		                                                         
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
		$add_pick = [
    			'title' => '新增物资',
    			'icon'  => 'fa fa-plus',
    			'class' => 'btn btn-xs btn-primary',
    			'id' => 'add_pick'
    	];
		$MaterialType = MaterialTypeModel::where('status',1)->column('id,title');
		$type = ' <select class="js-select2 form-control select2-hidden-accessible" id="type" name="type" data-allow-clear="true" data-placeholder="请选择一项" tabindex="-1" aria-hidden="true">';
        foreach ($MaterialType as $key => $value) {
            $type.='<option value="'.$key.'">'.$value.'</option>';            
        }
        $type.='</select>';
		$ck = HouseModel::where('status',1)->column('id,name');
        $house = '<select class="js-select2 form-control select2-hidden-accessible" id="house_id" name="house_id" data-allow-clear="true" data-placeholder="请选择一项" tabindex="-1" aria-hidden="true">';
        foreach ($ck as $key => $value) {
            $house.='<option value="'.$key.'">'.$value.'</option>';            
        }
        $house.='</select>';
$html = <<<EOF
            <div class="add_pick" style="display: none;height: 100%;overflow: auto;">
				<div class="form-group col-md-12 col-xs-12 " id="form_group_code">
					<label class="col-xs-12" for="code">编号</label>
				<div class="col-sm-12">
					<input class="form-control" type="text" id="code" name="code" value="" placeholder="请输入物品名称">
				</div>
				</div>
	            <div class="form-group col-md-12 col-xs-12 " id="form_group_name">
					<label class="col-xs-12" for="name">物品名称</label>
				<div class="col-sm-12">
					<input class="form-control" type="text" id="name" name="name" value="" placeholder="请输入物品名称">
				</div>
				</div>
				<div class="form-group col-md-12 col-xs-12 " id="form_group_type">
					<label class="col-xs-12" for="type">物品类型</label>
					<div class="col-sm-12">
						{$type}
					</div>
				</div>
				<div class="form-group col-md-12 col-xs-12 " id="form_group_version">
					<label class="col-xs-12" for="version">规格型号</label>
					<div class="col-sm-12">       
						<input class="form-control" type="text" id="version" name="version" value="" placeholder="请输入规格型号">
					</div>
				</div>
				<div class="form-group col-md-12 col-xs-12 " id="form_group_unit">
					<label class="col-xs-12" for="unit">计量单位</label>
					<div class="col-sm-12">
						<input class="form-control" type="text" id="unit" name="unit" value="" placeholder="请输入计量单位">
					</div>
				</div>
				<div class="form-group col-md-12 col-xs-12 " id="form_group_funit">
					<label class="col-xs-12" for="funit">辅计量单位</label>
					<div class="col-sm-12">
						<input class="form-control" type="text" id="funit" name="funit" value="" placeholder="请输入辅计量单位">
					</div>
				</div>
				<div class="form-group col-md-12 col-xs-12 " id="form_group_weight">
					<label class="col-xs-12" for="weight">重量</label>
					<div class="col-sm-12">
						<input class="form-control" type="number" id="weight" name="weight" value="0" placeholder="请输入重量">
					</div>
				</div>
				<div class="form-group col-md-12 col-xs-12 " id="form_group_size">
					<label class="col-xs-12" for="size">尺寸</label>
					<div class="col-sm-12">
						<input class="form-control" type="text" id="size" name="size" value="" placeholder="请输入尺寸">
					</div>
				</div>
				<div class="form-group col-md-12 col-xs-12 " id="form_group_house_id">
					<label class="col-xs-12" for="house_id">主放仓库</label>
					<div class="col-sm-12">
						{$house}
					</div>
				</div>                                                                                                                                         
			</div>                                                                                                                                                                                                                                                                                              
EOF;
		
    	// 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['name' => '物品名称']) // 设置搜索框
            ->addOrder('id,create_time') // 添加排序
            ->setPageTitle('选择物品')
            ->addColumns([ // 批量添加数据列
                ['id', '序号'], 
                ['code', '编号'], 
            	['name', '材料名称'],           	
            	['version', '规格型号',],
            	['unit', '计量单位'],
				['type', '类型','','','','hidden'],
            	['status', '启用状态', 'status'],
            ])
    	->setRowList($data_list) // 设置表格数据
    	->js('stock')
		->setExtraHtml($html, 'toolbar_bottom')
    	->addTopButton('pick', $btn_pick)
		->addTopButton('add_pick', $add_pick)
    	->assign('empty_tips', '暂无数据')
    	->fetch('admin@choose/choose'); // 渲染页面
    }
	//明细
     public function tech($pid = '',$materials_list = '')
    {
    	if($materials_list == '' || $materials_list == 'undefined') {
    		$html = '';	
    	}else{
    		$map = ['stock_purchase_detail.pid'=>$pid,'stock_material.id'=>['in',($materials_list)]];
    		$data = PurchaseModel::getDetail($map);
    		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>入库数量</td><td>单价</td><td>金额(元)</td></tr>';
    		foreach ($data as $k => $v){ 
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['itemsid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['rksl'].'</td><td>￥'.number_format($v['dj'],2).'</td><td>￥'.number_format($v['je'],2).'</td></tr>';
    		}   		
    		$html .= '</tbody></table></div>';   
    	}
    	return $html;
    }
	
    
}
