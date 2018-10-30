<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5 0005
 * Time: 14:57
 */

namespace app\tender\admin;


use app\admin\controller\Admin;
use app\tender\model\Clear as ClearModel;
use app\tender\model\ClearDetail as ClearDetailModel;
use app\common\builder\ZBuilder;
use app\tender\model\Materials as MaterialsModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\tender\model\Obj as ObjModel;
class Clear extends Admin
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
        $order = $this->getOrder('tender_clear.create_time desc');
        // 数据列表
        $data_list = ClearModel::getList($map,$order);
        $task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
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
						->addRightButton('task_list',$task_list,true) // 查看右侧按钮               
            ->setRowList($data_list) // 设置表格数据            
            ->fetch(); // 渲染模板
    }
    

    public function add(){
		$name = session('user_auth')['role_name'];    	
		if($this->request->isPost()){
			$data = $this->request->post();
			$data['authorized'] = UID;
			$data['date'] = strtotime($data['date']);
			// 验证
			$result = $this->validate($data, 'Clear');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			$data['number'] = 'CLQD'.date('YmdHis',time());
			
			
			//DUMP($data);die;
			if($model = ClearModel::create($data)){
				//记入行为
               // action_log('tender_materials_add', 'tender_materials', $model['id'], UID);
				//flow_detail($data['name'],'tender_materials','tender_materials','tender/materials/task_list',$model['id']);				
				foreach($data['mid'] as $k => $v){
            		$info = array();
            		$info = [
            				'pid'=>$model['id'],
            				'itemsid'=>$v,
            				'xysl'=>$data['xysl'][$k],
            		];  
            		ClearDetailModel::create($info);         		      	
            	}
            	      
				$this->success('新增成功！',url('index'));
			}else{
				$this->error('新增失败！');
			}
		}
		return Zbuilder::make('form')
		 ->addGroup(
        [
          '材料结算单' =>[
			['text:4','name','材料结算单主题'],
			['date:3','date','日期','',Date('Y-m-d')],			
			['select:4','obj_id','项目名称','',ObjModel::get_nameid()],
			['static:4','authorized','编制人','',get_nickname(UID)],	
			['files','file','附件'],
			['textarea','ps','备注'],	
          ],
          '需用明细' =>[
            ['hidden', 'materials_list'],
          ]
        ]
      )		
		->js('clear')
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
		                    <td>'.$v['code'].'</td>
		                    <td>'.$v['name'].'</td>
		                    <td>'.$v['version'].'</td>
		                    <td>'.$v['unit'].'</td>
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
              ['id', '序号'], 
              ['code', '编号'], 
            	['name', '物品名称'],           	
            	['version', '规格型号',],
            	['unit', '计量单位'],
            	['status', '启用状态', 'status'],
            ])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js)
    	->js('clear')
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