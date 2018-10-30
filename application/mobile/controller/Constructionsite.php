<?php
namespace app\mobile\controller;
use app\constructionsite\model\Page as PageModel;
use app\constructionsite\model\Change as ChangeModel;
use app\constructionsite\model\Log as LogModel;
use app\constructionsite\model\Finish as FinishModel;
/*
 
 * 联系人控制器*/
class Constructionsite extends Base{
	
	//图纸列表
	public function page($keywords = '') {
		if($this -> request -> isAjax()){
			$map = 'locate("'.$keywords.'", `tender_factpic`.`name`) OR locate("'.$keywords.'", `admin_user`.`nickname`)';
			$order=[];
			$lists = PageModel::getList($map,$order);
			$data_list = [];
			foreach ($lists as $key => $value) {
				$data_list[$key] = [
					'url'	=>	url('pagedetail',['id'=>$value['id']]),
					'top'	=>	'制图时间：'.date('Y-m-d',$value['date']),
					'left'	=>	$value['name'],
					'right'	=>	'',
					'bottom'=>	'责任人：'.get_nickname($value['nickname'])
				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');
	}
	//图纸详情
	public function pagedetail($id='') {

		if($id==null)$this->error('没有此项目');

		$info = PageModel::getOne($id);

		
		
		$data_list = detaillist([
						['date','日期','date'],
			      ['name','生产主题'],
			      ['obj_id','项目'],
						['uid','制单人'],
						['note','备注'],
						['file','生产图纸']							
					],$info);

		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details');
	}


	public function change($keywords = '') {
		if($this -> request -> isAjax()){
			$map = 'locate("'.$keywords.'", `constructionsite_change`.`title`)';
			$order=[];
			$lists  = ChangeModel::getList($map,$order);
			$data_list = [];
			foreach ($lists as $key => $value) {
				$data_list[$key] = [
					'url'	=>	url('changedetail',['id'=>$value['id']]),
					'top'	=>	'下发时间：'.date('Y-m-d',$value['create_time']),
					'left'	=>	$value['title'],
					'right'	=>	'',
					'bottom'=>	'责任人：'.get_nickname($value['wid'])
				];
			}
			return $data_list;
		}
		return $this->fetch('apply/lists');
	}
	//图纸详情
	public function changedetail($id='') {

		if($id==null)$this->error('没有此项目');

		$info = ChangeModel::getChange($id);

		
		
		$data_list = detaillist([
				[ 'title', '变更名称'],
                ['nickname', '填报人'],
 				['xname', '项目'],
                ['ti_username', '提出变更者'],
                ['money','变更金额'],
                [ 'old_imgs', '原图片'],
                ['old_file', '原文件'],
                [ 'new_imgs', '更换后图片'],
                ['new_file', '更换后文件'],
            	[ 'cause', '变更原因'],
            	['content','变更内容'],				
					],$info);

		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details');
	}

//施工日志

public function log($keywords = ''){
	
		if($this -> request -> isAjax()){
			$map = 'locate("'.$keywords.'", `constructionsite_log`.`work_content`) OR locate("'.$keywords.'", `admin_user`.`nickname`) OR locate("'.$keywords.'", `tender_obj`.`name`)';
			$order = $this -> getOrder();
			$lists = LogModel::getList($map,$order);
			$data_list = [];
			foreach ($lists as $key => $value) {
				$data_list[$key] = [
					'url'	=>	url('logdetail',['id'=>$value['id']]),
					'top'	=>	'日期：'.date('Y-m-d',$value['create_time']),
					'left'	=>	$value['xname'],
					'right'	=>	$value['nickname'],
					'bottom'=>	$value['work_content']
				];
			}
			return $data_list;
		}
	
	return $this->fetch('apply/lists');
}
	
	public function logdetail($id='') {

		if($id==null)$this->error('没有此项目');

		$info = LogModel::getLog($id);
		$info['am_weather'] = self::weather($info['am_weather']);
		$info['pm_weather'] = self::weather($info['pm_weather']);
		
		$data_list = detaillist([
			    ['xname', '项目名称'],
                ['nickname', '填报人'],
            	['am_weather', '上午天气'],
            	['pm_weather', '下午天气'],
            	['max_warm','最高气温'],
            	['min_warm','最低气温'],
            	['name', '车间',''],
            	['work_num','工人人数'],
            	['work_content', '施工内容'],
            	['work_wrong','施工问题'],				
					],$info);

		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details');
	}
 	function weather($id=null){
		switch ($id) {
			case 1:
				return '阴';
				break;
			case 2:
				return '雨';
				break;
			default:
				return '晴';
				break;
		}
	}   
	//竣工列表
	public function finish($keywords = ''){
		if($this -> request -> isAjax()){
			$map = 'locate("'.$keywords.'", `tender_obj`.`name`) OR locate("'.$keywords.'", `admin_user`.`nickname`)';
			$order = $this -> getOrder();
			$lists = FinishModel::getList($map,$order);
			$data_list = [];
			$state = [0 =>'进行中', 2=>'否决', 1=>'同意'];
			foreach ($lists as $key => $value) {
				$value['status'] = $state[$value['status']];
				$data_list[$key] = [
					'url'	=>	url('finishdetail',['id'=>$value['id']]),
					'top'	=>	date('Y-m-d',$value['s_time']).'-'.date('Y-m-d',$value['e_time']),
					'left'	=>	$value['item'],
					'right'	=>	$value['status'],
					'bottom'=>	$value['maker'],
				];
			}
			return $data_list;
		}
	
	return $this->fetch('apply/lists');
	}
	
	public function finishdetail($id = null){
		

		if($id==null)$this->error('没有此项目');

		$info = FinishModel::getOne($id);
		
		$data_list = detaillist([
		    	['date','日期', 'date'],
                ['item', '完工项目'],
            	['s_time', '开工日期', 'date'],
            	['e_time', '竣工日期', 'date'],
            	['obj_time','工期(天)'],
            	['maker', '申请人'],
            	['note','备注'],
			],$info);

		$this->assign('data_list',$data_list);
		
		return $this->fetch('apply/details');
	
	}
}
