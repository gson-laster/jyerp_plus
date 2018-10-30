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
use app\admin\model\FlowType as FlowTypeModel;
use app\admin\model\Flow as FlowModel;
use app\admin\model\Module as ModuleModel;

/**
 * 流程类型管理
 * @package app\admin\controller
 */
class Flowtype extends Admin
{
    /**
     * 首页
     * @author 王永吉 <739712704@qq.com>
     * @return mixed
     */
    public function index()
    {
        // 数据列表
        $data_list = FlowTypeModel::paginate(20);
        $fields = [
            ['hidden', 'id'],
            ['text', 'title', '流程类型名称', ''],
            ['number', 'sort', '显示顺序', '',100] ,
        ];
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->hideCheckbox()
            ->setPageTitle('流程类型') // 设置页面标题
            ->setSearch(['title' => '类型名称']) // 设置搜索框
            ->addColumns([ // 批量添加数据列
                ['title', '流程类型'],
                ['sort', '排序'],
                ['right_button', '操作', 'btn']
            ])
            ->autoAdd($fields,'admin_flow_type','', '', '', true) // 添加自动新增按钮
            ->autoEdit($fields,'admin_flow_type','', '', '', true) // 添加编辑按钮
            ->setTableName('admin_flow_type') // 指定数据表名
            ->addRightButton('delete') //添加删除按钮
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板
    }


}