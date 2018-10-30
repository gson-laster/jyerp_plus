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
    'title' => '文档',
    'icon' => 'fa fa-fw fa-clipboard',
    'url_type' => 'module_admin',
    'url_value' => 'document/index/index',
    'url_target' => '_self',
    'online_hide' => 0,
    'sort' => 100,
    'status' => 1,
    'child' => [
      [
        'title' => '文档管理',
        'icon' => 'fa fa-fw fa-th-large',
        'url_type' => 'module_admin',
        'url_value' => '',
        'url_target' => '_self',
        'online_hide' => 0,
        'sort' => 100,
        'status' => 1,
        'child' => [
          [
            'title' => '文档中心',
            'icon' => 'fa fa-fw fa-clipboard',
            'url_type' => 'module_admin',
            'url_value' => 'document/index/index',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '新增目录',
                'icon' => 'fa fa-fw fa-plus',
                'url_type' => 'module_admin',
                'url_value' => 'document/index/add',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
              [
                'title' => '上传文档',
                'icon' => 'fa fa-fw fa-arrow-up',
                'url_type' => 'module_admin',
                'url_value' => 'document/index/upfile',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
              [
                'title' => '编辑',
                'icon' => 'fa fa-fw fa-pencil',
                'url_type' => 'module_admin',
                'url_value' => 'document/index/edit',
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
