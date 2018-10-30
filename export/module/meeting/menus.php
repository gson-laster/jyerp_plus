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
    'title' => '会议',
    'icon' => 'fa fa-fw fa-assistive-listening-systems',
    'url_type' => 'module_admin',
    'url_value' => 'meeting/index/index',
    'url_target' => '_self',
    'online_hide' => 0,
    'sort' => 100,
    'status' => 1,
    'child' => [
      [
        'title' => '会议',
        'icon' => 'fa fa-fw fa-align-justify',
        'url_type' => 'module_admin',
        'url_value' => '',
        'url_target' => '_self',
        'online_hide' => 0,
        'sort' => 100,
        'status' => 1,
        'child' => [
          [
            'title' => '我的会议',
            'icon' => 'fa fa-fw fa-headphones',
            'url_type' => 'module_admin',
            'url_value' => 'meeting/index/index',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '参会人员',
                'icon' => 'fa fa-fw fa-group',
                'url_type' => 'module_admin',
                'url_value' => 'meeting/index/groups',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
              [
                'title' => '编辑',
                'icon' => '',
                'url_type' => 'module_admin',
                'url_value' => 'meeting/index/edit',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
            ],
          ],
          [
            'title' => '会议一览',
            'icon' => 'fa fa-fw fa-certificate',
            'url_type' => 'module_admin',
            'url_value' => 'meeting/index/lists',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
          ],
          [
            'title' => '新增会议',
            'icon' => 'fa fa-fw fa-plus',
            'url_type' => 'module_admin',
            'url_value' => 'meeting/index/add',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
          ],
          [
            'title' => '会议室',
            'icon' => 'fa fa-fw fa-institution',
            'url_type' => 'module_admin',
            'url_value' => 'meeting/meeting/rooms',
            'url_target' => '_self',
            'online_hide' => 0,
            'sort' => 100,
            'status' => 1,
            'child' => [
              [
                'title' => '编辑',
                'icon' => '',
                'url_type' => 'module_admin',
                'url_value' => 'meeting/meeting/edit',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
              [
                'title' => '删除会议室',
                'icon' => '',
                'url_type' => 'module_admin',
                'url_value' => 'meeting/meeting/delete',
                'url_target' => '_self',
                'online_hide' => 0,
                'sort' => 100,
                'status' => 1,
              ],
            ],
          ],
          [
            'title' => '新增会议室',
            'icon' => 'fa fa-fw fa-plus-circle',
            'url_type' => 'module_admin',
            'url_value' => 'meeting/meeting/add_room',
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
