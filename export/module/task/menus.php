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
 * 菜单信息
 */
return [
  [
    'title' => '任务',
    'icon' => 'fa fa-fw fa-folder-open',
    'url_type' => 'module_admin',
    'url_value' => 'task/index/index',
    'url_target' => '_self',
    'online_hide' => 0,
    'sort' => 100,
    'status' => 1,
    'child' => [
      [
        'title' => '任务管理',
        'icon' => 'fa fa-fw fa-th-list',
        'url_type' => 'module_admin',
        'url_value' => '',
        'url_target' => '_self',
        'online_hide' => 0,
        'sort' => 100,
        'status' => 1,
        'child' => [
          [
            'title' => '任务列表',
            'icon' => 'fa fa-fw fa-th',
            'url_type' => 'module_admin',
            'url_value' => 'task/index/index',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '发起任务',
                'icon' => 'fa fa-fw fa-plus',
                'url_type' => 'module_admin',
                'url_value' => 'task/index/add',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
              [
                'title' => '删除任务',
                'icon' => 'fa fa-fw fa-remove',
                'url_type' => 'module_admin',
                'url_value' => 'task/index/delete',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
              [
                'title' => '编辑任务',
                'icon' => 'fa fa-fw fa-pencil',
                'url_type' => 'module_admin',
                'url_value' => 'task/index/edit',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
            ],
          ],
          [
            'title' => '我的任务',
            'icon' => 'fa fa-fw fa-user',
            'url_type' => 'module_admin',
            'url_value' => 'task/index/mytask',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '查看',
                'icon' => '',
                'url_type' => 'module_admin',
                'url_value' => 'task/index/task_list',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
            ],
          ],
        ],
      ],
    ],
  ],
];
