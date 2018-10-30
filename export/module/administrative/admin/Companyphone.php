<?php
namespace app\administrative\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\admin\model\Attachment as AttachmentModel;
use app\administrative\model\Staffwhere as StaffwhereModel;
use app\user\model\Organization as OrganizationModel;
use app\user\model\Position as PositionModel;
use app\user\model\User as UserModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use think\Db;
/**
 * 员工通讯录
 */
class Companyphone extends Admin
{
	// 通讯录主页
	public function index(){
			$map = $this->getMap();
			if(!empty($map['nickname'])){
				$keyword = $this->request->get('keyword');
			}else{
				$keyword = '';
			}
			$this->assign('keyword',$keyword);
			$this->assign('resulet',self::getOrganization());
			$this->assign('html',self::getphone(1,$keyword));
            return $this->fetch(); // 渲染模板
	}

	//获取通讯列表
	public function getphone($id=null,$keyword=null){

		$empty_html ='<tr class="table-empty">
                        <td class="text-center empty-info" colspan="8">
                            <i class="fa fa-database"></i> 暂无数据<br>
                        </td>
                    </tr>';

		if($id==null)return $empty_html;
		if(!empty($keyword)){
			$map["locate('".$keyword."',`nickname`)"] = [">",0];
		}

		$oid = implode(',', OrganizationModel::getChildsId($id));

		$map['organization'] = empty($oid) ? $id : ['in',$id.','.$oid];

	    $phonelist = collection(UserModel::field('avatar,nickname,position,email,email_bind,mobile,mobile_bind')->where($map)->order('id')->select())->toArray();

	    if(!empty($phonelist)){
	    	//头像 职位
	    	$avatarlist = $position = array();

	    	foreach ($phonelist as $key => $value) {
	    		if($value['avatar']){
		    		$avatarlist[] = $value['avatar'];
	    		}
	    		if($value['position']){
	    			$position[] = $value['position'];
	    		}
	    	}

	    	$avatarlistlist = AttachmentModel::where('id','in',implode(',',$avatarlist))->column('path','id');
	    	$positionlist = PositionModel::where('id','in',implode(',',array_unique($position)))->column('title','id');

	    	//拼接html
	    	$html = '';
	    	$default_avatar = config('public_static_path').'admin/img/avatar.jpg';
	    	foreach ($phonelist as $key => $value) {
				$avatar = $value['avatar']==0 ? $default_avatar : '/'.$avatarlistlist[$value['avatar']];
				$mobile = empty($value['mobile']) ? '<span class="label label-danger">未填</span>' : $value['mobile']; 
				$email = empty($value['email']) ? '<span class="label label-danger">未填</span>' : $value['email']; 
				$position = empty($value['position']) ? '' : $positionlist[$value['position']];
				$html.='<tr class="">                                    	
				            <td><img src="'.$avatar.'" style="width:50px;height:50px;"></td>
				            <td>'.$value['nickname'].'</td>		
				            <td>'.$position.'</td>	                                    	
				            <td>'.$mobile.'</td>		                    
				            <td>'.$email.'</td>		                    
						</tr>';
	    	}

	    	return $html;

	    }else{

	    	return $empty_html;
	    }
	}


	//获取部门
	public function getOrganization(){
		$organization = OrganizationModel::field('id,title as text,pid')->select();
		$organizationall = self::recursion(collection($organization)->toArray(),0);
		return json_encode($organizationall);
	}

	/*
	 * 递归遍历
	 * @param $data array
	 * @param $id int
	 * return array
	 * */
	public function recursion($data, $id=0) {
		 $list = array();
		 foreach($data as $v) {
			 if($v['pid'] == $id) {
				 $v['nodes'] = self::recursion($data, $v['id']);
				 if(empty($v['nodes'])) {
				 	$v['nodes'] = '';
				 }
				 unset($v['pid']);
				 array_push($list, $v);
			 }
		 }
		 return $list;

	}
}   
