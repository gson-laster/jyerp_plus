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
    'title' => '资产',
    'icon' => 'fa fa-fw fa-jpy',
    'url_type' => 'module_admin',
    'url_value' => 'assets/index/index',
    'url_target' => '_self',
    'online_hide' => 0,
    'sort' => 100,
    'status' => 1,
    'child' => [
      [
        'title' => '资产管理',
        'icon' => 'fa fa-fw fa-th-large',
        'url_type' => 'module_admin',
        'url_value' => '',
        'url_target' => '_self',
        'online_hide' => 0,
        'sort' => 100,
        'status' => 1,
        'child' => [
          [
            'title' => '资产列表',
            'icon' => 'fa fa-fw fa-th-list',
            'url_type' => 'module_admin',
            'url_value' => 'assets/index/index',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '添加资产',
                'icon' => 'fa fa-fw fa-plus',
                'url_type' => 'module_admin',
                'url_value' => 'assets/index/add',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
              [
                'title' => '编辑资产',
                'icon' => 'fa fa-fw fa-pencil',
                'url_type' => 'module_admin',
                'url_value' => 'assets/index/edit',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
              [
                'title' => '删除资产',
                'icon' => 'fa fa-fw fa-remove',
                'url_type' => 'module_admin',
                'url_value' => 'assets/index/delete',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
              [
                'title' => '申请领用',
                'icon' => '',
                'url_type' => 'module_admin',
                'url_value' => 'assets/index/recipients',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
              [
                'title' => '申请归还',
                'icon' => '',
                'url_type' => 'module_admin',
                'url_value' => 'assets/index/returns',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
              [
                'title' => '同意领用',
                'icon' => '',
                'url_type' => 'module_admin',
                'url_value' => 'assets/index/determine',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
              [
                'title' => '不同意领用',
                'icon' => '',
                'url_type' => 'module_admin',
                'url_value' => 'assets/index/nodetermine',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
            ],
          ],
          [
            'title' => '资产类别',
            'icon' => 'fa fa-fw fa-th-list',
            'url_type' => 'module_admin',
            'url_value' => 'assets/category/index',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '添加类别',
                'icon' => 'fa fa-fw fa-plus',
                'url_type' => 'module_admin',
                'url_value' => 'assets/category/add',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
              [
                'title' => '编辑类别',
                'icon' => 'fa fa-fw fa-pencil',
                'url_type' => 'module_admin',
                'url_value' => 'assets/category/edit',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
              [
                'title' => '删除类别',
                'icon' => 'fa fa-fw fa-remove',
                'url_type' => 'module_admin',
                'url_value' => 'assets/category/delete',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
            ],
          ],
          [
            'title' => '我的资产',
            'icon' => 'fa fa-fw fa-user',
            'url_type' => 'module_admin',
            'url_value' => 'assets/dateil/index',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '申请归还',
                'icon' => 'fa fa-fw fa-pencil',
                'url_type' => 'module_admin',
                'url_value' => 'assets/dateil/returns',
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
