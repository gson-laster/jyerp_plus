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
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\admin\model\FlowField as FieldModel;
use app\admin\model\Flow as FlowModel;
use think\Db;

/**
 * 字段管理
 * @package app\admin\controller;
 */
class Flowfield extends Admin
{
    /**
     * 字段列表
     * @param null $id 流程表单项
     * @author 王永吉 <739712704@qq.com>
     */
    public function index($fid = null)
    {
        $fid === null && $this->error('参数错误');
        cookie('__forward__', $_SERVER['REQUEST_URI']);
        // // 查询
        $map = $this->getMap();
        $map['fid'] = $fid;
        // // 数据列表
        $data_list = FieldModel::where($map)->order('id desc')->paginate();
        $pageTitle = FlowModel::where('id',$fid)->value('title');
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setPageTitle($pageTitle.'-表单项')
            ->setSearch(['title' => '标题']) // 设置搜索框
            ->hideCheckbox()
            ->addColumns([ // 批量添加数据列
               ['id', 'ID'],
               ['title', '标题'],
               ['type', '类型', 'text', '', config('form_item_type')],
               ['create_time', '创建时间', 'datetime'],
               ['sort', '排序'],
               ['right_button', '操作', 'btn']
            ])
            ->addTopButton('back', ['href' => url('flow/index')]) // 批量添加顶部按钮
            ->addTopButton('add', ['href' => url('add', ['fid' => $fid])]) // 添加顶部按钮
            ->addRightButtons('edit,delete') // 批量添加右侧按钮
            ->setTableName('admin_flow_field') // 指定数据表名
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板
    }

    /**
     * 新增
     * @param string $fid 流程id
     * @author 王永吉<739712704@qq.com>
     * @return mixed
     */
    public function add($fid = '')
    {
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            
            $result = $this->validate($data, 'Flowfield');
            if(true !== $result) $this->error($result);
            
            // 如果是快速联动
            switch ($data['type']) {
                case 'linkages':
                    $data['key']    = $data['key']    == '' ? 'id'   : $data['key'];
                    $data['pid']    = $data['pid']    == '' ? 'pid'  : $data['pid'];
                    $data['level']  = $data['level']  == '' ? '2'    : $data['level'];
                    $data['option'] = $data['option'] == '' ? 'name' : $data['option'];
                    break;
                case 'number':
                    $data['type'] = 'text';
                    break;
            }
            $data['name'] = $data['title'];
            if ($field = FieldModel::create($data)) {
                    $this->success('新增成功', cookie('__forward__'));
            } else {
                $this->error('新增失败');
            }
        }
        
        $pageTitle = FlowModel::where('id',$fid)->value('title');
        // 显示添加页面
        return ZBuilder::make('form')
            ->setPageTitle($pageTitle.'-添加表单项')
            ->addFormItems([
                ['hidden', 'fid', $fid],
                ['text', 'title', '字段标题', '可填写中文,如:请假开始日期'],
                ['select', 'type', '字段类型', '', config('form_item_type')],
                ['select', 'define', '字段定义', '',[1=>"不可为空",2=>"必须为数字",3=>"邮箱"]],
                ['text', 'value', '字段默认值','如果是单选、多选、下拉、联动,则填写文字对应的数字,如 “男,女” 对应"0,1",默认值为男,这里为0'],
                ['select', 'style', '字段样式', '员工填写表单时此行所占', [1=>"半行",0=>"整行"]],
                ['textarea', 'options', '显示文字', '用于单选、多选、下拉、联动,如 “男,女”'],
                ['text', 'ajax_url', '异步请求地址', "如请求的地址是 <code>url('ajax/getCity')</code>，那么只需填写 <code>ajax/getCity</code>，或者直接填写以 <code>http</code>开头的url地址"],
                ['text', 'next_items', '下一级联动下拉框的表单名', "与当前有关联的下级联动下拉框名，多个用逗号隔开，如：area,other"],
                ['text', 'param', '请求参数名', "联动下拉框请求参数名，默认为配置名称"],
                ['text', 'level', '级别', '需要显示的级别数量，默认为2', 2],
                ['text', 'table', '表名', '要查询的表，里面必须含有id、name、pid三个字段，其中id和name字段可在下面重新定义'],
                ['text', 'pid', '父级id字段名', '即表中的父级ID字段名，如果表中的主键字段名为pid则可不填写'],
                ['text', 'key', '键字段名', '即表中的主键字段名，如果表中的主键字段名为id则可不填写'],
                ['text', 'option', '值字段名', '下拉菜单显示的字段名，如果表中的该字段名为name则可不填写'],
                ['text', 'ak', 'APPKEY', '百度编辑器APPKEY'],
                ['text', 'format', '格式'],
                ['textarea', 'tips', '字段说明', '字段补充说明'],
                ['text', 'sort', '排序', '', 100],
            ])
            ->setTrigger('type', 'linkage', 'ajax_url,next_items,param')
            ->setTrigger('type', 'linkages', 'table,pid,level,key,option')
            ->setTrigger('type', 'bmap', 'ak')
            ->setTrigger('type', 'text', 'define')
            ->setTrigger('type', 'masked,date,time,datetime', 'format')
            ->setTrigger('type', 'checkbox,radio,array,select,linkage,linkages', 'options')
            ->fetch();
    }

    /**
     * 编辑
     * @param null $id 字段id
     * @author 王永吉 <739712704@qq.com>
     * @return mixed
     */
    public function edit($id = null)
    {
        if ($id === null) $this->error('参数错误');

        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();  
            // 验证
            $result = $this->validate($data, 'Flowfield');
            if(true !== $result) $this->error($result);
            // 如果是快速联动
            if ($data['type'] == 'linkages') {
                $data['key']    = $data['key']    == '' ? 'id'   : $data['key'];
                $data['pid']    = $data['pid']    == '' ? 'pid'  : $data['pid'];
                $data['level']  = $data['level']  == '' ? '2'    : $data['level'];
                $data['option'] = $data['option'] == '' ? 'name' : $data['option'];
            }
            
            $FieldModel = new FieldModel();
            if ($FieldModel->isUpdate(true)->save($data)) {
                $this->success('修改成功', cookie('__forward__'));
            }
            $this->error('修改失败');
        }

        // 获取数据
        $info = FieldModel::get($id);
        $pageTitle = FlowModel::where('id',$info['fid'])->value('title');
        // 显示编辑 页面
        return ZBuilder::make('form')
            ->setPageTitle($pageTitle.'-编辑表单项')
            ->addFormItems([
                ['hidden', 'id'],
                ['text', 'title', '字段标题', '可填写中文'],
                ['select', 'type', '字段类型', '', config('form_item_type')],
                ['select', 'define', '字段定义', '',[1=>"不可为空",2=>"必须为数字",3=>"邮箱"]],
                ['text', 'value', '字段默认值'],
                ['select', 'style', '字段样式', '员工填写表单时此行所占', [1=>"半行",0=>"整行"]],
                ['textarea', 'options', '额外选项', '用于单选、多选、下拉、联动等类型'],
                ['text', 'ajax_url', '异步请求地址', "如请求的地址是 <code>url('ajax/getCity')</code>，那么只需填写 <code>ajax/getCity</code>，或者直接填写以 <code>http</code>开头的url地址"],
                ['text', 'next_items', '下一级联动下拉框的表单名', "与当前有关联的下级联动下拉框名，多个用逗号隔开，如：area,other"],
                ['text', 'param', '请求参数名', "联动下拉框请求参数名，默认为配置名称"],
                ['text', 'level', '级别', '需要显示的级别数量，默认为2'],
                ['text', 'table', '表名', '要查询的表，里面必须含有id、name、pid三个字段，其中id和name字段可在下面重新定义'],
                ['text', 'pid', '父级id字段名', '即表中的父级ID字段名，如果表中的主键字段名为pid则可不填写'],
                ['text', 'key', '键字段名', '即表中的主键字段名，如果表中的主键字段名为id则可不填写'],
                ['text', 'option', '值字段名', '下拉菜单显示的字段名，如果表中的该字段名为name则可不填写'],
                ['text', 'ak', 'APPKEY', '百度编辑器APPKEY'],
                ['text', 'format', '格式'],
                ['textarea', 'tips', '字段说明', '字段补充说明'],
                ['text', 'sort', '排序'],
            ])
            ->setTrigger('type', 'linkage', 'ajax_url,next_items,param')
            ->setTrigger('type', 'linkages', 'table,pid,level,key,option')
            ->setTrigger('type', 'bmap', 'ak')
            ->setTrigger('type', 'masked,date,time,datetime', 'format')
            ->setTrigger('type', 'checkbox,radio,array,select,linkage,linkages', 'options')
            ->setFormData($info)
            ->fetch();
    }


}
