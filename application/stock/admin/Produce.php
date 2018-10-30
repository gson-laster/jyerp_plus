<?php
namespace app\stock\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\stock\model\House as HouseModel;
use app\stock\model\Purchase as PurchaseModel;
use app\stock\model\Produce as ProduceModel;
use app\stock\model\Producedetail as ProducedetailModel;
use app\user\model\Organization as OrganizationModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\produce\model\Production as ProductionModel;
use app\task\model\Task_detail as Task_detailModel;
use app\stock\model\Account as AccountModel;
use app\tender\model\Obj as ObjModel;
use think\Db;
/**
 * 生产完工入库控制器
 */
class Produce extends Admin
{
	/*
	 * 生产入库列表
	 * @author HJP<957547207>
	 */
	public function index()
	{
		// 查询
		$map = $this->getMap();
		// 排序
		$order = $this->getOrder('stock_produce.create_time desc');
		// 数据列表
		$data_list = ProduceModel::getList($map,$order);
		 $task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
		// 使用ZBuilder快速创建数据表格
		return ZBuilder::make('table')
			->setSearch(['stock_produce.name' => '入库主题'], '', '', true)
			->addOrder(['code','create_time']) // 添加排序
			//->addFilter(['org_id'=>'admin_organization.title','putinid'=>'admin_organization.title']) // 添加筛选
			->addColumns([
				['code', '入库单号'],
				['intime','入库日期','date'],
				['sid','项目'],
				['intype','入库类别'],
				['house_id','仓库'],
				['note','备注'],
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
			$result = $this->validate($data, 'produce');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			//$data['code'] = 'SCRK'.date('YmdHis',time());
			$data['intime'] = strtotime($data['intime']);
			if($model = ProduceModel::create($data)){
				//flow_detail($data['name'],'stock_produce','stock_produce','stock/produce/task_list',$model['id']);
				//记入行为
				foreach($data['mid'] as $k => $v){
            		$info = array();
					$kcinfo = array();
            		$info = [
            				'pid'=>$model['id'],
            				'itemsid'=>$v,            				
							'type'=>$data['type'][$k],
            				'rksl'=>$data['rksl'][$k],
            		];  
					$kcinfo = [
            				'materialid'=>$v,
            				'number'=>$data['rksl'][$k],
							'material_type'=>$data['type'][$k],
							'ckid'=>$model['house_id'],
            		]; 
            		ProducedetailModel::create($info);
				if(empty(db::name('stock_stock')->where(['materialid'=>$v,'ckid'=>$model['house_id']])->find()))
					{
						db::name('stock_stock')->insert($kcinfo);
					}else{												
						$num = db::name('stock_stock')->where(['materialid'=>$v,'ckid'=>$model['house_id']])->field('number,total')->find();						
						$new_num = $num['number']+$kcinfo['number'];
						$total = $kcinfo['total']+$num['total'];
						$price = $total/$new_num;
						db::name('stock_stock')->where(['materialid'=>$v,'ckid'=>$model['house_id']])->update(['number'=>$new_num,'price'=>number_format($price,2),'total'=>$total]);
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
          '生产入库' =>[
            //['hidden','zrid'],           
            //['hidden', 'helpid'],
            ////['hidden', 'warehouses',UID],           
          	//['hidden', 'zdid',UID],           
          	//['hidden', 'deliverer',UID],
			//['text:4','name','入库主题'],			
			//['select:4','order_id','生产任务单','',ProductionModel::getName()],
			//['text:4','header','生产负责人','','','','disabled'],
			//['text:4','org_id','生产部门','','','','disabled'],		
			//['static:4', 'delivname', '交货人','',get_nickname(UID)],			
			//['static:4', 'warehname', '入库人','',get_nickname(UID)],
			//['text:4', 'zrname', '验收人'],
			//['select:4', 'putinid', '入库部门','', OrganizationModel::getMenuTree2()],
			//['static:4','zdname','制单人','',get_nickname(UID)],	
			//['files','file','附件'],
			//['textarea:8', 'helpname', '可查看该入库人员'],		
			['text:4','code','入库单号'],
			['date:4','intime','入库日期'],
			['select:4','sid','项目','',ObjModel::getaname()],
			['text:4','intype','入库类别'],
			['select:4','house_id','仓库','',HouseModel::getTree()],
			['text:4','note','备注'],	
          ],
          '新增物品' =>[
            ['hidden', 'materials_list'],
			['hidden', 'controller',request()->controller()],
          ]
        ]
      )		
		->js('stock')
		->setExtraHtml(outhtml2())
		->setExtraJs($html.outjs2())	
		->fetch();
	}
	public function get_Mateplan($mateplan = ''){		
		$materialsid = ProductionModel::where('id',$mateplan)->value('id');
		$map = ['ppid'=>$materialsid];
		$data = ProductionModel::getDetail($map);			
		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>仓库</td><td>入库数量</td><td>备注</td></tr>';
    		foreach ($data as $k => $v){ 
    			$html.='<tr><input type="hidden" name="type[]" value="'.$v['type'].'"><input type="hidden" name="mid[]" value="'.$v['smid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td><input type="hidden" name="ck[]" value="'.$v['house_id'].'">'.$v['ckname'].'</td><td><input type="number" name="rksl[]" value="'.$v['produce_num'].'"></td><td><input type="text" name="bz[]"></td></tr>';
    		}   		
    		$html .= '</tbody></table></div>';
		return $html;				
	}
	public function get_Detail($order_id = ''){
			$data = ProduceModel::get_Detail($order_id);
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
    	if (ProduceModel::destroy($ids)) {
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
		$info = ProduceModel::getOne($id);
		$info['materials_list'] = implode(ProduceModel::getMaterials($id),',');
		$info['helpname'] = Task_detailModel::get_helpname($info['helpid']);
		$info['controller'] = request()->controller();
		$info->intime = date('Y-m-d',$info['intime']);
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addGroup([
		'完工入库'=>[
			['hidden','id'],    			
			['static:4','code','入库单号'],
			['static:4','intime','入库日期'],
			['static:4','sid','供应商'],
			['static:4','intype','入库类别'],
			['static:4','house_id','仓库'],
			['static:4','note','备注'],									
		],
          '完工入库明细' =>[
            ['hidden', 'materials_list'],
			['hidden', 'controller'],
          ]			
		])
		->setExtraJs(outjs2())
		->setFormData($info)
		->js('stock')
		->fetch();
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
				$status = $v['status']?'<span class="label label-success">启用</span>':'<span class="label label-warning">禁用</span>';
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
							<td style="display:none;">'.$v['type'].'</td>
		                    <td>'.$status.'</td>		                 		                                                                                                                  		                                                         
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
		$add_pick = [
    			'title' => '新增物资',
    			'icon'  => 'fa fa-plus',
    			'class' => 'btn btn-xs btn-primary',
    			'id' => 'add_pick'
    	];
		$MaterialType = MaterialTypeModel::where('status',1)->column('id,title');
		$type = ' <select class="js-select2 form-control select2-hidden-accessible" id="type" name="type" data-allow-clear="true" data-placeholder="请选择一项" tabindex="-1" aria-hidden="true">';
        foreach ($MaterialType as $key => $value) {
            $type.='<option value="'.$key.'">'.$value.'</option>';            
        }
        $type.='</select>';
		$ck = HouseModel::where('status',1)->column('id,name');
        $house = '<select class="js-select2 form-control select2-hidden-accessible" id="house_id" name="house_id" data-allow-clear="true" data-placeholder="请选择一项" tabindex="-1" aria-hidden="true">';
        foreach ($ck as $key => $value) {
            $house.='<option value="'.$key.'">'.$value.'</option>';            
        }
        $house.='</select>';
$html = <<<EOF
            <div class="add_pick" style="display: none;height: 100%;overflow: auto;">
				<div class="form-group col-md-12 col-xs-12 " id="form_group_code">
					<label class="col-xs-12" for="code">编号</label>
				<div class="col-sm-12">
					<input class="form-control" type="text" id="code" name="code" value="" placeholder="请输入物品名称">
				</div>
				</div>
	            <div class="form-group col-md-12 col-xs-12 " id="form_group_name">
					<label class="col-xs-12" for="name">物品名称</label>
				<div class="col-sm-12">
					<input class="form-control" type="text" id="name" name="name" value="" placeholder="请输入物品名称">
				</div>
				</div>
				<div class="form-group col-md-12 col-xs-12 " id="form_group_type">
					<label class="col-xs-12" for="type">物品类型</label>
					<div class="col-sm-12">
						{$type}
					</div>
				</div>
				<div class="form-group col-md-12 col-xs-12 " id="form_group_version">
					<label class="col-xs-12" for="version">规格型号</label>
					<div class="col-sm-12">       
						<input class="form-control" type="text" id="version" name="version" value="" placeholder="请输入规格型号">
					</div>
				</div>
				<div class="form-group col-md-12 col-xs-12 " id="form_group_unit">
					<label class="col-xs-12" for="unit">计量单位</label>
					<div class="col-sm-12">
						<input class="form-control" type="text" id="unit" name="unit" value="" placeholder="请输入计量单位">
					</div>
				</div>
				<div class="form-group col-md-12 col-xs-12 " id="form_group_funit">
					<label class="col-xs-12" for="funit">辅计量单位</label>
					<div class="col-sm-12">
						<input class="form-control" type="text" id="funit" name="funit" value="" placeholder="请输入辅计量单位">
					</div>
				</div>
				<div class="form-group col-md-12 col-xs-12 " id="form_group_weight">
					<label class="col-xs-12" for="weight">重量</label>
					<div class="col-sm-12">
						<input class="form-control" type="number" id="weight" name="weight" value="0" placeholder="请输入重量">
					</div>
				</div>
				<div class="form-group col-md-12 col-xs-12 " id="form_group_size">
					<label class="col-xs-12" for="size">尺寸</label>
					<div class="col-sm-12">
						<input class="form-control" type="text" id="size" name="size" value="" placeholder="请输入尺寸">
					</div>
				</div>
				<div class="form-group col-md-12 col-xs-12 " id="form_group_house_id">
					<label class="col-xs-12" for="house_id">主放仓库</label>
					<div class="col-sm-12">
						{$house}
					</div>
				</div>                                                                                                                                         
			</div>                                                                                                                                                                                                                                                                                              
EOF;
		
    	// 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['name' => '物品名称']) // 设置搜索框
            ->addOrder('id,create_time') // 添加排序
            ->setPageTitle('选择物品')
            ->addColumns([ // 批量添加数据列
                ['id', '序号'], 
                ['code', '编号'], 
            	['name', '材料名称'],           	
            	['version', '规格型号',],
            	['unit', '计量单位'],
				['type', '类型','','','','hidden'],
            	['status', '启用状态', 'status'],
            ])
    	->setRowList($data_list) // 设置表格数据
    	->js('stock')
		->setExtraHtml($html, 'toolbar_bottom')
    	->addTopButton('pick', $btn_pick)
		->addTopButton('add_pick', $add_pick)
    	->assign('empty_tips', '暂无数据')
    	->fetch('admin@choose/choose'); // 渲染页面
    }
	//明细
     public function tech($pid = '',$materials_list = '')
    {
    	if($materials_list == '' || $materials_list == 'undefined') {
    		$html = '';	
    	}else{
    		$map = ['stock_produce_detail.pid'=>$pid,'stock_material.id'=>['in',($materials_list)]];
    		$data = ProduceModel::getDetail($map);
    		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>仓库</td><td>入库数量</td><td>金额(元)</td><td>备注</td></tr>';
    		foreach ($data as $k => $v){ 
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['itemsid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['ck'].'</td><td>'.$v['rksl'].'</td><td>'.$v['je'].'</td><td>'.$v['bz'].'</td></tr>';
    		}   		
    		$html .= '</tbody></table></div>';   
    	}
    	return $html;
    }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
