<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5 0005
 * Time: 14:57
 */

namespace app\tender\admin;


use app\admin\controller\Admin;
use app\tender\model\Profit as ProfitModel;
use app\common\builder\ZBuilder;
class Profit extends Admin
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
						$data_list = ProfitModel::getList($map,$data);
						$btn_tax= [
    					'title' => '设置税率',
    					'icon'  => 'fa fa-fw fa-key',
    					'class' => 'btn btn-primary',
    					'href'  => url('tax')
							];
            return ZBuilder::make('table')
                ->setSearch('tax', '请输入税率','','确定') // 设置搜索参数
                ->addColumns([ // 批量添加数据列)
                    ['__INDEX__','序号'],
                    ['name','项目名称'],
                    ['income', '收入 （元）'],
                    ['payeds', '材料支出 （元）'],
                    ['hires','租赁支出 （元）'],
                    ['others','间接费支出 （元）'],
                    ['salary','工资 （元）'],
                    ['tax','税率'],
                    ['profit', '毛利润 （元）'],
                    ['per_profit', '毛利率 %'],
                ])
                ->addFilter('tender_obj.name')
                ->setExtraHtml($js)
                ->setRowList($data_list) // 设置表格数据
                ->fetch(); // 渲染模板
  }
    
    
    
    		public function tax(){		
    			if($this -> request -> ispost()){
						$data = $this -> request ->post();
          }
          if($this -> request -> isget()){
          	$this->fetch('tax');
          	}
    	
    	}

}