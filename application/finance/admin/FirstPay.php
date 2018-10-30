<?php
namespace app\finance\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\finance\model\Finance_accmount as Finance_accmountmodel;
use app\finance\model\Finance_manager as Finance_managermodel;
use think\Db;
/*
 *账户期初
 * */
 class FirstPay extends Admin {
	protected $model;
	protected $bankInfo;//银行信息资料
	//protected $user;
    protected function _initialize(){
        parent::_initialize();
        $this -> model = new Finance_accmountmodel();
        $bank = new Finance_managermodel();
//      $arr = $bank::getAll();
//      $arrs = [];
//      foreach($arr as $v) {
//      	$arrs[$v['id']] = $v['name'];
//      }
        $this -> bankInfo = $bank::column('id,name');
      //  $this -> user = user::getUser();
    }
 	public function index(){
		$map = $this->getMap();
		$data_list = Finance_accmountmodel::getList($map);
		 $task_list = [
						'title' => '查看详情',
						'icon' => 'fa fa-fw fa-eye',
						'href' => url('edit',['id'=>'__id__'])
						];
		$total = 0;
		foreach($data_list as $v){
			$total += $v['first_money'];
		}
		$js = <<<EOF
			   <script type="text/javascript">
                $(function(){
                    $('tbody').append('<tr style="color:#ff0000"><td align="center">总计</td><td>-</td><td>-</td><td>-</td><td>{$total}</td><td>-</td><td>-</td></tr>');
                });
            </script>
EOF;
		return ZBuilder::make('table')
		  ->addColumns([ // 批量添加列
//	        ['id', 'id'],
	        ['name', '账户昵称', $this -> bankInfo],
	        ['accmount', '账户'],
	        ['bank', '开户银行'],
	        ['first_money', '期初金额'],
//	        ['big_money', '金额大写'],
	        ['nickname', '录入人', ],
//	        ['create_time', '创建时间', 'datetime'],
					['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
	        ['right_button', '操作','btn'],
	    ])
        ->addTopButton('add') // 添加顶部按钮
		->setPageTitle('账户期初')
		->addOrder('accmount,bank')
		->addRightButton('edit',$task_list,true)
		->addRightButton('delete')
		->addTopButton('delete') // 添加顶部按钮
		->setSearch(['finance_manager.name' => '账户昵称'], '', '', true) // 设置搜索参数
		->setTableName('finance_accmount') // 指定数据表名
		->setExtraJs($js)
		->setRowList($data_list)
		->fetch();
	
 	}
 		public function add(){
	
		if($this -> request -> ispost()){
			$data = $this -> request -> post();
			$r = $this -> Validate($data, 'first_pay');
			if(true !== $r) $this -> error($r);
			Finance_accmountmodel::white(null, $data);
            $this->success('添加成功', 'index');
		}
		$js = <<<EOF
            <script type="text/javascript">
                $(function(){
                   $('#first_money').attr('oninput','return Edit1Change();');				   					
                });
				var j=chineseNumber(document.getElementById("first_money").value);
				document.getElementById("big_money").value=j;		
				function Edit1Change(){			
					document.getElementById("big_money").value=chineseNumber(document.getElementById("first_money").value);
				}
					
            </script>
EOF;

		return ZBuilder::make('form')
		->addFormItems([
		// 批量添加表单项
			['hidden:6', 'operator', UID],
			['select:6', 'name', '账户名称', '', $this -> bankInfo],
			['text:6', 'accmount','账户'],
			['text:6', 'bank','开户银行'],
			['number:6', 'first_money','期初金额'],
			['text:6', 'big_money','金额大写'],
			['text:6', 'zrname','录入人','',get_nickname(UID)],			
//			['select:6', 'operator','录入人','', $this -> user, UID],			
		])
		->setExtraHtml(outhtml2())
		->setExtraJs($js.outjs2())
		->js('chineseNumber')
		->fetch();
	
	}
	public function edit($id = null){
		if($this -> request -> ispost()){
			$data = $this -> request ->post();
			$r = $this -> Validate($data, 'first_pay');
			if(true !== $r) $this -> error($r);
			Finance_accmountmodel::white(['id' => $id], $data);
            $this->success('添加成功', 'index');
		}
		if (null == $id) $this -> error('参数错误');
		$data_list = $this -> model -> where('id', $id) -> find();
		
		return ZBuilder::make('form')
		->addFormItems([
		// 批量添加表单项
			['select:6', 'name', '账户名称', '', $this -> bankInfo],
			['text:6', 'accmount','账户'],
			['text:6', 'bank','开户银行'],
			['number:6', 'first_money','期初金额'],
			['text:6', 'big_money','金额大写'],
			['select:6', 'operator','录入人','',],	
		])
//		-> addSelect('operator', '录入人', '', $this -> user, UID)
		-> setFormData($data_list)
		->fetch();
	
	}
	public function delete($ids = null){
		if(null == $ids) $this -> error('参数错误');
     	return $this->setStatus('delete');
	}
}