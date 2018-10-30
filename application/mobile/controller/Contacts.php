<?php
namespace app\mobile\controller;

use  app\user\model\User;
use app\user\model\Organization as OrganizationModel;
use app\mobile\model\Contacts as ContactsModel ;
use think\Db;
/*
 
 * 联系人控制器*/
class Contacts extends Base{
	/*
	* 通讯录列表*/
	public function index() {
		//取得所有数据
		$Lists=Db::name('admin_user')->select();
		//dump($Lists);die;
		//新增加一个元素，这个元素存储着nickname元素的首字符首字母；
		//$List=$Lists['data'];
			//给默认图片
//			halt($List);
		foreach($Lists as $key=>&$value){
			foreach($value as $key=>&$z){
				if($key=='avatar' && $z==null){
					$z=config('tytytytyty');				
				}
			}	
		}	
		//foreach($List as &$value){
			//$value['firstchar']=ContactsModel::getfirstchar($value['nickname']);
		//}
		//得到每个元素的firstchar属性；
		//$data = array_column($List, 'firstchar');
		//按照得到的firstchar属性给数组排序；
		//array_multisort($data,SORT_ASC,$List);
		//传递到视图
		$this->assign('a',$Lists);
		//渲染
		return $this -> fetch('index-hjp');
	}
	
	public function details($id='') {
		
		if($id==='')$this->error('没有此计划');
		$lists=User::getList(['admin_user.id'=>$id])->toArray();
		$list=$lists['data'][0];
		
		
		foreach($list as $key=>&$value){
			if($key=='zid' && $value==null)
				$value=config('tytytytyty');
		}	
		$data_list=detaillist([
					['zid','头像:','img'],
					["username",'用户名:'],
                    ["nickname", '昵称:'],
                    ["title", '部门:'],
                    ['ztitle','职位:'],
                    ["mobile", '联系电话:','tel'],
                   	["email", '邮箱:'],
		],$list);
	
		//halt($data_list);

		$this->assign('data_list',$data_list);
		return $this->fetch('apply/details');
	}
	
	
	/*
	 * 搜索函数
	 * 已作废
	 */
	public function serch($value){
		$lists=user::getList()->toArray();
		$list=$lists['data'];
		
		for($i=0,$count=count($list);$i<$count;$i++){
			if(strpos($list[$i]['nickname'],$value)===false){
				unset($list[$i]);
			}
		}
	
		return $list;
	}
	
}
?>