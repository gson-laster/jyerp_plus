<?php
namespace app\stock\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\stock\model\Allot as AllotModel;
use app\stock\model\Allotdetail as AllotdetailModel;
use app\user\model\Organization as OrganizationModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\stock\model\House as HouseModel;
use app\task\model\Task_detail as Task_detailModel;
/*
 * 库存调拨
 * HJP
 */
class Allot extends Admin
{
	/*
	 * 库存调拨列表
	 */
	public function index()
	{
		// 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('stock_allot.create_time desc');
        // 数据列表
        $data_list = AllotModel::getList($map,$order);
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];     
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
           ->setSearch(['stock_allot.name' => '调拨主题'], '', '', true)
			->addOrder(['code','create_time','dh_time']) // 添加排序
			->addFilter(['yhid'=>'admin_organization.title','dhid'=>'admin_organization.title']) // 添加筛选
			->addFilter('why',[0 => '平衡需求', 1=>'货物短缺']) // 添加筛选
            ->addColumns([ // 批量添加数据列
                ['code', '编号'], 
            	['name', '调拨主题'],
            	['zrid', '调拨负责人'],
            	['yhid', '要货部门'],
            	['dh_time', '要求到货日期','date'],
            	['why', '调拨原因',[0 => '平衡需求', 1=>'货物短缺']],
            	['dhid', '调货部门'],
            	['zdid', '制单人'],
            	['create_time', '制单时间','datetime'],
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
			$result = $this->validate($data, 'allot');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			$data['code'] = 'SCRK'.date('YmdHis',time());	
			$data['dh_time'] = strtotime($data['dh_time']);	
			if($model = AllotModel::create($data)){
				flow_detail($data['name'],'stock_allot','stock_allot','stock/allot/task_list',$model['id']);
				//记入行为
				foreach($data['mid'] as $k => $v){
            		$info = array();
            		$info = [
            				'pid'=>$model['id'],
            				'itemsid'=>$v,
            				'tbsl'=>$data['tbsl'][$k],
            				'ck'=>$data['ck'][$k],           			
            				'bz'=>$data['bz'][$k],
            		];  
            		AllotdetailModel::create($info);         		      	
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
          '新增发货' =>[
            ['hidden','zrid'],
            ['hidden','zdid',UID],           
          	['hidden', 'helpid'],  
			['text:4','name','调拨主题'],
			['text:4','zrname','调拨负责人'],
			['select:4','yhid','所属部门','', OrganizationModel::getMenuTree2()],
			['date:4','dh_time','要求到货日期'],
			['select:4','why','调拨原因','',[0 => '平衡需求', 1=>'货物短缺']],
			['select:4','dhid','调货部门','', OrganizationModel::getMenuTree2()],
			['static:4','zdname','制单人','',get_nickname(UID)],
			['files','file','附件'],			
			['textarea','note','摘要'],	
          ],
          '新增物品' =>[
            ['hidden', 'materials_list'],
			['hidden', 'controller',request()->controller()],
          ]
        ]
      )		
		->setExtraHtml(outhtml2())
		->setExtraJs($html.outjs2())
		->js('stock')
		->fetch();
	}
	public function choose_materials($materials = '',$pid = null)
    {    	
	$map['status'] = 1;
	$map['id'] = ['not in',$materials];
	if($pid!==null){
		$map['type'] = $pid;				
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
		                    <td>'.$v['code'].'</td>
		                    <td>'.$v['name'].'</td>		                   
		                    <td>'.$v['version'].'</td>
							<td>'.$v['unit'].'</td>
		                    <td>'.$v['price'].'</td>
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
    	// 排序
    	$order['create_time'] = 'desc';
    	// 数据列表
    	$data_list = MaterialModel::getList($map,$order);    
    	$btn_pick = [
    			'title' => '选择',
    			'icon'  => 'fa fa-plus-circle',
    			'class' => 'btn btn-xs btn-success',
    			'id' => 'pick'
    	];   		
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
                ['id', '序号'], 
                ['code', '编号'], 
            	['name', '物品名称'],
				['version', '规格型号',],
				['unit', '计量单位'], 				            	
            	['price','参考单价(元)'],
            	['status', '启用状态', 'status'],
            ])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($html)
    	->js('stock')
    	->addTopButton('pick', $btn_pick)
    	->assign('empty_tips', '暂无数据')
    	->fetch('admin@choose/choose'); // 渲染页面
    }
    public function delete($record = [])
    {
   		$ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    	// 删除节点
    	if (AllotModel::destroy($ids)) {
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
		$info = AllotModel::getOne($id);
		$info['materials_list'] = implode(AllotModel::getMaterials($id),',');
		$info['helpname'] = Task_detailModel::get_helpname($info['helpid']);
		$info->dh_time = date('Y-m-d',$info['dh_time']);
		$info['controller'] = request()->controller();
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addGroup([
		'库存调拨'=>[
			['hidden','id'],            
			['static:4','name','调拨主题'],
			['static:4','zrid','调拨负责人',],	
			['static:4','yhid','所属部门',],	
			['static:4','dh_time', '要求到货日期'],
			['select:4','why','调拨原因','',[0 => '平衡需求', 1=>'货物短缺']],		
			['static:4','dhid', '调货部门'],
			['static:4','zdid', '制单人'],	
			['archives','file','附件'],
			['static:4', 'helpname', '可查看该入库人员'],		
			['static','note','摘要'],											
		],
          '库存调拨明细' =>[
            ['hidden', 'materials_list'],
            ['hidden', 'controller'],
          ]			
		])
		->setExtraJs(outjs2())
		->setFormData($info)
		->js('stock')
		->fetch();
    }
	
	//明细
     public function tech($pid = '',$materials_list = '')
    {
    	if($materials_list == '' || $materials_list == 'undefined') {
    		$html = '';	
    	}else{
			$ck = HouseModel::column('id,name');
    		$map = ['stock_allot_detail.pid'=>$pid,'stock_material.id'=>['in',($materials_list)]];
    		$data = AllotModel::getDetail($map);
    		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>调拨仓库</td><td>调拨数量</td><td>备注</td></tr>';
    		foreach ($data as $k => $v){ 
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['itemsid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$ck[$v['ck']].'</td><td>'.$v['tbsl'].'</td><td>'.$v['bz'].'</td></tr>';
    		}   		
    		$html .= '</tbody></table></div>';
    
    	}
    	return $html;
    }
}
