<?php
namespace app\stock\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\stock\model\Stock as StockModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\House as HouseModel;
use app\stock\model\Bad	as BadModel;
use app\stock\model\MaterialType as MaterialTypeModel;
/**
 * 其他入库控制器
 */
class Stock extends Admin
{
	


	// 库存主页
	public function index(){
			$map = $this->getMap();
			if(!empty($map['stock_material.name'])){
				$keyword = $this->request->get('keyword');
			}else{
				$keyword = '';
			}
			$this->assign('keyword',$keyword);
			$this->assign('resulet',self::getmaterialtype());
			$this->assign('html',self::getmaterial(implode(MaterialTypeModel::column('id'),','),$keyword));
            return $this->fetch(); // 渲染模板
	}

	//获取物资类型
	public function getmaterialtype(){
		$materialtype = MaterialTypeModel::field('id,title as text,pid')->select();
		$materialtype = self::recursion(collection($materialtype)->toArray(),0);
		return json_encode($materialtype);
	}

	//获取物资列表
	public function getmaterial($id=null,$keyword=null){

		$empty_html ='<tr class="table-empty">
                        <td class="text-center empty-info" colspan="10">
                            <i class="fa fa-database"></i> 暂无数据<br>
                        </td>
                    </tr>';

		if($id==null)return $empty_html;

		if(!empty($keyword)){
			$map["stock_material.name"] = ["like","%".$keyword."%"];
		}
		
		if(is_numeric($id)){
			$materialtype = implode(',', MaterialTypeModel::getChildsId($id));
			$map['stock_stock.material_type'] = empty($materialtype) ? $id : ['in',$id.','.$materialtype];
		}else{
			$map['stock_stock.material_type'] = ['in',$id];
		}

		// dump($map);die;
	    $materialist = StockModel::getList($map);
	    if(!empty($materialist)){
	    	$html = '';
	    	foreach ($materialist as $key => $value) {
				$html.='<tr class="">                                    	
				            <td>'.$value['id'].'</td>		                    
				            <td>'.$value['material_code'].'</td>
				            <td>'.$value['material_name'].'</td>
				            <td>'.$value['material_type_name'].'</td>
				            <td>'.$value['material_version'].'</td>
				            <td>'.$value['material_unit'].'</td>
				            <td>'.$value['stock_name'].'</td>
				            <td>'.$value['number'].'</td>
				            <td>￥ '.number_format($value['price'],2).'</td>
				            <td>￥ '.number_format($value['total'],2).'</td>
						</tr>';
	    	}
	    	return $html;
	    }else{
	    	return $empty_html;
	    }

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
	//----------原先---------
	/*
	 * 采购入库列表  
	 * @author HJP<957547207>
	 */
	public function index2()
	{
		// 查询
		$map = $this->getMap();
		// 排序
		$order = $this->getOrder('stock_stock.create_time desc');
		// 数据列表
		$data_list = StockModel::getList($map,$order);
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
		// 使用ZBuilder快速创建数据表格
		return ZBuilder::make('table')
			->hideCheckbox()
			->setSearch(['id' => '编号'])
			->addColumns([
				['ckid','所属仓库'],
				['materialid','物资名称'],
				['number','现有库存'],
				['update_time','更新时间','datetime'],
				['right_button', '查看', 'btn']		
			])      
            ->setRowList($data_list) // 设置表格数据 
 			->addRightButton('task_list',$task_list,true) // 查看右侧按钮  
			->addTopButton('export', [
				'title' => '导出',
				'icon' => 'fa fa-sign-out',
				'class' => 'btn btn-primary ajax-get',
				'href' => url('export', http_build_query($this->request->param()))
			])
			->addTopButton('import', [
				'title' => '导入',
				'icon' => 'fa fa-fw fa-sign-in',
				'class' => 'btn btn-primary',
				'href' => url('import')
			])			
            ->fetch(); // 渲染模板
	}

	//库存流水账
	public function flow(){
		// 查询
		$map = $this->getMap();
		// 数据列表
		$data_list = StockModel::getFlow($map);
		// 使用ZBuilder快速创建数据表格
		return ZBuilder::make('table')
			->hideCheckbox()
			->setSearch(['id' => '编号'])
			->addColumns([
				['ckid','所属仓库'],
				['materialid','物资名称'],
				['number','现有库存'],
				['create_time','创建时间'],	
			])      
            ->setRowList($data_list) // 设置表格数据                                        
            ->fetch(); // 渲染模板
		
	}	

	//查看
    public function task_list($id = null){
    	if($id == null) $this->error('参数错误');		
		$info = BadModel::getOne($id);
		$info['materials_list'] = implode(BadModel::getMaterials($id),',');
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addGroup([
		'现有库存'=>[
			['hidden','id'],            
			['static:4','name','报损主题'],
			['static:4','zrid','经办人',],	
			['static:4','bsbm','报损部门',],	
			['static:4','ck','仓库'],
			['select:4','bstype','报损原因','',[0=>'物品折旧',1=>'物品损坏']],
			['static:4','bs_time','报损时间','',date('Y-m-d')],		
			['static:4','zdid', '制单人'],		
			['archives','file','附件'],		
			['static','note','摘要'],							
		],
          '现有库存明细' =>[
            ['hidden', 'materials_list'],
            ['hidden', 'old_plan_list'],
          ]			
		])
		->setExtraJs(outjs2())
		->setFormData($info)
		->js('test9')
		->fetch();
    }

	//导出
   public function export()
    {
        $map = $this->getMap();        
        $order = $this->getOrder();
        $data = StockModel::exportData($map,$order);
		if($data == null) $this->error('暂无数据！');
        $cellName = [
            ['ckid', 'auto', '所属仓库'],
            ['materialid', 'auto', '物资名称'],
            ['number', 'auto', '现有库存'],
			['update_time', 'auto', "更新时间"],
        ];
        plugin_action('Excel/Excel/export', ['stocklist', $cellName, $data]);
    }
}

