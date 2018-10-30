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
  'name' => 'tender',
  'title' => '项目',
  'identifier' => 'tender.ming.module',
  'icon' => 'fa fa-fw fa-fighter-jet',
  'description' => '项目模块',
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
    'tender_obj',
    'tender_hire',
    'tender_hire_detail',
    'tender_lease',
    'tender_lease_detail',
    'tender_margin',
    'tender_materials',
    'tender_materials_detail',
    'tender_plan',
    'tender_type',
    'tender_already_salary',
    'tender_contract_hire',
    'tender_contract_hire_detail',
    'tender_fact_salary',
    'tender_salary',
    'tender_schedule',
    'tender_schedule_detail',
    'tender_schedule_over',
  ],
  'database_prefix' => 'dp_',
];
