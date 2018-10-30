<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 15:42
 */

namespace app\constructionsite\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\tender\model\Obj as IModel; //项目;
use app\tender\model\Hire as planModel; // 租赁计划
use app\supplier\model\Supplier as SupplierModel;//供应商
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\tender\model\Hirecontract as HirecontractModel;
use app\tender\model\Hiredetail_contract;
class Hirecontract extends Admin
{
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('tender_contract_hire.id desc');
        
        // 数据列表
        $data_list = HirecontractModel::getList($map,$order);

        $task_list = [
            'title' => '查看详情',
            'icon' => 'fa fa-fw fa-eye',
            'href' => url('task_list',['id'=>'__id__'])
        ];
        
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['tender_contract_hire.name' => '合同名称','supplier_list.name'=>'供应商']) // 设置搜索框
            ->addOrder('id,number,money,create_time')
            ->addFilter(['obname'=>'tender_obj.name']) // 添加筛选
            ->addColumns([ // 批量添加数据列
           
                ['number','合同编号'],
                ['name', '合同名称'],
                ['money', '合同金额'],
                ['objname','供应商'],
                ['obname','所属项目'],
                ['authorizedname', '签订人'],
                ['create_time', '日期','date'],
                ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
               // ['status', '状态', 'status',''],
                ['right_button', '操作', 'btn']
            ])
            ->addTopButtons('delete') // 批量添加顶部按钮
            ->addRightButtons('delete')
            ->addRightButton('task_list',$task_list,true) // 查看右侧按钮
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板
    }


    public function add(){


        $name = session('user_auth')['role_name'];
        if($this->request->isPost()){
            $data = $this->request->post();
            //if (strtotime($data['start_time']) > strtotime($data['end_time'])) $this -> error('结束日期不得大于开始日期');
			$data['create_time'] = time();
			$data['create_uid'] = UID;
		//	$data['create_uid'] = UID;
			$data['people'] = $data['helpid'];
            $result = $this->validate($data, 'Hire');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            $data['money'] = number_format($data['money']);
            if($model = HirecontractModel::create($data)){
							 flow_detail($data['name'],'tender_contract_hire','tender_contract_hire','tender/hirecontract/task_list',$model['id']);
                //记入行为
                foreach($data['mid'] as $k => $v){
                    $info = array();
                    $info = [
                        'pid'=>$model['id'],
                        'itemsid'=>$v,
                        'xysl'=>$data['xysl'][$k],
                        'ckjg'=>$data['ckjg'][$k],
                        'sdate'=>$data['sdate'][$k],
                        'edate'=>$data['edate'][$k],
                        'hire_day'=>$data['hire_day'][$k],
                        'xj'=>$data['xj'][$k]
                    ];
                    Hiredetail_contract::create($info);
                }
                $this->success('新增成功！',url('index'));
            }else{
                $this->error('新增失败！');
            }
        }
        $itemList = IModel::get_nameid(); 		//所属项目
        $planList = PlanModel::getPlan();		//租赁计划
        $supList = SupplierModel::getOBJ(); 	//供应商
        
        
         $js = <<<EOF
            <script type="text/javascript">
                $(function(){
                   $('#money').attr('oninput','return Edit1Change();');				   					
                });
				var j=chineseNumber(document.getElementById("money").value);
				document.getElementById("big_money").value=j;		
				function Edit1Change(){			
					document.getElementById("big_money").value=chineseNumber(document.getElementById("money").value);
				}
				
				$('input[name="end_time"]').change(function(){
					if (new Date($(this).val()).getTime() < new Date($('input[name="start_time"]').val()).getTime()) {
						layer.msg('结束日期不得早于开始日期', {time: 3000})
						$(this).val('')
					}
				});
				$('input[name="start_time"]').change(function(){
					if (new Date($(this).val()).getTime() > new Date($('input[name="end_time"]').val()).getTime()) {
						layer.msg('开始日期不得晚于结束日期', {time: 3000})
						$(this).val('')
					}
				});
				
            </script>
EOF;
        
        
        return Zbuilder::make('form')
            ->addGroup(
                [
                    '租赁合同' =>[
                        ['hidden', 'helpid'],
                        ['hidden','authorized',UID],
                        ['date:3','date','日期','',date('Y-m-d')],
                        ['text:3','number','合同编号'],
                        ['text:3','name','合同名称'],
                        ['select:3','plan','租赁计划','', $planList],
                        ['select:3','obj_id','所属项目','', $itemList],
                        ['number:3','money','合同金额'],
                        ['text:3', 'big_money','金额大写'],	
                        ['select:3','ctype','合同类型','',[1=>'租赁合同',2=>'总承包租赁合同']],
                        ['date:3','start_time','开始日期'],
                        ['date:3','end_time','结束日期'],
                        ['select:3','supplier','供应商','', $supList],
                        ['select:3','ftype','结算方式','',[1=>'分段结算',2=>'竣工后一次结算',3=>'进度款结算']],
                        ['select:3','paytype','付款方式','',[1=>'按合同付款',2=>'按进度付款']],
                        ['number:3','premoney','预付金额'],
                        ['number:3','bzmoney','保证金'],
                        ['text:3','helpname','参与人员'],
                        ['static:4','authorizedname','填报人','',$name],
                        ['textarea','note','付款条件'],
                        ['textarea','notes','主要条款'],
                    ],
                    '需求明细' =>[
                        ['hidden', 'materials_list'],
                    ]
                ]
            )
            ->setExtraHtml(outhtml2())
            ->setExtraJs($js.outjs2())
						->js('chineseNumber,hire')
           
            ->fetch();
    }

    /**
     * 删除
     * @param array $record 行为日志
     * @author 黄远东<641435071@qq.com>
     * @return mixed
     */
    public function delete($record = [])
    {
        $ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
        // 删除节点
        if (HirecontractModel::destroy($ids)) {
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
        $info = HirecontractModel::getOne($id);
        //dump($info);
//      exit;
        $arr = [1=>'租赁合同',2=>'总承包租赁合同'];
        $info['ctype'] = $arr[$info['ctype']];
        
        $arrs = SupplierModel::getOBJ();
        $info['supplier'] = $arrs[$info['supplier']];
        
        $arrss = [1=>'分段结算',2=>'竣工后一次结算',3=>'进度款结算'];
        $info['ftype'] = $arrss[$info['ftype']];
        
        $info['date'] = date('Y-m-d', $info['create_time']);
        
        $payType = [1=>'按合同付款',2=>'按进度付款'];
        $info['paytype'] = $payType[$info['paytype']];
        
        $info['materials_list'] = implode(HirecontractModel::getMaterials($id),',');  
        
        
        return ZBuilder::make('form')
            ->addGroup([
                '租赁合同'=>[
                    ['hidden','id'],
                    ['hidden','authorized'],
                    ['static:3','date','日期'],
                    ['static:3','number','合同编号'],
                    ['static:3','name','合同名称'],
                    ['static:3','hireName','租赁计划'],
                    ['static:3','objname','所属项目'],
                    ['static:3','money','合同金额'],
                    ['static:3', "big_money", '大写金额'],
                    ['static:3','ctype','合同类型'],
                    ['static:3','start_time','开始日期'],
                    ['static:3','end_time','结束日期'],
                    ['static:3','supplier','供应商'],
                    ['static:3','ftype','结算方式',],
                    ['static:3','paytype','付款方式'],
                    ['static:3','premoney','预付金额'],
                    ['static:3','bzmoney','保证金'],
                    ['static:3','authorizedname','参与人员'],
                    ['static:3','authorizedname','填报人'],
                    ['static:3','note','付款条件'],
                    ['static:3','notes','主要条款'],
                    ['static:3','path','文件路径'],
                ],
                '租赁合同明细' =>[
                    ['hidden', 'materials_list'],
                    ['hidden', 'old_plan_list'],
                ]
            ])
            ->setExtraJs(outjs2())
            ->setFormData($info)
            ->hideBtn('submit')
            ->js('hire')
            ->fetch();
    }
    //弹出
    public function choose_materials($hire = '',$pid = null)
    {
    	
//  	halt($pid);
        $map['status'] = 1;
        if($pid!==null){
            $map['type'] = $pid;
            $map['id'] = ['not in',$hire];
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
        //dump($map);die;
//		echo 1;
        $data = MaterialModel::where($map)->select();
        $this->assign('data',$data);
        $this->assign('resulet',MaterialTypeModel::getOrganization());

        // 查询
        $map = $this->getMap();

        $map['id'] = ['not in',$hire];

        // 排序
        $order = $this->getOrder();

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
                	$('#pickinp').val({$hire});
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
                ['version', '规格型号',],
                ['unit', '单位'],
                ['status', '启用状态', 'status'],
            ])
            ->setRowList($data_list) // 设置表格数据
            ->setExtraJs($js)
            ->js('hire')
            ->addTopButton('pick', $btn_pick)
            ->assign('empty_tips', '暂无数据')
            ->fetch('admin@choose/choose'); // 渲染页面
    }
    //明细
    public function tech($pid = '',$materials_list = '')
    {
        if($materials_list == '' || $materials_list == 'undefined') {
            $html = '';
        }else{
            $map = ['tender_contract_hire_detail.pid'=>$pid,'stock_material.id'=>['in',($materials_list)]];
            $data = HirecontractModel::getDetail($map);
           
            $html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>需用数量</td><td>单价</td><td>计划进场日期</td><td>计划退场日期</td><td>租赁天数</td><td>小计</td></tr>';
            foreach ($data as $k => $v){
                $html.='<tr><input type="hidden" name="mid[]" value="'.$v['itemsid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['xysl'].'</td><td>'.$v['ckjg'].'</td><td>'.$v['sdate'].'</td><td>'.$v['edate'].'</td><td>'.$v['hire_day'].'</td><td>'.$v['xj'].'</td></tr>';
            }
            $html .= '</tbody></table></div>';

        }
        return $html;
    }

}