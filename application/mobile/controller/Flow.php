<?php
namespace app\mobile\controller;
use app\sales\model\Contract as ContractModel;
use app\flow\model\Itemdetail as ItemdetailModel;
use app\flow\model\Itemdetailstep;
use app\admin\model\Module as ModuleModel;
use app\user\model\User;
use think\Db;

/*
 
 * 联系人控制器*/
class Flow extends Base{
	
	//我的申请列表
	public function myflow($keywords = '') {

		if($this->request->isAjax()) {

			$map = 'locate("'.$keywords.'", `flow_itemdetail`.`title`)>0 AND flow_itemdetail.uid='.UID;
			$lists = ItemdetailModel::getList($map,'flow_itemdetail.id desc');

			$list_module = ModuleModel::getModule();
			$data_list = [];
			foreach ($lists as $key => &$value) {
	        	switch ($value['step']) {
	        		case 20:
	        			$value['step'] = '进行中';
	        			$remark = '';
	        			break;
	        		case 30:
	        			$value['step'] = '否决';
	        			break;
	        		case 40:
	        			$value['step'] = '同意';
	        			break;
	        	}
				$data_list[$key] = [
					'url'	=>	url('flowdetail',['id'=>$value['id']]),
					'top'	=>	'发起时间：'.date('Y-m-d',$value['create_time']),
					'left'	=>	$value['title'],
					'right'	=>	'所属：'.isset($list_module[$value['module']]) ? $list_module[$value['module']] : '',
					'bottom'=>	'最后审批时间：'.date('Y-m-d',$value['update_time']).'    结果：'.$value['step'],
				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');
		
	}
    //代办流程
    public function handletask($keywords = ''){

    	if($this->request->isAjax()) {
			$map = 'locate("'.$keywords.'", `flow_itemdetail`.`title`)>0 AND flow_itemdetail_step.user_id='.UID.' AND flow_itemdetail_step.result=0';

            $lists = ItemdetailModel::getMyflow($map,'flow_itemdetail_step.id desc');
            $list_module = ModuleModel::getModule();
            $data_list = [];
			foreach ($lists as $key => &$value) {
	        	switch ($value['step']) {
	        		case 20:
	        			$value['step'] = '进行中';
	        			$remark = '';
	        			break;
	        		case 30:
	        			$value['step'] = '否决';
	        			break;
	        		case 40:
	        			$value['step'] = '同意';
	        			break;
	        	}
				$data_list[$key] = [
					'url'	=>	url('flowban',['id'=>$value['id']]),
					'top'	=>	'发起时间：'.date('Y-m-d H:i',$value['ctime']),
					'left'	=>	$value['wtitle'],
					'right'	=>	'所属：'.isset($list_module[$value['module']]) ? $list_module[$value['module']] : '',
					'bottom'=>	'最后审批时间：'.date('Y-m-d H:i',$value['utime']).'&nbsp;&nbsp;'.$value['step'],
				];
			}
			return $data_list;
		}
			
		return $this->fetch('apply/lists');


    }
	//已办流程
    public function handletask_ok($keywords = ''){

    	if($this->request->isAjax()) {
			$map = 'locate("'.$keywords.'", `flow_itemdetail`.`title`)>0 AND flow_itemdetail_step.user_id='.UID.' AND flow_itemdetail_step.result>0';
            $lists = ItemdetailModel::getMyflow($map,'flow_itemdetail_step.id desc');
            $list_module = ModuleModel::getModule();
			foreach ($lists as $key => &$value) {
	        	switch ($value['step']) {
	        		case 20:
	        			$value['step'] = '进行中';
	        			$remark = '';
	        			break;
	        		case 30:
	        			$value['step'] = '否决';
	        			break;
	        		case 40:
	        			$value['step'] = '同意';
	        			break;
	        	}
				$data_list[$key] = [
					'url'	=>	url('flowdetail',['id'=>$value['id']]),
					'top'	=>	'发起时间：'.date('Y-m-d H:i',$value['ctime']),
					'left'	=>	$value['wtitle'],
					'right'	=>	'所属：'.isset($list_module[$value['module']]) ? $list_module[$value['module']] : '',
					'bottom'=>	'最后审批时间：'.date('Y-m-d H:i',$value['utime']).'&nbsp;&nbsp;'.$value['step'],
				];
			}
			return isset($data_list) ? $data_list : '';
		}
		return $this->fetch('apply/lists');

    }

	//审批详情
	public function flowdetail($id='') {

		if($id==null)$this->error('没有此流程');
		$info = ItemdetailModel::getOne($id);

		//审批记录
		$log = '';
        $item_list = Itemdetailstep::where('itemdetail_id',$info->id)->order('id asc')->select();

        foreach ($item_list as $key => &$value) {

        	$remark = '备注：'.($value['remark'] ? $value['remark'] : '').'<br><br>';
        	switch ($value['result']) {
        		case 0:
        			$value['result'] = '暂未审批';
        			$remark = '';
        			break;
        		case 1:
        			$value['result'] = '同意';
        			break;
        		case 2:
        			$value['result'] = '否决,审批结束';
        			break;
        	}
            
            $log .= get_nickname($value['user_id']).' '.$value['result'].'<br>'.$remark;

            

        }
        $info->log = $log;

		$data_list = detaillist([
				['title','审批标题'],
				['flow_title','流程名称'],
				['uid','申请人','user'],
				['create_time','发起时间','datetime'],
				['update_time','最后审批','datetime'],
				['step', '审批结果',[20 =>'进行中', 30=>'否决', 40=>'同意']],
				['log','审批日志'],
			],$info);

		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details');
	}

		//审批详情
	public function flowban($id='') {
		if($id==null)$this->error('没有此流程');
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['id'] = Itemdetailstep::where(['itemdetail_id'=>$id,'result'=>0])->value('id');
            $data['update_time'] = time();
            $uid = Itemdetailstep::where('id',$data['id'])->value('user_id');
            if(UID!=$uid){
                 $this->error('请审批人审批');           
            }
            if(Itemdetailstep::update($data)){
                if($data['result']==1){
                    next_step($id,Itemdetailstep::where('id',$data['id'])->value('step'));
                }else{
                    ItemdetailModel::update(['id'=>$id,'step'=>30]);
                    $thisflow = ItemdetailModel::where('id',$id)->field('trigger_id,table')->find();
                    db::name($thisflow['table'])->where('id',$thisflow['trigger_id'])->update(['status'=>2]);
                }
                ItemdetailModel::update(['id'=>$id,'update_time'=>time()]);
                $this->success('审批成功',url('handletask'));

            }else{
                $this->error('审批失败');
            }
        }

		$info = ItemdetailModel::getOne($id);
		//审批记录
		$log = '';
        $item_list = Itemdetailstep::where('itemdetail_id',$info->id)->order('id asc')->select();

        foreach ($item_list as $key => &$value) {

        	$remark = '备注：'.($value['remark'] ? $value['remark'] : '').'<br><br>';
        	switch ($value['result']) {
        		case 0:
        			$value['result'] = '暂未审批';
        			$remark = '';
        			break;
        		case 1:
        			$value['result'] = '同意';
        			break;
        		case 2:
        			$value['result'] = '否决,审批结束';
        			break;
        	}
            
            $log .= get_nickname($value['user_id']).' '.$value['result'].'<br>'.$remark;

            

        }
        $info->log = $log;

		$data_list = detaillist([
				['title','审批标题'],
				['flow_title','流程名称'],
				['uid','申请人','user'],
				['create_time','发起时间','datetime'],
				['update_time','最后审批','datetime'],
				['step', '审批结果',[20 =>'进行中', 30=>'否决', 40=>'同意']],
				['log','审批日志'],
			],$info);

		$this->assign('data_list',$data_list);
		return $this->fetch();
	}


}
