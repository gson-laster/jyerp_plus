<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5 0005
 * Time: 14:57
 */

namespace app\finance\admin;


use app\admin\controller\Admin;
use app\finance\model\Profits as ProfitsModel;
use app\common\builder\ZBuilder;
use app\tender\model\Obj as ObjModel;
use app\tender\model\Type as TypeModel;
use app\user\model\Organization as OrganizationModel;
use app\task\model\Task_detail as Task_detailModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
class Profits extends Admin
{
        public function index()
        {		        
            $map = $this->getMap();
            // 排序
            $order = $this->getOrder();
            
            $js = '<script>
    jQuery(function () {
        $(".dropdown-menu").remove();
        $("#search-btn").text("税率");
            });
    </script>';
            // 数据列表
           
            // $data = $this->request->post('tax');
            $data = empty($map['tax']) ? '' : trim($map['tax'][1],'%');
            unset($map['tax']);
            //dump($data);die;         
			$data_list = ProfitsModel::getList($map,$data);
		
			$btn_tax= [
			'title' => '设置税率',
			'icon'  => 'fa fa-fw fa-key',
			'class' => 'btn btn-primary',
			'href'  => url('tax')
				];
            return ZBuilder::make('table')
                ->setSearch('tax', '请输入税率','','确定') // 设置搜索参数
                ->hideCheckbox()
                ->addColumns([ // 批量添加数据列)
                    ['__INDEX__','序号'],
                    ['name','项目名称'],
                    ['obj_sum', '收入 （元）'],
                    ['materials_sum', '材料支出 （元）'],
                
                    ['facts_sum','工资 （元）'],
                    ['others_sum','其他 （元）'],
                    ['tax','税率'],
                    ['mlr', '毛利润 （元）'],
                    ['mll', '毛利率 %'],
                ])
                ->addFilter('tender_obj.name')
                ->setExtraHtml($js)
                ->setRowList($data_list) // 设置表格数据
                ->fetch(); // 渲染模板
  }
  
   /* 尾款单*/
	public function balance_payment(){

		
		// 获取查询条件
		$map = $this->getMap();
		$order = $this->getMap();
		// 数据列表
		$list = ObjModel::balance_payment($map,$order);
		    
		//获取昵称
		// 分页数据
		
		$look = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('look',['id'=>'__id__'])
		];
		$confirm = [
		    'title' => '确认项目尾款已结清吗?',
		    'icon'  => 'fa fa-fw fa-key',
		    'class' => 'btn btn-xs btn-default ajax-get confirm',
		    'data-title' => '确认项目尾款已结清吗？',
		    'href'  => url('confirm', ['id' => '__id__', 'name' => '__name__'])
		];
		return ZBuilder::make('table')
	//	->setSearch(['tender_obj.name' => '项目名称'], '', '', true) // 设置搜索参数
		->setPageTitle('尾款列表')
		->addColumns([
			['__INDEX__','编号'],
			['name','项目名称'],
			['contact','项目联系人'],
			['phone','项目联系人电话'],
			['contrack_name','合同名称'],
			['money','合同金额'],
			['gather','已收款金额'],
			['final_payment','尾款金额'],
			['account_status', '项目尾款结清','status','',[0 =>'未结清:info', 2=>'否决:danger', 1=>'同意:success']],			
			['right_button','操作','btn'],
		])
		->addOrder(['t.id']) // 添加排序
        //->addFilter('tender_obj.account_status', [0 =>'未结清', 2=>'否决', 1=>'同意']) // 
		->setRowList($list)//设置表格数据
		->hideCheckbox()
		->addRightButton('look',$look, true) // 查看右侧按钮 
		->addRightButton('confirm', $confirm) // 添加授权按钮
		->replaceRightButton(['account_status' => 1], '', 'confirm')
		->setTableName('constructionsite_finish')
		->fetch();
	
	}
	
	/*
	 
	 * 确定项目已结款*/
	
	public function confirm($id = null, $name = ''){
		if(is_null($id)) $this -> error('参数错误');
		flow_detail($name.'项目尾款结清','tender_confirm','tender_obj','finance/profits/look', $id);

		$this -> success('操作成功');
	}
	
	
	public function look($id = null) {
		if(is_null($id)) $this -> error('参数错误');
		$info =  ObjModel::balance_payment_id(['t.item' => $id]);
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addFormItems([
			['static:6','name', '项目名称'],
			['static:6','contact','项目联系人'],
			['static:6','phone','项目联系人电话'],
			['static:6','contrack_name','合同名称'],
			['static:6','money','合同金额'],
			['static:6','gather','已收款金额'],
			['static:6','final_payment','尾款金额'],
			['archives:12','file','竣工图'],
			
		])
		->setFormData($info)
		->fetch();
	}
  
  
  
  
  
  
  
  
  
  
  
  
  
  
}