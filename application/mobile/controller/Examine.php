<?php
	namespace app\mobile\controller;
	use app\admin\model\FlowField as FieldModel;
	use app\flow\model\FlowWork as WorkModel;
	use app\flow\model\FlowLog as FlowLogModel;
	use think\Db;
//	消息.我的审批

	class Examine extends Base{
//	 	
//function getTree($data, $Id)
//{
//	$tree = '';
//	foreach($data as $k => $v)
//	{
//	   if($v['pid'] == $Id)
//	   {         //父亲找到儿子
//	    $v['pids'] = $this -> getTree($data, $v['id']);
//	    $tree[] = $v;
//	    //unset($data[$k]);
//	   }
//	}
//	return $tree;
//}
     	public function lists($keywords = ''){
     		
 		   if($this -> request -> isAjax()) {
 		   	$map = [];
 		   	$map['l.result'] = 0;
            $map['l.user_id'] = UID;
 		   	    $lists = db::name('flow_log')
                    ->alias('l')
                    ->field('l.id as lid,w.id as id,w.title as wtitle,w.user_name as wusername,w.create_time as wcreate_time,admin_flow.title as ftitle')
                    ->join('flow_work w','l.wid=w.id','left')
                    ->join('admin_flow','w.fid=admin_flow.id','left')
                      ->where($map)
                    -> where('locate("'.$keywords.'",`w`.`title`)>0')
                    ->order('wcreate_time desc')
                    ->paginate();
	            $data_list = [];
	            foreach ($lists as $key => $value) {
	                $data_list[$key] = [
	                    'url'   =>  url('myflowdetail',['id'=>$value['id']]),
	                    'top'   =>  '发起时间'.date('Y-m-d H:i',$value['wcreate_time']),
	                    'left'  =>  $value['ftitle'],
	                    'right' =>  $value['wusername'], 
	                    'bottom'=>  $value['wtitle']
	                ];
	            }
	           
	            return $data_list;
	        }
	        return $this->fetch('apply/lists');
     	}
    	public function myflowdetail($id = null){
    		

	        if(empty($id))$this->error('请选择流程');
	
	        $work = WorkModel::get($id);
	
	        $data = json_decode($work->udf_data,true);
	
	        $fields = FieldModel::where('fid',$work->fid)->order('sort asc,id asc')->column(true);
	        //dump($work);die;
	        foreach ($fields as &$value) {
	
	            $value['name'] = $value['id'].'_zb';
	            // 解析options
	            if ($value['options'] != '') {
	                $value['options'] = parse_attr($value['options']);
	            }
	            $value['value'] = isset($data[$value['id']]) ? $data[$value['id']]: '';
	
	            switch ($value['type']) {
	                case 'linkage':// 解析联动下拉框异步请求地址
	                    if (!empty($value['ajax_url']) && substr($value['ajax_url'], 0, 4) != 'http') {
	                        $value['ajax_url'] = url($value['ajax_url']);
	                    }
	                    break;
	                case 'date':
	                    $value['value'] = date('Y-m-d',$value['value']);
	                    $value['type'] = 'static';
	                    break;
	                case 'time':
	                    $value['value'] = date('H:i',$value['value']);
	                    $value['type'] = 'static';
	                    break;
	                case 'datetime':
	                    $value['value'] = date('Y-m-d H:i',$value['value']);
	                    $value['type'] = 'static';
	                    break;
	                case 'text':
	                case 'textarea':
	                    $value['type'] = 'static';
	                    break;
	            }
	        }
	        
	        // 添加额外表单项信息
	        $log_id = FlowLogModel::where(['wid'=>$id,'result'=>0])->value('id');
	        $worklist = [
	            'title'=>$work->title,
	            'content'=>$work->content
	        ];
	        //审批情况
	        $worklog_list = FlowLogModel::where('wid',$id)->order('id asc')->select();
	        $this->assign('fields',$fields);
	        $this->assign('worklist',$work);
	        return $this->fetch('flowpath/myflowdetail', ['isExamine' => 1, 'log_id' => $log_id]);
    
    	}
    	//流程办理
    	public function ban($wid=null){

       		 if(empty($wid))$this->error("请选择流程");

		        if ($this->request->isPost()) {
		
		            $data = $this->request->post();
		            
		            $data['id'] = $data['log_id'];
		
		           if(empty($data['result'])){
		           	 $this -> error('请审核');
		           }
		
		            $data['update_time'] = time();
		            if(FlowLogModel::update($data)){
		
		                if($data['result']==1){
		                    $this->_next_step($wid,FlowLogModel::where('id',$data['id'])->value('step'));
		                }else{
		                    WorkModel::update(['id'=>$wid,'step'=>30]);
		                }
		                WorkModel::update(['id'=>$wid,'update_time'=>time()]);
		                $this->success('审批成功',url('handletask'));
		
		            }else{
		                $this->error('审批失败');
		            }
		        }
		    }
		     public function _next_step($wid, $step) {

            if (substr($step, 0, 1) == 2) {
                    if($this->_is_last_confirm($wid)){
                        WorkModel::where('id',$wid)->update(['step'=>40]);
                        return true;

                    }else{
                         $step++;
                    }
            }
            $data['wid'] = $wid;
            $data['step'] = $step;
            $data['user_id'] = $this -> _duty_id($wid, $step);
            $data['create_time'] = $data['update_time'] = time();

            if (strpos($data['user_id'], ",") !== false) {

                $emp_list = explode(",", $data['user_id']);
                foreach ($emp_list as $emp) {

                    $data['user_id'] = $emp;
                     if(FlowLogModel::create($data)){
                        return true;
                     }else{
                        return false;
                     }

                }

            } else {
                 if(FlowLogModel::create($data)){
                        return true;
                     }else{
                        return false;
                 }
            }
        
    }
        //获取下一位审批人的user_id
    function _duty_id($wid, $step) {
        if (substr($step, 0, 1) == 2) {
            $confirm = WorkModel::where(array('id' => $wid))->value("confirm");

            $arr_confirm = array_filter(explode("-", $confirm));

            return $arr_confirm[fmod($step, 10) - 1];
        }
    }
       // 判断是否是最后一位审批人
    function _is_last_confirm($flow_id) {
        $confirm = WorkModel::where(array('id' => $flow_id)) -> value("confirm");

        if (empty($confirm)) {
            return true;
        }
        $count = count(explode("-", $confirm));
        $log_count = FlowLogModel::where(['wid'=>$flow_id,'result'=>1])->count();
        $last_confirm = array_filter(explode("-", $confirm));
        $last_confirm_user_id = end($last_confirm);

        return (($last_confirm_user_id == UID) && ($count==$log_count));
    }
	 }
?>