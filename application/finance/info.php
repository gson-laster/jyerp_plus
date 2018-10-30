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
  'name' => 'finance',
  'title' => '财务管理',
  'identifier' => 'finance.ming.module',
  'icon' => 'fa fa-fw fa-folder-open',
  'description' => '财务管理模块',
  'author' => 'HJP',
  'version' => '1.0.0',
  'need_module' => [],
  'need_plugin' => [],
  'tables' => [
  	'finance_accmount',
  	'finance_gather',
  	'finance_hire',
  	'finance_info',
  	'finance_manager',
  	'finance_other',
  	'finance_ptype',
  	'finance_pway',
  	'finance_stuff',
  	'finance_receipts',
  	'standby_info',
  	'contract_income',
  ],
  'database_prefix' => 'dp_',
  

];
