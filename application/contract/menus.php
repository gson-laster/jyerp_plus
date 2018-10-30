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
    'title' => '合同管理',
    'icon' => 'fa fa-fw fa-user-circle-o',
    'url_type' => 'module_admin',
    'url_value' => 'contract/hire/index',
    'url_target' => '_self',
    'online_hide' => 0,
    'sort' => 100,
    'status' => 1,
    'child' => [
      [
        'title' => '租赁合同',
        'icon' => 'fa fa-fw fa-arrow-circle-down',
        'url_type' => 'module_admin',
        'url_value' => '',
        'url_target' => '_self',
        'online_hide' => 0,
        'sort' => 100,
        'status' => 1,
        'child' => [
          [
            'title' => '租赁合同',
            'icon' => 'fa fa-fw fa-star-o',
            'url_type' => 'module_admin',
            'url_value' => 'contract/hire/add',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '弹出',
                'icon' => '',
                'url_type' => 'module_admin',
                'url_value' => 'contract/hire/choose_materials',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
            ],
          ],
          [
            'title' => '租赁合同查询',
            'icon' => 'fa fa-fw fa-check-circle',
            'url_type' => 'module_admin',
            'url_value' => 'contract/hire/index',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '查看',
                'icon' => '',
                'url_type' => 'module_admin',
                'url_value' => 'contract/hire/task_list',
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
        'title' => '材料合同',
        'icon' => 'fa fa-fw fa-chrome',
        'url_type' => 'module_admin',
        'url_value' => '',
        'url_target' => '_self',
        'online_hide' => 0,
        'sort' => 100,
        'status' => 1,
        'child' => [
          [
            'title' => '材料合同',
            'icon' => 'fa fa-fw fa-plus-circle',
            'url_type' => 'module_admin',
            'url_value' => 'contract/materials/add',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '弹出',
                'icon' => 'fa fa-fw fa-leaf',
                'url_type' => 'module_admin',
                'url_value' => 'contract/materials/choose_materials',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
            ],
          ],
          [
            'title' => '材料合同查询',
            'icon' => 'fa fa-fw fa-search',
            'url_type' => 'module_admin',
            'url_value' => 'contract/materials/index',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '查看',
                'icon' => 'fa fa-fw fa-asterisk',
                'url_type' => 'module_admin',
                'url_value' => 'contract/materials/task_list',
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
        'title' => '收入合同',
        'icon' => 'fa fa-fw fa-binoculars',
        'url_type' => 'module_admin',
        'url_value' => '',
        'url_target' => '_self',
        'online_hide' => 0,
        'sort' => 100,
        'status' => 1,
        'child' => [
          [
            'title' => '收入合同',
            'icon' => 'fa fa-fw fa-ge',
            'url_type' => 'module_admin',
            'url_value' => 'contract/income/add',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
          ],
          [
            'title' => '收入合同查询',
            'icon' => 'fa fa-fw fa-search',
            'url_type' => 'module_admin',
            'url_value' => 'contract/income/index',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
          ],
        ],
      ],
    ],
  ],
];
