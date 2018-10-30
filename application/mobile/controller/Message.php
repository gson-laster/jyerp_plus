<?php
namespace app\mobile\controller;
use app\notice\model\Nuser as NuserModel;
use app\meeting\model\Lists as ListModel;
use app\meeting\model\MeetingUser as MUserModel;
use app\notice\model\Cate as CateModel;
use app\logs\model\Daily as DailyModel;
use app\logs\model\Plan as PlanModel;
use think\Db;
/*
 * 消息控制器*/
class Message extends Base{
	/*
	 
	 * 消息列表页*/
	public function index() {
		$map = [];
		$map['notice_user.uid'] = UID;
		
		//公告数据
		$notice = NuserModel::getLastOne($map, 'notice_user.create_time desc');
		//halt($notice);
		$notice_no_read = NuserModel::where(['is_read' => 0, 'uid' => UID]) -> count();
		//会议数据
		
		//日志计划最后一条
		$planMap = ['uid' => UID];
		$plan = PlanModel::getLastOne($planMap);
		$plan_no_read = PlanModel::where(['uid' => UID, 'status' => 0]) -> count();
		//日志
		$log  = DailyModel::getLastOne($planMap);
		$log_no_read = DailyModel::where(['uid' => UID, 'status' => 0]) -> count();
		//会议
		$meeting = ListModel::getLastOne(UID);
		$meeting_no_read = ListModel::getNoRead(UID);
		//待办流程
	   	    $examine = db::name('flow_log')
                ->alias('l')
                ->field('w.title')
                ->join('flow_work w','l.wid=w.id','left')
                ->where(['l.result'=> 0, 'l.user_id' => UID])
                ->order('w.create_time desc')
                ->limit(1)
                ->find();
		$list = [
			'notice' => [
				'data' => isset($notice['title']) ? $notice['title'] : '暂无...',
				'no_read' => $notice_no_read
			],
			'plan' => [
				'data' => isset($plan['title']) ? $plan['title'] : '暂无...',
				'no_read' => $plan_no_read
			],
			'log' => [
				'data' => isset($log['title']) ? $log['title'] : '暂无...',
				'no_read' => $log_no_read
			],
			'meeting' => [
				'data' => isset($meeting['title']) ? $meeting['title'] : '暂无...',
				'no_read' => $meeting_no_read
			],
			'examine' => [
				'data' => isset($examine['title']) ? $examine['title'] : '暂无...',
				'no_read' => isset($examine['title']) ? 1 : 0,
			]
			
		];
		return $this->fetch('', ['list' => $list]);
		
	}
	
	/*
	 
	 * 消息列表页*/
		
		
	public function notice_lists(){

	
		if($this -> request -> isAjax()){
					$map = $this->getMap();
					$map['notice_user.uid'] = UID;
					// 排序
					$order = $this->getOrder('notice_user.is_read asc,notice_user.create_time desc');
					// 数据列表
					$lists = NuserModel::getList($map,$order);
					$data_list = [];
					foreach ($lists as $key => $value) {
						$status = $value['is_read'] ? '(已阅)' : '(<span style="color: #ff0000">未读</span>)';
						$data_list[$key] = [
							'url'	=>	url('notice_details',['id'=>$value['id']]),
							'top'	=>	'发布时间：'.date('Y-m-d H:i',$value['create_time']).$status,
							'left'	=>	$value['title'],
							'right'	=> $value['cates'],
							'bottom'=>	$value['description']
						];
				}
				return $data_list;
		}
		return $this -> fetch('apply/lists');
		
	}
	/*
	 
	 * 公告类表*/
	public function notice_details($id = null){
		if(is_null($id)) $this -> error('参数错误');
		NuserModel::update(['id'=>$id,'is_read'=>1]);    
		$info = NuserModel::getOne($id);
		$cate = CateModel::getTree();
		$info['cate'] = $cate[$info['cate']];
		$info['create_time'] = date('Y-m-d H:i:s', $info['create_time']);
		$data_list = detaillist([
						['cate','公告类型'],
						['title','标题'],	
            			['description', '公告描述'],
            			['info', '公告详情'],
            			['create_time', '创建时间'],
						['note', '备注'],
					],$info);
		return $this -> fetch('apply/details', ['data_list' => $data_list]);
	}
	
		//设置状态方法
	public function format_status($s='', $e=''){
		$t = time();
		if($t < $s){
			return '<span class="label label-primary">会议未开始</span>';
		} else if ($t > $e ) {
			return '<span class="label label-success">会议已结束</span>';
		} else {
			return '<span class="label label-danger">会议进行中</span>';
		}
	}
	//会议列表
	public function meeting_lists(){
		if($this -> request -> isAjax()){
	      $where = 'u.user_id='.UID;
	      $order =  $this->getOrder();
          $map = $this->getMap();
          $data_list = ListModel::getMeetingList($map, $order, $where);
          $data = [];
          $state = [0 => '(<span style="color:#ff0000">未读</span>)', 1 => '(已读)'];
          foreach($data_list as $key => $value){
          		$states = $this -> format_status($value['s_time'], $value['e_time']);
          		$data[$key] = [
					'url'	=>	url('meeting_details',['id'=>$value['id'], 'rid' => $value['rid']]),
					'top'	=>	'会议日期：'.date('Y-m-d',$value['m_time']).$state[$value['is_read']],
					'left'	=>	$value['title'],
					'right'	=> date('H:i', $value['s_time']).'-'.date('H:i', $value['e_time']),
					'bottom'=>	$states
				];
          }
			//dump($list);die;
			return $data;
		}
		return $this -> fetch('apply/lists');
	}
	
	//会议详情
	
	public function meeting_details($id=null, $rid=null){
		if(empty($id)){
			$this -> error('参数错误');
		}
		$info = ListModel::getOne('l.id='.$id);
		$info['state'] = $this -> format_status($info['s_time'], $info['e_time']);
			$data_list = detaillist([
						['title','会议主题'],
						['s_time','开始时间','datetime'],
						['e_time','结束时间','datetime'],
					    ['title','会议地点'],
						['nickname','主持人'],
						['state','状态'],
					],$info);
					//修改此会议状态为已读
		MUserModel::where(['id' => $rid]) -> update(['is_read' => 1]);
		return $this -> fetch('apply/details', ['data_list' => $data_list]);
	}
	//工作日志
	public function log_list(){
		if($this -> request -> isAjax()){
	    	$map = $this->getMap();
	    	$map['uid'] = UID;
        	// 排序
        	$order = $this->getOrder('personnel_daily.status asc,personnel_daily.daily_time desc');
			$list = DailyModel::getList($map, $order);
			$data = [];
			$state = [0 => '(<span style="color:#ff0000">未阅</span>)', 1 => '(已阅)'];
			$type = [ 0 => '日报', 1 => '周报', 2 => '月报'];
	        foreach($list as $key => $value){
	        	$value['status'] = $state[$value['status']];
	        	$value['type'] = $type[$value['type']];
          		$data[$key] = [
					'url'	=>	url('log_details',['id'=>$value['id']]),
					'top'	=>	$value['daily_time'].$value['status'],
					'left'	=>	$value['title'],
					'right'	=> $value['type'],
					'bottom'=>	$value['positions']
				];
          	}
			//dump($list);die;
			return $data;
		}
		return $this -> fetch('apply/lists');
	}
	
	public function log_details($id = null){
		if(empty($id)){
			$this -> error('参数错误');
		}
		DailyModel::update(['id'=>$id,'status'=>1]);
		$info = DailyModel::getOne($id);
		$type = ['0'=>'日报','1'=>'周报','2'=>'月报'];
		$info['type'] = $type[$info['type']];
			$data_list = detaillist([
    			['type','日志类型'],
    			['nickname','姓名'],
    			['organizations', '部门'],
            	['positions', '职位'],        			
    			['title','标题'],
    			['daily_time', '报告时间'],
    			['info', '报告详情'],
    			['note', '备注'],
    		],$info);
		return $this -> fetch('apply/details', ['data_list' => $data_list]);
	}
	
	//工作计划
	public function plan_list(){
		
		if($this -> request -> isAjax()){
	    	$map = $this->getMap();
	    	$map['uid'] = UID;
        	// 排序
        	$order = $this->getOrder();
        	$order['personnel_plan.status'] = 'asc';
        	$order['personnel_plan.plan_time'] = 'desc';
			$list = PlanModel::getList($map, $order);
			$data = [];
			$state = [0 => '(<span style="color:#ff0000">未阅</span>)', 1 => '(已阅)'];
			$type = [ 0 => '日计划', 1 => '周计划', 2 => '月计划'];
	        foreach($list as $key => $value){
	        	$value['status'] = $state[$value['status']];
	        	$value['type'] = $type[$value['type']];
          		$data[$key] = [
					'url'	=>	url('plan_details',['id'=>$value['id']]),
					'top'	=>	$value['plan_time'].$value['status'],
					'left'	=>	$value['title'],
					'right'	=> $value['type'],
					'bottom'=>	$value['positions']
				];
          	}
			//dump($list);die;
			return $data;
		}
		return $this -> fetch('apply/lists');
	
	}
	
	public function plan_details($id = null){
		if(empty($id)){
			$this -> error('参数错误');
		}
		PlanModel::update(['id'=>$id,'status'=>1]);
		$info = PlanModel::getOne($id);
		$type = [ 0 => '日计划', 1 => '周计划', 2 => '月计划'];
		$state = [0 => '未阅', 1 => '已阅'];
		$info['type'] = $type[$info['type']];
		$info['status'] = $state[$info['status']];
			$data_list = detaillist([
            	['nickname', '姓名'],
            	['organizations', '部门'],
            	['positions', '职位'],            	
            	['title', '标题'],
            	['plan_time', '计划时间'],  
            	['create_time', '创建时间','datetime'],
            	['status', '查阅状态'],
            	['info', '计划详情'],
    		],$info);
		return $this -> fetch('apply/details', ['data_list' => $data_list]);
	}
}
?>