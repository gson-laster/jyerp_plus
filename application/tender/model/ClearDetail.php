<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5 0005
 * Time: 17:39
 */

namespace app\tender\model;


use think\Model;
use think\db;
class ClearDetail extends Model
{
  protected $table = '__TENDER_CLEAR_DETAIL__';
	protected $autoWriteTimestamp = true;
}