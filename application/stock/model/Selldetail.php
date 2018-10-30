<?php
	//销售出库明细
namespace app\stock\model;
use think\Model as ThinkModel;
use think\Db;
class Selldetail extends ThinkModel
{
	protected $table = '__STOCK_SELL_DETAIL__';
	protected $autoWriteTimestamp = true;


}
