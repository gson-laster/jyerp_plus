<?php
namespace app\finance\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\finance\model\Finance_receipts as Finance_receiptsmodel;
//use app\finance\model\Admin_user as user;
use app\tender\model\Obj as IModel;
use think\Db;
use think\Config;
use app\contract\model\Income;

/*
 *合同收款
 * */
 class Receipts extends Admin {
	protected $manager;
	protected $bankInfo;
	protected $bankMinInfo;
	protected $gathering_type; // 支付方式
	protected $user;
    protected function _initialize(){
        parent::_initialize();
        $this -> gathering_type = Config::get('pay_type');
//      $this -> model = new Finance_receiptsmodel();
        $this -> user = Db::name('admin_user') -> column('id,nickname');
    }
 	public function index(){
		$map = $this->getMap();
		$order = $this->getOrder();
		
		
		$data_list = Finance_receiptsmodel::getList($map,$order);
		/*
		 *总计
		 */
		$arr = ['gathering' => 0];
		foreach($data_list as $v){
			$arr['gathering'] += $v['gathering'];
		}
			$js = <<<EOF
			   <script type="text/javascript">
                $(function(){
                    $('tbody').append('<tr style="color:#ff0000"><td align="center">总计</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>{$arr['gathering']}</td><td>-</td><td>-</td></tr>');
                });
            </script>
EOF;
     $task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('edit',['id'=>'__id__'])
		];
		
		
			
		return ZBuilder::make('table')
			->addOrder('date,name,gathering')
			->addColumns([ // 批量添加列
                    ['id', '编号'],
					[ 'date', '日期', 'date'],
					[ 'number', '收款编号'],
					[ 'name','收款名称'],
					[ 'item','项目'],
					[ 'title','合同名称'],
					[ 'money','合同金额'],
					[ 'nail','甲方单位'],			
					[ 'gathering_type','收款类型', $this -> gathering_type],			
					[ 'fine','罚款'],			
					[ 'withhold','扣款'],			
					[ 'gathering','收款金额'],													
					[ 'operator','填报人', $this -> user],			
					['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
					['right_button', '操作','btn'],
			])
			->addTopButton('add') // 添加顶部按钮
			->setPageTitle('合同收款')
			->addRightButtons('delete')	
			->addRightButton('edit',$task_list,true)
			->addFilter('gathering_type',['按进度付款','按合同付款'])
			->addFilter('finance_receipts.status',['进行中','同意','否决'])
			->addTopButton('delete') // 添加顶部按钮
			->setTableName('Finance_receipts') // 指定数据表名
			->setSearch(['contract_income.title' => '合同名称','tender_obj.name' => '项目'], '', '', true) // 设置搜索参数
			->addTimeFilter('finance_receipts.date') // 添加时间段筛选
			->setExtraJs($js)
			->setRowList($data_list)
			->fetch();	
 	}
 		public function add(){		
		if($this -> request -> ispost()){
			$data = $this -> request ->post();
			$r = $this -> Validate($data, 'receipts');
			if(true !== $r) $this -> error($r);
			$data['date'] = strtotime($data['date']);
			$data['number'] = 'HTSK'.date('Ymdhis',time()).UID;			
		 if ($res = Finance_receiptsmodel::create($data)) {
            flow_detail($data['name'],'finance_receipts','finance_receipts','finance/receipts/edit',$res['id']);
                $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }
        }
		
		$get_item = IModel::get_nameid();
		
		$js = <<<EOF
            <script type="text/javascript">
                $(function(){
                   $('#gathering').attr('oninput','return Edit1Change();');				   					
                });
				var j=chineseNumber(document.getElementById("gathering").value);
				document.getElementById("big_money").value=j;		
				function Edit1Change(){			
					document.getElementById("big_money").value=chineseNumber(document.getElementById("gathering").value);
				}
					
            </script>
EOF;
		
		return ZBuilder::make('form')
		->addFormItems([
		// 批量添加表单项
			['hidden', 'operator', UID],
			['date:3', 'date', '日期','', date('Y-m-d', time())],
			['text:3', 'name','收款名称'],
			['linkage:3', 'item','项目', '', $get_item,'',url('get_title'),'title'],
			['select:3', 'title','合同名称'],
			['text:3', 'money','合同金额','','','','disabled'],
			['text:3', 'nail','甲方单位','','','','disabled'],			
			['select:3', 'gathering_type','收款类型', '', $this -> gathering_type],			
			['number:3', 'fine','罚款'],			
			['number:3', 'withhold','扣款'],			
			['number:3', 'gathering','收款金额'],	
			['text:3', 'big_money','金额大写'],							
			['text:3', 'zrid','填报人', '',get_nickname(UID)],			
			['textarea:12', 'note','备注'],			
		])
		->setExtraHtml(outhtml2())
		->setExtraJs($js.outjs2())
		->js('chineseNumber')
		->js('Receipts')
		->fetch();
	
	}
	
	
	 public function get_title($item = '')
    {
    	  
    		$res= Income::name('contract_income')->where('attach_item',$item)->where('status',1)->select();
        //dump($res);die;
        $array =array();
        foreach($res as $key=>$val){
            $array[] = ['key'=>$val['id'],'value'=>$val['title']];
        }
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        $arr['list'] =$array; //数据
        return json($arr);
    }
	
	
	public function getDetail($title = ''){
			$data = Finance_receiptsmodel::getDetail($title);
			//dump($data);die;
		return $data;
	}
	
	
	
	public function edit($id = null){
		if($this -> request -> ispost()){
			$data = $this -> request ->post();
			$r = $this -> Validate($data, 'receipts');
			if(true !== $r) $this -> error($r);
			$data['date'] = strtotime($data['date']);
			Finance_receiptsmodel::white(['id' => $id], $data);
            $this->success('添加成功', 'index');
		}
		if (null == $id) $this -> error('参数错误');
		$data_list = Finance_receiptsmodel::one(['id' => $id]);
		$data_list['date'] = date('Y-m-d', $data_list['date']);
		$data_list['gathering_type'] = $this -> gathering_type[$data_list['gathering_type']];
		
		return ZBuilder::make('form')
		->addFormItems([
		// 批量添加表单项
			['static:3', 'date', '日期'],
//			['static:3', 'number', '收款编号'],
			['static:3', 'name','收款名称'],
			['static:3', 'item','项目'],
			['static:3', 'title','合同名称'],
			['static:3', 'money','合同金额'],
			['static:3', "big_money", '大写金额'],
			['static:3', 'nail','甲方单位'],			
			['static:3', 'gathering_type', '收款类型'],			
			['static:3', 'fine','罚款'],			
			['static:3', 'withhold','扣款'],			
			['static:3', 'gathering','收款金额'],			
			
			['static:3', 'nickname','填报人'],			
			['static:12', 'note','备注'],
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