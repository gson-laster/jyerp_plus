<?php
namespace app\administrative\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\Organization as OrganizationModel;
use app\administrative\model\Customer as CustomerModel;
use app\user\model\User as UserModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use think\Db;
/**
 *  客户通讯录
 */
class Customer extends Admin
{
	// 通讯录主页
	public function index($group = 'my')
	{
	    $list_tab = [
	        'my' => ['title' => '我的客户', 'url' => url('index', ['group' => 'my'])],
	        'company' => ['title' => '公司客户', 'url' => url('index', ['group' => 'company'])],
	    ];

	    $map = $this->getMap();
	    $order = $this->getOrder();

	    switch ($group) {
	    	//我的客户
	    	case 'my':
	    		$fields = [
				    ['text:6', 'name', '客户公司名称', ''],
				    ['text:6', 'short', '简称', ''],
				    ['text:6', 'contact', '客户姓名', ''],
				    ['text:6', 'email', '邮箱', ''],
				    ['text:6', 'office_tel', '办公电话'],
				    ['text:6', 'mobile_tel', '手机号'],
				    ['text:6', 'qq', 'qq号'],
				    ['text:6', 'wechat', '微信号'],
				    ['text', 'address', '地址'],
				    ['hidden', 'add_user_id',UID],
				    ['hidden', 'id'],
				    ['textarea','remark','备注'],
				    ['radio:6','is_open','是否公开','',['否','是']],
				];
	    		$data_list = CustomerModel::getMylist($map,$order);
	            return ZBuilder::make('table')
	                ->setTabNav($list_tab,  $group)
	                ->setSearch(['name' => '公司名称','contact'=>'客户姓名','mobile_tel'=>'手机号','wechat'=>'微信号'],'','',true) // 设置搜索框
                    ->addColumns([ // 批量添加列
				        ['name', '客户公司'],
				        ['contact', '客户姓名'],
				        ['mobile_tel', '手机号'],
				        ['wechat', '微信号'],
				        ['is_open','是否公开','switch'],
				        ['right_button','操作','btn']
				    ])
				    ->setRowList($data_list) // 设置表格数据
				   	->autoAdd($fields, 'administrative_customer')
				   	->autoEdit($fields, 'administrative_customer')
				   	->addTopButton('delete') // 添加顶部按钮
				   	->addRightButtons('delete') // 添加编辑和删除按钮
	                ->fetch();
	        	break;
	        case 'company':
	        	//公司客户
	        	$data_list = CustomerModel::getCompanylist($map,$order);
	        	return ZBuilder::make('table')
	        	 	->setTabNav($list_tab,  $group)
	        	 	->setSearch(['nickname'=>'操作员','name' => '公司名称','contact'=>'客户姓名','mobile_tel'=>'手机号','wechat'=>'微信号'],'','',true) // 设置搜索框
	        		->hideCheckbox()
                    ->addColumns([ // 批量添加列
				        ['name', '客户公司'],
				        ['contact', '客户姓名'],
				        ['mobile_tel', '手机号'],
				        ['wechat', '微信号'],
				        ['nickname','操作员']
				    ])
				    ->setRowList($data_list) // 设置表格数据
	                ->fetch();
	        	break;
	    }
	}



}   
