<?php
namespace app\mobile\controller;
use app\personnel\model\Record as RecordModel;
use app\personnel\model\Papers as PapersModel;
use app\personnel\model\Award as AwardModel;
use app\personnel\model\Care as CareModel;
use app\personnel\model\Wage as WageModel;
use app\personnel\model\Work as WorkModel;
use app\personnel\model\Recruit as RecruitModel;
use app\user\model\User as UserModel;
use  app\user\model\User;

/*
 
 * 人事控制器*/
class Personnel extends Base{
	//人事档案列表
	public function record($keywords='') {
		if($this->request->isAjax()) {	
		
			$lists = RecordModel::getList('locate("'.$keywords.'",`admin_user`.`nickname`)>0 OR locate("'.$keywords.'",`admin_organization`.`title`)>0','personnel_record.create_time desc');
			$data_list = [];
			foreach ($lists as $key => $value) {				
				$data_list[$key] = [
					'url'	=>	url('recorddetail',['id'=>$value['id']]),
					'top'	=>	'制单时间：'.$value['create_time'],
					'left'	=>	$value['nickname'],
					'right'	=>	$value['title'],
					'bottom'=>	'入职时间：'.$value['in_time']
				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');		
	}
	//详情
	public function recorddetail($id='') {
		if($id==null)$this->error('没有此档案');
		$info = RecordModel::getOne($id);
		$data_list = detaillist([
				['avatar', '头像','img'],
				['username', '用户名'],
				['nickname', '昵称'],
				['role1', '角色'],
				['organization1','部门'],
				['position1', '职位'],
				['email', '邮箱'],		               
				['mobile', '手机号'],				
				['in_time', '入职时间'],
				['zz_time','转正时间'],
				['hobby','兴趣爱好'],
				['is_on', '在职状态', ['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职']],
			],$info);
		$this->assign('data_list',$data_list);		
		return $this->fetch('apply/details');
	}
	//证件列表
	public function papers($keywords='') {
		if($this->request->isAjax()){
			$lists = PapersModel::getList('locate("'.$keywords.'",`admin_user`.`nickname`) OR locate("'.$keywords.'",`personnel_papercat`.`title`)','personnel_papers.create_time desc');
			$data_list = [];
			foreach($lists as $key => $value){
				$data_list[$key] = [
					'url'   =>   url('papersdetail',['id'=>$value['id']]),
					'top'   =>   '制单时间：'.date('Y-m-d',$value['create_time']),
					'left'  =>   $value['nickname'],
					'right' =>   '证件类型：'.$value['paper_type1'],
					'bottom'=>   '生效日期：'.$value['start_time'].'——'.$value['end_time']
				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');		
	}
	public function papersdetail($id=''){
		if($id==null)$this->error('没有此证件');
		$info = PapersModel::getOne($id);
		$data_list = detaillist([
				['username', '用户名'],
				['nickname', '昵称'],
				['paper_type1', '证件类型'],
				['paper_code','证件编码'],
				['paper_organization','发证机构'],
				['start_time', '生效日期'],
				['end_time', '到期日期'],		               
				['paper_time', '取证日期'],				
				['code', '备注'],
			],$info);
		$this->assign('data_list',$data_list);		
		return $this->fetch('apply/details');
	}
	//奖惩
	public function award($keywords=''){
		if($this->request->isAjax()){
			$lists = AwardModel::getList('locate("'.$keywords.'", `admin_user`.`nickname`) OR locate("'.$keywords.'", `personnel_awardcate`.`name`)','personnel_award.create_time desc');
			$data_list = [];
			foreach($lists as $key => $value){
				$data_list[$key] = [
					'url'   =>   url('awarddetail',['id'=>$value['id']]),
					'top'   =>   '制单时间：'.date('Y-m-d',$value['create_time']),
					'left'  =>   $value['nickname'],
					'right' =>   '奖惩项目：'.$value['award_cate1'],
					'bottom'=>   '奖惩日期：'.$value['award_time']
				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');		
	}
	public function awarddetail($id=''){
		if($id==null)$this->error('没有此奖惩');
		$info = AwardModel::getOne($id);
		$data_list = detaillist([
				['username', '用户名'],
				['nickname', '昵称'],
				['award_type', '奖惩类型',['1'=>'慰问 ','2'=>'惩罚','3'=>'奖励']],
				['award_cate1','奖惩项目'],
				['money','奖励金额'],
				['good', '奖励物品'],
				['award_time', '奖惩日期'],		               				
				['code', '备注'],
			],$info);
		$this->assign('data_list',$data_list);		
		return $this->fetch('apply/details');
	}
	//关怀
	public function care($keywords = ''){
		if($this->request->isAjax()){
			$lists = CareModel::getList('locate("'.$keywords.'", `admin_user`.`nickname`)','personnel_care.create_time desc');
			$data_list = [];
			foreach($lists as $key => $value){
				$data_list[$key] = [
					'url'   =>   url('awarddetail',['id'=>$value['id']]),
					'top'   =>   '制单时间：'.date('Y-m-d',$value['create_time']),
					'left'  =>   $value['nickname'],					
					'bottom'=>   '奖惩日期：'.$value['care_time']
				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');	
	}
	public function caredetail($id=''){
		if($id==null)$this->error('没有此关怀');
		$info = CareModel::getOne($id);
		$data_list = detaillist([
				['username', '用户名'],
				['nickname', '昵称'],
				['care_type', '关怀类型',['1'=>'节日关怀','2'=>'生日关怀']],
				['money','关怀费用'],
				['holiday','关怀假期'],
				['good', '关怀物品'],
				['care_time', '奖惩日期'],		               				
				['code', '备注'],
			],$info);
		$this->assign('data_list',$data_list);		
		return $this->fetch('apply/details');
	}
	//薪资
	public function wage($keywords = ''){
		if($this->request->isAjax()){
			$lists = WageModel::getList('locate("'.$keywords.'", `admin_user`.`nickname`) OR locate("'.$keywords.'", `admin_organization`.`title`) OR locate("'.$keywords.'", `admin_position`.`title`)','personnel_wage.create_time desc');
			$data_list = [];
			foreach($lists as $key => $value){
				$data_list[$key] = [
					'url'   =>   url('wagedetail',['id'=>$value['id']]),
					'top'   =>   '制单时间：'.date('Y-m-d',$value['create_time']),
					'left'  =>   $value['nickname'],	
					'right' =>   '职位：'.$value['position1'],
					'bottom'=>   '部门：'.$value['organization1']
				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');	
	}
	public function wagedetail($id=''){
		if($id==null)$this->error('没有此薪资');
		$info = WageModel::getOne($id);
		$data_list = detaillist([
				['username', '用户名'],
				['nickname', '昵称'],
				['organization1', '部门'],
				['position1','职位'],
				['wage_type1','工资类型'],
				['base_pay', '基本工资','money'],
			],$info);
		$this->assign('data_list',$data_list);		
		return $this->fetch('apply/details');
	}
	//打卡
	public function work($keywords = ''){
		if($this->request->isAjax()){
		$map = 'locate("'.$keywords.'", `nickname`)>0';
        // 数据列表
        $lists = UserModel::where($map)->order('')->paginate();
			$data_list = [];
			foreach($lists as $key => $value){
				$data_list[$key] = [
					'url'   =>   url('workdetail',['uid'=>$value['id']]),
					'top'   =>   '打卡日期：'.date('Y-m-d',$value['create_time']),
					'left'  =>   $value['nickname'],	
					'right' =>   '打卡天数：'.count(WorkModel::where('uid',$value['id'])->select()),
					//'bottom'=>   '打卡时间：'.$value['on_time'].'--'.$value['off_time']
				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');	
	}
	public function workdetail($uid=''){
		if($uid==null)$this->error('没有此打卡');
		if($this->request->isAjax()){
		$map['uid'] = $uid;
		$lists = WorkModel::getList($map,'personnel_work.create_time desc');
			$data_list = [];
			foreach($lists as $key => $value){
				$data_list[$key] = [
					//'url'   =>   url('wagedetail',['id'=>$value['id']]),
					'top'   =>   '打卡日期：'.date('Y-m-d',$value['create_time']),
					'left'  =>   $value['nickname'],	
					'right' =>   '职位：'.$value['position1'],
					'bottom'=>   '打卡时间：'.$value['on_time'].'--'.$value['off_time']
				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');	
	}
	//招聘
	public function recruit($keywords = ''){
		if($this->request->isAjax()){
			$map = 'locate("'.$keywords.'", `admin_user`.`nickname`)>0 OR locate("'.$keywords.'", `personnel_recruit`.`title`)>0';
			
			$lists = RecruitModel::getList($map,'personnel_recruit.create_time desc');
			$data_list = [];
			foreach($lists as $key => $value){
				$data_list[$key] = [
					'url'   =>   url('recruitdetail',['id'=>$value['id']]),
					'top'   =>   '制单日期：'.date('Y-m-d',$value['create_time']),
					'left'  =>   $value['title'],	
					'right' =>   '申请人：'.$value['nickname'],	
					'bottom'=>   '到岗时间：'.$value['recruit_time']
				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');
	}
	public function recruitdetail($id=''){
		if($id==null)$this->error('没有此招聘');
		$info = RecruitModel::getOne($id);
		$data_list = detaillist([
				['username', '用户名'],
				['nickname', '昵称'],
				['title','招聘标题'],
				['description', '描述'],
				['recruit_time', '到岗时间'],   
				['info', '招聘详情'],
				['note', '备注'],
				['status', '审核状态',['-1'=>'待审核','1'=>'已通过','0'=>'已拒绝']],
			],$info);
		$this->assign('data_list',$data_list);		
		return $this->fetch('apply/details');
	}
}
