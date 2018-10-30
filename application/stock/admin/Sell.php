<?php
namespace app\stock\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\stock\model\House as HouseModel;
use app\stock\model\Sell as SellModel;
use app\stock\model\Selldetail as SelldetailModel;
use app\user\model\Organization as OrganizationModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\task\model\Task_detail as Task_detailModel;
use app\sales\model\Delivery as DeliveryModel;
use app\stock\model\Stock as StockModel;
/**
 * 销售出库控制器
 */
class Sell extends Admin
{
	/*
	 * @author HJP<957547207>
	 */
	public function index()
	{
		// 查询
		$map = $this->getMap();
		// 排序
		$order = $this->getOrder('stock_sell.create_time desc');
		// 数据列表
		$data_list = SellModel::getList($map,$order);
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
		// 使用ZBuilder快速创建数据表格
		return ZBuilder::make('table')
			->setSearch(['stock_sell.name' => '出库主题'], '', '', true)
			->addOrder(['code','ck_time']) // 添加排序
			->addFilter(['department'=>'admin_organization.title','ckbm'=>'admin_organization.title']) // 添加筛选
			->addColumns([
				['code', '编号'],
				['name', '出库主题'],
				['deliveryid', '销售发货订单'],
				['customer_name', '客户名称'],
				['department', '销售部门'],
				['uid', '业务员'],
				['zrid' ,'经办人'],
				['ckbm', '出库部门'],
				['ckid', '出库人'],
				['ck_time', '出库时间','datetime'],
				['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],			
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
			// 验证
			$result = $this->validate($data, 'sell');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			$data['code'] = 'SCRK'.date('YmdHis',time());
			$data['ck_time'] = strtotime($data['ck_time']);			
			if($model = SellModel::create($data)){
				flow_detail($data['name'],'stock_sell','stock_sell','stock/sell/task_list',$model['id']);
				//记入行为
				foreach($data['mid'] as $k => $v){
            		$info = array();
            		$info = [
            				'pid'=>$model['id'],
            				'itemsid'=>$v,
            				'ck'=>$data['ck'][$k],
            				'cksl'=>$data['cksl'][$k],
            				'je'=>$data['je'][$k],
							'bzxq'=>$data['bzxq'][$k],
            				'bz'=>$data['bz'][$k],
            		];  
					if(StockModel::where('ckid',$info['ck'])->value('number')<$info['cksl']){
						$this->error('库存不足');
					}else{
						SelldetailModel::create($info); 
					}            		        		      	
            	}
            	      
				$this->success('新增成功！',url('index'));
			}else{
				$this->error('新增失败！');
			}
		}
		$ck = HouseModel::column('id,name');
        $html = ' <script type="text/javascript">
            var house_select = \'<select name="ck[]">';
        foreach ($ck as $key => $value) {
            $html.='<option value="'.$key.'">'.$value.'</option>';
            
        }
        $html.='</select>\';
        </script>';
		return Zbuilder::make('form')
		 ->addGroup(
        [
          '销售出库' =>[
            ['hidden','zrid'],
            ['hidden','ckid',UID],
            ['hidden','zdid',UID],           
            ['hidden', 'helpid'],
			['text:4','name','出库主题'],
			['select:4', 'deliveryid', '销售发货订单','',DeliveryModel::getName()],
			['text:4', 'customer_name', '客户名称','','','','disabled'],
			['text:4', 'department', '销售部门','','','','disabled'],
			['text:4', 'uid', '业务员','','','','disabled'],
			['text:4', 'goodaddrss', '发货地址','','','','disabled'],
			['text:4', 'addrss', '收货地址','','','','disabled'],
			['text:4', 'zrname', '经办人'],
			['static:4', 'ckname', '出库人','',get_nickname(UID)],
			['select:4', 'ckbm', '出库部门','', OrganizationModel::getMenuTree2()],
			['date:4', 'ck_time', '出库日期'],
			['static:4','zdname','制单人','',get_nickname(UID)],			
			['files','file','附件'],
			['textarea:8', 'helpname', '可查看该入库人员'],		
			['textarea','note','摘要'],	
          ],
          '新增物品' =>[
            ['hidden', 'materials_list'],
          ]
        ]
      )		
		->js('test4')
		->setExtraHtml(outhtml2())
		->setExtraJs($html.outjs2())	
		->fetch();
	}
		public function get_Mateplan($mateplan = ''){

		if($mateplan == ''){
			return $html='<span>请选择销售发货</span>';	
		}
		$ck = HouseModel::column('id,name');
        $html2 = '<select name="ck[]">';
        foreach ($ck as $key => $value) {
            $html2.='<option value="'.$key.'">'.$value.'</option>';            
        }
        $html2.='</script>';
		
		$materialsid = DeliveryModel::where('id',$mateplan)->value('id');
		$map = ['pid'=>$materialsid];
		$data = DeliveryModel::getDetail($map);
			
		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>单价</td><td>出库数量</td><td>仓库</td><td>金额(元)</td><td>包装需求</td><td>备注</td></tr>';
    		foreach ($data as $k => $v){ 
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['itemsid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['price'].'</td><td><input type="number" name="cksl[]" value="'.$v['fhsl'].'"></td><td>'.$html2.'</td><td><input type="number" name="je[]" value="'.$v['price']*$v['fhsl'].'"></td><td><input type="text" name="bzxq[]" value="'.$v['bzxq'].'"></td><td><input type="text" name="bz[]"></td></tr>';
    		}   		
    		$html .= '</tbody></table></div>';
		return $html;				
	}
	public function get_Detail($deliveryid = ''){		
			$data = DeliveryModel::get_Detail($deliveryid);
		return $data;
	}
	public function delete($record = [])
    {
   		$ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    	// 删除节点
    	if (SellModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		$details = '生产任务ID('.$ids.'),操作人ID('.UID.')';
    		//action_log('produce_plan_delete', 'produce_plan', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
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
            	['status', '启用状态', 'status'],
            ])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js.$html)
    	->js('test4')
    	->addTopButton('pick', $btn_pick)
    	->assign('empty_tips', '暂无数据')
    	->fetch('admin@choose/choose'); // 渲染页面
    }

    //查看
    public function task_list($id = null){
    	if($id == null) $this->error('参数错误');		
		$info = SellModel::getOne($id);
		$info['materials_list'] = implode(SellModel::getMaterials($id),',');
		$info['helpname'] = Task_detailModel::get_helpname($info['helpid']);
		$info->ck_time = date('Y-m-d',$info['ck_time']);
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addGroup([
		'销售出库'=>[
			['hidden','id'],            
			['static:4','name','入库主题'],
			['static:4','deliveryid','销售发货订单'],
			['static:4','customer_name','客户名称'],
			['static:4','department','销售部门'],
			['static:4','uid','业务员'],
			['static:4','goodaddrss','发货地址'],			
			['static:4','addrss','收货地址'],
			['static:4','zdid','经办人',],	
			['static:4','ckid','出库人',],	
			['static:4','ckbm', '出库部门'],
			['static:4','ck_time', '出库日期'],
			['static:4','zdid', '制单人'],
			['archives','file','附件'],
			['static:8', 'helpname', '可查看该入库人员'],		
			['static','note','摘要'],										
		],
          '销售出库明细' =>[
            ['hidden', 'materials_list'],
            ['hidden', 'old_plan_list'],
          ]			
		])
		->setExtraJs(outjs2())
		->setFormData($info)
		->js('test4')
		->fetch();
    }
	
	//明细
     public function tech($pid = '',$materials_list = '')
    {
    	if($materials_list == '' || $materials_list == 'undefined') {
    		$html = '';	
    	}else{
    		$map = ['stock_sell_detail.pid'=>$pid,'stock_material.id'=>['in',($materials_list)]];
    		$data = SellModel::getDetail($map);
    		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>售价</td><td>仓库</td><td>出库数量</td><td>出库金额</td><td>包装需求</td><td>备注</td></tr>';
    		foreach ($data as $k => $v){ 
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['itemsid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['price'].'</td><td>'.$v['ck'].'</td><td>'.$v['cksl'].'</td><td>'.$v['je'].'</td><td>'.$v['bzxq'].'</td><td>'.$v['bz'].'</td></tr>';
    		}   		
    		$html .= '</tbody></table></div>';
    
    	}
    	return $html;
    }
}
