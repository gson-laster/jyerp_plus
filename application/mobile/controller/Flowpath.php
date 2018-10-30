<?php
namespace app\mobile\controller;
use app\admin\model\Flow as FlowModel;
use app\admin\model\FlowField as FieldModel;
use app\admin\model\Attachment as AttachmentModel;
use app\flow\model\FlowWork as WorkModel;
use app\admin\model\Module as ModuleModel;
use app\user\model\User as UserModel;
use app\user\model\Position as PositionModel;
use app\user\model\Organization as OrganizationModel;
use app\flow\model\FlowLog as FlowLogModel;
use think\Image;
use think\File;
use think\Hook;
use think\Db;
/*
 
 * 控制器*/
class Flowpath extends Base {
	
	public function index(){
		$menu = FlowModel::where('status',1)->field('id,tid,title')->order('sort','asc')->select();
		return $this -> fetch('', ['menu_list' => $menu]);
		
	}
	
	public function details($fid = null){
		if(is_null($fid)) $this -> error('参数错误');
		//获取该流程
		$fields = FieldModel::where('fid',$fid)->order('sort asc,id asc')->column(true);
		if ($this->request->isPost()) {

           $data2 = $this->request->post();

           foreach ($data2 as $key => &$value) {
               foreach ($fields as $k => $v) {
                   if(ltrim($key,'zd_')==$v['id']){
                        switch ($v['type']) {
                            case 'date':
                            case 'time':
                            case 'datetime':
                                $value = strtotime($value);
                                break;
                            case 'text':
                                switch ($v['define']) {
                                    case '1':
                                        if(empty($value)) 
                                            $this->error($v['title']."不能为空");
                                        break;
                                    case '2':
                                        if(is_numeric($value))
                                            $this->error($v['title']."必须为数字");
                                        break;
                                    case '3':
                                        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
                                        if ( !preg_match( $pattern, $value ) )
                                          $this->error($v['title']."不是邮箱");
                                        break;
                                }
                                break;
                            }

                        $udf_data[ltrim($key,'_zd')] = $value;

                   }
               }
           }

           $work = new WorkModel;
           $work->content = $data2['content'];
           $work->title = $data2['title'];
           if(empty($data2['content'])) $this->error('请输入原由');
           if(empty($data2['title'])) $this->error('请输入标题');
           $work->confirm = $this->_confirm($fid,1);
           $work->fid= $fid;
           $work->user_id = UID;
           $work->udf_data = empty($udf_data) ? "" : json_encode($udf_data);
           $work->user_name = get_nickname(UID);
           $work->create_time = time();
           $work->update_time = time();
           $work->step = 20;
           $work->save();

           if(!empty($work->id)){
                 $wid = $work->id;
                 if($this->_next_step($wid,$work->step)){
                    $this->success('已提交',url('myflow'));
                 }else{
                    WorkModel::destroy($wid);
                    $this->error('申请参数有误');
                 }
           }else{
                $this->error('申请参数有误');
           }
			
		}
        foreach ($fields as &$value) {
             $value['name'] = 'zd_'.$value['id'];
            // 解析options
            if ($value['options'] != '') {
                $value['options'] = parse_attr($value['options']);
            }
        
            switch ($value['type']) {
                case 'linkage':// 解析联动下拉框异步请求地址
                    if (!empty($value['ajax_url']) && substr($value['ajax_url'], 0, 4) != 'http') {
                        $value['ajax_url'] = url($value['ajax_url']);
                    }
                    break;
                case 'date':
                case 'time':
                case 'datetime':
                    $value['value'] = '';
                    break;
            }
        }
		$pageTitle = FlowModel::where('id',$fid)->value('title');
		$this -> assign('pageTitle', $pageTitle);
		$this -> assign('data_list', isset($fields) ? $fields : '');
		return $this -> fetch();
	}

    //我的申请
    public function my(){

        if($this -> request -> isAjax()) {
            $map['w.user_id'] = UID;
            $lists = WorkModel::myflow($map,'w.id desc');
            $data_list = [];
            foreach ($lists as $key => $value) {
                $data_list[$key] = [
                    'url'   =>  url('myflowdetail',['id'=>$value['id']]),
                    'top'   =>  '发起时间'.date('Y-m-d H:i',$value['create_time']),
                    'left'  =>  $value['title'],
                    'right' =>  $value['step']==30 ? '否决' : ($value['step']== 40 ? '成功' : '进行中'), 
                    'bottom'=>  '最后审批：'.date('Y-m-d H:i',$value['update_time'])
                ];
            }
           
            return $data_list;
        }
        return $this->fetch('apply/lists');
    }

    //我的申请详情
    public function myflowdetail($id){

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
        return $this->fetch();
    }
    public function upload($dir = 'images', $from = '', $module = '')
    {
        // 临时取消执行时间限制
        set_time_limit(0);
        if ($dir == '') $this->error('没有指定上传目录');
        return $this->saveFile($dir, $from, $module);
    }

    /**
     * 保存附件
     * @param string $dir 附件存放的目录
     * @param string $from 来源
     * @param string $module 来自哪个模块
     * @author 蔡伟明 <314013107@qq.com>
     * @return string|\think\response\Json
     */
    private function saveFile($dir = '', $from = '', $module = '')
    {
        // 附件大小限制
        $size_limit = $dir == 'images' ? config('upload_image_size') : config('upload_file_size');
        $size_limit = $size_limit * 1024;
        // 附件类型限制
        $ext_limit = $dir == 'images' ? config('upload_image_ext') : config('upload_file_ext');
        $ext_limit = $ext_limit != '' ? parse_attr($ext_limit) : '';
        // 缩略图参数
        $thumb = $this->request->post('thumb', '');
        // 水印参数
        $watermark = $this->request->post('watermark', '');

        // 获取附件数据
        $callback = '';
        $file_input_name = 'file';
        $file = $this->request->file($file_input_name);

        // 判断附件是否已存在
        if ($file_exists = AttachmentModel::get(['md5' => $file->hash('md5')])) {
            if ($file_exists['driver'] == 'local') {
                $file_path = PUBLIC_PATH. $file_exists['path'];
            } else {
                $file_path = $file_exists['path'];
            }
            return json([
                'code'   => 1,
                'info'   => '上传成功',
                'class'  => 'success',
                'id'     => $file_exists['id'],
                'path'   => $file_path
            ]);
        }

        // 判断附件大小是否超过限制
        if ($size_limit > 0 && ($file->getInfo('size') > $size_limit)) {
            return json([
                'code'   => 0,
                'class'  => 'danger',
                'info'   => '附件过大'
            ]);
        }

        // 判断附件格式是否符合
        $file_name = $file->getInfo('name');
        $file_ext  = strtolower(substr($file_name, strrpos($file_name, '.')+1));
        $error_msg = '';
        if ($ext_limit == '') {
            $error_msg = '获取文件信息失败！';
        }
        if ($file->getMime() == 'text/x-php' || $file->getMime() == 'text/html') {
            $error_msg = '禁止上传非法文件！';
        }
        if (preg_grep("/php/i", $ext_limit)) {
            $error_msg = '禁止上传非法文件！';
        }
        if (!preg_grep("/$file_ext/i", $ext_limit)) {
            $error_msg = '附件类型不正确！';
        }

        if ($error_msg != '') {
                return json([
                    'code'   => 0,
                    'class'  => 'danger',
                    'info'   => $error_msg
                ]);
        }

        // 附件上传钩子，用于第三方文件上传扩展
        if (config('upload_driver') != 'local') {
            $hook_result = Hook::listen('upload_attachment', $file, ['from' => 'mobile', 'module' => $module], true);
            if (false !== $hook_result) {
                return $hook_result;
            }
        }

        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move(config('upload_path') . DS . $dir);
        if($info){
            // 缩略图路径
            $thumb_path_name = '';
            // 图片宽度
            $img_width = '';
            // 图片高度
            $img_height = '';
            if ($dir == 'images') {
                $img = Image::open($info);
                $img_width  = $img->width();
                $img_height = $img->height();
                // 水印功能
                if ($watermark == '') {
                    if (config('upload_thumb_water') == 1 && config('upload_thumb_water_pic') > 0) {
                        $this->create_water($info->getRealPath(), config('upload_thumb_water_pic'));
                    }
                } else {
                    if (strtolower($watermark) != 'close') {
                        list($watermark_img, $watermark_pos, $watermark_alpha) = explode('|', $watermark);
                        $this->create_water($info->getRealPath(), $watermark_img, $watermark_pos, $watermark_alpha);
                    }
                }

                // 生成缩略图
                if ($thumb == '') {
                    if (config('upload_image_thumb') != '') {
                        $thumb_path_name = $this->create_thumb($info, $info->getPathInfo()->getfileName(), $info->getFilename());
                    }
                } else {
                    if (strtolower($thumb) != 'close') {
                        list($thumb_size, $thumb_type) = explode('|', $thumb);
                        $thumb_path_name = $this->create_thumb($info, $info->getPathInfo()->getfileName(), $info->getFilename(), $thumb_size, $thumb_type);
                    }
                }
            }

            // 获取附件信息
            $file_info = [
                'uid'    => session('user_auth.uid'),
                'name'   => $file->getInfo('name'),
                'mime'   => $file->getInfo('type'),
                'path'   => 'uploads/' . $dir . '/' . str_replace('\\', '/', $info->getSaveName()),
                'ext'    => $info->getExtension(),
                'size'   => $info->getSize(),
                'md5'    => $info->hash('md5'),
                'sha1'   => $info->hash('sha1'),
                'thumb'  => $thumb_path_name,
                'module' => $module,
                'width'  => $img_width,
                'height' => $img_height,
            ];

            // 写入数据库
            if ($file_add = AttachmentModel::create($file_info)) {
                $file_path = PUBLIC_PATH. $file_info['path'];
                  return json([
                          'code'   => 1,
                          'info'   => '上传成功',
                          'class'  => 'success',
                          'id'     => $file_add['id'],
                          'path'   => $file_path
                      ]);
            } else {
                return json(['code' => 0, 'class' => 'danger', 'info' => '上传失败']);
            }
        }else{
              return json(['code' => 0, 'class' => 'danger', 'info' => $file->getError()]);
        }
    }

        public function _confirm($fid,$type=null){

        $confirm = str_replace("-",",",ltrim(FlowModel::where('id',$fid)->value('confirm'),'form-'));

        // 员工id拼接
        if(!empty($type)){

            if($confirm==null)  $this->error("找不到审批人");
            $confirm_data = UserModel::where('position','in',$confirm)->column('id','position');
            $confirm_str = '';

            foreach (explode(",",$confirm) as $key => $value) {

                if($value==0){
                    $position=UserModel::where('id',UID)->value('position');
                    $pid=PositionModel::where('id',$position)->value('pid');
                    if($pid==0){
                        $confirm_str.='-'.UID;
                    }else{
                        $confirm_str.='-'.UserModel::where('position',$pid)->value('id');
                    }
                }else{
                    foreach ($confirm_data as $k => $v) {
                        if($value==$k){
                            $confirm_str.='-'.$v;
                        }
                    }
                }
            }

            $confirm_str = trim($confirm_str,'-');
            return $confirm_str;

        }
        //部门>职位 拼接
        $confirm_data = db::name('admin_position')
                        ->alias('p')
                        ->field('p.id,p.title,o.title as otitle')
                        ->join('admin_organization o','p.oid=o.id','left')
                        ->where('p.id','in',$confirm)
                        ->select();
        $confirm_str = "填写表单";
        foreach (explode(",",$confirm) as $key => $value) {

            if($value==0){
                $confirm_str .= "——>上级领导"; 
            }else{
                foreach ($confirm_data as $k => $v) {
                    if($value==$v['id']){
                        $confirm_str .= "——>".$v['otitle'].">".$v['title']; 
                    }
                }
            }

        }
        return $confirm_str;
    }

        //下一步骤
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
    //获取下一位审批人的user_id
    function _duty_id($wid, $step) {
        if (substr($step, 0, 1) == 2) {
            $confirm = WorkModel::where(array('id' => $wid))->value("confirm");

            $arr_confirm = array_filter(explode("-", $confirm));

            return $arr_confirm[fmod($step, 10) - 1];
        }
    }
	
}

