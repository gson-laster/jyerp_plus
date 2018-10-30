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
  'name' => 'supplier',
  'title' => '供应商',
  'identifier' => 'supplier.ji.module',
  'icon' => 'fa fa-fw fa-fax',
  'description' => '供应商模块',
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
    'supplier_phone',
    'supplier_list',
    'supplier_res',
    'supplier_type',
  ],
  'database_prefix' => 'dp_',
  'action' => [
    [
      'module' => 'supplier',
      'name' => 'supplier_type_add',
      'title' => '添加供应商类型',
      'remark' => '添加供应商类型',
      'rule' => '',
      'log' => '[user|get_nickname] 添加了供应商类型：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'supplier',
      'name' => 'supplier_type_delete',
      'title' => '删除供应商类型',
      'remark' => '删除供应商类型',
      'rule' => '',
      'log' => '[user|get_nickname] 删除了供应商类型：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'supplier',
      'name' => 'supplier_add',
      'title' => '添加供应商',
      'remark' => '添加供应商',
      'rule' => '',
      'log' => '[user|get_nickname] 添加了供应商：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'supplier',
      'name' => 'supplier_delete',
      'title' => '删除供应商',
      'remark' => '删除供应商',
      'rule' => '',
      'log' => '[user|get_nickname] 删除了供应商：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'supplier',
      'name' => 'supplier_edit',
      'title' => '更新供应商',
      'remark' => '更新供应商',
      'rule' => '',
      'log' => '[user|get_nickname] 更新了供应商：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'supplier',
      'name' => 'supplier_res_add',
      'title' => '添加供应商物品',
      'remark' => '添加供应商物品',
      'rule' => '',
      'log' => '[user|get_nickname] 添加了供应商物品：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'supplier',
      'name' => 'supplier_res_delete',
      'title' => '删除供应商物品',
      'remark' => '删除供应商物品',
      'rule' => '',
      'log' => '[user|get_nickname] 删除了供应商物品：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'supplier',
      'name' => 'supplier_res_edit',
      'title' => '更新供应商物品',
      'remark' => '更新供应商物品',
      'rule' => '',
      'log' => '[user|get_nickname] 更新了供应商物品：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'supplier',
      'name' => 'supplier_phone_add',
      'title' => '添加供应商联络记录',
      'remark' => '添加供应商联络记录',
      'rule' => '',
      'log' => '[user|get_nickname] 添加了供应商联络记录：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'supplier',
      'name' => 'supplier_phone_delete',
      'title' => '删除供应商联络记录',
      'remark' => '删除供应商联络记录',
      'rule' => '',
      'log' => '[user|get_nickname] 删除了供应商联络记录：ID为([details]]',
      'status' => 1,
    ],
    [
      'module' => 'supplier',
      'name' => 'supplier_phone_edit',
      'title' => '更新供应商联络记录',
      'remark' => '更新供应商联络记录',
      'rule' => '',
      'log' => '[user|get_nickname] 更新了供应商联络记录：ID为([details]]',
      'status' => 1,
    ],
  ],
];
