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
  'name' => 'meeting',
  'title' => '会议',
  'identifier' => 'meeting.ming.module',
  'icon' => 'fa fa-fw fa-newspaper-o',
  'description' => '会议',
  'author' => 'JiangJun',
  'author_url' => 'http://www.dolphinphp.com',
  'version' => '1.0.0',
  'tables' => [
    'meeting_list',
    'meeting_rooms',
  ],
  'database_prefix' => 'dp_',
];
