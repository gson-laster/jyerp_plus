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
        $data_list = ListModel::where($map)->order($order)->paginate();
        $page = $data_list->render();
        $users = Db::name('meeting_list')->column('user_id','id');
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
                ['compare','主持人',UserModel::getUser()],
                ['right_button', '操作', 'btn']
                ])
                // ->addFilter('name') // 添加筛选
                ->addOrder('id') // 添加筛选
                ->addTopButton('add') // 添加顶部按钮
                // ->addTopButton('enable',['table' => 'gift_list']) // 添加顶部按钮
                // ->addTopButton('disable',['table' => 'gift_list']) // 添加顶部按钮
                ->setPageTitle('会议列表')
                ->addRightButton('edit')
                ->addRightButton('jdkfjdkjfkdj', $btn_access,true)
                ->setRowList($data_list) // 设置表格数据
                ->setPages($page) // 设置分页数据
                ->fetch();
    }

    //会议一览
        public function lists()
    {

        $order = $this->getOrder();
        $map = $this->getMap();
        $data_list = ListModel::where($map)->order($order)->paginate();
        $page = $data_list->render();
        $users = Db::name('meeting_list')->column('user_id','id');
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
                ['compare','主持人',UserModel::getUser()],
                ['right_button', '操作', 'btn']
                ])
                // ->addFilter('name') // 添加筛选
                ->addOrder('id') // 添加筛选
                ->addTopButton('add') // 添加顶部按钮
                // ->addTopButton('enable',['table' => 'gift_list']) // 添加顶部按钮
                // ->addTopButton('disable',['table' => 'gift_list']) // 添加顶部按钮
                ->setPageTitle('会议列表')
                ->addRightButton('edit')
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
            $data['m_time'] = strtotime($data['m_time']);
            $data['s_time'] = strtotime($data['s_time']);
            $data['e_time'] = strtotime($data['e_time']);
            $data['user_id'] = implode(',', $data['user_id']);

            if ($slider = ListModel::create($data)) {
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }

        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([
                ['text', 'title', '会议主题'],
                ['date', 'm_time', '会议日期', '', '', 'yyyy/mm/dd'],
                ['time','s_time','开始时间','','','HH:mm'],
                ['time','e_time','结束时间','','','HH:mm'],
            ])
            ->addSelect('compare','主持人','请选择主持人',UserModel::getUser())
            ->addSelect('user_id','参会部门','请选择',OrgModel::getMenuTree2(),'','multiple')
            ->fetch();
    }

    public function groups($hid=null){
        if($hid==null)$this->error("请选择会议");
        $user_id = ListModel::where('id',$hid)->value('user_id');
        $user_name = implode(',',UserModel::where(['id'=>['in',$user_id]])->column('nickname'));
         return ZBuilder::make('form')
            ->addStatic('user_id', '参会人员', '',$user_name)
            ->hideBtn('submit')
            ->fetch();
    }

     public function edit($id = null)
    {
        if ($id === null) $this->error('缺少参数');

        // 保存数据
        if ($this->request->isPost()) {

            $data = $this->request->post();
            $data['m_time'] = strtotime($data['m_time']);
            $data['s_time'] = strtotime($data['s_time']);
            $data['e_time'] = strtotime($data['e_time']);
            if ($user = ListModel::update($data)) {

                $this->success('编辑成功', url('index'));
            } else {
                $this->error('编辑失败');
            }
        }

        // 获取数据
        $info = ListModel::where('id', $id)->find();
        // 使用ZBuilder快速创建表单
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
            ->setFormData($info) // 设置表单数据
            ->fetch();
    }

    

}
