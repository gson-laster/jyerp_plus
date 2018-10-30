<?php
namespace app\stock\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\stock\model\Check as CheckModel;
use app\stock\model\Checkdetail as CheckdetailModel;
use app\user\model\Organization as OrganizationModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\stock\model\House as HouseModel;
/**
 * 其他入库控制器
 */
class Check extends Admin
{
	/*
	 * 采购入库列表
	 * @author HJP<957547207>
	 */
	public function index()
	{
		// 查询
		$map = $this->getMap();
		// 排序
		$order = $this->getOrder('stock_check.create_time desc');
		// 数据列表
		$data_list = CheckModel::getList($map,$order);
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
		// 使用ZBuilder快速创建数据表格
		return ZBuilder::make('table')
			->setSearch(['stock_check.name' => '盘点主题'], '', '', true)
			->addOrder(['code','start_time','start_time','tz_time']) // 添加排序
			->addFilter(['pdbm'=>'admin_organization.title']) // 添加筛选
			->addFilter('pdtype',[-1=>'原材料',0=>'成品',1=>'半成品']) // 添加筛选
			->addColumns([
				['code', '编号'],
				['name', '盘点主题'],
				['zrid', '经办人'],
				['pdbm', '盘点部门'],
				['pdck', '盘点仓库'],	
				['pdtype', '盘点类型',[-1=>'原材料',0=>'成品',1=>'半成品']],
				['start_time','开始时间','date'],
				['end_time','结束时间','date'],
				['tzid','库存调整人'],
				['tz_time','库存调整日期','date'],
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
			$result = $this->validate($data, 'check');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			$data['code'] = 'SCRK'.date('YmdHis',time());	
			$data['start_time'] = strtotime($data['start_time']);
$data['end_time'] = strtotime($data['end_time']);
$data['tz_time'] = strtotime($data['tz_time']);			
			if($model = CheckModel::create($data)){
				flow_detail($data['name'],'stock_check','stock_check','stock/check/task_list',$model['id']);
				//记入行为
				foreach($data['mid'] as $k => $v){
            		$info = array();
            		$info = [
            				'pid'=>$model['id'],
            				'itemsid'=>$v,
            				'cbjg'=>$data['cbjg'][$k],
            				'xcsl'=>$data['xcsl'][$k],
            				'spsl'=>$data['spsl'][$k],
            				'cyl'=>$data['cyl'][$k],
            				'bz'=>$data['bz'][$k],
            		];  
            		CheckdetailModel::create($info);         		      	
            	}
            	      
				$this->success('新增成功！',url('index'));
			}else{
				$this->error('新增失败！');
			}
		}
		$js = <<<EOF
            <script type="text/javascript">
			
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
          '期末盘点' =>[
            ['hidden','zrid'],
            ['hidden','tzid',UID],
            ['hidden','zdid',UID],
			['text:4','name','盘点主题'],
			['text:4','zrname','经办人'],
			['select:4','pdbm','盘点部门','', OrganizationModel::getMenuTree2()],
			['select:4','pdck','盘点仓库','',HouseModel::getName()],
			['select:4','pdtype','盘点类型','',[-1=>'原材料',0=>'成品',1=>'半成品']],
			['date:4','start_time','开始时间','',date('Y-m-d')],
			['date:4','end_time','结束时间'],
			['static:4','tzname','库存调整人','',get_nickname(UID)],
			['date:4','tz_time','库存调整日期','',date('Y-m-d')],
			['static:4','zdname','制单人','',get_nickname(UID)],
			['files','file','附件'],
			['textarea','note','摘要'],	
          ],
          '盘点明细' =>[
            ['hidden', 'materials_list'],
          ]
        ]
      )		
		->setExtraHtml(outhtml2())
		->setExtraJs($js.outjs2())
		->js('test8')
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
            	['version', '规格型号',],
            	['unit', '单位'],
            	['status', '启用状态', 'status'],
            ])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js)
    	->js('test8')
    	->addTopButton('pick', $btn_pick)
    	->assign('empty_tips', '暂无数据')
    	->fetch('admin@choose/choose'); // 渲染页面
    }
	
	//查看
    public function task_list($id = null){
    	if($id == null) $this->error('参数错误');		
		$info = CheckModel::getOne($id);
		$info['materials_list'] = implode(CheckModel::getMaterials($id),',');
		$info->start_time = date('Y-m-d',$info['start_time']);
		$info->end_time = date('Y-m-d',$info['end_time']);
		$info->tz_time = date('Y-m-d',$info['tz_time']);
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addGroup([
		'期末盘点'=>[
			['hidden','id'],            
			['static:4','name','盘点主题'],
			['static:4','zrid','经办人',],	
			['static:4','pdbm','盘点部门',],	
			['static:4','pdck','盘点仓库'],
			['select:4','pdtype','盘点类型','',[-1=>'原材料',0=>'成品',1=>'半成品']],
			['static:4','start_time','开始时间'],		
			['static:4','end_time','结束时间'],
			['static:4','tzid','库存调整人'],
			['static:4','zdid', '制单人'],	
			['static:4','tz_time','库存调整日期'],	
			['archives','file','附件'],		
			['static','note','摘要'],								
		],
          '期末盘点明细' =>[
            ['hidden', 'materials_list'],
            ['hidden', 'old_plan_list'],
          ]			
		])
		->setExtraJs(outjs2())
		->setFormData($info)
		->js('test8')
		->fetch();
    }
	
	//明细
     public function tech($pid = '',$materials_list = '')
    {
    	if($materials_list == '' || $materials_list == 'undefined') {
    		$html = '';	
    	}else{
    		$map = ['stock_check_detail.pid'=>$pid,'stock_material.id'=>['in',($materials_list)]];
    		$data = CheckModel::getDetail($map);
    		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>成本价格</td><td>现存数量</td><td>实盘数量</td><td>差异量</td><td>备注</td></tr>';
    		foreach ($data as $k => $v){ 
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['itemsid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['cbjg'].'</td><td>'.$v['xcsl'].'</td><td>'.$v['spsl'].'</td><td>'.$v['cyl'].'</td><td>'.$v['bz'].'</td></tr>';
    		}   		
    		$html .= '</tbody></table></div>';
    
    	}
    	return $html;
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
    	if (CheckModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		$details = '生产任务ID('.$ids.'),操作人ID('.UID.')';
    		//action_log('produce_plan_delete', 'produce_plan', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }
}
