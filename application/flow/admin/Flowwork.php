<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\flow\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\admin\model\Flow as FlowModel;
use app\admin\model\FlowType as FlowTypeModel;
use app\admin\model\FlowField as FieldModel;
use app\flow\model\FlowLog as FlowLogModel;
use app\flow\model\FlowWork as WorkModel;
use app\admin\model\Module as ModuleModel;
use app\user\model\User as UserModel;
use app\user\model\Position as PositionModel;
use app\user\model\Organization as OrganizationModel;
use think\Db;
use think\Validate;


/**
 * 流程
 * @package app\admin\controller
 */
class Flowwork extends Admin
{
    /**
     * 首页
     * @author 王永吉 <739712704@qq.com>
     * @return mixed
     */
    public function index()
    {

       
     
        $this->assign('flowtype',FlowTypeModel::order('sort','asc')->field('id,title')->select());
        $this->assign('flow',FlowModel::where('status',1)->field('id,tid,title')->order('sort','asc')->select());
        return $this->fetch();
    }


    //添加审批
    public function add($fid=null){
        if($fid==null) $this->error('请选择流程');

        $fields = FieldModel::where('fid',$fid)->order('sort asc,id asc')->column(true);

        if ($this->request->isPost()) {

           $data2 = $this->request->post();
      
           foreach ($data2 as $key => &$value) {

               foreach ($fields as $k => $v) {
                   if(rtrim($key,'_zd')==$v['id']){
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

                        $udf_data[rtrim($key,'_zd')] = $value;

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
           $work->add_file = $data2['add_file'];
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
        // 获取表单项
        if(FlowModel::where('id',$fid)->value('is_dj')==1){
           

        }
        foreach ($fields as &$value) {
             $value['name'] = $value['id'].'_zd';
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
            if($value['style']==1){
                $layout[$value['name']] = 6;
            }else{
                $layout[$value['name']] = 12;
            }

        }

        if(empty($layout))$layout=null;
        // 添加额外表单项信息
        $extra_field = [
                ['name' => 'fid','type' => 'hidden', 'value' => $fid],
        ];

        $fields = array_merge($extra_field, $fields);
        $pageTitle = FlowModel::where('id',$fid)->value('title');
        $confirm = $this->_confirm($fid);
        if((FlowModel::where('id',$fid)->value('is_dj'))==1){
            $text = $this->danJu($fid,$confirm);

        }
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('发起流程——>'.$pageTitle) // 设置页面标题
             ->addFormItems([
                ['text:6', 'title', '标题'],
                ['static:6', 'send_time', '申请时间','',date('Y-m-d H:i',time())],
                ['static:6', 'username', '申请人','',get_nickname(UID)],
                ['static:6', 'o_p', '部门职位','',$this->_oposition(UID)],         
            ])
            ->setFormItems($fields)
            ->addStatic('str_confirm', '审批步骤','',$confirm)
            ->addFile('add_file','上传附件')
            ->addTextarea('content', '原由')
            ->layout($layout)
            ->fetch();

    }
    //代办流程
    public function handletask(){

            $order = $this->getOrder('id desc');
            $map = $this->getMap();

            $map['l.result'] = 0;
            $map['l.user_id'] = UID;

            $data_list = db::name('flow_log')
                    ->alias('l')
                    ->field('l.id as lid,w.id as id,w.title as wtitle,w.user_name as wusername,w.create_time as wcreate_time,admin_flow.title as ftitle')
                    ->join('flow_work w','l.wid=w.id','left')
                    ->join('admin_flow','w.fid=admin_flow.id','left')
                    ->where($map)
                    ->order($order)
                    ->paginate(10);

            $btn_Ban = [
                'title' => '办理',
                'icon'  => 'fa fa-fw fa-legal',
                'href'  => url('ban', ['wid' => '__id__'])
            ];
            $btn_Guo = [
                'title' => '过程',
                'icon'  => 'fa fa-fw fa-sort-amount-desc',
                'href'  => url('guo', ['wid' => '__id__'])
            ];

            return ZBuilder::make('table')
            ->addOrder(['lid'=>'l.id','wcreate_time'=>'w.create_time']) // 添加排序
            ->addFilter(['ftitle'=>'admin_flow.title']) // 添加筛选
            ->setSearch(['w.title' => '标题', 'w.user_name' => '发起人'],'','','搜索')
            ->hideCheckbox()
            ->setPageTitle('待办流程') // 设置页面标题
            // ->addFilter(['type_title' => 'admin_flow_type.title']) // 添加筛选
            ->addColumns([ // 批量添加数据列
                ['lid','流水号'],
                ['wtitle', '标题'],
                ['ftitle', '流程名称'],
                ['wusername', '发起人'],
                ['wcreate_time', '发起时间','datetime','没有填写日期时间', 'Y-m-d H:i'],
                ['right_button', '操作', 'btn']
            ])
            ->addRightButton('ban', $btn_Ban) // 添加授权按钮
            ->addRightButton('guo', $btn_Guo,true) // 添加授权按钮
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板
    }


    // 已办流程
    public function handletask_ok(){

            $order = $this->getOrder('id desc');
            $map = $this->getMap();

            $map['l.result'] = ['>',0];
            $map['l.user_id'] = UID;

            $data_list = db::name('flow_log')
                    ->alias('l')
                    ->field('l.id as lid,w.id as id,w.title as wtitle,w.user_name as wusername,w.create_time as wcreate_time,admin_flow.title as ftitle,w.step')
                    ->join('flow_work w','l.wid=w.id','left')
                    ->join('admin_flow','w.fid=admin_flow.id','left')
                    ->where($map)
                    ->order($order)
                    ->paginate(10);

            $btn_detail = [
                'title' => '查看详情',
                'icon'  => 'fa fa-fw fa-eye',
                'href'  => url('flow_detail', ['wid' => '__id__'])
            ];
            $btn_Guo = [
                'title' => '过程',
                'icon'  => 'fa fa-fw fa-sort-amount-desc',
                'href'  => url('guo', ['wid' => '__id__'])
            ];

            return ZBuilder::make('table')
            ->addOrder(['lid'=>'l.id','wcreate_time'=>'w.create_time']) // 添加排序
            ->addFilter(['ftitle'=>'admin_flow.title']) // 添加筛选
            ->setSearch(['w.title' => '标题', 'w.user_name' => '发起人'],'','','搜索')
            ->hideCheckbox()
            ->setPageTitle('已办流程') // 设置页面标题
            // ->addFilter(['type_title' => 'admin_flow_type.title']) // 添加筛选
            ->addColumns([ // 批量添加数据列
                ['lid','流水号'],
                ['wtitle', '标题'],
                ['ftitle', '流程名称'],
                ['wusername', '发起人'],
                ['wcreate_time', '发起时间','datetime','没有填写日期时间', 'Y-m-d H:i'],
                ['step', '状态','status','', [20 => '进行中:info', 30=>'中断:danger', 40=>'完成:success']],
                ['right_button', '详情', 'btn']
            ])
            ->addRightButton('detail', $btn_detail,true) // 添加授权按钮
            ->addRightButton('guo', $btn_Guo,true) // 添加授权按钮
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板

    }

    //流程办理
    public function ban($wid=null){

        if(empty($wid))$this->error("请选择流程");

        if ($this->request->isPost()) {

            $data = $this->request->post();
            
            $data['id'] = $data['log_id'];

            $result = $this->validate($data, 'FlowLog');
            if(true !== $result) $this->error($result);

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

        $work = WorkModel::get($wid);
        $data = json_decode($work->udf_data,true);
        $fields = FieldModel::where('fid',$work->fid)->order('sort asc,id asc')->column(true);

        foreach ($fields as &$value) {

             $value['name'] = $value['id'].'_zb';
            // 解析options
            if ($value['options'] != '') {
                $value['options'] = parse_attr($value['options']);
            }
            $value['value'] = $data[$value['id']];

            switch ($value['type']) {
                case 'linkage':// 解析联动下拉框异步请求地址
                    if (!empty($value['ajax_url']) && substr($value['ajax_url'], 0, 4) != 'http') {
                        $value['ajax_url'] = url($value['ajax_url']);
                    }
                    break;
                case 'date':
                    $value['value'] = date('Y-m-d',$value['value']);
                    break;
                case 'time':
                    $value['value'] = date('H:i',$value['value']);
                    break;
                case 'datetime':
                    $value['value'] = date('Y-m-d H:i',$value['value']);
                    break;
            }

            if($value['style']==1){
                $layout[$value['name']] = 6;
            }else{
                $layout[$value['name']] = 12;
            }

        }
        if(empty($layout))$layout=null;
        // 添加额外表单项信息
        $log_id = FlowLogModel::where(['wid'=>$wid,'result'=>0])->value('id');

        $worklist = [
            'title'=>$work->title,
            'content'=>$work->content
        ];

        //审批情况
        $worklog_list = FlowLogModel::where('wid',$wid)->order('id asc')->select();

        foreach ($worklog_list as $key => &$value) {

            $value['o_position'] = $this->_oposition($value['user_id']);
            $value['user_name'] = UserModel::where('id',$value['user_id'])->value('nickname');

        }
        $this->assign('log_id',$log_id);
        $this->assign('worklog_list',$worklog_list);

        return ZBuilder::make('form')
            ->setPageTitle('流程办理') // 设置页面标题
             ->addFormItems([
                ['static:6', 'title', '标题'],
                ['static:6', 'send_time', '申请时间','',date('Y-m-d H:i',time())],
                ['static:6', 'username', '申请人','',get_nickname(UID)],
                ['static:6', 'o_p', '部门职位','',$this->_oposition($work->user_id)], 
                ['archive:6','add_file', '附件','',$work->add_file],        
            ])
            ->setFormItems($fields)
            ->setFormData($worklist)
            ->addStatic('str_confirm', '审批步骤','',$this->_confirm($work->fid))
            // ->addFile('add_file','上传附件')
            ->addTextarea('content', '原由')
            ->layout($layout)
            ->hideBtn('submit,back')
            ->fetch('ban');
        
    }
    // 审批过程
    public function guo($wid=null){
        if($wid==null)$this->error('请选择流程');
        //审批情况
        $worklog_list = FlowLogModel::where('wid',$wid)->order('id asc')->select();

        foreach ($worklog_list as $key => &$value) {

            $value['o_position'] = $this->_oposition($value['user_id']);
            $value['user_name'] = UserModel::where('id',$value['user_id'])->value('nickname');

        }
        return ZBuilder::make('table')
        ->hideCheckbox()
        ->setPageTitle('审批过程') // 设置页面标题
        // ->addFilter(['type_title' => 'admin_flow_type.title']) // 添加筛选
        ->addColumns([ // 批量添加数据列
            ['user_name', '审批人'],
            ['o_position', '部门职位'],
            ['update_time', '审批时间','datetime','没有填写日期时间', 'Y-m-d H:i'],
            ['result', '审批结果','status','', [0 => '进行中:info', 1=>'同意:success', 2=>'否决:danger']],
        ])
        ->setRowList($worklog_list) // 设置表格数据
        ->fetch(); // 渲染模板
    }


    //我的申请
    public function myflow(){   

            $order = $this->getOrder('w.id desc');
            $map = $this->getMap();
            $map['w.user_id'] = UID;
            $data_list = db::name('flow_work')
                    ->alias('w')
                    ->field('w.id,w.title,w.update_time,w.create_time,w.user_name,w.user_id,w.step,w.fid,admin_flow.title as ftitle')
                    ->join('admin_flow','w.fid=admin_flow.id','left')
                    ->where($map)
                    ->order($order)
                    ->paginate(10);

            $btn_detail = [
                'title' => '查看详情',
                'icon'  => 'fa fa-fw fa-eye',
                'href'  => url('flow_detail', ['wid' => '__id__'])
            ];

            return ZBuilder::make('table')
            ->addOrder(['id'=>'w.id','create_time'=>'w.create_time','update_time'=>'w.update_time']) // 添加排序
            ->addFilter(['ftitle'=>'admin_flow.title']) // 添加筛选
            ->setSearch(['w.title' => '标题'],'','','搜索')
            ->hideCheckbox()
            ->setPageTitle('我的申请') // 设置页面标题
            // ->addFilter(['type_title' => 'admin_flow_type.title']) // 添加筛选
            ->addColumns([ // 批量添加数据列
                ['id','流水号'],
                ['title', '标题'],
                ['ftitle', '流程名称'],
                ['create_time', '发起时间','datetime','没有填写日期时间', 'Y-m-d H:i'],
                ['update_time', '最后审批','datetime','没有填写日期时间', 'Y-m-d H:i'],
                ['step', '审批结果','status','',[20 =>'进行中:info', 30=>'否决:danger', 40=>'同意:success']],
                ['right_button', '详情', 'btn']
            ])
            ->addRightButton('detail', $btn_detail) // 添加授权按钮
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板
            
    }
    //流程详情
    public function flow_detail($wid=null){
        if(empty($wid))$this->error('请选择流程');
              $work = WorkModel::get($wid);
        $data = json_decode($work->udf_data,true);

        $fields = FieldModel::where('fid',$work->fid)->order('sort asc,id asc')->column(true);
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
                    break;
                case 'time':
                    $value['value'] = date('H:i',$value['value']);
                    break;
                case 'datetime':
                    $value['value'] = date('Y-m-d H:i',$value['value']);
                    break;
            }

            if($value['style']==1){
                $layout[$value['name']] = 6;
            }else{
                $layout[$value['name']] = 12;
            }

        }
        
        if(empty($layout))$layout=null;
        // 添加额外表单项信息
        $log_id = FlowLogModel::where(['wid'=>$wid,'result'=>0])->value('id');

        $worklist = [
            'title'=>$work->title,
            'content'=>$work->content
        ];
        //审批情况
        $worklog_list = FlowLogModel::where('wid',$wid)->order('id asc')->select();

        foreach ($worklog_list as $key => &$value) {

            $value['o_position'] = $this->_oposition($value['user_id']);
            $value['user_name'] = UserModel::where('id',$value['user_id'])->value('nickname');

        }
        $this->assign('log_id',$log_id);
        $this->assign('flow_title',$worklist['title']);
        $this->assign('worklog_list',$worklog_list);
        return ZBuilder::make('form')
            ->setPageTitle('流程办理') // 设置页面标题
             ->addFormItems([
                ['static:6', 'title', '标题'],
                ['static:6', 'send_time', '申请时间','',date('Y-m-d H:i',time())],
                ['static:6', 'username', '申请人','',get_nickname(UID)],
                ['static:6', 'o_p', '部门职位','',$this->_oposition($work->user_id)],         
            ])
            ->setFormItems($fields)
            ->setFormData($worklist)
            ->addStatic('str_confirm', '审批步骤','',$this->_confirm($work->fid))
            // ->addFile('add_file','上传附件')
            ->addTextarea('content', '原由')
            ->layout($layout)
            ->hideBtn('submit,back')
            ->fetch('flow_detail');

    }
    //获取部门职位 用>链接
    public function _oposition($user_id=null){
        $position = UserModel::where('id',$user_id)->value('position');
        $position = $position = PositionModel::get($position);
        return (OrganizationModel::where('id',$position->oid)->value('title')).'>'.$position->title;
    }


    //拼接审批步骤人员 
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

    //获取单据
    function danJu($fid=null,$confirm=''){

        $text = FlowModel::where('id',$fid)->value('dj');
                


    }

}
