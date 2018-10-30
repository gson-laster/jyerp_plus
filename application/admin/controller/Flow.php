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

namespace app\admin\controller;

use app\common\builder\ZBuilder;
use app\admin\model\Flow as FlowModel;
use app\admin\model\FlowType as FlowTypeModel;
use app\user\model\Organization as OrganizationModel;
use app\user\model\Position as PositionModel;
use app\admin\model\Module as ModuleModel;
use think\Db;

/**
 * 流程设置
 * @package app\admin\controller
 */
class Flow extends Admin
{
    /**
     * 首页
     * @author 王永吉 <739712704@qq.com>
     * @return mixed
     */
    public function index()
    {
        $map = $this->getMap();
        $data_list = FlowModel::getList($map);
        $fields = [
                ['hidden', 'id'],
                ['select:6', 'tid','流程类型','选择类型',FlowTypeModel::getList()],
                ['text:6', 'doc_no_format', '编号', '编号'],
                ['text:6', 'title', '流程名称', '流程名称'],
                ['text:6', 'short', '简称', '简称'],
                ['number:6', 'sort', '排序','',100],

        ];

        $btn_access = [
            'title' => '审批步骤',
            'icon'  => 'fa fa-fw fa-sticky-note',
            'href'  => url('examine', ['fid' => '__id__'])
        ];
        $btn_field = [
            'title' => '流程表单项',
            'icon'  => 'fa fa-fw fa-table',
            'href'  => url('flowfield/index', ['fid' => '__id__'])
        ];
        $btn_dj = [
            'title' => '单据',
            'icon'  => 'fa fa-fw fa-hospital-o',
            'href'  => url('djadd', ['fid' => '__id__'])
        ];
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->hideCheckbox()
            ->setPageTitle('流程列表') // 设置页面标题
            ->setSearch(['title' => '名称']) // 设置搜索框
            ->addFilter(['type_title' => 'admin_flow_type.title']) // 添加筛选
            ->addColumns([ // 批量添加数据列
                ['title', '名称'],
                ['doc_no_format', '文档编码'],
                ['type_title', '类型'],
                ['sort', '排序','text.edit'],
                ['status', '是否显示', 'switch'],
                ['right_button', '操作', 'btn']
            ])
            ->autoAdd($fields) // 添加新增按钮
            ->autoEdit($fields) // 添加新增按钮
            ->addRightButtons('delete') // 批量添加右侧按钮
            ->addRightButton('examine', $btn_access,true)
            ->addRightButton('field', $btn_field) 
            ->addRightButton('dj', $btn_dj) 
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板
    }
    //审批步骤
    public function examine($fid=null){
        if($fid==null){
            $this->error('请选择流程');
        }
        if ($this->request->isPost()) {
            $data['id'] = $this->request->post('fid');
            $data['confirm'] = $this->request->post('splc');
            if(empty($data['confirm']) || $data['confirm']=="form") $this->error('步骤不能为空');
            if(FlowModel::update($data)){
                  $this->success('设置成功');
              }else{
                  $this->error('设置失败');
              }
        }else{
            $organization = json_encode(OrganizationModel::getMenuTree2());
            $position = json_encode(Db::name('admin_position')->where('status',1)->field('id,oid,pid,title')->select());
            $flow = FlowModel::field('confirm')->find($fid);
            if(!empty($flow['confirm'])){
                $flow = explode('-',ltrim($flow['confirm'],'form-'));
                $this->assign('flow',$flow);
            }
            $this->assign('organization',$organization);
            $this->assign('position',$position);
            $this->assign('fid',$fid);
            return $this->fetch('examine');
        }

    }

    //单据
    public function djadd($fid=null){
             if($fid==null){
                $this->error('请选择流程');
             }
             //获取流程
            // $organization = json_encode(OrganizationModel::getMenuTree2());
            // $position = json_encode(Db::name('admin_position')->where('status',1)->field('id,oid,pid,title')->select());
            // $flow = FlowModel::field('confirm')->find($fid);
            // if(!empty($flow['confirm'])){
            //     $flow = explode('-',ltrim($flow['confirm'],'form-'));
            //     $this->assign('flow',$flow);

            // }
             if($this->request->isPost()){
                $data = $this->request->post();
                if($data['dj']=='' || empty($data['dj']) || $data['dj']=="<p><br></p>"){
                    $this->error('单据不能为空');
                }else{
                    $data = ['id'=>$data['id'],'dj'=>$data['dj'],'is_dj'=>1];
                    $flow = new FlowModel();
                    if($flow->isUpdate(true)->save($data)){
                        $this->success('修改成功');
                    }else{
                        $this->error('修改失败');
                    }
                }
            }
            $flow = FlowModel::find($fid);

            return ZBuilder::make('form')
               ->setPageTitle('单据') // 设置页面标题
               ->addFormItems([
                ['hidden','id'],
                ['wangeditor','dj', '内容']
            ])
            ->setFormData($flow)
            ->fetch();
    }
}
