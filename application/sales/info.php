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
  'name' => 'sales',
  'title' => '销售',
  'identifier' => 'sales.ming.module',
  'icon' => 'fa fa-fw fa-handshake-o',
  'description' => '销售模块',
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
    'sales_contract',
    'sales_offer',
    'sales_opport',
    'sales_order',
    'sales_plan',
    'sales_delivery',
  ],
  'database_prefix' => 'dp_',
  'action' => [],
];
