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
use app\admin\model\Itemflow as ItemflowModel;
use app\admin\model\Module as ModuleModel;
use app\user\model\Organization as OrganizationModel;
use app\user\model\Position as PositionModel;

/**
 * 项目审批
 * @package app\admin\controller
 */
class Itemflow extends Admin
{
    /**
     * 首页
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function index()
    {
        // // 查询
        $map = $this->getMap();
        // // 数据列表
        $data_list = ItemflowModel::where($map)->order('id desc')->paginate();
        // // 所有模块的名称和标题
        $list_module = ModuleModel::getModule();

        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setPageTitle('项目流程') // 设置页面标题
            ->setSearch(['name' => '标识', 'title' => '名称']) // 设置搜索框
            ->addColumns([ // 批量添加数据列
                ['id', 'ID'],
                ['name', '标识'],
                ['title', '名称'],
                // ['remark', '描述'],
                ['module', '所属模块', 'callback', function($module, $list_module){
                    return isset($list_module[$module]) ? $list_module[$module] : '未知';
                }, $list_module],
                ['status', '状态', 'switch'],
                ['right_button', '操作', 'btn']
            ])
            // ->autoAdd($fields, '', true, true) // 添加自动新增按钮
            // ->autoEdit($fields, '', true, true) // 添加自动编辑按钮
            ->addTopButtons('add,enable,disable,delete') // 批量添加顶部按钮
            ->addRightButtons('edit,delete') // 批量添加右侧按钮
            ->addFilter('module', $list_module)
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板
    }


    public function add(){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            //验证
            $result = $this->validate($data, 'Itemflow');
            //验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if ($res = ItemflowModel::create($data)) {
                   
                // 记录行为
                $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }

        }
                $js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
    $('#flow_html').click(function(){
            var flow = $('#flow').val();
            //iframe窗
            layer.open({
              type: 2,
              title: '选择负责人',
              shadeClose: true,
              shade: 0.3,
              maxmin: true, //开启最大化最小化按钮
              area: ['70%', '70%'],
              content: '/admin.php/admin/itemflow/examine/flow/'+flow
            });      
    })})
</script>
EOF;
        $list_module = ModuleModel::getModule();
        return ZBuilder::make('form')
        ->setPageTitle('流程设置')
        ->addFormItems([
                    ['select', 'module', '所属模块', '', $list_module],
                    ['text', 'name', '行为标识', '由英文字母和下划线组成'],
                    ['text', 'title', '行为名称', ''],
                    ['textarea','flow_html','审批步骤'],
                    ['hidden','flow'],
                    ['radio', 'status', '立即启用', '', ['否', '是'], 1],
                ])
        ->setExtraJs($js) 
        ->fetch();
    }

    public function edit($id=null){

        if($id==null)$this->error('缺少更新条件');

            if ($this->request->isPost()) {
            $data = $this->request->post();
            //验证
            $result = $this->validate($data, 'Itemflow');
            //验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            $itemflow = new ItemflowModel();
            if ($itemflow->isUpdate(true)->save($data)) {
                 $this->success('修改成功',url('index'));
            }else {
                $this->error('修改失败');
            }

        }
                $js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
    $('#flow_html').click(function(){
            var flow = $('#flow').val();
            //iframe窗
            layer.open({
              type: 2,
              title: '选择负责人',
              shadeClose: true,
              shade: 0.3,
              maxmin: true, //开启最大化最小化按钮
              area: ['70%', '70%'],
              content: '/admin.php/admin/itemflow/examine/flow/'+flow
            });      
    })})
</script>
EOF;
        $itemflow = ItemflowModel::where('id',$id)->find();
        $list_module = ModuleModel::getModule();
        return ZBuilder::make('form')
        ->setPageTitle('流程设置')
        ->addFormItems([
                    ['hidden','id'],
                    ['select', 'module', '所属模块', '', $list_module],
                    ['text', 'name', '行为标识', '由英文字母和下划线组成'],
                    ['text', 'title', '行为名称', ''],
                    ['textarea','flow_html','审批步骤'],
                    ['hidden','flow'],
                    ['radio', 'status', '立即启用', '', ['否', '是'], 1],
                ])
        ->setExtraJs($js) 
        ->setFormData($itemflow)
        ->fetch();

    }
    public function examine($flow=null){
            $organization = json_encode(OrganizationModel::getMenuTree2());
            $position = json_encode(PositionModel::where('status',1)->field('id,oid,pid,title')->select());
            // $flow = FlowModel::field('confirm')->find($fid);
            if(!empty($flow)){
                $flow = explode('-',ltrim($flow,'form-'));
                $this->assign('flow',$flow);
            }
            $this->assign('organization',$organization); 
            $this->assign('position',$position);
            // return $this->fetch('examine');
        
            return $this->fetch('examine');
        

    }
}