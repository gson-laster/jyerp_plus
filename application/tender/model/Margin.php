<?php
	//合同
namespace app\tender\model;
use think\Model as ThinkModel;
use think\Db;
class Margin extends ThinkModel
{
	protected $table = '__TENDER_MARGIN__';
	protected $autoWriteTimestamp = true;
}
