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
use app\flow\model\Itemdetail as ItemdetailModel;
use app\flow\model\Itemdetailstep;
use app\admin\model\Module as ModuleModel;
use app\user\model\User as UserModel;
use app\user\model\Position as PositionModel;
use app\user\model\Organization as OrganizationModel;
use think\Db;
use think\Validate;


/**
 * 流程
 * @package 
 */
class Item extends Admin
{
    /**
     * 首页
     * @author 王永吉 <739712704@qq.com>
     * @return mixed
     */
    public function myflow()
    {
            $order = $this->getOrder('flow_itemdetail.id desc');
            $map = $this->getMap();

            //审批 失败成功 筛选
            if (!empty($map['flow_itemdetail.step']) || isset($map['flow_itemdetail.step'])) {
                if($map['flow_itemdetail.step']==1){
                    unset($map['flow_itemdetail.step']);
                }
            }
 

            $map['flow_itemdetail.uid'] = UID;

            $data_list = ItemdetailModel::getList($map,$order);
            // $btn_detail = [
            //     'title' => '查看详情',
            //     'icon'  => 'fa fa-fw fa-eye',
            //     'href'  => url('guo', ['wid' => '__id__'])
            // ];
            $list_module = ModuleModel::getModule();

            $btn_Guo = [
                'title' => '过程',
                'icon'  => 'fa fa-fw fa-sort-amount-desc',
                'href'  => url('guo', ['wid' => '__id__'])
            ];
            return ZBuilder::make('table')
            ->addOrder(['create_time'=>'flow_itemdetail.create_time','update_time'=>'flow_itemdetail.update_time']) // 添加排序
            ->addFilter('admin_itemflow.module',$list_module) // 添加筛选
            ->setSearch(['flow_itemdetail.title' => '标题'],'','','搜索')
            ->hideCheckbox()
            ->setPageTitle('我的申请') // 设置页面标题
            // ->addFilter(['type_title' => 'admin_flow_type.title']) // 添加筛选
            ->addColumns([ // 批量添加数据列
                ['title', '标题'],
                ['flow_title', '流程名称'],
                ['module', '所属模块', 'callback', function($module, $list_module){
                    return isset($list_module[$module]) ? $list_module[$module] : '未知';
                }, $list_module],
                ['create_time', '发起时间','datetime'],
                ['update_time', '最后审批时间','datetime'],
                ['step', '审批结果','status','',[20 =>'进行中:info', 30=>'否决:danger', 40=>'同意:success']],
                ['url', '审批内容'],
                ['right_button', '审批详情', 'btn']
            ])
            ->addTopSelect('flow_itemdetail.step', '', [1=>'全部',30=>'审批失败',40=>'审批成功',20=>'审批中'],1) 
            ->addRightButton('guo',$btn_Guo,true) // 添加授权按钮
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板
    }

    // 审批过程
    public function guo($wid=null){
        if($wid==null)$this->error('请选择流程');
        //审批情况
        $item_list = Itemdetailstep::where('itemdetail_id',$wid)->order('id asc')->select();
        foreach ($item_list as $key => &$value) {

            $value['o_position'] = $this->_oposition($value['user_id']);
            $value['user_name'] = get_nickname($value['user_id']);

        }
        return ZBuilder::make('table')
        ->hideCheckbox()
        ->setPageTitle('审批过程') // 设置页面标题
        // ->addFilter(['type_title' => 'admin_flow_type.title']) // 添加筛选
        ->addColumns([ // 批量添加数据列
            ['user_name', '审批人'],
            ['o_position', '部门职位'],
            ['update_time', '审批时间','datetime','未审批', 'Y-m-d H:i'],
            ['result', '审批结果','status','', [0 => '进行中:info', 1=>'同意:success', 2=>'否决:danger']],
            ['remark', '备注'],
        ])
        ->setRowList($item_list) // 设置表格数据
        ->fetch(); // 渲染模板
    }
   
    //代办流程
    public function handletask(){

            $order = $this->getOrder('flow_itemdetail_step.id desc');
            $map = $this->getMap();
            $map['flow_itemdetail_step.result'] = 0;
            $map['flow_itemdetail_step.user_id'] = UID;

            $data_list = ItemdetailModel::getMyflow($map,$order);
            $list_module = ModuleModel::getModule();
            // dump($data_list);
            // die;
            $btn_Ban = [
                'title' => '流程办理',
                'icon'  => 'fa fa-fw fa-legal',
                'href'  => url('ban', ['wid' => '__id__'])
            ];


            return ZBuilder::make('table')
            ->addTimeFilter('flow_itemdetail.create_time') // 添加时间段筛选
            ->addOrder(['ctime'=>'flow_itemdetail.create_time','utime'=>'flow_itemdetail.update_time']) // 添加排序
            ->addFilter('admin_itemflow.module',$list_module) // 添加筛选
            ->setSearch(['flow_itemdetail.title' => '标题', 'admin_user.nickname' => '发起人'],'','','搜索')
            ->hideCheckbox()
            ->setPageTitle('待办流程') // 设置页面标题
            // ->addFilter(['type_title' => 'admin_flow_type.title']) // 添加筛选
            ->addColumns([ // 批量添加数据列
                ['wtitle', '标题'],
                ['ftitle', '行为名称'],
                ['module', '所属模块',$list_module],
                ['fnickname', '发起人'],
                ['ctime', '发起时间','datetime','没有填写日期时间', 'Y-m-d H:i'],
                ['utime', '最后审批时间','datetime','没有填写日期时间', 'Y-m-d H:i'],
                ['url', '审批内容'],
                ['right_button', '操作', 'btn']
            ])
            ->addRightButton('ban', $btn_Ban) // 添加授权按钮
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板
    }


    // 已办流程
    public function handletask_ok(){

            $order = $this->getOrder('flow_itemdetail_step.id desc');
            $map = $this->getMap();
            $map['flow_itemdetail_step.result'] = ['>',0];
            $map['flow_itemdetail_step.user_id'] = UID;

            $data_list = ItemdetailModel::getMyflow($map,$order);
            $list_module = ModuleModel::getModule();

            $btn_Guo = [
                'title' => '过程',
                'icon'  => 'fa fa-fw fa-sort-amount-desc',
                'href'  => url('guo', ['wid' => '__id__'])
            ];

            return ZBuilder::make('table')
            ->addTimeFilter('flow_itemdetail.create_time') // 添加时间段筛选
            ->addOrder(['ctime'=>'flow_itemdetail.create_time','utime'=>'flow_itemdetail.update_time']) // 添加排序
            ->addFilter('admin_itemflow.module',$list_module) // 添加筛选
            ->setSearch(['flow_itemdetail.title' => '标题', 'admin_user.nickname' => '发起人'],'','','搜索')
            ->hideCheckbox()
            ->setPageTitle('待办流程') // 设置页面标题
            // ->addFilter(['type_title' => 'admin_flow_type.title']) // 添加筛选
            ->addColumns([ // 批量添加数据列
                ['wtitle', '标题'],
                ['ftitle', '行为名称'],
                ['module', '所属模块',$list_module],
                ['fnickname', '发起人'],
                ['ctime', '发起时间','datetime','没有填写日期时间', 'Y-m-d H:i'],
                ['utime', '最后审批时间','datetime','没有填写日期时间', 'Y-m-d H:i'],
                ['step', '审批结果','status','',[20 =>'进行中:info', 30=>'否决:danger', 40=>'同意:success']],
                ['url', '审批内容'],
                ['right_button', '操作', 'btn']
            ])
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

            $result = $this->validate($data, 'Itemdetailstep');
            if(true !== $result) $this->error($result);
            $data['update_time'] = time();
            $uid = Itemdetailstep::where('id',$data['id'])->value('user_id');
            if(UID!=$uid){
                 $this->error('请审批人审批');           
            }
            if(Itemdetailstep::update($data)){

                if($data['result']==1){
                    next_step($wid,Itemdetailstep::where('id',$data['id'])->value('step'));
                }else{
                    ItemdetailModel::update(['id'=>$wid,'step'=>30]);
                    $thisflow = ItemdetailModel::where('id',$wid)->field('trigger_id,table')->find();
                    db::name($thisflow['table'])->where('id',$thisflow['trigger_id'])->update(['status'=>2]);
                }
                ItemdetailModel::update(['id'=>$wid,'update_time'=>time()]);
                $this->success('审批成功',url('handletask'));

            }else{
                $this->error('审批失败');
            }
        }

        $item_list = Itemdetailstep::where('itemdetail_id',$wid)->order('id asc')->select();
        foreach ($item_list as $key => &$value) {

            $value['o_position'] = $this->_oposition($value['user_id']);
            $value['user_name'] = get_nickname($value['user_id']);

        }
        $this->assign('log_id',Itemdetailstep::where(['itemdetail_id'=>$wid,'result'=>0])->value('id'));
        $this->assign('item_list',$item_list);

        return ZBuilder::make('form')->fetch('ban');
        
    }

    
    //获取部门职位 用>链接
    public function _oposition($user_id=null){
        $position = UserModel::where('id',$user_id)->value('position');
        $position = PositionModel::get($position);
        return (OrganizationModel::where('id',$position->oid)->value('title')).'>'.$position->title;
    }


}


