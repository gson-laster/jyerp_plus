<?php
namespace app\purchase\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\purchase\model\Ask as AskModel;
use app\purchase\model\Plan as PlanModel;
use app\purchase\model\Hetong as HetongModel;
use app\purchase\model\Order as OrderModel;
use app\purchase\model\Money as MoneyModel;
use app\purchase\model\Type as TypeModel;
use app\admin\model\Access as AccessModel;
use app\user\model\Organization as OrganizationModel;
use app\supplier\model\Type as SupplierTypeModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\purchase\model\OrderMaterial as OrderMaterialModel;
use app\supplier\model\Supplier as SupplierModel;
use think\Db;
/**
 *  施工日志
 */
class Order extends Admin
{
	//
	public function lists()
	{

        $map = $this->getMap();
        $order = $this->getOrder('purchase_order.id desc');

        $btn_detail = [
            'title' => '查看详情',
            'icon'  => 'fa fa-fw fa-search',
            'href'  => url('detail', ['id' => '__id__'])
        ];

        $data_list = OrderModel::getList($map,$order);

        $purchase_type = TypeModel::where('status=1')->column('id,name');  //采购类型
        $pay_type = [0=>'现金',1=>'转账',2=>'支票',3=>'微信',4=>'支付宝']; //支付方式
        $arrival_type = [0=>'一次性交货',1=>'分批交货'];                   //交货方式
        $transport_type = [0=>'空运',1=>'海运',2=>'快递'];                 //运输方式
        $balance_type = [ 0=>'分段结算',1=>'合同结算',2=>'进度结算',3=>'竣工后一次结算']; //结算方式


        return ZBuilder::make('table')
                    ->setSearch(['purchase_order.name'=>'主题','purchase_order.supplier_username'=>'对方代表'],'','',true) // 设置搜索框
                    ->addTimeFilter('purchase_order.order_time') // 添加时间段筛选
                    ->addFilter(['purchase_type_name' => 'purchase_type.name']) // 添加筛选
                    ->addFilter(['purchase_organization_name' => 'admin_organization.title']) // 添加筛选
                    ->addFilter('pay_type',$pay_type) // 添加筛选
                    ->addFilter('arrival_type',$arrival_type) // 添加筛选
                    ->addFilter('transport_type',$transport_type) // 添加筛选
                    ->addFilter('balance_type',$balance_type) // 添加筛选
                    ->hideCheckbox()
                    ->addOrder('purchase_order.number,purchase_order.order_time') // 添加排序
                    ->addColumns([ // 批量添加列
                        ['number', '编号'],
                        ['name', '主题'],
//                      ['supplier_name', '供应商'],
                        ['purchase_type_name', '采购类型',$purchase_type],
                        ['purchase_nickname', '采购员'],
                        ['purchase_organization_name', '采购部门'],
                        ['is_add_tax', '是否增值税','status','',[0=>'否',1=>'是']],
                        ['pay_type', '支付方式',$pay_type],
                        ['order_time', '签单时间','date'],
                        ['arrival_type', '交货方式',$arrival_type],
                        ['transport_type', '运输方式',$transport_type],
                        ['balance_type', '结算方式',$balance_type],
//                      ['supplier_username', '对方代表'],
                        ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                        ['right_button','操作']
                    ])
                    // ->setRowList($data_list) // 设置表格数据
                    ->addRightButton('btn', $btn_detail,true) // 添加授权按钮
                    ->setRowList($data_list) // 设置表格数据 
                    ->fetch();
	        	
	        	
	}

	public function index()
	{

        $map = $this->getMap();
        $order = $this->getOrder('purchase_order.id desc');

     	$btn_detail = [
		    'title' => '查看详情',
		    'icon'  => 'fa fa-fw fa-search',
		    'href'  => url('detail', ['id' => '__id__'])
		];

		$data_list = OrderModel::getList($map,$order);

        $purchase_type = TypeModel::where('status=1')->column('id,name');  //采购类型
        $pay_type = [0=>'现金',1=>'转账',2=>'支票',3=>'微信',4=>'支付宝']; //支付方式
        $arrival_type = [0=>'一次性交货',1=>'分批交货'];                   //交货方式
        $transport_type = [0=>'空运',1=>'海运',2=>'快递'];                 //运输方式
        $balance_type = [ 0=>'分段结算',1=>'合同结算',2=>'进度结算',3=>'竣工后一次结算']; //结算方式


        return ZBuilder::make('table')
                    ->setSearch(['purchase_order.name'=>'主题','purchase_order.supplier_username'=>'对方代表'],'','',true) // 设置搜索框
                    ->addTimeFilter('purchase_order.order_time') // 添加时间段筛选
                    ->addFilter(['purchase_type_name' => 'purchase_type.name']) // 添加筛选
                    ->addFilter(['purchase_organization_name' => 'admin_organization.title']) // 添加筛选
                    ->addFilter('pay_type',$pay_type) // 添加筛选
                    ->addFilter('arrival_type',$arrival_type) // 添加筛选
                    ->addFilter('transport_type',$transport_type) // 添加筛选
                    ->addFilter('balance_type',$balance_type) // 添加筛选
                    ->hideCheckbox()
                    ->addOrder('purchase_order.number,purchase_order.order_time') // 添加排序
                    ->addColumns([ // 批量添加列
                        ['number', '编号'],
                        ['name', '主题'],
//                      ['supplier_name', '供应商'],
                        ['purchase_type_name', '采购类型',$purchase_type],
                        ['purchase_nickname', '采购员'],
                        ['purchase_organization_name', '采购部门'],
                        ['is_add_tax', '是否增值税','status','',[0=>'否',1=>'是']],
                        ['pay_type', '支付方式',$pay_type],
                        ['order_time', '签单时间','date'],
                        ['arrival_type', '交货方式',$arrival_type],
                        ['transport_type', '运输方式',$transport_type],
                        ['balance_type', '结算方式',$balance_type],
//                      ['supplier_username', '对方代表'],
                        ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                        ['right_button','操作']
				    ])
				    // ->setRowList($data_list) // 设置表格数据
				    ->addRightButton('btn', $btn_detail,true) // 添加授权按钮
				    ->addRightButton('delete') //添加删除按钮
				    ->addTopButton('add') //添加删除按钮
                    ->setRowList($data_list) // 设置表格数据 
	                ->fetch();
	        	
	}

	public function add(){

        if ($this->request->isPost()) {
            $data = $this->request->post();
            // 验证
            $data['number'] = 'CGDD'.date('YmdHis',time());
            $data['order_time'] = strtotime($data['order_time']);
            $data['create_time'] = time();
            $data['create_uid'] = UID;
            $data['order_uid'] = $data['zrid'];
            $data['look_user'] = '-'.implode('-',explode(',',$data['helpid'])).'-';
            $a = HetongModel::getOne($data['source_id']);
            $data['purchase_type']=$a->purchase_type;
            $data['purchase_organization']=$a->purchase_organization;
            $data['purchase_uid']=$a->purchase_uid;
            $data['is_add_tax']=$a->is_add_tax;
         
            $result = $this->validate($data, 'Order');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            if(empty($data['mid'])){
            	$this->error('请填写物资明细');
            }else{
	            if ($res = OrderModel::create($data)) {
	                foreach($data['mid'] as $k => $v){
	                    $info = array();
	                    $info = [
	                            'aid'=>$res['id'],
	                            'wid'=>$v,
	                            'num'=>$data['num'][$k],
	                            'plan_num'=>$data['plan_num'][$k],
	                            'plan_money'=>$data['plan_money'][$k],
	                            'supplier'=>$data['supplier'][$k],
	                            'supplier_username'=>$data['supplier_username'][$k],
	                    ];  
	                    OrderMaterialModel::create($info);                      
	                } 
	                flow_detail($data['name'],'purchase_order','purchase_order','purchase/order/detail',$res['id']);
	                action_log('purchase_order_add', 'purchase_order', $res['id'], UID, $res['id']);
	                $this->success('新增成功',url('index'));
	            } else {
	                $this->error('新增失败');
	            }
	        }
        }
         $ydnumber = HetongModel::where('status','1')->column('id,name');
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('添加合同')           
            ->addGroup(
                    [
                        '订单信息' =>[
                            ['text:3', 'name', '主题'],
                            ['select:3','source_id','合同单号','',$ydnumber],
                            ['text:3','purchase_type','采购类型','','','','disabled'],
//                          ['linkage:3','supplier_type', '供应商类型','',SupplierTypeModel::column('id,name'),'',url('get_supplier_name'),'supplier_id'],
//                          ['select:3','supplier_id', '供应商名称'],
//                          ['linkage:3','source','源单类型','',[0=>'无源单',1=>'采购申请',2=>'采购计划',3=>'采购询价',4=>'采购合同'],'',url('get_yd'),'source_id'],
                            ['text:3','purchase_organization', '采购部门','','','','disabled'],
                            ['text:3','purchase_uid','采购员','','','','disabled'],
                            ['text:3','is_add_tax','是否增值税','','','','disabled'],
                            ['select:3','pay_type','支付方式','',[0=>'现金',1=>'转账',2=>'支票',3=>'微信',4=>'支付宝']],
                            ['date:3','order_time','签约时间'],
//                          ['text:3', 'supplier_username', '对方代表'],	
                            ['text:3', 'zrname', '我方代表'],
                            ['text:3', 'supplier_order', '供方订单号'],
                            ['select:3','arrival_type','交货方式','',[0=>'一次性交货',1=>'分批交货']],
                            ['select:3','transport_type','运货方式','',[0=>'空运',1=>'海运',2=>'快递']],
                            ['select:3','balance_type','结算方式','',[ 0=>'分段结算',1=>'合同结算',2=>'进度结算',3=>'竣工后一次结算']],
                            ['select:3','money_type','币种','',[ 0=>'美元',1=>'人民币',2=>'欧元']],
                            ['number:3', 'rate', '汇率'],
                            ['static:3','  ','制单人','',get_nickname(UID)], 
                            ['files:6','file',' 附件'],
                            ['textarea', 'helpname', '可查看人员'],
                            ['hidden','helpid'],                
                            ['hidden','zrid'],
                            ['wangeditor', 'remark','备注'],
                        ],
                        '订单物资明细' =>[
                            ['hidden', 'materials_list'],
                        ]
                    ])    
            ->setExtraHtml(outhtml2())
            ->setExtraJs(outjs2()) 
            ->js('order')    
            ->fetch();

	}
	public function get_Detail($source_id = ''){
		$data = HetongModel::getOne($source_id);
		//获取采购类型
		$data->leixing=TypeModel::where('id',$data->purchase_type)->column('name');
		//获取采购部门
		$data->bumen=OrganizationModel::where('id',$data->purchase_organization)->column('title');
		//获取采购员
		$data->caigouyuan=UserModel::where('id',$data->purchase_uid)->column('nickname');
		//获取是否增值
		$data->is_add_tax=$data->is_add_tax===0?'否':'是';

		return $data;
	}
	
	
	public function get_Mateplan($mateplan = '',$ptype = ''){
//		if($ptype == 1){
//		$materialsid = AskModel::where('id',$mateplan)->value('id');
//		$map = ['aid'=>$materialsid];
//		$data = AskModel::getMaterial($map);
//		}elseif($ptype == 2){
//		$materialsid = PlanModel::where('id',$mateplan)->value('id');
//		$map = ['aid'=>$materialsid];
//		$data = PlanModel::getMaterial($map);	
//		}elseif($ptype == 3){
//		$materialsid = MoneyModel::where('id',$mateplan)->value('id');
//		$map = ['aid'=>$materialsid];
//		$data = MoneyModel::getMaterial($map);	
//		}elseif($ptype == 4){
		$materialsid = HetongModel::where('id',$mateplan)->value('id');
		$map = ['aid'=>$materialsid];
		$data = HetongModel::getMaterial($map);	
//		}else{
//			return $html='<span>请选择源单类型</span>';
//		}
	
//		halt($data);
		 	   		
		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name">
			<table class="table table-bordered">
				<tbody>
					<tr>
						<td>物品名称</td>
						<td>仓库</td>
						<td>单位</td>
						<td>规格</td>
						<td>售价</td>
						<td>采购数量</td>
						<td>金额</td>
						<td>供应商</td>
						<td>对方代表人</td></tr>';
    		foreach ($data as $k => $v){ 
    			
    				$bom = SupplierModel::column('id,name');
    			$html2=[];
				$html2 = '<select name="supplier[]">';
		        foreach ($bom as $key => $value) {
		        	if($v['supplier_id']==$key )
		            	$html2.='<option selected value="'.$key.'">'.$value.'</option>';
		            else
		            	$html2.='<option  value="'.$key.'">'.$value.'</option>';
		        }
		        $html2.='</select>';
    			
    			
    			
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['wid'].'">
    				<input type="hidden" name="mlid[]" value="'.$v['id'].'">
    					<td>'.$v['name'].'</td>
    					<td>'.$v['ckname'].'</td>
    					<td>'.$v['unit'].'</td>
    					<td>'.$v['version'].'</td>
    					<td><input type="number" oninput="input(this)" class="jg" name="plan_num[]" value="'.$v['plan_num'].'"></td>
    					<td><input type="number" oninput="input(this)" class="sl" name="num[]" value="'.$v['num'].'"></td>
    					<td><input type="number" readonly="readonly" name="plan_money[]" class="zj"  value="'.$v['plan_num']*$v['num'].'"></td>
    					<td>'.$html2.'</td>
    					<td><input type="text" name="supplier_username[]" value="'.$v['supplier_username'].'"  ></td></tr>
    					';
    		}   		
    		$html .= '</tbody></table></div>';
		return $html;
	}
	////采购详情     
	public function detail($id=null){
        if($id==null)return $this->error('缺少参数');
        
        $detail = OrderModel::getOne($id);
        $detail['order_user'] = get_nickname($detail['order_uid']);
        $detail['create_uid'] = get_nickname($detail['create_uid']);
        $detail['create_time'] = date('Y-m-d',$detail['create_time']);
        //源单
//      if($detail['source']==1){
//          $ydnumber = AskModel::column('id,name');
//      }elseif($detail['source']==2){
//          $ydnumber = PlanModel::column('id,name');
//      }elseif($detail['source']==3){
//          $ydnumber = MoneyModel::column('id,title');
//      }elseif($detail['source']==4){
            $ydnumber = HetongModel::where(['id'=>$detail->source_id])->column('name');
//      }else{
//          $ydnumber = [0=>'无源单'];
//      }
        $detail->materials_list = implode(OrderMaterialModel::where('aid',$id)->column('id,wid'),',');
        $detail->source_id=$ydnumber[0];
        return ZBuilder::make('form')
        ->setPageTitle('详情')             
        ->addGroup(
            [
                    '订单信息' =>[
                        ['hidden','id'],
                        ['static:3', 'name', '主题'],
                        ['static:3','purchase_type_name','采购类型'],
//                      ['static:3','supplier_name', '供应商名称'],
                        ['static:3','create_uid','制单人','',get_nickname(UID)], 
//                      ['linkage:3','source','源单类型','',[0=>'无源单',1=>'采购申请',2=>'采购计划',3=>'采购询价',4=>'采购合同'],'',url('get_yd'),'source_id'],
                        ['static:3','source_id','合同单号','',$ydnumber],
                        ['static:3','purchase_organization_name', '采购部门'],
                        ['static:3','purchase_nickname','采购员'],
                        ['select:3','is_add_tax','是否增值税','',[0=>'否',1=>'是']],
                        ['select:3','pay_type','支付方式','',[0=>'现金',1=>'转账',2=>'支票',3=>'微信',4=>'支付宝']],
                        ['date:3','order_time','签约时间'],
                    
                        ['select:3','arrival_type','交货方式','',[0=>'一次性交货',1=>'分批交货']],
                        ['select:3','transport_type','运货方式','',[0=>'空运',1=>'海运',2=>'快递']],
                        ['select:3','balance_type','结算方式','',[ 0=>'分段结算',1=>'合同结算',2=>'进度结算',3=>'竣工后一次结算']],
                        ['select:3','money_type','币种','',[ 0=>'美元',1=>'人民币',2=>'欧元']],
                        ['static:3', 'rate', '汇率'],
                        ['static:3', 'supplier_order', '供方订单号'],
                        ['static:3', 'create_time', '制单日期'],
//                      ['static:3', 'supplier_username', '对方代表'],
                        ['static:3', 'order_user', '我方签约人'],
                        ['archives:6','file',' 附件'],
                        ['wangeditor', 'remark','备注'],
                ],
                    '合同物资明细' =>[
                    ['hidden', 'materials_list'],
                    ['hidden', 'old_plan_list'],
                ]
            ])     
        ->setFormData($detail)    
        ->hideBtn('submit') 
        ->js('order')   
        ->fetch();

	}

	//删除
	public function delete($ids = null){		
		if($ids == null) $this->error('参数错误');
		$map['id'] = $ids;
		if($model = OrderModel::where($map)->delete()){	
			//记录行为
        	action_log('purchase_ask_delete', 'purchase_ask', $map['id'], UID,$map['id']);			
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}		
	}
    


    public function get_yd($source = '')
    {
        if($source==1){
            $list = AskModel::column('id,name');
        }elseif($source==2){
            $list = PlanModel::column('id,name');
        }elseif($source==3){
            $list = MoneyModel::column('id,title');
        }elseif($source==4){
            $list = HetongModel::column('id,name');
        }else{
            $list= ['0'=>'无']; 
        }
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        foreach ($list as $key => $value) {
             $arr['list'][] = ['key'=>$key,'value'=>$value];
        }
        return json($arr);
    }

    public function get_tj($purchase_organization = '')
    {
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        $ht = UserModel::where('organization',$purchase_organization)->column('id,nickname');
        foreach ($ht as $key => $value) {
             $arr['list'][] = ['key'=>$key,'value'=>$value];
        }
        return json($arr);
    }

        //供应商
    public function get_supplier_name($supplier_type = '')
    {
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        $ht = SupplierModel::where('type',$supplier_type)->column('id,name');
        foreach ($ht as $key => $value) {
             $arr['list'][] = ['key'=>$key,'value'=>$value];
        }
        return json($arr);
    }

     public function choose_materials($materials = '',$pid = null)
    {       
    $map['status'] = 1;
    if($pid!==null){
        $map['type'] = $pid;
        $map['id'] = ['not in',$materials];     
        $data = MaterialModel::where($map)->select();           
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
                            <td>'.$v['price_tax'].'</td>
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
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['name' => '物品名称']) // 设置搜索框
            ->addOrder('id,create_time') // 添加排序
            ->setPageTitle('选择物品')
            ->addColumns([ // 批量添加数据列
                ['id', '编号'], 
                ['name', '物品名称'],
                ['code', '物品编号'],
                ['unit', '单位'],
                ['version', '规格型号',],
                ['price_tax', '含税售价'],
                ['color', '颜色'],
                ['brand', '品牌'],
                ['status', '启用状态', 'status'],
            ])
        ->setRowList($data_list) // 设置表格数据
        ->setExtraJs($js)
        ->js('order')
        ->addTopButton('pick', $btn_pick)
        ->assign('empty_tips', '暂无数据')
        ->fetch('admin@choose/choose'); // 渲染页面
    }

            //编辑生成物品表格
    public function tech($pid = '',$materials_list = '')
    {
        //dump($materials_list);die;
        $html = $materials_list;
        if($materials_list == '' || $materials_list == 'undefined') {

            $html = ''; 

        }else{

            $map = ['purchase_order_material.aid'=>$pid,'stock_material.id'=>['in',($materials_list)]];

            $data = OrderModel::getMaterial($map);

            $html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name">
            	<table class="table table-bordered">
            		<tbody>
            			<tr>
            				<td>物品名称</td>
            				<td>仓库</td>
            				<td>单位</td>
            				<td>规格</td>
            				<td>售价</td>
            				<td>采购数量</td>
            				<td>金额</td>
            				<td>供应商</td>
            				<td>对方代表</td>
            				</tr>';

            foreach ($data as $k => $v){ 
                $html.='<tr><input type="hidden" name="mid[]" value="'.$v['wid'].'">
                	<input type="hidden" name="mlid[]" value="'.$v['id'].'">
                		<td>'.$v['name'].'</td>
                		<td>'.$v['ckname'].'</td>
                		<td>'.$v['unit'].'</td>
                		<td>'.$v['version'].'</td>
                		<td>￥' . number_format($v['plan_num'],2) . '</td>
                		<td>'.$v['num'].'</td>
                		<td>￥' . number_format($v['plan_money'],2) . '</td>
                		<td>'.$v['sname'].'</td>
                		<td>'.$v['supplier_username'].'</td>
                		</tr>';
            }           

            $html .= '</tbody></table></div>';
    
        }

        return $html;
    }
}   
