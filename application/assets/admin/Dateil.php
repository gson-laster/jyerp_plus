<?php
namespace app\assets\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\assets\model\Assets_dateil as Assets_dateilModel;
use app\assets\model\Assets_select as Assets_selectModel;
use app\assets\model\Assets_category as Assets_categoryModel;
use app\user\model\Organization as OrganizationModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use app\assets\model\common;
use think\Db;
/**
 * 我的资产
 * @author HJP
 */
class Dateil extends Admin
{
	//获取部门
	public function get_bm_name()
    {
       $result = array();
    	$where['status'] = ['egt', 1];
    
    	// 获取菜单
    	$category = Db::name('admin_organization')->where($where)->select();
    	foreach ($category as $v) {
    		$result[$v['id']] = $v['title'];
    	}
    	return $result;
    }
	public function index()
	{		
		// 获取查询条件
        $map = $this->getMap();  
         //类别
        $types = Assets_categoryModel::getType();
        //部门
        $get_bm_name = Assets_selectModel::get_bm_name();            
		// 数据列表
        $data_list = Assets_selectModel::where(['zrid'=>UID])
        			 ->paginate();
        // 分页数据
        $page = $data_list->render();       
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
        // 使用ZBuilder快速创建数据表格
		return ZBuilder::make('table')
		->setPageTitle('我的资产') // 设置页面标题
		->hideCheckbox()
		->addColumns([
			['id', 'ID'],
			['name','名称'],
			['specifications','规格'],
			['categoryid','类别','text','',$types],
			['number','数量'],
			['zrid','归属人','text','',Assets_selectModel::get_name()],
			['departmentid','所属部门','text','',$get_bm_name],
			['status','状态', 'status', '', [-2=>'暂无', -1=>'申请失败', 0=>'申请中', 1=>'可用', 2=>'申请成功']],						
			['right_button', '操作','btn'],
		])		
        ->addRightButton('task_list',$task_list,true) // 查看右侧按钮 
        ->setRowList($data_list) // 设置表格数据
		->fetch(); // 渲染页面
	}
	//查看
	public function task_list($id = null){
		if($id == null) $this->error('参数错误');			
			$info = Assets_selectModel::where('id', $id)->find();			
			$status = $info['status'];
			$zrid = $info['zrid'];
			 //类别
        	$types = Assets_categoryModel::getType();
        	//部门
        $get_bm_name = Assets_selectModel::get_bm_name();
			// 使用ZBuilder快速创建表单
			return ZBuilder::make('form')
			->hideBtn('submit')
			->addFormItems([
				['hidden', 'id'],
				['hidden','uid'],
				['hidden','zrid'],				
				['select:6', 'categoryid', '类别', '', $types,'','disabled'],
				['text:6', 'name', '名称','','','','disabled'],
				['text:6', 'specifications','规格','','','','disabled'],
				['number:6','number','数量','','','','','','disabled'],
				['text:6', 'zrname','归属人','',get_nickname($zrid),'','disabled'],
				['select:6', 'departmentid','所属部门','',$get_bm_name,'','disabled'],			
				['number:6','money','发票金额(元)','','','','','','disabled'],
				['datetime:6','invoice_time','发票日期','','','','disabled'],
				['textarea','note','备注','','','disabled'],
				['radio', 'status', '状态', '', [-2=>'停用', -1=>'申请失败', 0=>'申请中', 1=>'可用', 2=>'申请成功'],$status,'','disabled'],			
			])
			->setFormData($info)
			->fetch();

	}
	//我的申请
	public function myrecipients(){
		$data_list = Db::view('assets_dateil')
				->view('assets_select',['categoryid','name','specifications','departmentid'],'assets_select.id=assets_dateil.selectid')
				->where(['assets_dateil.uid'=>UID])
        			 ->paginate();
        			  //类别
        	$types = Assets_categoryModel::getType();
        	//部门
        	$return = [
		    'title' => '申请归还',
		    'icon'  => 'fa fa-fw fa-refresh',
		    'class' => 'btn btn-xs btn-default ajax-get confirm',
		    'href'  => url('returns', ['id' => '__id__']),
		    'data-title' => '确定归还？',
		    
		];
        $get_bm_name = Assets_selectModel::get_bm_name();
        			 // 使用ZBuilder快速创建数据表格
		return ZBuilder::make('table')
		->setPageTitle('我的申请') // 设置页面标题
		->hideCheckbox()
		->addColumns([
			['categoryid','类别','text','',$types],	
			['name','名称'],	
			['specifications','规格'],
			['departmentid','所属部门','text','',$get_bm_name],	
			['create_time','申请时间','date'],	
			['status','状态', 'status', '', [-2=>'停用', -1=>'申请失败', 0=>'申请中', 1=>'可用', 2=>'申请成功',3=>'归还成功']],
			['right_button', '操作','btn'],				
		])		
		->addRightButton('return',$return) // 归还右侧按钮
		->replaceRightButton(['status' => ['not in', '2']], '<button class="btn btn-danger btn-xs" type="button" disabled>不可操作</button>')
        ->setRowList($data_list) // 设置表格数据
		->fetch(); // 渲染页面
	}
	//申请列表
	public function recipientlist(){
		$data_list = Db::view('assets_dateil',['uid','status','id'])
				->view('assets_select',['categoryid','name','zrid','specifications','departmentid','create_time'],'assets_select.id=assets_dateil.selectid')
				->where(['assets_select.zrid'=>UID])
        			 ->paginate();
        			  //类别
        	$types = Assets_categoryModel::getType();
        	//部门
        $get_bm_name = Assets_selectModel::get_bm_name();
        $determine = [
		    'title' => '同意领取',
		    'icon'  => 'fa fa-fw fa-yc-square',
		    'class' => 'btn btn-xs btn-default ajax-get confirm',
		    'href'  => url('determine', ['id' => '__id__']),
		    'data-title' => '确定同意！'
		];
		$nodetermine = [
		    'title' => '不同意',
		    'icon'  => 'fa fa-fw fa-window-close',
		    'class' => 'btn btn-xs btn-default ajax-get confirm',
		    'href'  => url('nodetermine', ['id' => '__id__']),
		    'data-title' => '确定不同意！'
		];
        			 // 使用ZBuilder快速创建数据表格
		return ZBuilder::make('table')
		->setPageTitle('申请列表') // 设置页面标题
		->hideCheckbox()
		->addColumns([	
			['id','ID'],		
			['categoryid','类别','text','',$types],	
			['name','名称'],	
			['specifications','规格'],
			['uid','申请人','text','',Assets_selectModel::get_name()],
			['zrid','归属人','text','',Assets_selectModel::get_name()],
			['departmentid','所属部门','text','',$get_bm_name],	
			['create_time','申请时间','date'],	
			['status','状态', 'status', '', [-2=>'停用', -1=>'申请失败', 0=>'申请中', 1=>'可用', 2=>'申请成功',3=>'归还成功']],
			['right_button', '操作','btn'],				
		])		
		->addRightButton('determine',$determine) // 同意右侧按钮 
		->addRightButton('nodetermine',$nodetermine) // 不同意右侧按钮 
		->replaceRightButton(['status' => ['not in', '0']], '<button class="btn btn-danger btn-xs" type="button" disabled>不可操作</button>')
        ->setRowList($data_list) // 设置表格数据
		->fetch(); // 渲染页面
	}
	public function determine($id = null){		
		if ($id === null) $this->error('缺少参数');
		//修改 assets_dateil表status状态2
		$map['id'] = $id;
		$map['status'] = 0;
		$info = ['status' => 2];
		$data2 = Assets_dateilModel::where($map)->find();
		$selectid = $data2['selectid'];
		Assets_dateilModel::where($map)->update($info);
		$data = Assets_selectModel::where(['id'=>$selectid])->find();
		if($data['number'] == 0){
			return $this->error('库存不足！');
		}
		$data['number'] = $data['number']-1;		
		//修改 Assets_select表status状态2
		$info2 = ['number'=>$data['number']];
		$data1 = Assets_selectModel::where(['id'=>$selectid])->update($info2); 
		if($data1){
			return $this->success('同意成功');
		}  		                  
	}
	public function nodetermine($id = null){		
		if ($id === null) $this->error('缺少参数');
		//修改 assets_dateil表status状态2
		$map['id'] = $id;
		$map['status'] = 0;
		$info = ['status' => -1];
		$data2 = Assets_dateilModel::where($map)->find();
		$selectid = $data2['selectid'];
		$data1 = Assets_dateilModel::where($map)->update($info); 
		if($data1){
			return $this->success('不同意成功');
		} 
	}
		//申请领用
	public function recipients($id = null){		
		$organid = session('user_auth')['organization'];
		if ($id === null) $this->error('缺少参数');
		//存assets_dateil表
		$info = ['uid' => UID,'selectid' => $id,'status' => 0];
		$data = Assets_dateilModel::create($info); 
		//修改 Assets_select表status状态0
		$info2 = ['uid' => UID,'departmentid'=>$organid,'status' => 0];
		Assets_selectModel::where(['id'=>$id])->update($info2);          
        return $this->success('申请成功,等待上级回复!');
	}
	public function returns($id = null){		
		if ($id === null) $this->error('缺少参数');
			//修改 assets_dateil表status状态2
		$map['id'] = $id;
		$map['status'] = 2;
		$info = ['status' => 3];
		$data2 = Assets_dateilModel::where($map)->find();
		$selectid = $data2['selectid'];
		Assets_dateilModel::where($map)->update($info);
		$data = Assets_selectModel::where(['id'=>$selectid])->find();		
		$data['number'] = $data['number']+1;		
		//修改 Assets_select表status状态2
		$info2 = ['number'=>$data['number']];
		$data1 = Assets_selectModel::where(['id'=>$selectid])->update($info2); 
		if($data1){
			return $this->success('归还成功！');
		} 
	}
}
