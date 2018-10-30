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
use app\meeting\model\Rooms as RoomsModel;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
// use app\meeting\model\Meeting as MeetingModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use app\user\model\Role as RoleModel;
use util\Tree;
use think\Db;
use think\Request;
use think\Validate;
use app\meeting\model\Lists as ListModel;

/**
 * 用户默认控制器
 * @package app\user\admin
 */
class Meeting extends Admin
{

	protected $occupy_room_id = [];
	
	public function _initialize(){
		parent::_initialize();
		$this -> occupy_room_id = ListModel::getRoom();
	}
    /**
     * 新增
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function add()
    {
    
        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();
            // 验证
            $result = $this->validate($data, 'User');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if ($user = UserModel::create($data)) {
                Hook::listen('user_add', $user);
                // 记录行为
                action_log('user_add', 'admin_user', $user['id'], UID);
                $this->success('新增成功', url('index'));
            } else {
                $this->error('新增失败');
            }
        }

        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('新增') // 设置页面标题
            ->addFormItems([ // 批量添加表单项
                ['text', 'username', '用户名', '必填，可由英文字母、数字组成'],
                ['text', 'nickname', '昵称', '可以是中文'],
                ['select', 'role', '角色', '', RoleModel::getTree(null, false)],
                ['text', 'email', '邮箱', ''],
                ['password', 'password', '密码', '必填，6-20位'],
                ['text', 'mobile', '手机号'],
                ['image', 'avatar', '头像'],
                ['radio', 'status', '状态', '', ['禁用', '启用'], 1]
            ])
            ->fetch();
    }

    /**
     * 编辑
     * @param null $id 用户id
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function edit($id = null)
    {
        if ($id === null) $this->error('缺少参数');

        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();

            if (RoomsModel::update($data)) {
                $user = RoomsModel::get($data['id']);
                // Hook::listen('user_edit', $user);
                // 记录行为
                // action_log('user_edit', 'admin_user', $user['id'], UID, get_nickname($user['id']));
                $this->success('编辑成功','meeting/rooms');
            } else {
                $this->error('编辑失败');
            }
        }

        // 获取数据
        $info = RoomsModel::where('id', $id)->field('password', true)->find();

        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('编辑') // 设置页面标题
            ->addFormItems([ // 批量添加表单项
                ['hidden', 'id'],
                ['text', 'name', '会议室名字'],
                ['text', 'r_number','可容纳人数'],
                ['text','r_resource','配置资源'],
                ['text','r_sort','显示顺序'],
            ])
            ->setFormData($info) // 设置表单数据
            ->fetch();
    }

    /**
     * 删除用户
     * @param array $ids 用户id
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function delete($ids = [])
    {
        // Hook::listen('user_delete', $ids);
        return $this->setStatus('delete');
    }



    //会议室列表
    public function rooms()
    {
        $order = $this->getOrder();
        $map = $this->getMap();
        if(empty($order)){
        	$order = 'r_sort asc';
        }
        $data_list = RoomsModel::where($map)->order($order)->paginate();
        $page = $data_list->render();
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->addColumns([ // 批量添加列
                ['id', 'ID'],
                ['name', '会议室'],
                ['r_number','可容纳人数'],
                ['r_resource', '资源配备'],
                ['r_sort','显示顺序'],
                ['name','状态', 'callback', [$this, 'setStatus'], '__data__'],
                ['right_button', '操作', 'btn']
                ])
                // ->addFilter('name') // 添加筛选
                ->addOrder('id,r_sort') // 添加筛选
                ->addRightButton('edit',['table' => 'meeting_rooms']) // 添加顶部按钮
                ->addRightButton('delete',['table' => 'meeting_rooms']) // 添加顶部按钮
                ->addTopButton('delete',['table' => 'meeting_rooms']) // 添加顶部按钮
                ->setPageTitle('会议室')
                ->setRowList($data_list) // 设置表格数据
                ->setPages($page) // 设置分页数据
                ->fetch(); 
    }
	public function setStatus($id = '', $v = []){
		$i = $v['id'];
		if(in_array($i, $this -> occupy_room_id)){
			return '<span class="label label-danger">当前时间已占用</span>';
		} else {
			return '<span class="label label-primary">当前时间未占用</span>';
		}
	}
    //新增会议室
    public function add_room()
    {
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
			$r = $this -> validate($data, 'Meeting');
			if($r !== true){
				$this -> error($r);
			}

            if ($slider = RoomsModel::create($data)) {
                $this->success('新增成功', 'meeting/rooms');
            } else {
                $this->error('新增失败');
            }
        }

        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([
                ['text', 'name', '会议室名字'],
                ['text', 'r_number','可容纳人数'],
                ['text','r_resource','配置资源'],
                ['text','r_sort','显示顺序'],
            ])
            ->fetch();
    }
}
