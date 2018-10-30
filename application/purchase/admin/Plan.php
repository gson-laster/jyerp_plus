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
use app\purchase\model\PlanMaterial as PlanMaterialModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\supplier\model\Supplier as SupplierModel;
use app\sales\model\Order as OrderModel;
use app\tender\model\Materials as MaterialsModel;
use think\Db;
/**
 *  施工日志
 */
class Plan extends Admin
{
	//
	public function lists()
	{

        $map = $this->getMap();
        $order = $this->getOrder('purchase_plan.id desc');

        $btn_detail = [
            'title' => '查看详情',
            'icon'  => 'fa fa-fw fa-search',
            'href'  => url('detail', ['id' => '__id__'])
        ];

        $data_list = PlanModel::getList($map,$order);
        $cgtype = TypeModel::where('status=1')->column('id,name');
        return ZBuilder::make('table')
                    ->setSearch(['purchase_plan.name'=>'主题','puser.nickname'=>'计划员','wuser.nickname'=>'制单员','cuser.nickname'=>'采购员'],'','',true) // 设置搜索框
                    ->addTimeFilter('purchase_plan.ptime') // 添加时间段筛选
                    ->addFilter('purchase_plan.tid',$cgtype) // 添加筛选
                    ->hideCheckbox()
                    ->addOrder('purchase_plan.number,purchase_plan.ptime') // 添加排序
                    ->addColumns([ // 批量添加列
                        ['number', '编号'],
                        ['name', '主题'],
                        ['tid', '采购类型','text','',$cgtype],
                        ['pnickname', '计划员'],
                        ['cnickname', '采购员'],
                        ['oname', '采购部门'],
                        ['ptime', '计划日期','date'],
                        ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                        ['wnickname','制单人'],
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
        $order = $this->getOrder('purchase_plan.id desc');

     	$btn_detail = [
		    'title' => '查看详情',
		    'icon'  => 'fa fa-fw fa-search',
		    'href'  => url('detail', ['id' => '__id__'])
		];

		$data_list = PlanModel::getList($map,$order);
        $cgtype = TypeModel::where('status=1')->column('id,name');
        return ZBuilder::make('table')
	        	 	->setSearch(['purchase_plan.name'=>'主题','puser.nickname'=>'计划员','wuser.nickname'=>'制单员','cuser.nickname'=>'采购员'],'','',true) // 设置搜索框
	        	 	->addTimeFilter('purchase_plan.ptime') // 添加时间段筛选
	        	 	->addFilter('purchase_plan.tid',$cgtype) // 添加筛选
	        		->hideCheckbox()
	        		->addOrder('purchase_plan.number,purchase_plan.ptime') // 添加排序
                    ->addColumns([ // 批量添加列
                        ['number', '编号'],
                        ['name', '主题'],
                        ['tid', '采购类型','text','',$cgtype],
                        ['pnickname', '计划员'],
                        ['cnickname', '采购员'],
                        ['oname', '采购部门'],
                        ['ptime', '计划日期','date'],
                        ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                        ['wnickname','制单人'],
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
            $data['number'] = 'CGJH'.date('YmdHis',time());
            $data['ptime'] = strtotime($data['ptime']);
            $data['create_time'] = time();
            $data['wid'] = UID;
            $data['pid'] = $data['zrid'];

            $result = $this->validate($data, 'Plan');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            if(empty($data['mid'])){
            	$this->error('请填写物资明细');
            }else{
            	 if ($res = PlanModel::create($data)) {
                //    
                foreach($data['mid'] as $k => $v){
                    $info = array();
                    $info = [
                            'aid'=>$res['id'],
                            'wid'=>$v,
                            'plan_num'=>$data['plan_num'][$k],
                            'plan_money'=>$data['plan_money'][$k],
                            'supplier'=>$data['supplier'][$k],
                            'bj_money'=>$data['bj_money'][$k]
                    ];
                    PlanMaterialModel::create($info);
                }
                flow_detail($data['name'],'purchase_plan','purchase_plan','purchase/plan/detail',$res['id']);
                action_log('purchase_plan_add', 'purchase_plan', $res['id'], UID, $res['id']);
                $this->success('新增成功',url('index'));
	            } else {
	                $this->error('新增失败');
	            }
            }
        }
        
		$ydnumber = MaterialsModel::where(['status'=>'1'])->column('id,name');
		
        $supplier = SupplierModel::column('id,name');
        $html = ' <script type="text/javascript">
            var supplier_select = \'<select name="supplier[]">';
        foreach ($supplier as $key => $value) {
            $html.='<option value="'.$key.'">'.$value.'</option>';

        }
        $html.='</select>\';
        </script>';

//         使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('添加申请')
             ->addGroup(
                    [
                        '申请信息' =>[
                                        ['text:3', 'name', '主题'],
                                        ['select:3','tid','采购类型','',TypeModel::where('status=1')->column('id,name')],
                                        ['date:3', 'ptime', '询价时间'],
                                        ['number:3','number','询价次数'],
                                        ['text:3','zrname','计划员'],
                                        ['linkage:3','oid', '采购部门', '',OrganizationModel::column('id,title'),'', url('get_tj'),'cid'],
                                        ['select:3','cid','采购员'],
                                           //这里把采购类型去掉
//                                      ['linkage:3','ptype','源单类型','',[1=>'采购通知'],'',url('get_yd'),'prate'],
                                        ['select:3','prate','备料单号','',$ydnumber],
                                        ['static:3','  ','制单人','',get_nickname(UID)],
                                        ['static:3', 'create_time', '制单日期','',date('Y-m-d')],
                                        ['files:6','file',' 附件'],
                                        ['hidden','zrid'],
                                        ['wangeditor', 'remark','备注'],
                        ],
                        '物资明细' =>[
                            ['hidden', 'materials_list'],
                        ]
                    ])
            ->js('plan')
            ->setExtraHtml(outhtml2())
            ->setExtraJs($html.outjs2())
            ->fetch();
	}
	public function get_Mateplan($mateplan = ''){
		if($mateplan == ''){
			return $html='<span>请选择源单类型</span>';
		}
		$bom = SupplierModel::column('id,name');
        $html2 = '<select name="supplier[]">';
        foreach ($bom as $key => $value) {
            $html2.='<option value="'.$key.'">'.$value.'</option>';
        }
        $html2.='</select>';
//		$materialsid = MaterialsModel::where('id',$mateplan)->value('id');
		$map = ['pid'=>$mateplan];
		$data = MaterialsModel::getDetail($map);
//		halt($data);
	
		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>仓库</td><td>单位</td><td>规格</td><td>售价</td><td>数量</td><td>小计</td><td>供应商</td></tr>';
    		foreach ($data as $k => $v){
    			$html.='<tr>
    				<input type="hidden" name="mid[]" value="'.$v['itemsid'].'">
    					<input type="hidden" name="mlid[]" value="'.$v['id'].'">
    						<td>'.$v['name'].'</td>
    						<td>'.$v['ckname'].'</td>
    						<td>'.$v['unit'].'</td>
    						<td>'.$v['version'].'</td>
    				<td><input type="number" class="jg" oninput="input(this)"  name="bj_money[]" value=""></td>
    				<td><input type="number" class="sl" oninput="input(this)" name="plan_num[]" value="'.$v['xysl'].'"></td>
    				<td><input type="number" class="zj" readonly="readonly" name="plan_money[]" value=""></td>
    				<td>'.$html2.'</td></tr>';
    		}
    		$html .= '</tbody></table></div>';
		return $html;
	}
	////采购详情
	public function detail($id=null){

		if($id==null) $this->error('缺少参数');

		$detail = PlanModel::getOne($id);
		
//		var_dump($detail['ptype']);
        //源单
//		if($detail['ptype']==1){
															//根据从表PlanModel中的外键字段prate查询主表的id得到的主题
			//$ydnumber = MaterialsModel::where(['status'=>'1'])->where(['id'=>$detail->prate])->column('name');
//			halt($ydnumber);
//		}else{
//          $ydnumber = [0=>'无源单'];
//      }

        $detail->plantime = date('Y-m-d',$detail['ptime']);
        $detail->ctime = date('Y-m-d',$detail['create_time']);
        $detail->materials_list = implode(PlanMaterialModel::where('aid',$id)->column('id,wid'),',');
		//$detail->prate =  $ydnumber[0];
	
        //采购类型
        $cgtype = TypeModel::where('status=1')->column('id,name');



        return ZBuilder::make('form')
        ->setPageTitle('详情')
        ->addGroup(
                [
                    '计划信息' =>[
                        ['hidden', 'id'],
                        ['static:3', 'name', '主题'],
                        ['static:3','tname','采购类型','',$cgtype],
                        ['static:3', 'plantime', '计划时间'],
                        ['static:3','pnickname','计划员'],
                        ['static:3','cnickname','采购员'],
                        //这里把采购类型去掉
//                      ['linkage:3','ptype','源单类型','',[0=>'无源单',1=>'采购通知'],url('get_yd'),'prate'],
                        ['static:3','prate','备料单号'],
                        ['static:3','wnickname','制单人','',get_nickname(UID)],
                        ['static:3', 'ctime', '制单日期'],
                        ['archives:6','file',' 附件'],
                        ['wangeditor', 'remark','备注'],
                    ],
                    '计划物资明细' =>[
                        ['hidden', 'materials_list'],
                        ['hidden', 'old_plan_list'],
                    ]
                ])
        ->setFormData($detail)
        ->hideBtn('submit')
        ->js('plan')
        ->fetch();

	}

	//删除
	// public function delete($ids = null){
	// 	if($ids == null) $this->error('参数错误');
	// 	$map['id'] = $ids;
	// 	if($model = AskModel::where($map)->delete()){
	// 		//记录行为
 //        	action_log('purchase_ask_delete', 'purchase_ask', $map['id'], UID,$map['id']);
	// 		$this->success('删除成功');
	// 	}else{
	// 		$this->error('删除失败');
	// 	}
	// }



    public function get_yd($ptype = '')
    {
    	if($ptype==0){
    		$list= ['0'=>'无'];
    	}elseif($ptype==1){
    		$list = MaterialsModel::where(['status'=>'1'])->column('id,name');
    	}
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        foreach ($list as $key => $value) {
             $arr['list'][] = ['key'=>$key,'value'=>$value];
        }
        return json($arr);

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

         //编辑生成物品表格
     public function tech($pid = '',$materials_list = '')
    {

        $html = $materials_list;

        if($materials_list == '' || $materials_list == 'undefined') {

            $html = '';

        }else{
            $map = ['purchase_plan_material.aid'=>$pid,'stock_material.id'=>['in',($materials_list)]];

            $data = PlanModel::getMaterial($map);
//          halt($data);
            $html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>仓库</td><td>单位</td><td>规格</td><td>售价</td><td>数量</td><td>小计</td><td>供应商</td></tr>';

            foreach ($data as $k => $v){
                $html.='<tr>
                	<input type="hidden" name="mid[]" value="'.$v['wid'].'">
                		<input type="hidden" name="mlid[]" value="'.$v['id'].'">
                			<td>'.$v['name'].'</td>
                			<td>'.$v['ckname'].'</td>
                			<td>'.$v['unit'].'</td>
                			<td>'.$v['version'].'</td>
                			<td>￥' . number_format($v['bj_money'],2) . '</td>
                			<td>'.$v['plan_num'].'</td>
                			<td>￥' . number_format($v['bj_money']*$v['plan_num'],2) . '</td>
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
        $supplier = SupplierModel::column('id,name');
        $html = 'var supplier_select = \'<select name="supplier[]">';
        foreach ($supplier as $key => $value) {
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
                ['code', '物品编号'],
                ['unit', '单位'],
                ['version', '规格型号',],
                ['price_tax', '含税售价'],
                ['color', '颜色'],
                ['brand', '品牌'],
                ['status', '启用状态', 'status'],
            ])
        ->setRowList($data_list) // 设置表格数据
        ->setExtraJs($js.$html)
        ->js('plan')
        ->addTopButton('pick', $btn_pick)
        ->assign('empty_tips', '暂无数据')
        ->fetch('admin@choose/choose'); // 渲染页面
    }

}   
