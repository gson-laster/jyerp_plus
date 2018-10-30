<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\personnel\model;

use think\Model as ThinkModel;

/**
 * 薪资模型
 * @package app\cms\model
 */
class Wage extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__PERSONNEL_WAGE__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
   
    // 定义修改器
    public function setInTimeAttr($value)
    {
        return $value != '' ? strtotime($value) : '';
    }
    public function setZzTimeAttr($value)
    {
        return $value != '' ? strtotime($value) : '';
    }
    public function setLzTimeAttr($value)
    {
    	return $value != '' ? strtotime($value) : '';
    }
    public function setPoliticalTimeAttr($value)
    {
    	return $value != '' ? strtotime($value) : '';
    }
    public function setWorkTimeAttr($value)
    {
    	return $value != '' ? strtotime($value) : '';
    }
    public function setProfessorTimeAttr($value)
    {
    	return $value != '' ? strtotime($value) : '';
    }
    public function setGraduationTimeAttr($value)
    {
    	return $value != '' ? strtotime($value) : '';
    }
    
  
    public function getInTimeAttr($value)
    {
        return $value != 0 ? date('Y-m-d', $value) : '';
    }
    public function getZzTimeAttr($value)
    {
        return $value != 0 ? date('Y-m-d', $value) : '';
    }
    public function getLzTimeAttr($value)
    {
    	return $value != 0 ? date('Y-m-d', $value) : '';
    }
    public function getPoliticalTimeAttr($value)
    {
    	return $value != 0 ? date('Y-m-d', $value) : '';
    }
    public function getWorkTimeAttr($value)
    {
    	return $value != 0 ? date('Y-m-d', $value) : '';
    }
    public function getProfessorTimeAttr($value)
    {
    	return $value != 0 ? date('Y-m-d', $value) : '';
    }
    public function getGraduationTimeAttr($value)
    {
    	return $value != 0 ? date('Y-m-d', $value) : '';
    }

    
    /**
     * 获取档案列表
     * @param array $map 筛选条件
     * @param array $order 排序
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getList($map = [], $order = [])
    {
    	$data_list = self::view('personnel_wage', true)    	
    	->view("admin_user", ['username','nickname','avatar','sex','birth','role','organization','position','is_on','status'], 'admin_user.id=personnel_wage.uid', 'left')
    	->where($map)
    	->order($order)
    	->paginate();
    	return $data_list;
    }
    
    /**
     * 获取档案
     * @param array $map 筛选条件
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public static function getOne($id = '',$map = [])
    {
    	$data_list = self::view('personnel_wage', true)
    	->view("admin_user", ['username','nickname','avatar','sex','birth','role','organization','position','is_on','status','email','mobile'], 'admin_user.id=personnel_wage.uid', 'left')
    	->where(['personnel_wage.id'=>$id]) 
    	->where($map)
    	->find();
    	return $data_list;
    }
}