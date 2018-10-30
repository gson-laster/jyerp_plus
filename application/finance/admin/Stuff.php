<?php
namespace app\finance\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\finance\model\Finance_stuff as StuffModel;
use think\Db;
use app\finance\model\Finance_manager as Finance_managermodel;
//use app\finance\model\Admin_user as user;
use think\Config;
use app\tender\model\Obj as IModel;
use app\supplier\model\Supplier;
use app\contract\model\Materials;
use app\finance\model\Bank as BModel;
/*
 *材料付款
 * */
 class Stuff extends Admin {
//	protected $model;
	protected $manager;
	protected $bankInfo;
	protected $bankMinInfo;
    protected function _initialize(){
        parent::_initialize();
//      $this -> model = new stuffModel();
/*
 
 * 获取账户信息*/
        $bankInfo = Finance_managermodel::getList();
		$arr = [];
		$arrs = [];
		foreach($bankInfo as $v) {
			if($v['status'] == 1) {
//				$arr[$v['id']] = '账户名称:'.$v['name'].', 账号:'.$v['accmount'].', 开户银行:'.$v['bank'];
				$arr[$v['id']] = $v['name'];
			}
			$arrs[$v['id']] = $v['name'];
		}
		$this -> bankInfo = $arr;
		$this -> bankMinInfo = $arrs;
    }
    
 	public function index(){
		$map = $this->getMap();
		$data_list = StuffModel::getList($map);
		$arr = ['pay' => 0, 'stock' => 0, 'notpay' => 0];
		foreach($data_list as $v) {
			$arr['pay'] +=  $v['pay'];
			$arr['stock'] +=  $v['stock'];
			$arr['notpay'] +=  $v['notpay'];
		}
				    $js = <<<EOF
            <script type="text/javascript">
                $(function(){
                    $('tbody').append('<tr style="color:#ff0000"><td align="center">总计</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>{$arr["stock"]}</td><td>-</td><td>{$arr["notpay"]}</td><td>{$arr["pay"]}</td><td>-</td></tr>');
                });
            </script>
EOF;
   	$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('edit',['id'=>'__id__'])
		];
		return ZBuilder::make('table')
			->setSearch(['tender_obj.name'=>'项目','supplier_list.name'=>'供应商'],'','',true) // 设置搜索框
      ->addTimeFilter('finance_stuff.date') // 添加时间段筛选
      ->addOrder('finance_stuff.date,name') // 添加排序
		  ->addColumns([ // 批量添加列
			[ 'date', '日期', 'date'],
			[ 'number', '付款编号'],
			[ 'name','付款名称'],
			[ 'item','所属项目'],
			[ 'supplier','供应商'],			
			[ 'account','银行账户', $this -> bankMinInfo],			
			[ 'moneyed','已结算金额'],			
			[ 'payed','已支付金额'],			
			[ 'stock','累计入库金额'],			
			[ 'allpay','累计付款金额'],			
			[ 'notpay','未付金额'],			
			[ 'pay','付款金额'],
			['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],			
	    ['right_button', '操作','btn'],
	    ])
    ->addTopButton('add') // 添加顶部按钮
		->setPageTitle('材料付款')
		->addRightButtons('delete')
		->addRightButton('edit',$task_list,true)
		->addTopButton('delete') // 添加顶部按钮
		->setTableName('Finance_stuff') // 指定数据表名
		->setExtraJs($js)
		->setRowList($data_list)
		->fetch();
	
 	}
 		public function add(){
		if($this -> request -> ispost()){
			$data = $this -> request ->post();
			$r = $this -> Validate($data, 'stuff');
			if(true !== $r) $this -> error($r);
			
			
			$data['item'] = StuffModel::getItem($data['source_number']);
			$data['supplier'] =StuffModel::getSupplier($data['source_number']);
		
			
			            
			$data['date'] = strtotime($data['date']);
			$data['number'] = 'CGXJ'.date('Ymdhis',time()).UID;
			
			
			//dump($data);die;
			if ($res = StuffModel::create($data)) {
            	flow_detail($data['name'],'finance_stuff','finance_stuff','finance/stuff/edit',$res['id']);
                $this->success('新增成功',url('index'));
            }else {
                $this->error('新增失败');
           
           }
         }    
		$source = Materials::getName();
		$js = <<<EOF
            <script type="text/javascript">
                $(function(){
                   $('#pay').attr('oninput','return Edit1Change();');				   					
                });
				var j=chineseNumber(document.getElementById("pay").value);
				document.getElementById("big_money").value=j;		
				function Edit1Change(){			
					document.getElementById("big_money").value=chineseNumber(document.getElementById("pay").value);
				}
					
            </script>
EOF;
		
		
		
		
		return ZBuilder::make('form')
		->addFormItems([
		// 批量添加表单项
			['hidden:6', 'operator', UID],
			['date:3', 'date', '日期','',date('Y-m-d')],
//		['text:3', 'number', '付款编号'],
			['text:3', 'name','付款名称'],
			['select:3', 'type','源单类型', '', [1=>'材料合同'],1],
			['select:3', 'source_number','源单号','',$source],
			['text:3', 'objname','所属项目','','','','disabled'],
			['text:3', 'suname','供应商','','','','disabled'],			
			['select:3', 'account','银行账户', '',BModel::where('id','>','0')->column('id,name')],			
			['number:3', 'moneyed','已结算金额'],			
			['number:3', 'payed','已支付金额'],			
			['number:3', 'stock','累计入库金额'],			
			['number:3', 'allpay','累计付款金额'],			
			['number:3', 'notpay','未付金额'],			
			['text:3', 'zrid','经办人', '',get_nickname(UID)],			
			['number:3', 'pay','付款金额'],
			['text:3','big_money','金额大写'],			
			['text:3', 'info','(供)账户信息'],			
			['textarea:12', 'note','付款说明'],			
		])
		
		->setExtraHtml(outhtml2())
		->setExtraJs($js.outjs2())
		->js('Stuff')
		->js('chineseNumber')
		->fetch();
	}

	
	
	public function getDetail($source_number = ''){
			$data = StuffModel::getDetail($source_number);
			//dump($data);die;
		return $data;
	}
	
	
	
	public function edit($id = null){
		if($this -> request -> ispost()){
			$data = $this -> request ->post();
			$r = $this -> Validate($data, 'stuff');
			if(true !== $r) $this -> error($r);
			$data['date'] = strtotime($data['date']);
			StuffModel::white(['id' => $id], $data);
            $this->success('添加成功', 'index');
		}
		if (null == $id) $this -> error('参数错误');
		$data_list = StuffModel::one(['id' => $id]);
		$data_list['date'] = date('Y-m-d', $data_list['date']);
		
		$data=[1=>'材料合同'];
		$data_list['type'] = $data[$data_list['type']];
		
		return ZBuilder::make('form')
		->addFormItems([
		// 批量添加表单项
			['static:6', 'date', '日期'],
			['static:6', 'number', '付款编号'],
			['static:6', 'name','付款名称'],
			['static:6', 'type','源单类型'],
			['static:6', 'souname','源单号'],
			['static:6', 'item','所属项目'],
			['static:6', 'supplier','供应商'],			
			['static:6', 'mname','银行账户'],			
			['static:6', 'moneyed','已结算金额'],			
			['static:6', 'payed','已支付金额'],			
			['static:6', 'stock','累计入库金额'],			
			['static:6', 'allpay','累计付款金额'],			
			['static:6', 'notpay','未付金额'],			
			['static:6', 'operator','经办人'],			
			['static:6', 'pay','付款金额'],
			['static:3', "big_money", '大写金额'],			
			['static:6', 'info','(供)账户信息'],			
			['static:12', 'note','付款说明'],	
		])
		-> setPageTitle('详情')
		->hideBtn('submit')
		-> setFormData($data_list)
		->fetch();
	
	}
	public function delete($ids = null){
		if(null == $ids) $this -> error('参数错误');
     	return $this->setStatus('delete');
	}
}