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
    'title' => '流程',
    'icon' => 'fa fa-fw fa-object-group',
    'url_type' => 'module_admin',
    'url_value' => 'flow/flowwork/index',
    'url_target' => '_self',
    'online_hide' => 0,
    'sort' => 100,
    'status' => 1,
    'child' => [
      [
        'title' => '流程审批',
        'icon' => 'fa fa-fw fa-object-group',
        'url_type' => 'module_admin',
        'url_value' => '',
        'url_target' => '_self',
        'online_hide' => 0,
        'sort' => 100,
        'status' => 1,
        'child' => [
          [
            'title' => '流程发起',
            'icon' => 'fa fa-fw fa-sticky-note',
            'url_type' => 'module_admin',
            'url_value' => 'flow/flowwork/index',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '流程添加',
                'icon' => 'fa fa-fw fa-plus',
                'url_type' => 'module_admin',
                'url_value' => 'flow/flowwork/add',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
            ],
          ],
          [
            'title' => '待办流程',
            'icon' => 'fa fa-fw fa-pencil-square-o',
            'url_type' => 'module_admin',
            'url_value' => 'flow/flowwork/handletask',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '办理',
                'icon' => 'fa fa-fw fa-legal',
                'url_type' => 'module_admin',
                'url_value' => 'flow/flowwork/ban',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
              [
                'title' => '过程',
                'icon' => 'fa fa-fw fa-sort-amount-desc',
                'url_type' => 'module_admin',
                'url_value' => 'flow/flowwork/guo',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
            ],
          ],
          [
            'title' => '已办流程',
            'icon' => 'fa fa-fw fa-check-square-o',
            'url_type' => 'module_admin',
            'url_value' => 'flow/flowwork/handletask_ok',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
          ],
          [
            'title' => '我的申请',
            'icon' => 'fa fa-fw fa-id-badge',
            'url_type' => 'module_admin',
            'url_value' => 'flow/flowwork/myflow',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '详情',
                'icon' => 'fa fa-fw fa-eye',
                'url_type' => 'module_admin',
                'url_value' => 'flow/flowwork/flow_detail',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
            ],
          ],
        ],
      ],
      [
        'title' => '项目流程',
        'icon' => 'fa fa-fw fa-font-awesome',
        'url_type' => 'module_admin',
        'url_value' => '',
        'url_target' => '_self',
        'online_hide' => 0,
        'sort' => 100,
        'status' => 1,
        'child' => [
          [
            'title' => '我的申请',
            'icon' => 'fa fa-fw fa-street-view',
            'url_type' => 'module_admin',
            'url_value' => 'flow/item/myflow',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '过程',
                'icon' => '',
                'url_type' => 'module_admin',
                'url_value' => 'flow/item/guo',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
            ],
          ],
          [
            'title' => '待办流程',
            'icon' => 'fa fa-fw fa-edit',
            'url_type' => 'module_admin',
            'url_value' => 'flow/item/handletask',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '过程',
                'icon' => '',
                'url_type' => 'module_admin',
                'url_value' => 'flow/item/guo',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
              [
                'title' => '流程办理',
                'icon' => '',
                'url_type' => 'module_admin',
                'url_value' => 'flow/item/ban',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
            ],
          ],
          [
            'title' => '已办流程',
            'icon' => 'fa fa-fw fa-check-square-o',
            'url_type' => 'module_admin',
            'url_value' => 'flow/item/handletask_ok',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '审批过程',
                'icon' => '',
                'url_type' => 'module_admin',
                'url_value' => 'flow/item/guo',
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
