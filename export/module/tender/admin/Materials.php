<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\tender\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\tender\model\Materials as MaterialsModel;
use app\stock\model\Material as MaterialModel;
use app\tender\model\Materialsdetail as MaterialsdetailModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\tender\model\Obj as ObjModel;

/**
 * 材料管理控制器
 * @package app\produce\admin
 */
class Materials extends Admin
{
    /**
     * 工作中心列表
     * @author HJP <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('tender_materials.create_time desc');
        // 数据列表
        $data_list = MaterialsModel::getList($map,$order);
        $task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
						->addOrder(['code','create_time']) // 添加排序
            ->setSearch(['tender_materials.name' => '计划名称','tender_obj.name' => '项目名称'], '', '', true) // 设置搜索参数
            ->addColumns([ // 批量添加数据列
                ['code', '编号'], 
            	['name', '计划名称'],
            	['obj_id', '项目名称'],
            	['authorizedname', '编制人'],
            	['create_time', '日期','datetime'],
				['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
            	['right_button', '操作', 'btn']
            ])
            ->addTopButtons('delete') // 批量添加顶部按钮
            ->addRightButtons('delete')   
 						->addRightButton('task_list',$task_list) // 查看右侧按钮                 
            ->setRowList($data_list) // 设置表格数据            
            ->fetch(); // 渲染模板
    }
    

    public function add(){
		$name = session('user_auth')['role_name'];    	
		if($this->request->isPost()){
			$data = $this->request->post();
			// 验证
			$result = $this->validate($data, 'materials');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			$data['code'] = 'XMGL'.date('YmdHis',time());
			if($model = MaterialsModel::create($data)){
				//记入行为
                action_log('tender_materials_add', 'tender_materials', $model['id'], UID);
flow_detail($data['name'],'tender_materials','tender_materials','tender/materials/task_list',$model['id']);				
				foreach($data['mid'] as $k => $v){
            		$info = array();
            		$info = [
            				'pid'=>$model['id'],
            				'itemsid'=>$v,
            				'xysl'=>$data['xysl'][$k],
            				'ckjg'=>$data['ckjg'][$k],
            				'xj'=>$data['xj'][$k],
            				'bz'=>$data['bz'][$k],
            		];  
            		MaterialsdetailModel::create($info);         		      	
            	}
            	      
				$this->success('新增成功！',url('index'));
			}else{
				$this->error('新增失败！');
			}
		}
		return Zbuilder::make('form')
		 ->addGroup(
        [
          '材料需用计划' =>[
          	['hidden','authorized',UID],
			['text:4','name','计划主题'],			
			['select:4','obj_id','项目名称','',ObjModel::get_nameid()],
			['static:4','authorizedname','编制人','',$name],	
			['files','file','附件'],
			['textarea','note','备注'],	
          ],
          '需用明细' =>[
            ['hidden', 'materials_list'],
          ]
        ]
      )		
		->js('test')
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
		$info['materials_list'] = implode(MaterialsModel::getMaterials($id),',');
		return ZBuilder::make('form')
		->addGroup([
		'编辑发货'=>[
			['hidden','id'],
			['hidden','authorized'],
			['static:4','name','计划名称'],
			['static:4','obj_id','项目名称'],
			['static:4','authorizedname','编制人'],
			['archives','file','附件'],
			['static:4','note','备注'],										
		],
          '新增物品' =>[
            ['hidden', 'materials_list'],
            ['hidden', 'old_plan_list'],
          ]			
		])
		->setExtraJs(outjs2())
		->setFormData($info)
		->js('test')
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
    	->js('test')
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
    		$map = ['tender_materials_detail.pid'=>$pid,'stock_material.id'=>['in',($materials_list)]];
    		$data = MaterialsModel::getDetail($map);
    		//dump($data);die;
    		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>需用数量</td><td>参考价格</td><td>小计</td><td>备注</td></tr>';
    		foreach ($data as $k => $v){ 
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['itemsid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['xysl'].'</td><td>'.$v['ckjg'].'</td><td>'.$v['xj'].'</td><td>'.$v['bz'].'</td></tr>';
    		}   		
    		$html .= '</tbody></table></div>';
    
    	}
    	return $html;
    }
	}
	
	
	
	
	
	
	
	
	
	
	