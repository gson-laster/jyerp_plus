<?php
namespace app\assets\model;

use think\Model as ThinkModel;
use think\Db;

class Assets_select extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__ASSETS_SELECT__';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
   
}
