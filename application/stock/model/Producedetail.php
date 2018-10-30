<?php
	//生产入库明细
namespace app\stock\model;
use think\Model as ThinkModel;
use think\Db;
class Producedetail extends ThinkModel
{
	protected $table = '__STOCK_PRODUCE_DETAIL__';
	protected $autoWriteTimestamp = true;


}
