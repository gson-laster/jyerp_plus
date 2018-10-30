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

namespace app\meeting\model;

use think\Model as ThinkModel;
use think\Db;

/**
 * 广告模型
 * @package app\cms\model
 */
class Lists extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__MEETING_LIST__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    // 定义修改器
    public function setStartTimeAttr($value)
    {
        return $value != '' ? strtotime($value) : 0;
    }
    public function setEndTimeAttr($value)
    {
        return $value != '' ? strtotime($value) : 0;
    }
    public function getStartTimeAttr($value)
    {
        return $value != 0 ? date('Y-m-d', $value) : '';
    }
    public function getEndTimeAttr($value)
    {
        return $value != 0 ? date('Y-m-d', $value) : '';
    }
    //获取我的会议
    public static function getMeetingList($map = [], $order = '', $where=''){
		if(empty($order)){
			$order = 'u.is_read asc,l.m_time desc';
		}
		$data = Db::name('meeting_user')
					-> alias('u')
					-> field('l.title, l.e_time, l.s_time, l.m_time, a.nickname, l.id, u.is_read, r.name, u.id as rid')
					-> join('meeting_list l', 'u.meeting_id=l.id', 'left')
					-> join('admin_user a', 'l.compare=a.id')
					-> join('meeting_rooms r', 'l.room_id = r.id', 'left')
					-> where($map)
					-> where($where)
					-> order($order)
					-> paginate();
    	return $data;
    }
    //获取单个会议详情
    public static function getOne($map){
		$data = Db::name('meeting_list')
					-> alias('l')
					-> field('l.title, l.e_time, l.s_time, l.m_time, a.nickname, l.id, u.is_read, u.id as rid, r.name, l.user_id')
					-> join('meeting_user u', 'u.meeting_id=l.id', 'left')
					-> join('admin_user a', 'l.compare=a.id')
					-> join('meeting_rooms r', 'l.room_id = r.id', 'left')
					-> where($map)
					-> find();
    	return $data;
    }
    //获取当前时间被占用的会议室
    public static function getRoom(){
    	$t = time();
    	$map = 's_time < '.$t.' AND e_time>'.$t;
    	$list = self::where($map) -> column('id,room_id');
    	return $list;
    } 
//  获取当前用户最后一条会议
	public static function getLastOne($uid = null){
		if(empty($uid)){
			$this -> error('参数错误');
		}
   		return Db::name('meeting_user')
			-> alias('u')
			-> field('l.title')
			-> join('meeting_list l', 'l.id=u.meeting_id', 'left')
			-> where(['u.user_id' => $uid])
			-> order('l.m_time desc')
			-> limit(1)
			-> find();
   	} 
 	//  获取当前用户未读一条会议
	public static function getNoRead($uid = null){
		if(empty($uid)){
			$this -> error('参数错误');
		}
 		return Db::name('meeting_user')
			-> where(['user_id' => $uid, 'is_read' => 0])
			-> limit(1)
			-> count();
 	} 
}