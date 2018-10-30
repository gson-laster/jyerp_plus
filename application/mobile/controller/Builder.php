<?php
namespace app\mobile\controller;
use app\notice\model\Cate as CateModel; //通知分类
use app\mobile\model\Builder as Bmodel;  //
use app\notice\model\Nlist as NlistModel; //通知
use app\notice\model\Nuser as NuserModel;
//use app\task\model\Task_detail;  //
use app\meeting\model\Lists as ListModel; //会议
use think\Db; //获取部门
use app\logs\model\Daily as DailyModel; //日志

use app\logs\model\Plan as PlanModel;	//计划

use app\finance\model\Finance as FinanceModel; //费用报销

use app\tender\model\Obj;

use app\user\model\Position; //职位

use app\user\model\User as UserModel;

use app\meeting\model\MeetingUser as MUserModel;
/*
 
 * 新建控制器*/
class Builder extends Base{
	//菜单列表
	public function index() {
		
		return $this -> fetch();
		
	}
	
	/*
	 
	 * 新建公告*/
	
	public function build_notice(){
		
	
		 // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
//          if($data['cate'] !=1){
//          	if(!$data['to_user']) $this->error('通知单位不能为空！');	            
//          } 
            $data['uid'] = UID; 
            // 验证
            $result = $this->validate($data, 'BuildNotice');
            if (true !== $result) $this->error($result);
			
			if($data['cate'] == 2) {
				$user_list = Db::name('admin_user') -> where(['organization' => Db::name('admin_user') -> where(['id' => UID]) -> value('organization')]) -> column('id,nickname');
				$data['to_user'] = implode(',', array_keys($user_list));
				$data['noticer'] = implode(',', $user_list);
			}
            
            if ($notice = NlistModel::create($data)) {
            	$this -> release($notice['id']);
                // 记录行为
            	$details    = '详情：用户ID('.UID.'),公告ID('.$notice['id'].')';
                action_log('notice_list_add', 'notice_list', $notice['id'], UID, $details);
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }
        //分类
		$cate = CateModel::getTree([]);
		//所有人员
		$staff = Db::name('admin_user') -> alias('a') -> join('admin_organization o', 'a.organization=o.id', 'left')  -> order('a.nickname') -> where(['a.status' => 1]) -> column('a.id,a.nickname,o.title');
		//$list = Task_detail::getTree([]);
	
		return $this -> fetch('', ['cate' => $cate, 'staff' => $staff]);
	
	}
	
	
	//公告发布
	
	    public function release($id = null)
    {
    	if($id == null) $this->error('缺少参数');
		$notice =  NlistModel::get($id); 

		if($notice['cate'] == 1){
			$user = UserModel::where(['status'=>1])->select();			
			foreach ($user as $k => $v){
				$data = [
						'lid'=>$id,
						'uid'=>$v['id'],
						'cate'=>$notice['cate']
				];
				NuserModel::create($data);
			}
		}else if($notice['cate'] == 2){
			$user = UserModel::where(['status'=>1,'organization'=>['in',$notice['to_user']]])->select();
			foreach ($user as $k => $v){
				$data = [
						'lid'=>$id,
						'uid'=>$v['id'],
						'cate'=>$notice['cate']
				];
				NuserModel::create($data);
			}
		}else if($notice['cate'] == 3){
			$user = explode(',', $notice['to_user']);
			foreach ($user as $k => $v){
				$data = [
						'lid'=>$id,
						'uid'=>$v,
						'cate'=>$notice['cate']
				];
				NuserModel::create($data);
			}
		}else{
			$user = explode(',', $notice['to_user']);
			foreach ($user as $k => $v){
				$data = [
						'lid'=>$id,
						'uid'=>$v,
						'cate'=>$notice['cate']
				];
				NuserModel::create($data);
			}			
		}
		
		if (NlistModel::update(['id'=>$id,'status'=>1])) {
			// 记录行为
			$details = '发布公告，公告ID('.$id.')';
    		action_log('notice_list_release', 'notice_list', $id, UID, $details);
    		$this->success('发布成功');
		} else {
			$this->error('发布失败');
		}
		
    	
    }
	
	//新建会议
	public function build_meeting(){
		if($this -> request -> isPost()){
			$data = $this -> request -> post();
			$data['s_time'] = strtotime($data['m_time']." ".$data['s_time']);
			$data['e_time'] = strtotime($data['m_time']." ".$data['e_time']);
			$data['m_time'] = strtotime($data['m_time']);
	        $result = $this->validate($data, 'build_meeting');
	        $item_id = explode(',', $data['user_id']);
	        $data['user_id'] = implode(',', Db::name('admin_user') -> where(['status' => 1]) -> where('organization', 'in', $item_id) -> column('id'));
            if (true !== $result) $this->error($result);
			$model = ListModel::create($data);
			if($model){
				$m = $this -> meeting_user($model['id']);
				if($m){
					$this -> success('新增成功', 'index');
				} else {
					$this -> success('新增失败');
				}
				
			} else {
				$this -> success('新增失败');
			}
			
		}
		$orginization = Db::name('admin_organization') -> where(['status' => 1]) -> column('id,title');
		$staff = Db::name('admin_user') -> alias('a') -> join('admin_organization o', 'a.organization=o.id', 'left')  -> order('a.nickname') -> where(['a.status' => 1]) -> column('a.id,a.nickname,o.title');
		
		return $this -> fetch('', ['staff' => $staff, 'orginization' => $orginization]);
	}
	
	public function meeting_user($id = null){
    	if(is_null($id)) $this -> error('新增失败');
    	$data = ListModel::where('id='.$id) -> field('user_id') -> find();
    	if($data && !empty($data['user_id'])){
	    	$user_id = explode(',', $data['user_id']);
	    	
	    	$data_list = [];
	    	$time = time();
	    	foreach($user_id as $v){
	    		$arr = [
	    			'meeting_id' => $id,
	    			'user_id'    => $v,
	    			'create_time' => $time,
	    			'update_time' => $time,
	    		];
	    		array_push($data_list, $arr);
	    	}
	    	$um = new MUserModel;
	    	$m = $um -> insertAll($data_list);
	    	return $m;
    	} else {
    		return 1;
    	}
    }
	//工作日志
	public function log_book(){
		if($this -> request -> isPost()) {
			$data = $this -> request -> post();
			$data['uid'] = UID;
			$data['oid'] = Db::name('admin_user') -> where('id', UID) -> value('organization');
			$r = $this -> validate($data, 'LogBook');
			if(true !== $r) $this -> error($r);
			
			
			if($model = DailyModel::create($data)){
				$this -> success('添加成功', 'index');
			} else {
				$this -> error('添加失败');
			}
		}
		
		return $this -> fetch();
	}
	
	//工作计划
	
	public function work_plan(){
		
		if($this -> request -> isPost()) {
			$data = $this -> request -> post();
			$data['uid'] = UID;
			$data['oid'] = Db::name('admin_user') -> where('id', UID) -> value('organization');
			$r = $this -> validate($data, 'WorkPlan');
			if(true !== $r) $this -> error($r);
			
			
			if($model = PlanModel::create($data)){
				$this -> success('添加成功', 'index');
			} else {
				$this -> error('添加失败');
			}
		}
		
		
		return $this -> fetch();
	}
	
	//费用报销
	
	public function Reimbursement(){
	    if ($this->request->isPost()) {
            $data = $this->request->post();
            // 验证
			$data['maker'] = UID;
			$data['time'] = date('Y-m-d', time());
//            dump($data);die;
            $result = $this->validate($data, 'receipts');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            $data['number'] = 'FYBX'.date('YmdHis',time());
            if ($res = FinanceModel::create($data)) {
            	flow_detail($data['title'],'finace_reimburse','finance_info','finance/reimburse/edit',$res['id']);
               $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }
        }
		$staff = Db::name('admin_user') -> alias('a') -> join('admin_organization o', 'a.organization=o.id', 'left')  -> order('a.nickname') -> where(['a.status' => 1]) -> column('a.id,a.nickname,o.title');
		$orginization = Db::name('admin_organization') -> where(['status' => 1]) -> column('id,title');
		$Obj = Obj::getaname();
		$apply_subject = Config('apply_subject');
		return $this -> fetch('', ['staff' => $staff, 'organization' => $orginization, 'obj' => $Obj, 'apply_subject' => $apply_subject, 'Position' => Position::getTree()]);
	}
	
}
?>