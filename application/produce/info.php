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
  'name' => 'produce',
  'title' => '生产',
  'identifier' => 'produce.dong.module',
  'icon' => 'fa fa-fw fa-vcard-o',
  'description' => '生产模块',
  'author' => 'HuangYuanDong',
  'author_url' => '',
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
    'produce_cost',
    'produce_mateplan',
    'produce_mateplan_list',
    'produce_materials',
    'produce_materials_list',
    'produce_plan',
    'produce_plan_list',
    'produce_procedure',
    'produce_production',
    'produce_production_list',
    'produce_technology',
    'produce_technology_line',
    'produce_workcenter',
  ],
  'database_prefix' => 'dp_',
];
