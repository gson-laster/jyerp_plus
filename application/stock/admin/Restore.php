<?php
namespace app\stock\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\stock\model\Borrow as BorrowModel;
use app\stock\model\House as HouseModel;
use app\stock\model\Restore as RestoreModel;
use app\stock\model\Restoredetail as RestoredetailModel;
use app\user\model\Organization as OrganizationModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\task\model\Task_detail as Task_detailModel;
/**
 * 其他入库控制器
 */
class Restore extends Admin
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
		$order = $this->getOrder('stock_restore.create_time desc');
		// 数据列表
		$data_list = RestoreModel::getList($map,$order);
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
		// 使用ZBuilder快速创建数据表格
		return ZBuilder::make('table')
			->setSearch(['stock_restore.name' => '出库主题'], '', '', true)
			->addOrder(['code','jh_time']) // 添加排序
			->addFilter(['jcbm'=>'admin_organization.title','fhbm'=>'admin_organization.title']) // 添加筛选
			->addColumns([
				['code', '编号'],
				['name', '返还主题'],
				['order_id', '借货单'],
				['jhbm', '借货部门'],
				['jhname', '借货人'],
				['jh_time', '借货日期','date'],
				['jcbm', '被借部门'],	
				['zrid', '返还人'],
				['fhbm', '返还部门'],
				['rkid', '入库人'],
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
			$result = $this->validate($data, 'restore');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			$data['code'] = 'SCRK'.date('YmdHis',time());	
			$data['fh_time'] = strtotime($data['fh_time']);	
			if($model = RestoreModel::create($data)){
				flow_detail($data['name'],'stock_restore','stock_restore','stock/restore/task_list',$model['id']);
				//记入行为
				foreach($data['mid'] as $k => $v){
            		$info = array();
            		$info = [
            				'pid'=>$model['id'],
            				'itemsid'=>$v,
            				'ck'=>$data['ck'][$k],
            				'rksl'=>$data['rksl'][$k],
            				'bz'=>$data['bz'][$k],
            		];  
            		RestoredetailModel::create($info);         		      	
            	}
            	      
				$this->success('新增成功！',url('index'));
			}else{
				$this->error('新增失败！');
			}
		}
		return Zbuilder::make('form')
		 ->addGroup(
        [
          '返还申请' =>[
            ['hidden','zrid'],
            ['hidden','helpid'],
            ['hidden','rkid',UID],
            ['hidden','zdid',UID],
			['text:4','name','返还主题'],
			['select:4','order_id','借货单','',BorrowModel::getName()],
			['text:4', 'jhbm', '借货部门','','','','disabled'],
			['text:4', 'jhname', '借货人','','','','disabled'],
			['text:4', 'jh_time', '借货日期','','','','disabled'],
			['text:4', 'jcbm', '被借部门','','','','disabled'],
			['text:4','zrname','返还人'],
			['select:4','fhbm','返还部门','', OrganizationModel::getMenuTree2()],
			['date:4','fh_time','返还时间'],
			['static:4','rkname','入库人','',get_nickname(UID)],			
			['static:4','zdname','制单人','',get_nickname(UID)],	
			['files','file','附件'],
			['textarea:8', 'helpname', '可查看该入库人员'],		
			['textarea','note','摘要'],	
          ],
          '物品明细' =>[
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
	public function get_Mateplan($mateplan = ''){
		$ck = HouseModel::column('id,name');				
		$map = ['pid'=>$mateplan];
		$data = BorrowModel::getDetail($map);			
		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>仓库</td><td>预计归还数量</td><td>实际归还数量</td><td>备注</td></tr>';
    		foreach ($data as $k => $v){ 
    			$html.='<tr><input type="hidden" name="ck[]" value="'.$v['ck'].'"><input type="hidden" name="mid[]" value="'.$v['itemsid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$ck[$v['ck']].'</td><td>'.$v['fhsl'].'</td><td><input type="number" name="rksl[]"></td><td><input type="text" name="bz[]"></td></tr>';
    		}   		
    		$html .= '</tbody></table></div>';
		return $html;				
	}
	public function get_Detail($order_id = ''){
			$data = RestoreModel::get_Detail($order_id);
			$data['jh_time'] = date('Y-m-d',$data['jh_time']);
		return $data;
	}
	public function delete($record = [])
    {
   		$ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    	// 删除节点
    	if (RestoreModel::destroy($ids)) {
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
		$info = RestoreModel::getOne($id);
		$info['materials_list'] = implode(RestoreModel::getMaterials($id),',');
		$info['helpname'] = Task_detailModel::get_helpname($info['helpid']);
		$info->jh_time = date('Y-m-d',$info['jh_time']);
		$info->fh_time = date('Y-m-d',$info['fh_time']);
		$info['controller'] = request()->controller();
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addGroup([
		'借贷返还'=>[
			['hidden','id'],            
			['static:4','name','返还主题'],
			['static:4','order_id','借货单'],
			['static:4', 'jhbm', '借货部门'],
			['static:4','jhname','借货人',],	
			['static:4','jh_time','借货日期',],	
			['static:4','jcbm','被借部门',],	
			['static:4','zrid','返还人'],
			['static:4','fhbm','返还部门'],	
			['static:4','fh_time','返还时间'],	
			['static:4','rkid','入库人'],	
			['static:4','zdid', '制单人'],	
			['archives','file','附件'],
			['static:4', 'helpname', '可查看该入库人员'],		
			['static','note','摘要'],	
		],
          '借贷返还明细' =>[
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
    		$map = ['stock_restore_detail.pid'=>$pid,'stock_material.id'=>['in',($materials_list)]];
    		$data = RestoreModel::getDetail($map);
    		$html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>实还数量</td><td>备注</td></tr>';
    		foreach ($data as $k => $v){ 
    			$html.='<tr><input type="hidden" name="mid[]" value="'.$v['itemsid'].'"><input type="hidden" name="mlid[]" value="'.$v['id'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['rksl'].'</td><td>'.$v['bz'].'</td></tr>';
    		}   		
    		$html .= '</tbody></table></div>';    
    	}
    	return $html;
    }
    
}
