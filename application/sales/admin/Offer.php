<?php
	
namespace app\sales\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\sales\model\Opport as OpportModel;
use app\sales\model\Offer as OfferModel;
use app\user\model\Organization as OrganizationModel;
use app\task\model\Task_detail as Task_detailModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use think\Db;
/**
 * 任务控制器
 * @author HJP
 */
class Offer extends Admin
{	
	//销售机会
	public function index(){
		// 获取查询条件
		$map = $this->getMap();
		// 排序
		$order = $this->getOrder('sales_offer.create_time desc');
		// 数据列表
		$data_list = OfferModel::getList($map,$order);
		// 分页数据
		$page = $data_list->render();
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-search',
			'href' => url('task_list',['id'=>'__id__'])
		];
		return ZBuilder::make('table')
		->setPageTitle('报价列表')
		->setSearch(['sales_offer.name' => '报价名称', 'customer' => '客户名称'], '', '', true) // 设置搜索参数
		->addColumns([
			['code','编号'],
			['name','报价名称'],
			['monophycode','单源号'],
			['customer','客户名称'],
			['phone','客户联系方式(手机)'],			
			['document_time','报价时间','date'],
			['zrid','业务员'],		
			['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],			
			['right_button','操作','btn'],
		])
		->addOrder(['code','document_time']) // 添加排序
		->addTopButtons(['delete'])//添加顶部按钮
		->addRightButtons(['delete' => ['data-tips' => '删除报价将无法恢复。']])
		->addRightButton('task_list',$task_list,true) // 查看右侧按钮 
		->setRowList($data_list)//设置表格数据
		->fetch();
	}
	//添加销售机会
	public function add(){
		if($this->request->isPost()){
			$data = $this->request->post();
			// 验证
			$result = $this->validate($data, 'Offer');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			$data['code'] = 'XSBJ'.date('YmdHis',time());
            $data['document_time'] = strtotime($data['document_time']);
            $data['end_time'] = strtotime($data['end_time']);
            $data['zdid'] = UID;
			//查看人员，隔开
			$data['helpid'] = ','.$data['helpid'];
			if($model = OfferModel::create($data)){
				flow_detail($data['name'],'sales_offer','sales_offer','sales/offer/task_list',$model['id']);
				//记入行为
				
				$this->success('新增成功！',url('index'));
			}else{
				$this->error('新增失败！');
			}
		}
		$date = date('Y-m-d');
		return Zbuilder::make('form')
		->addFormItems([
			['hidden','zrid',UID],
			['hidden','zdid',UID],
			['hidden','helpid'],
			['text:4','name','报价名称'],
			['linkage:4','monophyletic','源单类型','',[0=>'无来源',1=>'销售机会'],'',url('get_yd'),'monophycode'],
			['select:4','monophycode','源单号'],
			['text:4','customer_name','客户名称','','','','disabled'],
			['text:4','phone','客户联系方式(手机)','','','','disabled'],
			['select:4','paytype','支付方式','',[-2=>'转账',-1=>'支付宝',0=>'微信',1=>'支票',2=>'现金']],
			['select:4','goodtype','交货方式','',[0=>'一次性交货',1=>'分批交货']],
			['select:4','transport','运送方式','',[-1=>'空运',0=>'海运',1=>'快递']],
			['select:4','currency','币种','',[-1=>'美元',0=>'人民币',1=>'欧元']],
			['number:4','parities','汇率%'],
			['text:4','zrname','业务员','',get_nickname(UID)],
			['select:4','department','所属部门','', OrganizationModel::getMenuTree2()],	
			['date:4','document_time','报价日期','',$date],
			['date:4','end_time','截止日期'],	
			['static:4','zdname','制单人','',get_nickname(UID)],	
			['static:4','create_time','制单时间','',$date],
			['textarea:6','helpname','可查看人员'],	
			['files','file','附件'],			
			['textarea','note','备注'],						
		])
		->setExtraHtml(outhtml2())
		->setExtraJs(outjs2())
		->js('Offer')
		->fetch();
	}
	public function get_Detail($monophycode = ''){
			$data = OfferModel::get_Detail($monophycode);
		return $data;
	}
	public function edit($id = null){
		if($id == null) $this->error('参数错误');
		if($this->request->isPost()){
			$data = $this->request->post();
			// 验证
			$result = $this->validate($data, 'Offer');
			// 验证失败 输出错误信息
			if(true !== $result) $this->error($result);
			if($model = OfferModel::update($data)){
				//记录行为
				
				return $this->success('修改成功',url('index'));
			}else{
				return $this->error('修改失败');
			}
		}
		$info = OfferModel::where('id',$id)->find();
		//获取昵称
			$nickname = Task_detailModel::get_nickname();
			$zrid = $info['zrid'];
			$helpid = $info['helpid'];
			$helpmane = Task_detailModel::get_helpname($helpid);
			$customer_name = OpportModel::customer_name();
		$phone = OpportModel::get_phone();
		$date = date('Y-m-d');
		return ZBuilder::make('form')
		->addFormItems([
			['hidden', 'id'],
			['hidden','zrid'],
			['hidden','helpid'],
			['text:6','name','报价名称'],
			['select:6','monophyletic','报价来源','',OpportModel::get_monophyletic()],
			['text:6','customer_name','客户名称'],
			['select:6','customer_name1','客户名称1','',$customer_name,'','','hidden'],
			['number:6','phone','客户联系方式(手机)'],
			['select:6','phone1','客户联系方式1','',$phone,'','','hidden'],
			['select:6','paytype','支付方式','',[-1=>'支付宝',0=>'微信',1=>'银行卡']],
			['select:6','goodtype','交货方式','',GoodModel::get_good()],
			['select:6','transport','运送方式','',TransportModel::get_Transport()],
			['date:6','document_time','报价日期','',$date],
			['text:6','zrname','业务员','',$nickname[$zrid]],
			['number:6','money','总金额(元)'],
			['number:6','tax','总税(元)'],
			['select:6','department','所属部门','', OrganizationModel::getMenuTree2()],	
			['date:6','end_time','截止日期'],		
			['textarea:6','helpname','可查看人员','',$helpmane],			
			['textarea','note','备注'],
			['radio','status','状态', '', ['禁用', '启用'], 1],	
		])
		->setExtraHtml(outhtml2())
		->setExtraJs(outjs2())
		->setFormData($info)
		->js('test')
		->fetch();
	}
	public function delete($record = [])
    {
   		$ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    	// 删除节点
    	if (OfferModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		//$details = '生产任务ID('.$ids.'),操作人ID('.UID.')';
    		//action_log('produce_plan_delete', 'produce_plan', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }
	   public function get_yd($monophyletic = '')
    {
        if($monophyletic==0){
            $list= ['0'=>'无']; 
        }elseif($monophyletic==1){
            $list = OpportModel::getName();
        }
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        foreach ($list as $key => $value) {
             $arr['list'][] = ['key'=>$key,'value'=>$value];
        }
        return json($arr);
    }
    //查看
	public function task_list($id = null){
		if($id == null) $this->error('参数错误');
		$info = OfferModel::getOne($id);
        $info->document_time = date('Y-m-d',$info['document_time']);
        $info->create_time = date('Y-m-d',$info['create_time']);
        $info->end_time = date('Y-m-d',$info['end_time']);
		$info['helpname'] = Task_detailModel::get_helpname($info['helpid']);
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addFormItems([
			['hidden', 'id'],
			['static:4','name','报价名称'],
			['linkage:4','monophyletic','源单类型','',[0=>'无来源',1=>'销售机会']],
			['static:4','monophycode','源单号'],
			['static:4','customer','客户名称'],
			['static:4','phone','客户联系方式(手机)'],
			['select:4','paytype','支付方式','',[-2=>'转账',-1=>'支付宝',0=>'微信',1=>'支票',2=>'现金']],
			['select:4','goodtype','交货方式','',[0=>'一次性交货',1=>'分批交货']],
			['select:4','transport','运送方式','',[-1=>'空运',0=>'海运',1=>'快递']],
			['select:4','currency','币种','',[-1=>'美元',0=>'人民币',1=>'欧元']],
			['static:4','parities','汇率%'],
			['static:4','zrname','业务员'],
			['static:4','bm','所属部门'],	
			['static:4','document_time','报价日期'],
			['static:4','end_time','截止日期'],
			['static:4','create_time','制单时间'],
			['static:4','zdid','制单人'],
			['static','helpname','可查看人员'],			
			['archives','file','附件'],		
			['static','note','备注'],			
		])
		->setFormData($info)
		->fetch();
	}
}
