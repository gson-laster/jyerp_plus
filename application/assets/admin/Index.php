<?php
namespace app\assets\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\assets\model\Assets_select as Assets_selectModel;
use app\assets\model\Assets_category as Assets_categoryModel;
use app\assets\model\Assets_dateil as Assets_dateilModel;
use app\user\model\Organization as OrganizationModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use app\assets\model\common;
use think\Db;
/**
 * 资产控制器
 * @author HJP
 */
class Index extends Admin
{
	
    
	public function index()
	{
		$auth = session('user_auth');
		//获取登录用户的权限		
		$rid = $auth['role'];
					
		// 获取查询条件
        $map = $this->getMap();
        //类别
        $types = Assets_categoryModel::getType();
        //部门
        $get_bm_name = Assets_selectModel::get_bm_name();
		// 数据列表
        $data_list = Db::view('assets_select')->where($map)->paginate();
        // 分页数据
        $page = $data_list->render();
       	$recipients = [
		    'title' => '申请领用',
		    'icon'  => 'fa fa-fw fa-hand-paper-o',
		    'class' => 'btn btn-xs btn-default ajax-get confirm',
		    'href'  => url('recipients', ['id' => '__id__']),
		    'data-title' => '确定领用'
		];
//		$return = [
//		    'title' => '申请归还',
//		    'icon'  => 'fa fa-fw fa-refresh',
//		    'class' => 'btn btn-xs btn-default ajax-get confirm',
//		    'href'  => url('returns', ['id' => '__id__']),
//		    'data-title' => '真的要归还吗？',
//		    'data-tips' => '请归还到二三其奇!'
//		];
//		$determine = [
//		    'title' => '同意领取',
//		    'icon'  => 'fa fa-fw fa-yc-square',
//		    'class' => 'btn btn-xs btn-default ajax-get confirm',
//		    'href'  => url('determine', ['id' => '__id__']),
//		    'data-title' => '确定同意！'
//		];
//		$nodetermine = [
//		    'title' => '不同意',
//		    'icon'  => 'fa fa-fw fa-window-close',
//		    'class' => 'btn btn-xs btn-default ajax-get confirm',
//		    'href'  => url('nodetermine', ['id' => '__id__']),
//		    'data-title' => '确定不同意！'
//		];
//      if($rid == 3){
//			// 使用ZBuilder快速创建数据表格
//		return ZBuilder::make('table')
//		->setPageTitle('资产列表') // 设置页面标题
//		->setSearch(['name' => '名称', 'categoryid' => '类别']) // 设置搜索参数
//		->addColumns([
//			['id', 'ID'],
//			['name','名称'],
//			['specifications','规格'],
//			['categoryid','类别','text','',$types],
//			['uid','申请人','text','',$nickname],
//			['departmentid','所属部门','text','',$get_bm_name],			
//			['status','状态', 'status', '', [-2=>'停用', -1=>'申请失败', 0=>'申请中', 1=>'可用', 2=>'申请成功']],			
//			['right_button', '操作','btn'],
//		])		
//      ->addRightButton('recipients',$recipients) // 领用右侧按钮 
//      ->addRightButton('return',$return) // 归还右侧按钮
//      ->replaceRightButton(['status' => 1], '','return')//不显示归还
//      ->replaceRightButton(['status' => 2], '','recipients')//不显示领用
//      ->replaceRightButton(['status' => ['not in', '1,2']], '<button class="btn btn-danger btn-xs" type="button" disabled>不可操作</button>')
//      ->replaceRightButton(['uid'=>['not in',UID]], '<button class="btn btn-danger btn-xs" type="button" disabled>不可操作</button>')
//      ->setRowList($data_list) // 设置表格数据
//      ->setTableName('assets_select')
//		->fetch(); // 渲染页面
//		}else{
			 // 使用ZBuilder快速创建数据表格
		return ZBuilder::make('table')
		->setPageTitle('资产列表') // 设置页面标题
		->setSearch(['name' => '名称', 'categoryid' => '类别']) // 设置搜索参数
		->addColumns([
			['id', 'ID'],
			['name','名称'],
			['specifications','规格'],
			['categoryid','类别','text','',$types],
			['number','数量'],
			['zrid','归属人','text','',Assets_selectModel::get_name()],
			['departmentid','所属部门','text','',$get_bm_name],
			['status','状态', 'status', '', [-2=>'暂无', 1=>'可用']],						
			['right_button', '操作','btn'],
		])
		->addTopButtons('delete') // 批量添加顶部按钮
		->addRightButton('recipients',$recipients) // 领用右侧按钮 
//      ->addRightButton('nodetermine',$nodetermine) // 归还右侧按钮
		->replaceRightButton(['status' => ['not in', '1']],'','recipients')//不显示同意
//      ->replaceRightButton(['status' => ['not in', '0']],'','nodetermine')//不显示不给
        ->addRightButtons(['edit', 'delete' => ['data-tips' => '删除资产信息无法恢复。']]) // 批量添加右侧按钮        
        ->setRowList($data_list) // 设置表格数据
        ->setTableName('assets_select')
		->fetch(); // 渲染页面	
//		}
       	
	}
	//申请领用
	public function recipients($id = null){				
		if ($id === null) $this->error('缺少参数');
		$date2 = Assets_selectModel::where(['id'=>$id])->find();		
		if ($date2['number']<=0){			
			return $this->error('库存不足!');
		}
			//修改 Assets_select表status状态0	
		$data3 = Assets_dateilModel::where(['selectid' => $id,'uid'=>UID])->order('create_time desc')->find();	
		if(in_array($data3['status'],['0','2'])) $this->error('你已申请过,请查看我的申请');				
		$info2 = ['uid' => UID];
		Assets_selectModel::where(['id'=>$id])->update($info2); 
			//存assets_dateil表
		$info = ['uid' => UID,'selectid' => $id,'status' => 0];
		$data = Assets_dateilModel::create($info); 		         
        return $this->success('申请成功,等待上级回复!');				
	}
	//申请归还
//	public function returns($id = null){		
//		if ($id === null) $this->error('缺少参数');
//			//修改 assets_dateil表status状态1
//			$map['selectid'] = $id;
//			$map['returnid'] = 0;
//			$map['status'] = 2;
//			$info = ['status' => 3,'returnid'=>1];
//			Assets_dateilModel::where($map)->update($info);
//				//修改 Assets_select表status状态1
//			$info2 = ['uid' => 0,'departmentid'=>1,'status' => 1];
//			Assets_selectModel::where(['id'=>$id])->update($info2);  		          
//         return $this->success('归还成功');
//	}
//	public function determine($id = null){		
//		if ($id === null) $this->error('缺少参数');
//		//修改 assets_dateil表status状态2
//		$map['selectid'] = $id;
//		$map['returnid'] = 0;
//		$map['status'] = 0;
//		$info = ['status' => 2];
//		Assets_dateilModel::where($map)->update($info);
//		//修改 Assets_select表status状态2
//		$map2['id'] = $id;
//		$map2['status'] = 0;
//		$info2 = ['status' => 2];
//		Assets_selectModel::where($map2)->update($info2);   		          
//      return $this->success('同意成功');
//	}
//	public function nodetermine($id = null){		
//		if ($id === null) $this->error('缺少参数');
//		//修改 assets_dateil表status状态-1
//		$map['selectid'] = $id;
//		$map['returnid'] = 0;
//		$info = ['status' => -1];
//		Assets_dateilModel::where($map)->update($info);
//		//修改 Assets_select表status状态-1
//		$map2['id'] = $id;
//		$map2['status'] = 0;
//		$info2 = ['status' => -1];
//		Assets_selectModel::where($map2)->update($info2);   		            		          
//      return $this->success('不同意成功');
//	}
	//增加
	public function add() {
		if($this->request->isPost()){
			$data = $this->request->post();
			if ($model = Assets_selectModel::create($data)) {				              
                $this->success('新增成功', url('index'));
            } else {
                $this->error('新增失败');
            }
		}
		
		 //类别
        $types = Assets_categoryModel::getType();
		// 使用ZBuilder快速创建表单
		return ZBuilder::make('form')
		->addFormItems([// 批量添加表单项
			['hidden','zrid',UID],
			['select:6', 'categoryid', '类别', '', $types],
			['text:6', 'name', '名称'],
			['text:6', 'specifications','规格'],
			['number:6','number','数量'],
			['text:6', 'zrname','归属人','',get_nickname(UID)],
			['select:6', 'departmentid','所属部门','', OrganizationModel::getMenuTree2(), '', url('get_user'), 'user_id', 'organization'],			
			['number:6','money','发票金额(元)'],
			['datetime:6','invoice_time','发票日期'],
			['textarea','note','备注'],
			['radio', 'status', '状态', '', [-2=>'停用', 1=>'可用'],1],
		])
		->setExtraHtml(outhtml2())
		->setExtraJs(outjs2())
		->fetch();
	}
	//编辑
	public function edit($id = null){
		if ($id === null) $this->error('缺少参数');
		if ($this->request->isPost()) {
   		$data = $this->request->post();  		
   		if ($model = Assets_selectModel::update($data)) {           
            return $this->success('修改成功', url('index'));
        } else {
            return $this->error('修改失败');
        }
		}
		
		//类别
        $types = Assets_categoryModel::getType();
        //部门
        $get_bm_name = Assets_selectModel::get_bm_name();
        //查哪条
		$info = Assets_selectModel::where('id', $id)->find();
       	// 使用ZBuilder快速创建表单
       	return ZBuilder::make('form')
       	->addFormItems([// 批量添加表单项
       		['hidden', 'id'],
       	 	['select', 'categoryid', '类别', '', $types],
			['text', 'name', '名称'],
			['text', 'specifications','规格'],
			['select','uid','申请人','',Assets_selectModel::get_name()],
			['select', 'departmentid','所属部门', '',$get_bm_name],
			['text', 'procurement','采购人'],
			['number','money','发票金额(元)'],
			['datetime','invoice_time','发票日期'],
			['radio', 'status', '状态', '', [-2=>'停用', -1=>'申请失败', 0=>'申请中', 1=>'可用', 2=>'申请成功']],
		])
		->setFormData($info)
        ->fetch();
	}
	//删除
	public function delete($ids = null){
		if ($ids === null) $this->error('参数错误');             
        return $this->setStatus('delete');
	}
}   
