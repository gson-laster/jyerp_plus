<?php
namespace app\purchase\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\purchase\model\Ask as AskModel;
use app\purchase\model\Plan as PlanModel;
use app\purchase\model\Arrival as ArrivalModel;
use app\purchase\model\Type as TypeModel;
use app\admin\model\Access as AccessModel;
use app\user\model\Organization as OrganizationModel;
use app\purchase\model\Order as OrderModel;
use app\purchase\model\OrderMaterial as OrderMaterial;
use app\supplier\model\Type as SupplierTypeModel;
use app\supplier\model\Supplier as SupplierModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\purchase\model\ArrivalMaterial as ArrivalMaterialModel;
use think\Db;
/**
 *  施工日志
 */
class Arrival extends Admin
{
    //
    public function lists()
    {
        $map = $this->getMap();
        $order = $this->getOrder('purchase_arrival.id desc');

        $btn_detail = [
            'title' => '查看详情',
            'icon'  => 'fa fa-fw fa-search',
            'href'  => url('detail', ['id' => '__id__'])
        ];
        $type = TypeModel::where('status=1')->column('id,name');
        $data_list = ArrivalModel::getList($map,$order);
       
        $arrival_type = [0=>'一次性交货',1=>'分批交货'];                   //交货方式
        $transport_type = [0=>'空运',1=>'海运',2=>'快递'];                 //运输方式
        return ZBuilder::make('table')
                    ->setSearch(['purchase_arrival.name'=>'主题','admin_user.nickname'=>'点收人'],'','',true) // 设置搜索框
                    ->addTimeFilter('purchase_arrival.consignee_time') // 添加时间段筛选
                    ->addFilter(['purchase_organization_name' => 'admin_organization.title']) // 添加筛选
                    // ->addFilter('purchase_arrival.tid',TypeModel::where('status=1')->column('id,name')) // 添加筛选
                    ->addFilter('arrival_type',$arrival_type) // 添加筛选
                    ->addFilter('transport',$transport_type) // 添加筛选
                    ->hideCheckbox()
                    ->addOrder('purchase_arrival.number,purchase_arrival.consignee_time') // 添加排序
                    ->addColumns([ // 批量添加列
                        ['number', '单据编号'],
                        ['name', '主题'],
                        ['ctype', '采购类型',$type],
//                      ['supplier_name', '供应商'],
						['ciagouyuan','采购员'],
                        ['purchase_organization_name', '部门'],
                        ['arrival_type', '交货方式',$arrival_type],
                        ['transport', '运货方式',$transport_type],
                        ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                        ['consignee_user', '点收人'],
                        ['consignee_time', '点收日期','date'],
                        ['right_button','操作']
                    ])
                    ->setRowList($data_list) // 设置表格数据
                    ->addRightButton('btn', $btn_detail,true) // 添加授权按钮
                    ->fetch();
                
                
    }

    public function index()
    {

        $map = $this->getMap();
        $order = $this->getOrder('purchase_arrival.id desc');

        $btn_detail = [
            'title' => '查看详情',
            'icon'  => 'fa fa-fw fa-search',
            'href'  => url('detail', ['id' => '__id__'])
        ];
        $type = TypeModel::where('status=1')->column('id,name');
        $data_list = ArrivalModel::getList($map,$order);
        $arrival_type = [0=>'一次性交货',1=>'分批交货'];                   //交货方式
        $transport_type = [0=>'空运',1=>'海运',2=>'快递'];                 //运输方式
        return ZBuilder::make('table')
                    ->setSearch(['purchase_arrival.name'=>'主题','admin_user.nickname'=>'点收人'],'','',true) // 设置搜索框
                    ->addTimeFilter('purchase_arrival.consignee_time') // 添加时间段筛选
                    ->addFilter(['purchase_organization_name' => 'admin_organization.title']) // 添加筛选
                    ->addFilter('purchase_ask.tid',TypeModel::where('status=1')->column('id,name')) // 添加筛选
                    ->addFilter('arrival_type',$arrival_type) // 添加筛选
                    ->addFilter('transport',$transport_type) // 添加筛选
                    ->hideCheckbox()
                    ->addOrder('purchase_arrival.number,purchase_arrival.consignee_time') // 添加排序
                    ->addColumns([ // 批量添加列
                        ['number', '单据编号'],
                        ['name', '主题'],
                        ['ctype', '采购类型',$type],
//                      ['supplier_name', '供应商'],
                        ['purchase_organization_name', '部门'],
                        ['ciagouyuan','采购员'],
                        ['arrival_type', '交货方式',$arrival_type],
                        ['transport', '运货方式',$transport_type],
                        ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                        ['consignee_user', '点收人'],
                        ['consignee_time', '点收日期','date'],
                        ['right_button','操作']
                    ])
                    ->setRowList($data_list) // 设置表格数据
                    ->addRightButton('btn', $btn_detail,true) // 添加授权按钮
                    ->addRightButton('delete') //添加删除按钮
                    ->addTopButton('add') //添加删除按钮
                    ->fetch();
                
    }

    public function add(){

        if ($this->request->isPost()) {
        	
        	
        	
            $data = $this->request->post();

            $data['look_user'] = '-'.implode('-',explode(',',$data['helpid'])).'-';
            $data['consignee'] = $data['zrid'];
            // 验证
            
            $data['number'] = 'CGDH'.date('YmdHis',time());
            $data['consignee_time'] = strtotime($data['consignee_time']);
            $data['wid'] = UID;
            $data['create_time'] = time();
            //获取合同中的采购类型，采购部门,是否增值;还有采购员
            $order = OrderModel::getOne($data['pnumber']);
			$data['ctype']=$order->purchase_type;
			$data['oid']=$order->purchase_organization;
			$data['is_add_tax']=$order->is_add_tax;
			$data['cid']=$order->purchase_uid;
			
			//这个是order明细中的供应商id；
			$Material= OrderMaterial::where('aid',$data['pnumber'])->column('supplier');

            $result = $this->validate($data, 'Arrival');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            if(empty($data['mid'])){
            	$this->error('请填写物资明细');
            }else{
	            if ($res = ArrivalModel::create($data)) {
	                foreach($data['mid'] as $k => $v){
	                    $info = array();
	                    $info = [
	                            'aid'=>$res['id'],
	                            'wid'=>$v,
								'num'=>$data['num'][$k],						
	                            'price'=>$data['plan_num'][$k],
	                            'plan_money'=>$data['plan_money'][$k],
	                            'remarks'=>$data['remarks'][$k],
	                            'buhege_num'=>$data['baojian_num'][$k],
	                            'hege_num'=>$data['shijian_num'][$k],
	                            'supplier_username'=>$data['supplier_username'][$k],
	                            'supplier_id'=>$Material[$k],
	                           
	                    ];  
	                    ArrivalMaterialModel::create($info);
	                } 
	                flow_detail($data['name'],'purchase_arrival','purchase_arrival','purchase/arrival/detail',$res['id']);
	                action_log('purchase_arrival_add', 'purchase_arrival', $res['id'], UID, $res['id']);
	                $this->success('新增成功',url('index'));
	            } else {
	                $this->error('新增失败');
	            }
            }
        }
         $ydnumber = OrderModel::where('status','1')->column('id,name');
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('添加到货')           
            ->addGroup(
                    [
                        '到货信息' =>[
                            ['text:3', 'name', '主题'],
                            ['select:3','pnumber','订单号','',$ydnumber],
//                          ['linkage:3','supplier_type', '供应商类型','',SupplierTypeModel::column('id,name'),'',url('get_supplier_name'),'sid'],
//                          ['select:3','sid', '供应商名称'],
//                          ['linkage:3','ptype','源单类型','',[0=>'无源单',1=>'采购订单'],'',url('get_yd'),'pnumber'],
                            ['text:3','ctype','采购类型','','','','disabled'],
                            ['text:3','oid', '采购部门','','','','disabled'],
                            ['text:3','cid','采购员','','','','disabled'],
                            ['text:3','is_add_tax','是否增值税','','','','disabled'],
                            ['select:3','arrival_type','交货方式','',[0=>'一次性交货',1=>'分批交货']],
                            ['select:3','transport','运货方式','',[0=>'空运',1=>'海运',2=>'快递']],
                            ['select:3','balance_type','结算方式','',[ 0=>'分段结算',1=>'合同结算',2=>'进度结算',3=>'竣工后一次结算']],
                            ['select:3','currency','币种','',[ 0=>'美元',1=>'人民币',2=>'欧元']],
                            ['number:3', 'rate', '汇率'],
                            ['text:3', 'zrname', '点收人'],
                            ['date:3','consignee_time','点收时间'],
                            ['text:6', 'shipping_address', '发货地址'],
                            ['text:6', 'arrival_address', '收货地址'],
                            ['static:3','  ','制单人','',get_nickname(UID)], 
                            ['files:6','file',' 附件'],
                            ['textarea', 'helpname', '可查看人员'],
                            ['hidden','helpid'],
                            ['hidden','zrid'],
                            ['wangeditor', 'remark','备注'],
                        ],
                        '到货物资明细' =>[
                            ['hidden', 'materials_list'],
                        ]
                    ])    
             ->js('arrival') 
             ->setExtraHtml(outhtml2())
            ->setExtraJs(outjs2()) 
            ->fetch();

    }
    
    public function get_Detail($pnumber = ''){
    	
		$data = OrderModel::getOne($pnumber);
		
	
		//获取采购类型
		$data->leixing=TypeModel::where('id',$data->purchase_type)->column('name');
		//获取采购部门
		$data->bumen=OrganizationModel::where('id',$data->purchase_organization)->column('title');
		//获取采购员
		$data->caigouyuan=UserModel::where('id',$data->purchase_uid)->column('nickname');
		//获取是否增值
		
		$data->is_add_tax=$data->is_add_tax===0?'否':'是';
//		halt($data);
//
		return $data;
	}
    
    
    
	public function get_Mateplan($mateplan = '',$ptype = ''){
//		if($ptype == 1){
		$materialsid = OrderModel::where('id',$mateplan)->value('id');
		$map = ['aid'=>$materialsid];
		$data = OrderModel::getMaterial($map);
	
//		halt($data);
	
//		}else{
//			return $html='<span>请选择源单类型</span>';
//		}		      		
		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name">
			<table class="table table-bordered">
				<tbody>
					<tr>
						<td>物品名称</td>
						<td>仓库</td>
						<td>单位</td>
						<td>规格</td>
						<td>售价</td>
						<td>到货数量</td>
						<td>金额</td>
						<td>备注</td>
						<td>报检数量</td>
						<td>实检数量</td>
						<td>供应商</td>
						<td>供应商签约人</td>
						</tr>';
    		foreach ($data as $k => $v){ 
    			
    			$bom = SupplierModel::column('id,name');
//  			$html2=[];
			
//		        foreach ($bom as $key => $value) {
//		        	if($v['supplier']==$key )
//		        	$html2[]=[$key=>$value];
//		           
//		        }
		        
//		       	
		     
    			$html.='<tr>
    				<input type="hidden" name="mid[]" value="'.$v['wid'].'">
    					<input type="hidden" name="price[]" value="'.$v['price'].'">
    						<input type="hidden" name="mlid[]" value="'.$v['id'].'">
    							<td>'.$v['name'].'</td>
    							<td>'.$v['ckname'].'</td>
    							<td>'.$v['unit'].'</td>
    							<td>'.$v['version'].'</td>
    							<td><input type="number" oninput="input(this)" class="jg" name="plan_num[]" value="'.$v['plan_num'].'"></td>
    							<td><input type="number" oninput="input(this)" class="sl" name="num[]" value="'.$v['num'].'"></td>
    							<td><input type="number" readonly="readonly" class="zj" name=plan_money[] value="'.$v['plan_num']*$v['num'].'" > </td>
    							<td><input type="text" name="remarks[]"></td>
    							<td><input type="number" name="baojian_num[]"></td>
    							<td><input type="number" name="shijian_num[]"></td>
    							<td><input type="text" readonly="readonly" name="supplier[]"  value="'.$bom[$v['supplier']].'"  ></td>
    							<td><input type="text" readonly="readonly" name="supplier_username[]" value="'.$v['supplier_username'].'"  ></td>
    							</tr>
    							</tr>';
    		}   		
    		$html .= '</tbody></table></div>';
		return $html;
 	
	}
    //详情 
    public function detail($id=null){
	
        if($id==null)return $this->error('缺少参数');
        
        $detail = ArrivalModel::getOne($id);
        $detail['consignee'] = get_nickname($detail['consignee']);
        $detail['wid'] = get_nickname($detail['wid']);
        $detail['consignee_time'] = date('Y-m-d',$detail['consignee_time']);
        $detail['create_time'] = date('Y-m-d',$detail['create_time']);
        //源单
//      if($detail['ptype']==1){
            $ydnumber = OrderModel::where(['id'=>$detail->pnumber])->column('name');
//          halt($ydnumber);
//      }else{
//          $ydnumber = [0=>'无源单'];
//      }
        $detail->materials_list = implode(ArrivalMaterialModel::where('aid',$id)->column('id,wid'),',');
        $detail->pnumber=$ydnumber[0];
        $a=UserModel::where('id',$detail->cid)->column('nickname');
        $detail->cid=$a[0];
        return ZBuilder::make('form')
            ->setPageTitle('添加到货')           
            ->addGroup(
                    [
                        '到货信息' =>[
                            ['hidden','id'],
                            ['static:3', 'name', '主题'],
                            ['static:3','purchase_type_name','采购类型'],
//                          ['static:3','supplier_name', '供应商名称'],
                            ['static:3','purchase_organization_name', '采购部门'],
//                          ['linkage:3','ptype','源单类型','',[0=>'无源单',1=>'采购订单'],'',url('get_yd'),'pnumber'],
                            ['static:3','pnumber','订单号','',$ydnumber],
                            ['static:3','cid','采购员'],
                            ['select:3','is_add_tax','是否增值税','',[0=>'否',1=>'是']],
                            ['select:3','arrival_type','交货方式','',[0=>'一次性交货',1=>'分批交货']],
                            ['select:3','transport','运货方式','',[0=>'空运',1=>'海运',2=>'快递']],
                            ['select:3','balance_type','结算方式','',[ 0=>'分段结算',1=>'合同结算',2=>'进度结算',3=>'竣工后一次结算']],
                            ['select:3','currency','币种','',[ 0=>'美元',1=>'人民币',2=>'欧元']],
                            ['static:3', 'rate', '汇率'],
                            ['static:3', 'consignee', '点收人'],
                            ['static:3','consignee_time','点收时间'],
                            ['static:3','wid','制单人'], 
                            ['static:3','create_time','制单时间'], 
                            ['static:6', 'shipping_address', '发货地址'],
                            ['static:6', 'arrival_address', '收货地址'],
                            ['archives:6','file',' 附件'],
                            ['wangeditor', 'remark','备注'],
                        ],
                        '到货物资明细' =>[
                            ['hidden', 'materials_list'],
                            ['hidden', 'old_plan_list'],
                        ]
                    ])    
            ->setFormData($detail)    
            ->hideBtn('submit') 
            ->js('arrival')   
            ->fetch();

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


    //删除
    public function delete($ids = null){        
        if($ids == null) $this->error('参数错误');
        $map['id'] = $ids;
        if($model = ArrivalModel::where($map)->delete()){   
            //记录行为
            action_log('purchase_ask_delete', 'purchase_ask', $map['id'], UID,$map['id']);          
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }       
    }

    public function get_yd($ptype = '')
    {
//      if($ptype==1){
            $list = OrderModel::where('status',1)->column('id,name');
//      }else{
//          $list= ['0'=>'无']; 
//      }
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        foreach ($list as $key => $value) {
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
        ->js('arrival')
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

            $map = ['purchase_arrival_material.aid'=>$pid,'stock_material.id'=>['in',($materials_list)]];

            $data = ArrivalModel::getMaterial($map);
//			halt($data);
            $html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name">
            	<table class="table table-bordered">
            		<tbody>
            			<tr>
            				<td>名称</td>
            				<td>仓库</td>
            				<td>单位</td>
            				<td>规格</td>
            				<td>售价</td>
            				<td>到货数量</td>
            				<td>总价</td>
            				<td>备注</td>
            				<td>报检数量</td>
            				<td>实检数量</td>
            				<td>供应商</td>
            				<td>供应商签约人</td>
            				</tr>';


            foreach ($data as $k => $v){ 
                $html.='<tr>
                	<input type="hidden" name="mid[]" value="'.$v['wid'].'">
                		<input type="hidden" name="mlid[]" value="'.$v['id'].'">
                			<td>'.$v['name'].'</td>
                			<td>'.$v['ckname'].'</td>
                			<td>'.$v['unit'].'</td>
                			<td>'.$v['version'].'</td>
                			<td>￥' . number_format($v['price'],2) . '</td>
                			<td>'.$v['num'].'</td>
                			<td>￥' . number_format($v['price']*$v['num'],2) . '</td>
                			<td>'.$v['remarks'].'</td>
                			<td>'.$v['buhege_num'].'</td>
                			<td>'.$v['hege_num'].'</td>
                			<td>'.$v['sname'].'</td>
                			<td>'.$v['supplier_username'].'</td>
                			</tr>';
            }           

            $html .= '</tbody></table></div>';
    
        }

        return $html;
    }



}   
