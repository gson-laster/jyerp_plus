<?php
namespace app\assets\model;

use think\Model as ThinkModel;
use think\Db;

class Assets_dateil extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__ASSETS_DATEIL__';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
   
}
