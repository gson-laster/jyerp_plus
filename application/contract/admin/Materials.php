<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/1 0001
 * Time: 09:46
 */

namespace app\contract\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\contract\model\MaterialsDetil;
//use app\contract\model\Obj as ObjModel;
use app\tender\model\Obj as ObjModel; //项目;
use app\contract\model\Materials as MaterialsModel;
//use app\contract\model\Supplier;
use app\supplier\model\Supplier;//供应商
use app\tender\model\Materialsdetail;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
//use app\contract\model\Supplier as SupplierModel;
use app\tender\model\Materials as MModel;
class Materials extends Admin
{

    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('contract_materials.create_time desc');
        // 数据列表
        $data_list = MaterialsModel::getList($map,$order);
        
        
        

        $task_list = [
            'title' => '查看详情',
            'icon' => 'fa fa-fw fa-eye',
            'href' => url('task_list',['id'=>'__id__'])
        ];
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['lname'=>'供应商']) // 设置搜索框
            ->addFilter(['obname'=>'tender_obj.name']) // 添加筛选           
            ->addTimeFilter('contract_materials.create_time')
            ->addOrder('id,number,money,create_time,name')
            ->addColumns([ // 批量添加数据列               
                ['number','合同编号'],
                ['name', '合同名称'],
                ['money', '合同金额'],
                ['obname','所属项目'],
                ['lname','供应商'],
                ['create_time', '日期','date'],
     						['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                ['right_button', '操作', 'btn']
            ])

            ->addTopButtons('delete') // 批量添加顶部按钮
            ->addRightButtons('delete')
        	//->setTableName('contract_materials') // 指定数据表名
            ->addRightButton('task_list',$task_list,true) // 查看右侧按钮
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板
    }


    public function add(){


        $name = session('user_auth')['role_name'];
        if($this->request->isPost()){
            $data = $this->request->post();
            // 验证
            $data['obj_id'] = MaterialsModel::getItem($data['snumber']);                 
            $result = $this->validate($data, 'Materials');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            if($model = MaterialsModel::create($data)){
							
						
							
							
							flow_detail($data['name'],'contract_materials','contract_materials','contract/materials/task_list',$model['id']);        
                //记入行为
//                action_log('tender_materials_add', 'tender_materials', $model['id'], UID);
                foreach($data['mid'] as $k => $v){
                    $info = array();
                    $info = [
                        'pid'=>$model['id'],
                        'itemsid'=>$v,
                        'xysl'=>$data['xysl'][$k],
                        'ckjg'=>$data['ckjg'][$k],
                        'tax'=>$data['tax'][$k],
                        'havetax'=>$data['havetax'][$k],
                        'notax'=>$data['notax'][$k],
                        'xj'=>$data['xj'][$k]
                    ];
                    MaterialsDetil::create($info);
                }

                $this->success('新增成功！',url('index'));
            }else{
                $this->error('新增失败！');
            }
        }
        
        
        $stype = [1=>'材料需求计划'];
        
        
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
				$('input[name="edate"]').change(function(){
					if (new Date($(this).val()).getTime() < new Date($('input[name="sdate"]').val()).getTime()) {
						layer.msg('结束日期不得早于开始日期', {time: 3000})
						$(this).val('')
					}
				});
				$('input[name="sdate"]').change(function(){
					if (new Date($(this).val()).getTime() > new Date($('input[name="edate"]').val()).getTime()) {
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
                        ['hidden', 'fileid'],
                        ['hidden','authorized',UID],
                        ['date:3','date','日期','',date('Y-m-d')],
                        ['text:3','number','合同编号'],
                        ['text:3','name','合同名称'],
                        ['Linkage:3','stype','源单类型','',$stype,'',url('get_snumber'),'snumber'],
                        ['select:3','snumber','源单号'],
                        ['select:3','ctype','合同类型','',[1=>'材料合同'],1],
                        ['text:3','objname','所属项目','','','','disabled'],
                        ['date:3','sdate','开始日期'],
                        ['date:3','edate','结束日期'],
                        ['number:3','money','合同金额'],
                        ['text:3', 'big_money','金额大写'],
                        ['number:3','premoney','预付金额'],
                        ['text:3','bzmoney','保证金'],
                        ['select:3','supplier','供应商','',Supplier::getOBJ()],
                        ['text:3','supplier_w','供应商签约人',],
                        ['select:3','is_add','是否为增值税','',[0=>'是',1=>'否']],
                        ['select:3','paytype','支付方式','',[1=>'现金',2=>'转账',3=>'支票',4=>'微信']],
                        ['select:3',' ftype','结算方式','',[1=>'分段结算',2=>'合同结算',3=>'进度结算',4=>'竣工后一次性结算']],
                        ['select:3','handle_type','交货方式','',[0=>'分批交货',1=>'一次性交货']],
                        ['text:3','place','签约地点'],
                        ['text:3','people','我方签约人'],
                        ['text:4','authorizedname','录入人','',$name],
                        ['textarea','note','付款条件'],
                        ['textarea','notes','主要条款'],
                        ['textarea','remark','备注'],
                    ],
                    '需求明细' =>[
                        ['hidden', 'materials_list'],
                    ]
                ]
            )
            ->setExtraJs($js.outjs2())
						->js('chineseNumber')
            ->js('contract')
            ->js('Materials')
            ->fetch();
    }
    public function getSnumber($snumber = ''){
			$data = MaterialsModel::getDetail1($snumber);
			//dump($data);die;
		return $data;
	}
	
	
	
    
     public function get_snumber($stype=''){
        $res= MModel::getStype($stype);
       	//dump($res);die;
        $array =array();
        foreach($res as $key=>$val){
            $array[] = ['key'=>$val['id'],'value'=>$val['name']];
        }
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
       
        $arr['list'] =$array; //数据
        //dump($arr);die;
        return json($arr);
    }
    
    
    public function getDetail($snumber = ''){
			$data = MaterialsModel::getDetail1($snumber);
			//dump($data);die;
		return $data;
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
        if (MaterialsModel::destroy($ids)) {
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
        $info = MaterialsModel::getOne($id);
        $arr = [1=>'现金',2=>'转账',3=>'支票',4=>'微信'];
        $info['paytype'] = $arr[$info['paytype']];
        
        
        $arr1 = [1=>'是',0=>'否'];
        $info['is_add'] = $arr1[$info['is_add']];
        $arr2=[1=>'分段结算',2=>'合同结算',3=>'进度结算',4=>'竣工后一次性结算'];
        $info['ftype'] = $arr2[$info['ftype']];
        
        
        $arr3=[0=>'分批交货',1=>'一次性交货'];
        $info['handle_type'] = $arr3[$info['handle_type']];
        
        
        $arr4=[1=>'材料需求计划'];
        $info['stype'] = $arr4[$info['stype']];
        $arr5=[1=>'材料合同'];
        $info['ctype'] = $arr5[$info['ctype']];



        $info['materials_list'] = implode(MaterialsModel::getMaterials($id),',');

        return ZBuilder::make('form')
            ->addGroup([
                '材料合同'=>[
                    ['hidden','id'],
                    ['hidden','authorized'],
                    ['static:3','date','日期'],
                    ['static:3','number','合同编号'],
                    ['static:3','name','合同名称'],
                    ['static:3','stype','源单类型'],
                    ['static:3','snumber','源单号'],
                    ['static:3','ctype','合同类型'],
                    ['static:3','objname','所属项目'],
                    ['static:3','sdate','开始日期'],
                    ['static:3','edate','结束日期'],
                    ['static:3','money','合同金额'],
                    ['static:3', "big_money", '大写金额'],
                    ['static:3','premoney','预付金额'],
                    ['static:3','bzmoney','保证金'],
                    ['static:3','supplier','供应商'],
                    ['static:3','supplier_w','供应商签约人'],
                    ['static:3','is_add','是否为增值税'],
                    ['static:3','paytype','支付方式',],
                    ['static:3','ftype','结算方式'],
                    ['static:3','handle_type','交货方式'],
                    ['static:3','place','签约地点'],
                    ['static:3','people','我方签约人'],
                    ['static:4','authorizedname','录入人'],
                    ['static','note','付款条件'],
                    ['static','notes','主要条款'],
                    ['static','remark','备注'],
                    ['static:4','path','文件路径'],
                ],
                '材料合同明细' =>[
                    ['hidden', 'materials_list'],
                    ['hidden', 'old_plan_list'],
                ]
            ])
            ->setExtraJs(outjs2())
            ->setFormData($info)
            ->js('contract')
            ->hideBtn('submit')
            ->fetch();
    }
    //弹出
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
            ->js('contract')
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
            $map = ['contract_materials_detail.pid'=>$pid,'stock_material.id'=>['in',($materials_list)]];
            $data = MaterialsModel::getDetail($map);
            //dump($data);die;
            $html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>需用数量</td><td>单价</td><td>税率%</td><td>含税价格</td><td>不含税价格</td><td>小计</td><td>操作</td></tr>';
            foreach ($data as $k => $v){
                $html.='<tr><input type="hidden" name="mid[]" value="'.$v['itemsid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['xysl'].'</td><td>'.$v['ckjg'].'</td><td>'.$v['tax'].'</td><td>'.$v['havetax'].'</td><td>'.$v['notax'].'</td><td>'.$v['xj'].'</td><td><a href="javascript:;" onclick="delMaterials(this,\''.$v['itemsid'].'\')">删除</a></td></tr>';
            }
            $html .= '</tbody></table></div>';

        }
        return $html;
    }


}