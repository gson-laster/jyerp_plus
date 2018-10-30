<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/1 0001
 * Time: 14:11
 */

namespace app\contract\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\contract\model\Income as IncomeModel;
//use app\contract\model\Obj as ObjModel;
use app\tender\model\Obj as ObjModel; //项目;
//use app\user\model\Organization as OrganizationModel;OrganizationModel::getMenuTree2()
use app\contract\model\IncomeDetail;
use app\contract\model\Materials as MaterialsModel;
class Income extends Admin
{
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('contract_income.id desc');
        // 数据列表
        $data_list = IncomeModel::getList($map,$order);
        $task_list = [
            'title' => '查看详情',
            'icon' => 'fa fa-fw fa-eye',
            'href' => url('task_list',['id'=>'__id__'])
        ];
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['title' => '合同名称']) // 设置搜索框
            ->addFilter(['sname'=>'tender_obj.name']) // 添加筛选
            ->addFilter('type',[1=>'总价合同',2=>'单价合同',3=>'成本加酬金合同'])
            ->addTimeFilter('date') // 添加时间段筛选
            ->addColumns([ // 批量添加数据列
              
                ['number','合同编号'],
                ['title', '合同名称'],
                ['type','合同类型', [1=>'总价合同',2=>'单价合同',3=>'成本加酬金合同']],
                ['nail','甲方单位'],
                ['second_party','乙方单位'],
                ['sname','所属项目'],
                ['nickname','签订人'],
                ['date','签订日期'],
                ['money', '合同金额'],
                ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                ['right_button', '操作', 'btn']
            ])
            ->addOrder('id,number,date,money,create_time')
            ->setSearch(['tender_obj.name'=>'所属项目'],'','',true) // 设置搜索框
            
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
            // 验证
            $data['operator']=$data['zrid'];
            $result = $this->validate($data, 'Income');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            if($model = IncomeModel::create($data)){
            	
            			flow_detail($data['title'],'contract_income','contract_income','contract/income/task_list',$model['id']);         
                //记入行为
//              action_log('tender_materials_add', 'tender_materials', $model['id'], UID);
                foreach($data['qdzm'] as $k => $v){
                    $info = array();
                    $info = [
                        'pid'=>$model['id'],
                        'qdzm'=>$v,
                        'dw'=>$data['dw'][$k],
                        'test_num'=>$data['test_num'][$k],
                        'zhdj'=>$data['zhdj'][$k],
                        'sum'=>$data['sum'][$k],
                        'bz'=>$data['bz'][$k]
                    ];
                    IncomeDetail::create($info);
                }

                $this->success('新增成功！',url('index'));
            }else{
                $this->error('新增失败！');
            }
        }

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
				$('input[name="end_date"]').change(function(){
					if (new Date($(this).val()).getTime() < new Date($('input[name="begin_date"]').val()).getTime()) {
						layer.msg('结束日期不得早于开始日期', {time: 3000})
						$(this).val('')
					}
				});
				$('input[name="begin_date"]').change(function(){
					if (new Date($(this).val()).getTime() > new Date($('input[name="end_date"]').val()).getTime()) {
						layer.msg('开始日期不得晚于结束日期', {time: 3000})
						$(this).val('')
					}
				});	
            </script>
EOF;
        return Zbuilder::make('form')
            ->addGroup(
                [
                    '收入合同' =>[
                        ['hidden', 'fileid'],
                        ['hidden', 'zrid'],
                        ['hidden','authorized',UID],
                        ['date:4', "date", '日期', '', date('Y-m-d')],
                        ['text:4', "number", '合同编号'],
                        ['text:4', "title", '合同标题'],
                        ['select:4', "attach_item", '所属项目','',ObjModel::get_nameid()],
                        ['select:4', "type", '合同类型','',[1=>'总价合同',2=>'单价合同',3=>'成本加酬金合同']],
                        ['date:4', "begin_date", '开始日期'],
                        ['date:4', "end_date", '结束日期',],
                        ['number:4', "money", '合同金额'],
                        ['text:4', "big_money", '大写金额'],
                        ['text:4', "nail", '甲方单位'],
                        ['text:4', "second_party", '乙方单位'],
                        ['text:4', "zrname", '签订人'],
                        ['select:4', "pay_type", '付款方式','',[1=>'按进度付款',2=>'按合同付款']],
                        ['select:4', "balance", '结算方式','',[1=>'按月结算',2=>'分段结算',3=>'目标结算',4=>'竣工后一次结算',5=>'其他']],
                        ['number:4', "advances_received",'预付款'],
                        ['number:4', "bail", '保证金'],
                        ['textarea', "collection_terms", '收款条件'],
                        ['textarea', "main_requirements", '主要条款'],
                        ['textarea','remark','备注'],
                        ['file:4','path','文件路径'],
                    ],
                    '工程量清单' =>[
                        ]

                ]
            )
            ->setExtraHtml(outhtml2())
            ->setExtraJs($js.outjs2())
						->js('chineseNumber')
            ->js('test1')
            ->fetch();
    }

    /**
     * 删除
     * @param array $record 行为日志
     * @author 黄远东<641435071@qq.com>
     * @return mixed
     */
    // public function delete($record = [])
    // {
    //     $ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    //     // 删除节点
    //     if (MaterialsModel::destroy($ids)) {
    //         // 记录行为
    //         $ids = is_array($ids)?implode(',',$ids):$ids;
    //         $details = '生产任务ID('.$ids.'),操作人ID('.UID.')';
    //         //action_log('produce_plan_delete', 'produce_plan', $ids, UID, $details);
    //         $this->success('删除成功');
    //     } else {
    //         $this->error('删除失败');
    //     }
    // }
    //查看

//查看
    public function task_list($id = null){


        if($id == null) $this->error('参数错误');
        $info = IncomeModel::getOne($id);
        $arr = [1=>'总价合同',2=>'单价合同',3=>'成本加酬金合同'];
        $info['type'] = $arr[$info['type']]; 		//合同类型
        
      	$pay_type = [1=>'按进度付款',2=>'按合同付款'];
      	$info['pay_type'] = $pay_type[$info['pay_type']];
      	
      	$balance = [1=>'按月结算',2=>'分段结算',3=>'目标结算',4=>'竣工后一次结算',5=>'其他'];		//结算方式
      	$info['balance'] = $balance[$info['balance']];
      	
      	$details_list = IncomeDetail::getList($id);
     
      	$strs = '';
      	foreach($details_list as $v) {
//    		echo $strs;
//    			dump($v['id']);
//    	exit;
      		$strs = $strs.'<tr class="">
                                  
            
           
        <td class="">
                        '.$v["qdzm"].'                                   </td>
        <td class="">
                       '.$v["dw"].'                                     </td>
        <td class="">
                     '.$v["test_num"].'                                     </td>
        <td class="">
                       '.$v["zhdj"].'                                      </td>
        <td class="">
                     '.$v["sum"].'                                        </td>
        <td class="">
                      '.$v["bz"].'                                       </td>
   
                                                      </tr>';
      	}
      	$js = <<<EOF
      		<script type="text/javascript">
                $(function(){
      			
      			var j = `<table class="table table-builder table-hover table-bordered table-striped js-table-checkable">
                                <thead>
                                    <tr>
                 
                                                                                <th class="column-type ">
                                            
                                            清单子目                                            <span>
                                            
                                                                                        </span>
                                        </th>
                                                                                <th class="column-address ">
                                            
                                            单位                                            <span>
                                            
                                                                                        </span>
                                        </th>
                                                                                <th class="column-zrid ">
                                            
                                            工程量                                            <span>
                                            
                                                                                        </span>
                                        </th>
                                                                                <th class="column-tender_time ">
                                            
                                            综合单价                                            <span>
                                            
                                                                                        </span>
                                        </th>
                                                                                <th class="column-unit ">
                                            
                                            合价                                            <span>
                                            
                                                                                        </span>
                                        </th>
                                                                                <th class="column-contact ">
                                            
                                            备注                                            <span>
                                            
                                                                                        </span>
                                        </th>
                          
                                   
                                                                            </tr>
                                </thead>
                                <tbody>
                                                       
                                       ${strs}                            
                                </tbody>
                            </table>`;
    $('#tab-2').append(j);
                        });    
      		</script>
EOF;



				

        return ZBuilder::make('form')
            ->addGroup([
                '收入合同'=>[
                    ['hidden','id'],
                    ['hidden','authorized'],
                    ['static:3',"date", '日期'],
                    ['static:3',"number", '合同编号'],
                    ['static:3',"title", '合同标题'],
                    ['static:3',"objname", '所属项目'],
                    ['static:3',"type", '合同类型'],
                    ['static:3',"begin_date", '开始日期'],
                    ['static:3',"end_date", '结束日期'],
                    ['static:3',"money", '合同金额'],
                    ['static:3', "big_money", '大写金额'],
                    ['static:3',"nail", '甲方单位'],
                    ['static:3',"second_party", '乙方单位'],
                    ['static:3',"authorizedname", '签订人'],
                    ['static:3',"pay_type", '付款方式'],
                    ['static:3',"balance", '结算方式'],
                    ['static:3',"advances_received",'预付款'],
                    ['static:3',"bail", '保证金'],
                    ['static:3',"collection_terms", '收款条件'],
                    ['static:3',"main_requirements", '主要条款'],
                   
                    ['static','remark','备注'],
                   // ['static:4','path','文件路径'],
                ],
                '收入合同明细' =>[
                    ['hidden', 'materials_list'],
                    ['hidden', 'old_plan_list'],
                ]
            ])
		->setExtraJs($js)
            
            ->setFormData($info)
//          ->js('test2')
            ->hideBtn('submit')
            ->fetch();
    }
    //弹出
    public function choose_materials($materials = '',$pid = null)
    {
        $map['status'] = 1;
        if($pid!==null){
            $map['type'] = $pid;
            //$map['id'] = ['not in',$materials];
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

        $data = MaterialModel::where($map)->select();
        $this->assign('data',$data);
        $this->assign('resulet',MaterialTypeModel::getOrganization());

        // 查询
        $map = $this->getMap();

        $map['id'] = ['not in',$materials];

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
                ['version', '规格型号',],
                ['unit', '单位'],
                ['status', '启用状态', 'status'],
            ])
            ->setRowList($data_list) // 设置表格数据
            ->setExtraJs($js)
            ->js('test1')
            ->addTopButton('pick', $btn_pick)
            ->assign('empty_tips', '暂无数据')
            ->fetch('admin@choose/choose'); // 渲染页面
    }
    //明细
    public function tech($pid = '',$materials_list = '')
    {
        if($materials_list == '' || $materials_list== 'undefined') {
            $html = '';
        }else{
            $map = ['contract_materials_detail.pid'=>$pid,'stock_material.id'=>['in',($materials_list)]];
            $data = MaterialsModel::getDetail($map);
            //dump($data);die;
            $html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>需用数量</td><td>单价</td><td>计划进场日期</td><td>计划退场日期</td><td>租赁天数</td><td>小计</td><td>操作</td></tr>';
            foreach ($data as $k => $v){
                $html.='<tr><input type="hidden" name="mid[]" value="'.$v['itemsid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['xysl'].'</td><td>'.$v['ckjg'].'</td><td>'.$v['tax'].'</td><td>'.$v['havetax'].'</td><td>'.$v['notax'].'</td><td>'.$v['xj'].'</td><td><a href="javascript:;" onclick="delMaterials(this,\''.$v['itemsid'].'\')">删除</a></td></tr>';
            }
            $html .= '</tbody></table></div>';

        }
        return $html;
    }

}