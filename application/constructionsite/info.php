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
  'name' => 'constructionsite',
  'title' => '施工现场',
  'identifier' => 'constructionsite.ji.module',
  'icon' => 'fa fa-fw fa-tasks',
  'description' => '施工现场模块',
  'author' => 'WYJ',
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
    'constructionsite_change',
    'constructionsite_log',
    'constructionsite_plan',
    'constructionsite_tell',
  ],
  'database_prefix' => 'dp_',
  'action' => [
    [
      'module' => 'constructionsite',
      'name' => 'constructionsite_log_add',
      'title' => '添加施工日志',
      'remark' => '添加施工日志',
      'rule' => '',
      'log' => '[user|get_nickname] 添加了施工日志：[details]',
      'status' => 1,
    ],
    [
      'module' => 'constructionsite',
      'name' => 'constructionsite_log_delete',
      'title' => '删除施工日志',
      'remark' => '删除施工日志',
      'rule' => '',
      'log' => '[user|get_nickname] 删除了施工日志：日志ID([details]]',
      'status' => 1,
    ],
    [
      'module' => 'constructionsite',
      'name' => 'constructionsite_change_add',
      'title' => '添加设计变更',
      'remark' => '添加设计变更',
      'rule' => '',
      'log' => '[user|get_nickname] 更新了设计变更：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'constructionsite',
      'name' => 'constructionsite_change_delete',
      'title' => '删除设计变更',
      'remark' => '删除设计变更',
      'rule' => '',
      'log' => '[user|get_nickname] 删除了设计变更：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'constructionsite',
      'name' => 'constructionsite_tell_add',
      'title' => '添加技术交底',
      'remark' => '添加技术交底',
      'rule' => '',
      'log' => '[user|get_nickname] 更新了技术交底：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'constructionsite',
      'name' => 'constructionsite_tell_delete',
      'title' => '删除技术交底',
      'remark' => '删除技术交底',
      'rule' => '',
      'log' => '[user|get_nickname] 删除了技术交底：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'constructionsite',
      'name' => 'constructionsite_plan_add',
      'title' => '添加组织方案',
      'remark' => '添加组织方案',
      'rule' => '',
      'log' => '[user|get_nickname] 更新了组织方案：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'constructionsite',
      'name' => 'constructionsite_plan_delete',
      'title' => '删除组织方案',
      'remark' => '删除组织方案',
      'rule' => '',
      'log' => '[user|get_nickname] 删除了组织方案：ID为([details]]',
      'status' => 1,
    ],
  ],
];
