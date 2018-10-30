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

/**
 * 模块信息
 */
return [
  'name' => 'notice',
  'title' => '公告',
  'identifier' => 'notice.dong.module',
  'icon' => 'fa fa-fw fa-volume-up',
  'description' => '公告模块',
  'author' => 'HuangYuanDong',
  'author_url' => '',
  'version' => '1.0.0',
  'need_module' => [
    [
      'admin',
      'admin.dolphinphp.module',
      '1.0.0',
    ],
  ],
  'need_plugin' => [],
  'tables' => [
    'notice_list',
    'notice_cate',
    'notice_user',
  ],
  'database_prefix' => 'dp_',
  'action' => [
    [
      'module' => 'notice',
      'name' => 'notice_cate_add',
      'title' => '新增公告类型',
      'remark' => '新增公告类型',
      'rule' => '',
      'log' => '[user|get_nickname] 新增公告类型：[details]',
      'status' => 1,
    ],
    [
      'module' => 'notice',
      'name' => 'notice_cate_edit',
      'title' => '编辑公告类型',
      'remark' => '编公告类型',
      'rule' => '',
      'log' => '[user|get_nickname] 编公告类型：[details]',
      'status' => 1,
    ],
    [
      'module' => 'notice',
      'name' => 'notice_cate_delete',
      'title' => '删除公告类型',
      'remark' => '删除公告类型',
      'rule' => '',
      'log' => '[user|get_nickname] 删除公告类型：[details]',
      'status' => 1,
    ],
    [
      'module' => 'notice',
      'name' => 'notice_cate_enable',
      'title' => '启用公告类型',
      'remark' => '启用公告类型',
      'rule' => '',
      'log' => '[user|get_nickname] 启用公告类型：[details]',
      'status' => 1,
    ],
    [
      'module' => 'notice',
      'name' => 'notice_cate_disable',
      'title' => '禁用公告类型',
      'remark' => '禁用公告类型',
      'rule' => '',
      'log' => '[user|get_nickname] 禁用公告类型：[details]',
      'status' => 1,
    ],
    [
      'module' => 'notice',
      'name' => 'notice_list_add',
      'title' => '新增公告',
      'remark' => '新增公告',
      'rule' => '',
      'log' => '[user|get_nickname] 新增公告：[details]',
      'status' => 1,
    ],
    [
      'module' => 'notice',
      'name' => 'notice_list_edit',
      'title' => '编辑公告',
      'remark' => '编辑公告',
      'rule' => '',
      'log' => '[user|get_nickname] 编辑公告：[details]',
      'status' => 1,
    ],
    [
      'module' => 'notice',
      'name' => 'notice_list_delete',
      'title' => '删除公告',
      'remark' => '删除公告',
      'rule' => '',
      'log' => '[user|get_nickname] 删除公告：[details]',
      'status' => 1,
    ],
    [
      'module' => 'notice',
      'name' => 'notice_list_release',
      'title' => '发布公告',
      'remark' => '发布公告',
      'rule' => '',
      'log' => '[user|get_nickname] 发布公告：[details]',
      'status' => 1,
    ],
    [
      'module' => 'notice',
      'name' => 'notice_list_cancel',
      'title' => '撤销公告',
      'remark' => '撤销公告',
      'rule' => '',
      'log' => '[user|get_nickname] 撤销公告：[details]',
      'status' => 1,
    ],
    [
      'module' => 'notice',
      'name' => 'notice_user_delete',
      'title' => '删除我的公告',
      'remark' => '删除我的公告',
      'rule' => '',
      'log' => '[user|get_nickname] 删除我的公告：[details]',
      'status' => 1,
    ],
  ],
  'access' => [
    'group' => [
      'tab_title' => '公告授权',
      'table_name' => 'notice_cate',
      'primary_key' => 'id',
      'parent_id' => 'pid',
      'node_name' => 'title',
    ],
  ],
];
