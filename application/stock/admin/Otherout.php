<?php
namespace app\stock\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\stock\model\House as HouseModel;
use app\stock\model\Otherout as OtheroutModel;
use app\stock\model\Otheroutdetail as OtheroutdetailModel;
use app\user\model\Organization as OrganizationModel;
use app\tender\model\Clear as ClearModel;
use app\tender\model\ClearDetail as ClearDetailModel;
use app\stock\model\Material as MaterialModel;
use app\task\model\Task_detail as Task_detailModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\stock\model\Stock as StockModel;
use app\supplier\model\Client as ClientModel;
use app\stock\model\Account as AccountModel;
use app\tender\model\Obj as ObjModel;
use think\Db;
/**
 * 其他入库控制器
 */
class Otherout extends Admin
{
	/*
	 * @author HJP<957547207>
	 */
	public function index()
	{
		// 查询
		$map = $this->getMap();
		// 排序
		$order = $this->getOrder('stock_otherout.create_time desc');
		// 数据列表
		$data_list = OtheroutModel::getList($map,$order);
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
		// 使用ZBuilder快速创建数据表格
		return ZBuilder::make('table')
			->addTimeFilter('stock_otherout.create_time') // 添加时间段筛选
			->setSearch(['stock_otherout.name' => '出库主题'], '', '', true)
			->addOrder(['code','ck_time']) // 添加排序
			->addFilter(['ckbm'=>'admin_organization.title']) // 添加筛选
			->addFilter('why',[-2=>'购入',-1=>'退货',0=>'退料',1=>'完工',2=>'售出']) // 添加筛选
			->addColumns([
				['code', '出库单号'],
				['intime','出库日期','date'],
				['sid','项目'],
				['intype','出库类别'],
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
			$account = db::name('stock_stock')->where('materialid','in',$data['materials_list'])->column('materialid,number');					
			$stock = db::name('stock_material')->where('id','in',$data['materials_list'])->column('id,name,version');			
			if(!empty($data['cksl'])){				
				foreach ($data['cksl'] as $key => $value) {					
					if(empty($account[$data['mid'][$key]]))$this->error($stock[$data['mid'][$key]]['name'].'仓库不足');						
					if(!$value){
						$this->error($stock[$data['mid'][$key]]['name'].'数量不能为空');
					}else{
						if($value>$account[$data['mid'][$key]]){
							$this->error($stock[$data['mid'][$key]]['name'].' 规格 '.$stock[$data['mid'][$key]]['version'].' 库存不足');
						}else{
							//$this->error('库存没有'.$stock[$data['mid'][$key]]['name']);
						}
					}
				}	
			}
			// 验证
			$result = $this->validate($data, 'Otherout');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);	
			//$data['code'] = 'SCRK'.date('YmdHis',time());	
			$data['intime'] = strtotime($data['intime']);
			if($model = OtheroutModel::create($data)){
				//flow_detail($data['name'],'stock_otherout','stock_otherout','stock/otherout/task_list',$model['id']);
				//记入行为
				foreach($data['mid'] as $k => $v){
            		$info = array();
            		$info = [
            				'pid'=>$model['id'],
            				'itemsid'=>$v,
            				'cksl'=>$data['cksl'][$k],
							'dj'=>$data['dj'][$k],
							'je'=>$data['je'][$k],
            		];			
					OtheroutdetailModel::create($info);  					
					$num = db::name('stock_stock')->where(['materialid'=>$v,'ckid'=>$model['house_id']])->value('number');
					$price = db::name('stock_stock')->where(['materialid'=>$v,'ckid'=>$model['house_id']])->value('price');
					$new_num = $num-$info['cksl'];
					$total = $price*$new_num;
					db::name('stock_stock')->where(['materialid'=>$v,'ckid'=>$model['house_id']])->update(['number'=>$new_num,'total'=>$total]);					
            	}           	      
				$this->success('新增成功！',url('index'));
			}else{
				$this->error('新增失败！');
			}
		}
		return Zbuilder::make('form')
		 ->addGroup(
        [
          '其他出库' =>[
            //['hidden','zrid'],
            //['hidden','ckid',UID],
          	//['hidden', 'fileid'],            
            //['hidden','zdid',UID],
            //['hidden', 'helpid'],
			//['text:4','name','出库主题'],
			//['text:4', 'goodaddrss', '发货地址'],
			//['text:4', 'addrss', '收货地址'],
			//['text:4', 'zrname', '经办人'],
			//['text:4', 'ckname', '出库人','',get_nickname(UID)],
			//['select:4', 'ckbm', '出库部门','', OrganizationModel::getMenuTree2()],
			//['select:4','why','出库原因','',[-2=>'购入',-1=>'退货',0=>'退料',1=>'完工',2=>'售出']],	
			//['date:4', 'ck_time', '出库时间'],		
			//['static:4','zdname','制单人','',get_nickname(UID)],	
			//['files','file','附件'],
			//['textarea:8', 'helpname', '可查看该入库人员'],		
			['text:4','code','出库单号'],
			['date:4','intime','出库日期'],
			['select:4','sid','项目','',ObjModel::getaname()],
			['text:4','intype','出库类别'],
			['select:4','house_id','仓库','',HouseModel::getTree()],
			['text:4','note','备注'],	
          ],
          '新增物品' =>[
            ['hidden', 'materials_list'],
			['hidden', 'controller',request()->controller()],
          ]
        ]
      )		
		->setExtraHtml(outhtml2())
		->setExtraJs(outjs2())
		->js('stock')
		->fetch();
	}
	 public function delete($record = [])
    {
   		$ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    	// 删除节点
    	if (OtheroutModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		$details = '生产任务ID('.$ids.'),操作人ID('.UID.')';
    		//action_log('produce_plan_delete', 'produce_plan', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }
	public function creatMaterial(){
	    $data = $this->request->Get();
		if(MaterialModel::create($data)){
				$msg = '新增成功';
		}else{
				$mag = '新增失败';
		}
		return $msg;
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
	//查看
    public function task_list($id = null){
    	if($id == null) $this->error('参数错误');		
		$info = OtheroutModel::getOne($id);
		$info['materials_list'] = implode(OtheroutModel::getMaterials($id),',');
		//$info['helpname'] = Task_detailModel::get_helpname($info['helpid']);
		$info['controller'] = request()->controller();
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addGroup([
		'编辑发货'=>[
			['hidden','id'],            
			['static:4','code','出库单号'],
			['static:4','intime','出库日期'],
			['static:4','sid','项目'],
			['static:4','intype','出库类别'],
			['static:4','house_id','仓库'],
			['static:4','note','备注'],								
		],
          '新增物品' =>[
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
    		$map = ['stock_otherout_detail.pid'=>$pid,'stock_material.id'=>['in',($materials_list)]];
    		$data = OtheroutModel::getDetail($map);
    		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>数量</td><td>单价</td><td>金额</td></tr>';
    		foreach ($data as $k => $v){ 
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['itemsid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['cksl'].'</td><td>￥'.number_format($v['dj'],2).'</td><td>￥'.number_format($v['je'],2).'</td></tr>';
    		}   		
    		$html .= '</tbody></table></div>';    
    	}
    	return $html;
    }  

    public function inform(){
    	// 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('tender_clear.create_time desc');
        // 数据列表
        $data_list = ClearModel::getList($map,$order);
        $task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_lists',['id'=>'__id__'])
		];
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
						->addOrder(['code','create_time']) // 添加排序
						->hideCheckbox()
						->addFilter(['obj_id'=>'tender_obj.name'])
						->addTimeFilter('tender_clear.create_time')
            ->setSearch(['admin_user.nickname' => '制单人'], '', '', true) // 设置搜索参数
            ->addColumns([ // 批量添加数据列
            	['__INDEX__',''],
              ['number', '编号'], 
              ['create_time', '日期','date'],
            	['name', '结算名称'],
            	['obj_id', '项目名称'],
            	['authorized', '编制人'],
            	
						
            	['right_button', '操作', 'btn']
            ])
            //->addTopButtons('delete') // 批量添加顶部按钮
            //->addRightButtons('delete')   
			->addRightButton('task_lists',$task_list,true) // 查看右侧按钮               
            ->setRowList($data_list) // 设置表格数据
            ->js('clear')            
            ->fetch(); // 渲染模板




    }

    public function task_lists($id = null){
    	if($id == null) $this->error('参数错误');		
		$info = ClearModel::getOne($id);
		$info['materials_list'] = implode(ClearModel::getMaterials($id),',');
		$info['date']=date('Y-m-d',$info['date']);
		return ZBuilder::make('form')
		->addGroup([
		'材料计划'=>[
			['hidden','id'],
			['static:4','name','材料结算单主题'],
			['static:3','date','日期'],			
			['static:4','obj_id','项目名称'],
			['static:4','authorized','编制人'],	
			['static','ps','备注'],	
			['archives','file','附件'],									
		],
          '材料计划明细' =>[
            ['hidden', 'materials_list'],
            ['hidden', 'old_plan_list'],
          ]			
		])
		->setExtraJs(outjs2())
		->setFormData($info)
		->HideBtn('submit')
		->js('clear')
		->fetch();
    }


    public function techs($pid = '',$materials_list = '')
    {
    	if($materials_list == '' || $materials_list == 'undefined') {
    		$html = '';	
    	}else{
    		$map = ['tender_clear_detail.pid'=>$pid,'stock_material.id'=>['in',($materials_list)]];
    		$data = ClearModel::getDetail($map);
    		//dump($data);die;
    		$html = '<span class="btn btn-success" onclick="dddd();" style="margin:10px">打印明细</span><!--startprint--><div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>需用数量</td></tr>';
    		foreach ($data as $k => $v){ 
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['itemsid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['xysl'].'</td></tr>';
    		}   		
    		$html .= '</tbody></table></div><!--endprint-->';
    
    	}
    	return $html;
    }



}
