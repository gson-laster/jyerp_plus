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

namespace app\meeting\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\meeting\model\Lists as ListModel;
use app\meeting\model\Rooms as RoomsModel;
use app\user\model\User as UserModel;
use app\user\model\Organization as OrgModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use util\Tree;
use think\Db;
use think\Request;
use app\meeting\model\MeetingUser as MUserModel;


/**
 * 用户默认控制器
 * @package app\user\admin
 */
class Index extends Admin
{
    /**
     * 用户首页
     * @return mixed
     */

    //我的会议
    public function index()
    {

        $order = $this->getOrder();
        $map = $this->getMap();
        if(isset($map['m_time']) && !empty($map['m_time'])){
	        $map['m_time'][1][0] = strtotime($map['m_time'][1][0]);
	        $map['m_time'][1][1] = strtotime($map['m_time'][1][1]);
        }
        $where = 'u.user_id='.UID;
        $data_list = ListModel::getMeetingList($map, $order, $where);
      
        $page = $data_list->render();
       // $users = Db::name('meeting_list')->column('user_id','id');
        $btn_access = [
            'title' => '详情',
            'icon'  => 'fa fa-fw fa-group',
            'href'  => url('groups', ['hid' => '__id__', 'rid' => '__rid__'])
        ];
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->addColumns([ // 批量添加列
                ['id', '序号'],
                ['title', '会议主题'],
                ['m_time','会议日期','datetime','','Y/m/d'],
                ['s_time','开始时间','datetime','','H:i'],  
                ['e_time', '结束时间','datetime','','H:i'],
                ['nickname','主持人'],
                ['name','会议地址'],
                ['is_read','是否已读', 'status','', [0 => '未读', 1 => '已阅']],
                ['s_time','状态','callback', [$this, 'format_status'], '__data__'],
                ['right_button', '操作', 'btn']
                ])
                ->addOrder('id,m_time', ['table' => 'meeting_list']) // 添加筛选
                
                ->addFilter('is_read', [0 => '未读', 1 => '已阅']) // 添加筛选
              //  ->addFilter('nickname') // 添加筛选
                ->setSearch([ 'title' => '会议名', 'nickname' => '主持人']) // 设置搜索参数
                ->setPageTitle('会议列表')
                ->addTimeFilter('m_time') // 添加时间段筛选
                ->hideCheckbox()
                ->addRightButton('jdkfjdkjfkdj', $btn_access,true)
                ->setRowList($data_list) // 设置表格数据
            	->setTableName('meeting_user')
                
                ->setPages($page) // 设置分页数据
                ->fetch();
    }

	//设置状态方法
	public function format_status($s='', $value=[]){
		$t = time();
		$e = $value['e_time'];
		if($t < $s){
			return '<span class="label label-primary">会议未开始</span>';
		} else if ($t > $e ) {
			return '<span class="label label-success">会议已结束</span>';
		} else {
			return '<span class="label label-danger">会议进行中</span>';
		}
	}
    //会议一览
        public function lists()
    {

        $order = $this->getOrder();
        $map = $this->getMap();
        if(isset($map['m_time']) && !empty($map['m_time'])){
	        $map['m_time'][1][0] = strtotime($map['m_time'][1][0]);
	        $map['m_time'][1][1] = strtotime($map['m_time'][1][1]);
        }
        if(empty($order)){
        	$order = 'm_time desc';
        }
        $data_list = ListModel::view('meeting_list') -> view('admin_user', 'nickname', 'meeting_list.compare=admin_user.id', 'left') -> where($map)->order($order)->paginate();
        $page = $data_list->render();
        $btn_access = [
            'title' => '参会人员',
            'icon'  => 'fa fa-fw fa-group',
            'href'  => url('groups', ['hid' => '__id__'])
        ];
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->addColumns([ // 批量添加列
                ['id', 'ID'],
                ['title', '会议主题'],
                ['m_time','会议日期','datetime','','Y/m/d'],
                ['s_time','开始时间','datetime','','H:i'],
                ['e_time', '结束时间','datetime','','H:i'],
                ['nickname','主持人'],
                ['s_time','状态','callback', [$this, 'format_status'], '__data__'],
                
                ['right_button', '操作', 'btn']
                ])
                // ->addFilter('name') // 添加筛选
                ->addOrder('id,m_time', ['table' => 'meeting_list']) // 添加筛选
              
                ->setSearch([ 'title' => '会议名', 'nickname' => '主持人']) // 设置搜索参数
                ->addTimeFilter('m_time') // 添加时间段筛选
            
                ->setPageTitle('会议列表')
                ->addRightButton('edit')
                ->hideCheckbox()
                ->addRightButton('jdkfjdkjfkdj', $btn_access,true)
                ->setRowList($data_list) // 设置表格数据
                ->setPages($page) // 设置分页数据
                ->fetch();
    }

    //新增会议
    public function add()
    {

        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            $result = $this -> validate($data, 'AddMeeting');
            if($result !== true){
            	$this -> error($result);
            }
            $data['s_time'] = strtotime($data['m_time'].' '.$data['s_time']);
            $data['e_time'] = strtotime($data['m_time'].' '.$data['e_time']);
            if($data['s_time'] > $data['e_time']){
            	$this -> error('开始时间不得大于结束时间');
            }
            $data['m_time'] = strtotime($data['m_time']);
            $data['compare'] = $data['zrid'];
            $where = '(s_time<'.$data["e_time"].' AND s_time>'.$data["s_time"].') OR (e_time<'.$data["e_time"].' AND e_time>'.$data["s_time"].')';
            $map = [];
            $map['room_id'] = $data["room_id"];
            $title = ListModel::where($map) -> where($where) -> field('title') -> find();
        	if($title){
           	 	$this -> error('与'.$title['title'].'的时间冲突');
           	}
           	if($data['type'] == 0) {
           		$data['helpid'] = str_replace('undefined,','', $data['helpid']);
           		if(empty($data['helpid'])){
           			$this -> error('参会人员不能为空');
           		}
           		$data['user_id'] = substr($data['helpid'], 1, -1);
            	
           	} else if($data['type'] == 1) {
       			if(empty($data['ori_id'])){
           			$this -> error('参会部门不能为空');
           		}
           		$map = [];
           		$map['organization'] = ['in', $data['ori_id']];
            	$user_list = Db::name('admin_user') -> where($map) -> field('id') -> select();
            	$data['user_id'] = '';
            	foreach($user_list as $v){
            		$data['user_id'].=$v['id'].',';
            	}
            	$data['user_id'] = substr($data['user_id'], 0, -1);

           	}
            if ($slider = ListModel::create($data)) {
                $m = $this -> meeting_user($slider['id']);
                	if($m){
				    		$this -> success('添加成功', 'index');
				    	} else {
				    		ListModel::where(['id' => $slider['id']]) -> delete();
				    		$this -> error('添加失败');
				    	}
            } else {
                $this->error('新增失败');
            }
        }

        // 显示添加页面
        $room_list = RoomsModel::where(['state' => 1]) -> column('id,name');
        return ZBuilder::make('form')
            ->addFormItems([
          		['hidden', 'helpid'],
          		['hidden', 'zrid'],
                ['text', 'title', '会议主题'],
                ['date', 'm_time', '会议日期', '', '', 'yyyy/mm/dd'],
                ['time','s_time','开始时间','','','HH:mm'],
                ['time','e_time','结束时间','','','HH:mm'],
                ['text', 'zrname', '主持人', ''],
                ['select', 'room_id', '会议地点', '', $room_list],
                
                ['select','type','类型', '', [0 => '通知到人', 1 => '通知到部门'], 1],
                ['text', 'helpname', '参会人员', '']
            ])
            ->setTrigger('type', '0', 'helpname')
            ->setTrigger('type', '1', 'ori_id')
            ->addSelect('ori_id','参会部门','请选择',OrgModel::getMenuTree2(),'','multiple')
		    ->setExtraJs(outjs2())
		    ->setExtraHtml(outhtml2())
            ->fetch();
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

    public function groups($hid=null, $rid=null){
    	if(!is_null($rid)){
    		MUserModel::where(['id' => $rid]) -> update(['is_read' => 1]);
    	}
        if($hid==null)$this->error("请选择会议");
        $detail = ListModel::getOne('l.id='.$hid);
        $detail['m_time'] = date('Y-m-d', $detail['m_time']);
        $detail['s_time'] = date('H:i', $detail['s_time']);
        $detail['e_time'] = date('H:i', $detail['e_time']);
        $user_id = explode(',', $detail['user_id']);
        $user_name = implode(',',UserModel::where(['id'=>['in',$user_id]])->column('nickname'));
         return ZBuilder::make('form')
         -> addFormItems([
               	['static', 'title', '会议主题'],
                ['static', 'm_time','会议日期'],
                ['static', 's_time','开始时间'],  
                ['static', 'e_time', '结束时间'],
                ['static', 'nickname','主持人'],
                ['static', 'name','会议地址'],
                ])
            ->setFormData($detail)
            ->addStatic('', '参会人员', '',$user_name)
            ->hideBtn('submit')
            ->fetch();
    }

     public function edit($id = null)
    {
        if ($id === null) $this->error('缺少参数');

        // 保存数据
        if ($this->request->isPost()) {

            $data = $this->request->post();
            $data['zrid'] = $data['compare'];
          $result = $this -> validate($data, 'AddMeeting');
            if($result !== true){
            	$this -> error($result);
            }
            $data['s_time'] = strtotime($data['m_time'].' '.$data['s_time']);
            $data['e_time'] = strtotime($data['m_time'].' '.$data['e_time']);
            if($data['s_time'] > $data['e_time']){
            	$this -> error('开始时间不得大于结束时间');
            }
            $data['m_time'] = strtotime($data['m_time']);
            $where = '(s_time<'.$data["e_time"].' AND s_time>'.$data["s_time"].') OR (e_time<'.$data["e_time"].' AND e_time>'.$data["s_time"].')';
            $map = [];
            $map['room_id'] = $data["room_id"];
            $map['id'] = ['neq', $id];
            $title = ListModel::where($map) -> where($where) -> field('title') -> find();
        	if($title){
           	 	$this -> error('与'.$title['title'].'的时间冲突');
           	}
            if ($user = ListModel::update($data)) {

                $this->success('编辑成功', url('index'));
            } else {
                $this->error('编辑失败');
            }
        }

        // 获取数据
        $info = ListModel::where('id', $id)->find();
        // 使用ZBuilder快速创建表单
        $room_list = RoomsModel::where(['state' => 1]) -> column('id,name');
        
        return ZBuilder::make('form')
            ->setPageTitle('编辑') // 设置页面标题
            ->addFormItems([ // 批量添加表单项
                ['hidden', 'id'],
                ['text', 'title', '会议标题'],
            ])
            ->addDate('m_time','开会日期','',$info['m_time'])
            ->addTime('s_time','开会时间','',$info['s_time'],'HH:mm')
            ->addTime('e_time','会议结束','',$info['e_time'],'HH:mm')
            ->addSelect('compare','主持人','请选择主持人',UserModel::getUser())
            ->addSelect('room_id','会议地点','请选择会议地点',$room_list)
            
            ->setFormData($info) // 设置表单数据
            ->fetch();
    }

    

}
