<?php
namespace app\assets\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\assets\model\Assets_dateil as Assets_dateilModel;
use app\assets\model\Assets_select as Assets_selectModel;
use app\assets\model\Assets_category as Assets_categoryModel;
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
		//获取登录用户的权限
		$rid = session('user_auth')['role'];
		// 获取查询条件
        $map = $this->getMap();  
         //类别
        $types = Assets_categoryModel::getType();
        //部门
        $get_bm_name = $this->get_bm_name();            
		// 数据列表
        $data_list = Db::view('assets_dateil')
        			 ->view('assets_select',['categoryid','name','specifications','departmentid'],'assets_select.id=assets_dateil.selectid')
        			 ->where($map)
        			 ->where(['assets_dateil.uid'=>UID])
        			 ->paginate();
        // 分页数据
        $page = $data_list->render();
        $recipients = [
		    'title' => '申请领用',
		    'icon'  => 'fa fa-fw fa-hand-paper-o',
		    'class' => 'btn btn-xs btn-default ajax-get',
		    'href'  => url('recipients', ['id' => '__id__'])
		];
		$return = [
		    'title' => '申请归还',
		    'icon'  => 'fa fa-fw fa-refresh',
		    'class' => 'btn btn-xs btn-default ajax-get',
		    'href'  => url('returns', ['id' => '__id__'])
		];
        // 使用ZBuilder快速创建数据表格
		return ZBuilder::make('table')
		->setPageTitle('我的资产') // 设置页面标题
		->hideCheckbox()
		->addColumns([
			['categoryid','类别','text','',$types],	
			['name','名称'],	
			['specifications','规格'],
			['departmentid','所属部门','text','',$get_bm_name],	
			['status','状态', 'status', '', [-2=>'停用', -1=>'申请失败', 0=>'申请中', 1=>'可用', 2=>'申请成功',3=>'归还成功']],
			['create_time','申请时间'],		
			['right_button', '操作','btn'],
		])		
        ->addRightButton('recipients',$recipients) // 领用右侧按钮 
        ->addRightButton('return',$return) // 归还右侧按钮
        ->replaceRightButton(['status' => 1], '','return')//不显示归还
        ->replaceRightButton(['status' => 2], '','recipients')//不显示领用
        ->replaceRightButton(['status' => 2,'returnid' => 1], '','return')//已归还不显示归还
        ->replaceRightButton(['status' => ['not in', '1,2']], '<button class="btn btn-danger btn-xs" type="button" disabled>不可操作</button>')
        ->setRowList($data_list) // 设置表格数据
		->fetch(); // 渲染页面
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
			//修改 assets_dateil表status状态1
			$selectid = Assets_dateilModel::where(['id'=>$id])->value('selectid');
			$map['id'] = $id;
			$map['returnid'] = 0;
			$map['status'] = 2;
			$info = ['status' => 3,'returnid'=>1];
			Assets_dateilModel::where($map)->update($info);
					//修改 Assets_select表status状态1
			$info2 = ['uid' => 0,'departmentid'=>1,'status' => 1];
			Assets_selectModel::where(['id'=>$selectid])->update($info2);  		          
           return $this->success('归还成功');
	}
}
