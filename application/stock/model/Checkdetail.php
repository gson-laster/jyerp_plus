<?php
	//其他出库明细
namespace app\stock\model;
use think\Model as ThinkModel;
use think\Db;
class Checkdetail extends ThinkModel
{
	protected $table = '__STOCK_CHECK_DETAIL__';
	protected $autoWriteTimestamp = true;


}
