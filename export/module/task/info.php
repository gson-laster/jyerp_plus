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
  'name' => 'task',
  'title' => '任务',
  'identifier' => 'task.ming.module',
  'icon' => 'fa fa-fw fa-folder-open',
  'description' => '任务模块',
  'author' => 'HJP',
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
    'task_dateil',
  ],
  'database_prefix' => 'dp_',
  'action' => [],
  'access' => [
    'group' => [
      'tab_title' => '栏目授权',
      'table_name' => 'cms_column',
      'primary_key' => 'id',
      'parent_id' => 'pid',
      'node_name' => 'name',
    ],
  ],
];
