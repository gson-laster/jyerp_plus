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
  'name' => 'contract',
  'title' => '合同管理',
  'identifier' => 'contract.Liu.module',
  'icon' => 'fa fa-fw fa-newspaper-o',
  'description' => '合同管理模块',
  'author' => 'JinYao',
  'author_url' => '',
  'version' => '1.0.0',
  'database_prefix' => 'dp_',
  'tables' => [
    'contract_hire',
    'contract_hire_detail',
    'contract_income',
    'contract_list',
    'contract_materials',
    'contract_materials_detail',
  ],
];
