<?php
	//其他出库明细
namespace app\stock\model;
use think\Model as ThinkModel;
use think\Db;
class Borrowdetail extends ThinkModel
{
	protected $table = '__STOCK_BORROW_DETAIL__';
	protected $autoWriteTimestamp = true;


}
