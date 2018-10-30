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
  'name' => 'purchase',
  'title' => '采购',
  'identifier' => 'purchase.ji.module',
  'icon' => 'fa fa-fw fa-archive',
  'description' => '采购模块',
  'author' => 'WYJ',
  'version' => '1.0.0',
  'need_module' => [
    [
      'admin',
      'admin.dolphinphp.module',
      '1.0.0',
    ],
  ],
  'action' => [
    [
      'module' => 'purchase',
      'name' => 'purchase_type_add',
      'title' => '添加采购类型',
      'remark' => '添加采购类型',
      'rule' => '',
      'log' => '[user|get_nickname] 添加了采购类型：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'purchase',
      'name' => 'purchase_type_delete',
      'title' => '删除采购类型',
      'remark' => '删除采购类型',
      'rule' => '',
      'log' => '[user|get_nickname] 删除了采购类型：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'purchase',
      'name' => 'purchase_ask_add',
      'title' => '添加采购申请',
      'remark' => '添加采购申请',
      'rule' => '',
      'log' => '[user|get_nickname] 添加了采购申请：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'purchase',
      'name' => 'purchase_ask_delete',
      'title' => '删除采购申请',
      'remark' => '删除采购申请',
      'rule' => '',
      'log' => '[user|get_nickname] 删除了采购申请：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'purchase',
      'name' => 'purchase_plan_add',
      'title' => '添加采购计划',
      'remark' => '添加采购计划',
      'rule' => '',
      'log' => '[user|get_nickname] 添加了采购计划：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'purchase',
      'name' => 'purchase_plan_delete',
      'title' => '删除采购计划',
      'remark' => '删除采购计划',
      'rule' => ' ',
      'log' => '[user|get_nickname] 删除了采购计划：ID为([details]]',
      'status' => 1,
    ],
  ],
  'database_prefix' => 'dp_',
];
