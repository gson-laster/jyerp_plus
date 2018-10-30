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
use app\purchase\model\MoneyMaterial as MoneyMaterialModel;
use app\purchase\model\Money as MoneyModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\supplier\model\Supplier as SupplierModel;
use app\sales\model\Order as OrderModel;
use app\tender\model\Materials as MaterialsModel;
use think\Db;

class Money extends Admin
{
    public function lists(){
        //采购询价列表主页
        $map = $this->getMap();
        $order = $this->getOrder('purchase_money.id desc');
        $data_list = MoneyModel::getList($map,$order);

        $supplier = SupplierModel::where('status=1')->column('id,name');
        $type = TypeModel::where('status=1')->column('id,name');


        $btn_detail = [
            'title' => '查看详情',
            'icon'  => 'fa fa-fw fa-search',
            'href'  => url('detail', ['id' => '__id__'])
        ];
        //使用ZBuilder构建表格展示数据
        return ZBuilder::make('table')
            ->setSearch(['number'=>'编号','purchase_money.title'=>'主题',],'','',true) // 设置搜索框
            ->addTimeFilter('purchase_money.create_time') // 添加时间段筛选
            ->addFilter('purchase_money.cid',$type) // 添加筛选
            ->hideCheckbox()
            ->addOrder('number,price_time,purchase_money.create_time') // 添加排序
            ->addColumns([ // 批量添加列
                ['number', '编号'],
                ['title', '主题'],
//              ['sid', '供应商',$supplier],
                ['anickname', '询价员'],
                ['cid', '采购类型',$type],
                ['price_time', '询价日期','date'],
                ['create_time', '创建日期','date'],
                ['price_type','币种',[0=>'美元',1=>'人民币',2=>'欧元']],
                ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                ['right_button','操作']
            ])
            ->setRowList($data_list) // 设置表格数据
            ->addRightButton('btn', $btn_detail,true) // 添加授权按钮
            ->fetch();
    }
    /*
     * 采购询价列表主页
     */
    public function index(){
        //采购询价列表主页
        $map = $this->getMap();
        $order = $this->getOrder('purchase_money.id desc');
        $data_list = MoneyModel::getList($map,$order);

        $supplier = SupplierModel::where('status=1')->column('id,name');
        $type = TypeModel::where('status=1')->column('id,name');


        $btn_detail = [
            'title' => '查看详情',
            'icon'  => 'fa fa-fw fa-search',
            'href'  => url('detail', ['id' => '__id__'])
        ];
        //使用ZBuilder构建表格展示数据
        return ZBuilder::make('table')
            ->setSearch(['number'=>'编号','purchase_money.title'=>'主题',],'','',true) // 设置搜索框
            ->addTimeFilter('purchase_money.create_time') // 添加时间段筛选
            ->addFilter('purchase_money.cid',$type) // 添加筛选
            ->hideCheckbox()
            ->addOrder('number,price_time,purchase_money.create_time') // 添加排序
            ->addColumns([ // 批量添加列
                ['number', '编号'],
                ['title', '主题'],
//              ['sid', '供应商',$supplier],
                ['anickname', '询价员'],
                ['cid', '采购类型',$type],
                ['price_time', '询价日期','date'],
                ['create_time', '创建日期','date'],
                ['price_type','币种',[0=>'美元',1=>'人民币',2=>'欧元']],
                ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                ['right_button','操作']
            ])
            ->setRowList($data_list) // 设置表格数据
            ->addRightButton('btn', $btn_detail,true) // 添加授权按钮
            ->addRightButton('delete') //添加删除按钮
             ->addTopButton('add') //添加删除按钮
            ->fetch();
    }

    /*
     * 添加采购询价
     */

    public function add(){

        if ($this->request->isPost()) {
            $data = $this->request->post();
            
         
         	
            // 验证
            $data['number'] = 'CGXJ'.date('YmdHis',time());
            $data['price_time'] = strtotime($data['price_time']);
            $data['create_time'] = time();
            $data['wid'] = UID;
            $data['askuid'] = $data['zrid'];
           	//获取计划中的类型和部门
            $a=PlanModel::get($data['pnumber']);
           	$data['cid']=$a->tid;
           	$data['oid']=$a->oid;				
//          $data['sid']=$a
//          halt($data);
            $result = $this->validate($data, 'Money');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            if(empty($data['mid'])){
            	$this->error('请填写物资明细');
            }else{
            	if ($res = MoneyModel::create($data)) {
	                foreach($data['mid'] as $k => $v){
	                    $info = array();
	                    $info = [
	                            'aid'=>$res['id'],
	                            'wid'=>$v,             
	                            'num'=>$data['plan_num'][$k],            
	                            'plan_num'=>$data['price'][$k],
	                            'plan_money'=>$data['plan_money'][$k],
	                            'supplier'=>$data['supplier'][$k],
	                    ];  
	                    MoneyMaterialModel::create($info);                      
	                } 
	                flow_detail($data['title'],'purchase_money','purchase_money','purchase/money/detail',$res['id']);
	                action_log('purchase_plan_add', 'purchase_plan', $res['id'], UID, $res['id']);
	                $this->success('新增成功',url('index'));
	            } else {
	                $this->error('新增失败');
	            }
            }
        }
         $ydnumber = PlanModel::where('status','1')->column('id,name');
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('采购询价')
            ->addGroup(
                    [
                        '询价信息' =>[
                            ['hidden','zrid'],
                            ['text:3','title','主题'],
//                          ['select:3','sid','供应商','',SupplierModel::where('status=1')->column('id,name')],
//                          ['linkage:3','ptype','源单类型','',[0=>'无源单',1=>'采购通知',2=>'采购计划'],'',url('get_yd'),'pnumber'],
                            ['select:3','pnumber','计划单号','',$ydnumber],
                            ['text:3','zrname','询价员'],
                            ['text:3','tid','采购类型','','','','disabled'],
                            ['text:3','oid','采购部门','','','','disabled'],
                            ['number:3','price_number','询价次数'],
                            ['date:3', 'price_time', '询价日期'],
                            ['select:3','price_type','币种','',[0=>'美元',1=>'人民币',2=>'欧元'],1],
                            ['number:3', 'rate', '汇率',''],
                            ['select:3','is_add','是否为增值税','',[1=>'是',0=>'否']],
                            ['static:3','wid','制单人','',get_nickname(UID)],
                            ['static:3', 'create_time', '制单日期','',date('Y-m-d')],
                            ['files','file','附件'],
                            ['wangeditor', 'remark','备注']
                        ],
                        '询价物资明细' =>[
                            ['hidden', 'materials_list'],
                        ]
                    ])
            ->js('money')        
            ->setExtraHtml(outhtml2())
            ->setExtraJs(outjs2()) 
            ->fetch();


    }
    public function get_Detail($pnumber = ''){
			$data = planModel::getOne($pnumber);
//			halt($data);
		return $data;
	}
	public function get_Mateplan($mateplan = '',$ptype = ''){
//		if($ptype == 1){
//		$materialsid = MaterialsModel::where('id',$mateplan)->value('id');
//		$map = ['pid'=>$materialsid];
//		$data = MaterialsModel::getDetail($map);
//		}elseif($ptype == 2){
		$materialsid = PlanModel::where('id',$mateplan)->value('id');
		$map = ['aid'=>$materialsid];
		$data = PlanModel::getMaterial($map);	
//		}else{
//			return $html='<span>请选择源单类型</span>';
//		}


		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name">
			<table class="table table-bordered">
				<tbody>
					<tr>
						<td>物品名称</td>
				
						<td>单位</td>
						<td>规格</td>
						<td>参考金额</td>
						<td>询价金额</td>
						<td>采购数量</td>
						<td>实际金额</td>
						<td>供应商</td>
					</tr>';
    		foreach ($data as $k => $v){ 
    			
    			//为了让下拉菜单默认显示计划里的供应商
//  			{
    			$bom = SupplierModel::column('id,name');
    			$html2=[];
				$html2 = '<select name="supplier[]">';
		        foreach ($bom as $key => $value) {
		        	if($v['supplier']==$key )
		            	$html2.='<option selected value="'.$key.'">'.$value.'</option>';
		            else
		            	$html2.='<option  value="'.$key.'">'.$value.'</option>';
		        }
		        $html2.='</select>';
//		       }

    			$html.='<tr>
    				<input type="hidden" name="mid[]" value="'.$v['wid'].'">
    					<input type="hidden" name="mlid[]" value="'.$v['id'].'">
    						<td>'.$v['name'].'</td>
    						<td>'.$v['unit'].'</td>
    						<td>'.$v['version'].'</td>
    						<td>'.$v['price'].'</td>
    						<td><input type="number" oninput="input(this)" class="jg" name="price[]" value="'.$v['price'].'"></td>
    						<td><input type="number" oninput="input(this)" class="sl" name="plan_num[]" value="'.$v['plan_num'].'"></td>
    						<td><input type="number" readonly="readonly" class="zj" name="plan_money[]" value="'.$v['price']*$v['plan_num'].'"></td>
    						<td>'.$html2.'</td>
    						</tr>';
    						
    		}   		
    		$html .= '</tbody></table></div>';
		return $html;
	}
    /*
     * 采购询价详情
     */

    public function detail($id = null)
    {

        if($id==null)return $this->error('缺少参数');
        
        $detail = MoneyModel::getOne($id);
//      halt($detail);
        //源单
//      if($detail['ptype']==1){
//          $ydnumber = MaterialsModel::where(['status'=>'1'])->column('id,name');
//      }elseif($detail['ptype']==2){
            $ydnumber = PlanModel::where(['id'=>$detail->pnumber])->column('name');

//      }else{
//          $ydnumber = [0=>'无源单'];
//      }
        $detail->prictime = date('Y-m-d',$detail['price_time']);
        $detail->ctime = date('Y-m-d',$detail['create_time']);
        $detail->materials_list = implode(MoneyMaterialModel::where('aid',$id)->column('id,wid'),',');
        $detail->pnumber=$ydnumber[0];
        $cgtype = TypeModel::where('status=1')->column('id,name');
        return ZBuilder::make('form')
        ->setPageTitle('详情')           
        ->addGroup(
                [
                    '询价信息' =>[
                        ['hidden', 'id'],
                        ['static:3','title','主题'],
//                      ['static:3','sname','供应商'],
//                      ['linkage:3','ptype','源单类型','',[0=>'无源单',1=>'采购通知',2=>'采购计划'],$detail['ptype'],url('get_yd'),'pnumber'],
                        ['static:3','pnumber','计划单号','',$ydnumber],
                        ['static:3','anickname','询价员'],
                        ['static:3','tname','采购类型'],
                        ['static:3','oname','采购部门'],
                        ['static:3','price_number','询价次数'],
                        ['static:3', 'prictime', '询价日期'],
                        ['select:3','price_type','币种','',[0=>'美元',1=>'人民币',2=>'欧元']],
                        ['static:3', 'rate', '汇率'],
                        ['select:3','is_add','是否为增值税','',[1=>'是',0=>'否']],
                        ['static:3','wnickname','制单人'],
                        ['static:3', 'ctime', '制单日期'],
                        ['archives','file','附件'],
                        ['wangeditor', 'remark','备注']
                    ],
                    '询价物资明细' =>[
                        ['hidden', 'materials_list'],
                        ['hidden', 'old_plan_list'],
                    ]
                ])     
        ->setFormData($detail)    
        ->hideBtn('submit') 
        ->js('money')
        ->fetch();
        }


   public function get_yd($ptype = '')
    {
        if($ptype==0){
            $list= ['0'=>'无']; 
        }elseif($ptype==1){
            $list = MaterialsModel::where(['status'=>'1'])->column('id,name');
        }elseif($ptype==2){
            $list = PlanModel::column('id,name');
        }
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        foreach ($list as $key => $value) {
             $arr['list'][] = ['key'=>$key,'value'=>$value];
        }
        return json($arr);
    }



    //编辑生成物品表格
    public function tech($pid = '',$materials_list = '')
    {
//        dump($materials_list);die;
        $html = $materials_list;
        if($materials_list == '' || $materials_list == 'undefined') {

            $html = ''; 

        }else{

            $map = ['purchase_money_material.aid'=>$pid,'stock_material.id'=>['in',($materials_list)]];
            $data = MoneyModel::getMaterial($map);
//			halt($data);
            $html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name">
            	<table class="table table-bordered">
            		<tbody>
            			<tr>
            				<td>物品名称</td>
            				<td>单位</td>
            				<td>规格</td>
            				<td>询价金额</td>
            				<td>采购数量</td>
            				<td>实际金额</td>
            				<td>供应商</td>
            			</tr>';

            foreach ($data as $k => $v){
                $html.='<tr>
                	<input type="hidden" name="mid[]" value="'.$v['wid'].'">
                		<input type="hidden" name="mlid[]" value="'.$v['id'].'">
                			<td>'.$v['name'].'</td>
                			<td>'.$v['unit'].'</td>
                			<td>'.$v['version'].'</td>
                			<td>￥' . number_format($v['plan_num'],2) . '</td>
                			<td>'.$v['num'].'</td>
               			<!--此处没有取plan_money,这样在提交的的时候修改就可以在完成的时候算出来，而不是取一个修改之前的值--!>
                			<td>￥' . number_format($v['plan_num']*$v['num'],2) . '</td>
                			<td>'.$v['sname'].'</td>
                		</tr>';
            }           
            $html .= '</tbody></table></div>';
    
        }

        return $html;
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
        ->js('money')
        ->addTopButton('pick', $btn_pick)
        ->assign('empty_tips', '暂无数据')
        ->fetch('admin@choose/choose'); // 渲染页面
    }
}