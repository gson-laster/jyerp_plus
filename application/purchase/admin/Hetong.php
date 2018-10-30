<?php
namespace app\purchase\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\purchase\model\Ask as AskModel;
use app\purchase\model\Plan as PlanModel;
use app\purchase\model\Money as MoneyModel;
use app\purchase\model\Hetong as HetongModel;
use app\purchase\model\Type as TypeModel;
use app\admin\model\Access as AccessModel;
use app\user\model\Organization as OrganizationModel;
use app\sales\model\Order as OrderModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\purchase\model\HetongMaterial as HetongMaterialModel;
use app\supplier\model\Type as SupplierTypeModel;
use app\supplier\model\Supplier as SupplierModel;
use think\Db;
/**
 *  施工日志
 */
class Hetong extends Admin
{
    //
    public function lists()
    {

        $map = $this->getMap();
        $order = $this->getOrder('purchase_hetong.id desc');

        $btn_detail = [
            'title' => '查看详情',
            'icon'  => 'fa fa-fw fa-search',
            'href'  => url('detail', ['id' => '__id__'])
        ];

        $data_list = HetongModel::getList($map,$order);
        $purchase_type = TypeModel::where('status=1')->column('id,name');  //采购类型
        $pay_type = [0=>'现金',1=>'转账',2=>'支票',3=>'微信',4=>'支付宝']; //支付方式
        $arrival_type = [0=>'一次性交货',1=>'分批交货'];                   //交货方式
        $transport_type = [0=>'空运',1=>'海运',2=>'快递'];                 //运输方式
        $balance_type = [ 0=>'美元',1=>'人民币',2=>'欧元']; 
                       //结算方式
        return ZBuilder::make('table')
                    ->setSearch(['purchase_hetong.name'=>'主题','puser.nickname'=>'我方签约人'],'','',true) // 设置搜索框
                    ->addTimeFilter('purchase_hetong.hetong_time') // 添加时间段筛选
                    ->addFilter(['purchase_type_name' => 'purchase_type.name']) // 添加筛选
                    ->addFilter(['purchase_organization_name' => 'admin_organization.title']) // 添加筛选
                    ->addFilter('pay_type',$pay_type) // 添加筛选
                    ->addFilter('arrival_type',$arrival_type) // 添加筛选
                    ->addFilter('transport_type',$transport_type) // 添加筛选
                    ->addFilter('balance_type',$balance_type) // 添加筛选
                    ->hideCheckbox()
                    ->addOrder('purchase_hetong.number,purchase_hetong.hetong_time') // 添加排序
                    ->addColumns([ // 批量添加列
                        ['number', '编号'],
                        ['name', '主题'],
//                      ['supplier_name', '供应商'],
                        ['purchase_type_name', '采购类型',$purchase_type],
                        ['purchase_nickname', '采购员'],
                        ['purchase_organization_name', '采购部门'],
                        ['is_add_tax', '是否增值税','status','',[0=>'否',1=>'是']],
                        ['pay_type', '支付方式',$pay_type],
                        ['hetong_time', '签约时间','date'],
                        ['arrival_type', '交货方式',$arrival_type],
                        ['transport_type', '运输方式',$transport_type],
                        ['balance_type', '结算方式',$balance_type],
                        ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                        ['hetong_address', '签约地点'],
                        ['right_button','操作']
                    ])
                    // ->setRowList($data_list) // 设置表格数据
                    ->addRightButton('btn', $btn_detail,true) // 添加授权按钮
                    ->setRowList($data_list) // 设置表格数据 
                    ->fetch();
                
                
    }

    public function index()
    {   
        dump(session(''));
        die;
        $map = $this->getMap();
        $order = $this->getOrder('purchase_hetong.id desc');

        $btn_detail = [
            'title' => '查看详情',
            'icon'  => 'fa fa-fw fa-search',
            'href'  => url('detail', ['id' => '__id__'])
        ];

        $data_list = HetongModel::getList($map,$order);
        //dump($data_list);die;
        $purchase_type = TypeModel::where('status=1')->column('id,name');  //采购类型
        $pay_type = [0=>'现金',1=>'转账',2=>'支票',3=>'微信',4=>'支付宝']; //支付方式
        $arrival_type = [0=>'一次性交货',1=>'分批交货'];                   //交货方式
        $transport_type = [0=>'空运',1=>'海运',2=>'快递'];                 //运输方式
        $balance_type = [ 0=>'美元',1=>'人民币',2=>'欧元']; 
                       //结算方式
        return ZBuilder::make('table')
                    ->setSearch(['purchase_hetong.name'=>'主题','puser.nickname'=>'我方签约人'],'','',true) // 设置搜索框
                    ->addTimeFilter('purchase_hetong.hetong_time') // 添加时间段筛选
                    ->addFilter(['purchase_type_name' => 'purchase_type.name']) // 添加筛选
                    ->addFilter(['purchase_organization_name' => 'admin_organization.title']) // 添加筛选
                    ->addFilter('pay_type',$pay_type) // 添加筛选
                    ->addFilter('arrival_type',$arrival_type) // 添加筛选
                    ->addFilter('transport_type',$transport_type) // 添加筛选
                    ->addFilter('balance_type',$balance_type) // 添加筛选
                    ->hideCheckbox()
                    ->addOrder('purchase_hetong.number,purchase_hetong.hetong_time') // 添加排序
                    ->addColumns([ // 批量添加列
                        ['number', '编号'],
                        ['name', '主题'],
//                      ['supplier_name', '供应商'],
                        ['purchase_type', '采购类型'],
                        ['purchase_nickname', '采购员'],
                        ['purchase_organization', '采购部门'],
                        //['is_add_tax', '是否增值税','status','',[0=>'否',1=>'是']],
                        ['pay_type', '支付方式',$pay_type],
                        ['hetong_time', '签约时间','date'],
                        ['arrival_type', '交货方式',$arrival_type],
                        ['transport_type', '运输方式',$transport_type],
                        ['balance_type', '结算方式',$balance_type],
                        ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                        ['hetong_address', '签约地点'],
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
            $data['number'] = 'CGHT'.date('YmdHis',time());
            $data['hetong_time'] = strtotime($data['hetong_time']);
            $data['create_time'] = time();
            $data['create_uid'] = UID;
            $data['hetong_uid'] = $data['zrid'];
            if(UID == 1){
                $data['helpid'] = ','.UID.$data['helpid'];
            }else{
                $data['helpid'] = ','.'1'.','.UID.$data['helpid'];
            }   
            //$map = [];
            //以传递过来的询价单号得到采购部门，采购类型，是否增值
            $money=PlanModel::getid($data['source_id']);
            //dump($money);die;
            //再以询价中的pnumber字段的到计划中填写的采购员
            $plan_cgy= PlanModel::where('id',$data['source_id'])->column('cid');
                //dump($plan_cgy);die;
//          halt($plan_cgy);
            //依次次填入数组中
            $data['purchase_type']=$money['tid'];//采购类型
            $data['purchase_organization']=$money['oid'];//采购部门
            $data['purchase_uid']=$money['cid'];//采购员     
            
            //dump($data);die;     
            $result = $this->validate($data, 'Hetong');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
             if(empty($data['mid'])){
                $this->error('请填写物资明细');
            }else{
                if ($res = HetongModel::create($data)) {
                    foreach($data['mid'] as $k => $v){
                        $info = array();
                        $info = [
                                'aid'=>$res['id'],
                                'wid'=>$v,                            
                                //'num'=>$data['num'][$k],  
                                'plan_num'=>$data['plan_num'][$k],                    
                                'plan_money'=>$data['plan_money'][$k],
                                'bj_money'=>$data['bj_money'][$k],
                                'supplier'=>$data['supplier'][$k],
                                'supplier_username'=>$data['supplier_username'][$k],
                                'supplier_id'=>$data['supplier'][$k],
                        ];  
                        HetongMaterialModel::create($info);                      
                    } 
                    flow_detail($data['name'],'purchase_hetong','purchase_hetong','purchase/hetong/detail',$res['id']);
                    action_log('purchase_hetong_add', 'purchase_hetong', $res['id'], UID, $res['id']);
                    $this->success('新增成功',url('index'));
                } else {
                    $this->error('新增失败');
                }
            }
        }
        $ydnumber = PlanModel::getMes();        
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('添加合同')   
            ->addGroup(
                    [
                        '合同信息' =>[
                            ['hidden','helpid'],
                            ['text:3', 'name', '主题'],
                            ['select:3','source_id','申请单号','',$ydnumber],
                            ['text:3','purchase_type','采购类型','','','','disabled'],
//                          ['linkage:3','supplier_type', '供应商类型','',SupplierTypeModel::column('id,name'),'',url('get_supplier_name'),'supplier_id'],
//                          ['select:3','supplier_id', '供应商名称'],
//                          ['linkage:3','source','源单类型','',[0=>'无源单',1=>'采购申请',2=>'采购计划',3=>'采购询价'],'',url('get_yd'),'source_id'],
                            ['text:3','purchase_organization', '采购部门', '','','','disabled'],
                            ['text:3','purchase_uid','采购员','','','','disabled'],
                         
                            ['select:3','pay_type','支付方式','',[0=>'现金',1=>'转账',2=>'支票',3=>'微信',4=>'支付宝']],
                            ['date:3','hetong_time','签约时间'],
//                          ['text:3', 'supplier_username', '供应商签约人'],
                            ['text:3', 'zrname', '我方签约人'],
                            ['select:3','arrival_type','交货方式','',[0=>'一次性交货',1=>'分批交货']],
                            ['select:3','transport_type','运货方式','',[0=>'空运',1=>'海运',2=>'快递']],
                            ['select:3','balance_type','结算方式','',[ 0=>'记账结算',1=>'备用金结算']],
                            ['select:3','money_type','币种','',[ 0=>'美元',1=>'人民币',2=>'欧元']],
                            ['number:3', 'rate', '汇率'],
                            ['text:6', 'hetong_address', '签约地点'],
                            ['static:3',' ','制单人','',get_nickname(UID)], 
                            ['static:3', 'create_time', '制单日期','',date('Y-m-d')],
                            ['files:6','file',' 附件'],
                            ['textarea','helpname','可查看人员(不填只自己和超级管理员可见)'],
                            ['hidden','zrid'],
                            ['wangeditor', 'remark','备注'],
                        ],
                        '合同物资明细' =>[
                            ['hidden', 'materials_list'],
                        ]
                    ])  
            ->js('hetong')      
            ->setExtraHtml(outhtml2())
            ->setExtraJs(outjs2()) 
            ->fetch();

    }
    
    public function get_Detail($source_id = ''){
            $data = PlanModel::getOne($source_id);
//          halt($data);
//          echo $data->cid;
//          echo "<hr>";
//          echo $data->oid;
//          echo "<hr>";
//          echo $data->is_add;
//          echo "<hr>";
//          echo $data->cuserid;
//          die();
            //$data->is_add=$data->is_add===0?'否':'是';
        
        return $data;
    }
    
    public function get_Mateplan($mateplan = '',$ptype = ''){
//      if($ptype == 1){
//      $materialsid = AskModel::where('id',$mateplan)->value('id');
//      $map = ['aid'=>$materialsid];
//      $data = AskModel::getMaterial($map);
//      }elseif($ptype == 2){
//      $materialsid = PlanModel::where('id',$mateplan)->value('id');
//      $map = ['aid'=>$materialsid];
//      $data = PlanModel::getMaterial($map);   
//      }elseif($ptype == 3){
        $materialsid = PlanModel::where('id',$mateplan)->value('id');
        $map = ['aid'=>$materialsid];
        $data = PlanModel::getPlans($map);
        //dump($data);die;
        //dump($data);die;
//      }else{
//          return $html='<span>请选择源单类型</span>';
//      }
        
        
//      halt($data);
        $html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name">
            <table class="table table-bordered">
                <tbody><tr><td>物品名称</td>
                    
                    <td>单位</td>
                    <td>规格</td>
                    <td>售价</td>
                    <td>数量</td>
                    <td>采购金额</td>
                    <td>供应商</td>
                    <td>供应商签约人</td>
                    </tr>';
            foreach ($data as $k => $v){ 
                
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


                $html.='<tr><input type="hidden" name="mid[]" value="'.$v['wid'].'">
                    <input type="hidden" name="mlid[]" value="'.$v['id'].'">
                        <td>'.$v['name'].'</td>
                        <td>'.$v['unit'].'</td>
                        <td>'.$v['version'].'</td>
                        <td><input type="number" class="jg" oninput="input(this)" name="bj_money[]" value="'.$v['bj_money'].'"></td>
                        <td><input type="number" class="sl" oninput="input(this)" name="plan_num[]" value="'.$v['plan_num'].'"></td>
                        <td><input type="number" readonly="readonly" name="plan_money[]" class="zj" value="'.$v['plan_money'].'">
                        </td>
                        <td>'.$html2.'</td>
                        <td><input type="text" name="supplier_username[]" ></td>
                        </tr>';
                        
            
            }           
            $html .= '</tbody></table></div>';
        return $html;
    }
    ////采购详情     
    public function detail($id=null){
        if($id==null)return $this->error('缺少参数');        
        $detail = HetongModel::getOne($id);
        $detail['hetong_user'] = get_nickname($detail['hetong_uid']);
        $detail['create_uid'] = get_nickname($detail['create_uid']);
        $detail['create_time'] = date('Y-m-d',$detail['create_time']);
       // $purchase_type = TypeModel::where('status=1')->column('id,name');
        //源单
//      if($detail['source']==1){
//          $ydnumber = AskModel::column('id,name');
//      }elseif($detail['source']==2){
//          $ydnumber = PlanModel::column('id,name');
//      }elseif($detail['source']==3){
//$ydnumber = MoneyModel::where(['id'=>$detail->source_id])->column('title');
//      }else{
//          $ydnumber = [0=>'无源单'];
//      }
        $detail->materials_list = implode(HetongMaterialModel::where('aid',$id)->column('id,wid'),',');
        //$detail->source_id=$ydnumber[0];
                $detail->helpid = get_helpname($detail['helpid']);
        
//      $detail
        
        return ZBuilder::make('form')
        ->setPageTitle('详情')             
        ->addGroup(
            [
                    '合同信息' =>[
                        ['hidden', 'id'],
                        ['static:3', 'name', '主题'],
                        ['static:3','purchase_type_name','采购类型'],
//                      ['static:3','supplier_name', '供应商名称'],
                        ['static:3','create_uid','制单人'], 
//                      ['linkage:3','source','源单类型','',[0=>'无源单',1=>'采购申请',2=>'采购计划',3=>'采购询价'],'',url('get_yd'),'source_id'],
                        ['static:3','source_id','询价单号'],
                        ['static:3','purchase_organization_name', '采购部门'],
                        ['static:3','purchase_nickname','采购员'],
                      
                        ['select:3','pay_type','支付方式','',[0=>'现金',1=>'转账',2=>'支票',3=>'微信',4=>'支付宝']],
                        ['date:3','hetong_time','签约时间'],
                    
                        ['select:3','arrival_type','交货方式','',[0=>'一次性交货',1=>'分批交货']],
                        ['select:3','transport_type','运货方式','',[0=>'空运',1=>'海运',2=>'快递']],
                        ['select:3','balance_type','结算方式','',[ 0=>'分段结算',1=>'合同结算',2=>'进度结算',3=>'竣工后一次结算']],
                        ['select:3','money_type','币种','',[ 0=>'美元',1=>'人民币',2=>'欧元']],
                        ['static:3', 'rate', '汇率'],

                        ['static:3', 'create_time', '制单日期'],
//                      ['static:3', 'supplier_username', '供应商签约人'],
                        ['static:3', 'hetong_user', '我方签约人'],
                        ['static:6', 'hetong_address', '签约地点'],
                        ['archives:6','file',' 附件'],
                                                ['static','helpid','可查看人员'],
                        ['wangeditor', 'remark','备注'],
                ],
                    '合同物资明细' =>[
                    ['hidden', 'materials_list'],
                    ['hidden', 'old_plan_list'],
                ]
            ])     
        ->setFormData($detail)    
        ->hideBtn('submit') 
        ->js('hetong')   
        ->fetch();

    }

    //删除
    public function delete($ids = null){        
        if($ids == null) $this->error('参数错误');
        $map['id'] = $ids;
//      halt($ids);
        if($model = HetongModel::where($map)->delete()){    
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
        ->js('hetong')
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

            $map = ['purchase_hetong_material.aid'=>$pid,'stock_material.id'=>['in',($materials_list)]];

            $data = HetongModel::getMaterial($map);
            

            $html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>物品名称</td>
                            <td>单位</td>
                            <td>规格</td>
                            <td>售价</td>
                            <td>采购数量</td>
                            <td>金额</td>
                            <td>供应商</td>
                            <td>供应商签约人</td>
                            </tr>';

            foreach ($data as $k => $v){ 
                $html.='<tr><input type="hidden" name="mid[]" value="'.$v['wid'].'">
                    <input type="hidden" name="mlid[]" value="'.$v['id'].'">
                        <td>'.$v['name'].'</td>
                        <td>'.$v['unit'].'</td>
                        <td>'.$v['version'].'</td>
                        <td>￥' . number_format($v['bj_money'],2) . '</td>
                        <td>'.$v['plan_num'].'</td>
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
