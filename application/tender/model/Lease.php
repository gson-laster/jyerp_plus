<?php
	//材料计划
namespace app\tender\model;
use think\Model as ThinkModel;
use think\Db;
class Lease extends ThinkModel
{
	protected $table = '__TENDER_LEASE__';
	protected $autoWriteTimestamp = true;
}
