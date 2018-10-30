<?php
	//采购入库明细
namespace app\stock\model;
use think\Model as ThinkModel;
use think\Db;
class Purchasedetail extends ThinkModel
{
	protected $table = '__STOCK_PURCHASE_DETAIL__';
	protected $autoWriteTimestamp = true;


}
