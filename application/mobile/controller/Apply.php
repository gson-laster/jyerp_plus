<?php
namespace app\mobile\controller;
use app\admin\model\Mobilemenu as MobilemenuModel;
/*
 
 * 应用中心控制器,一级菜单*/
class Apply extends Base{
	
    public function _initialize(){
    	parent::_initialize();
		
        $action = request()->action();
        if($action!='index'){
        	// $this->level($action);
    			$SidebarMenu = MobilemenuModel::getSidebarMenu();
				//当前节点id
				$linkid = MobilemenuModel::getLinkId(['url_value'=>'mobile/apply/'.$action]);
				
				foreach ($SidebarMenu as $key => $value){
					if($value['id']==$linkid){
						$menu_list = isset($value['child']) ? $value['child'] : '';
					}
				}
				$this->assign('menu_list',$menu_list);
        }

    }

	/*
	 * 面包屑菜单
	*/
	public function index() {

		return $this->fetch();
		
	}

	//销售列表
	public function sales(){

		return $this->fetch('level');

	}
	//技术列表
	public function tender(){

		return $this->fetch('level');

	}

	//流程列表
	public function flow(){

		return $this->fetch('level');

	}
	
	//采购列表
	public function purchase(){
		
		return $this->fetch('level');
		
		}
	//生产列表
	public function produce(){
		
			return $this->fetch('level');
		
		}
		
		//施工列表
	public function constructionsite(){
		
			return $this->fetch('level');
		}
		//财务列表
	public function finance(){
		
			return $this->fetch('level');
		}
		//仓库列表
	public function stock(){
			return $this->fetch('level');
		}
		//人事列表
	public function personnel(){
		return $this->fetch('level');
	}
}
