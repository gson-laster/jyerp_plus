<?php
	//材料需用明细
namespace app\tender\model;
use think\Model as ThinkModel;
use think\Db;
class Materialsdetail extends ThinkModel
{
	protected $table = '__TENDER_MATERIALS_DETAIL__';
	protected $autoWriteTimestamp = true;
}
