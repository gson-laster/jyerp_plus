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

namespace app\admin\controller;

use app\common\builder\ZBuilder;
use app\tender\model\Obj as ObjModel;
use think\Db;

/**
 * 系统日志控制器
 * @package app\admin\controller
 */
class Obj extends Admin
{
    /**
     * 日志列表
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function index()
    {// 获取查询条件
		$map = $this->getMap();
		// 排序
		$order = $this->getOrder('tender_obj.create_time desc');
		// 数据列表
		$data_list = ObjModel::getList($map,$order); 
		$type = Db::name('tender_type')->where('status',1)->column('id,name');		
		// 分页数据
		$page = $data_list->render();
		$task_list = [
			'title' => '查看详情',
			'icon' => 'fa fa-fw fa-eye',
			'href' => url('task_list',['id'=>'__id__'])
		];
		return ZBuilder::make('table')
		->hideCheckbox()
		->addTimeFilter('tender_obj.create_time') // 添加时间段筛选
		->setSearch(['tender_obj.name' => '项目名称'], '', '', true) // 设置搜索参数
		->addFilter('tender_obj.type',$type) // 添加筛选
		->setPageTitle('项目列表')
		->addColumns([
			['code','编号'],
			['name','项目名称'],
			['type','项目类型',$type],
			['address','项目地址'],
			['zrid','责任人'],
			['unit','建设单位'],
			['contact','联系人'],
			['create_time','立项时间','date'],
			['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
			['right_button','操作','btn'],
		])
		->addOrder(['code']) // 添加排序
		->setRowList($data_list)//设置表格数据
		->addRightButton('task_list',$task_list, true) // 查看右侧按钮 
		->fetch();
	}
	//查看
	public function task_list($id = null){
		if($id == null) $this->error('参数错误');
		$info = ObjModel::getOne($id);
		return ZBuilder::make('form')
		->hideBtn('submit')
		->addFormItems([
			['static:6','name','项目名称'],
			['static:4','sale','销售合同'],
			['static:6','address','项目地址'],
			['static:6','info','项目简介'],
			['static:4','type','项目类型'],
			['static:4','zrid','项目追踪人'],
			['static:4','bmid','所属部门'],									
			['static:4','unit','建设单位'],
			['static:4','contact','联系人'],
			['static:4','phone','联系电话'],
			['static:4','lxaddrss','联系地址'],
			['static:4','lxid','立项人'],
			['archives','file','附件'],		
			['static','note','备注'],		
		])
		->setFormData($info)
		->fetch();
	}
}