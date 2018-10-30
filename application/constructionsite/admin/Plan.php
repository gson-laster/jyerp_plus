<?php
namespace app\constructionsite\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\admin\model\Access as AccessModel;
use app\constructionsite\model\Plan as PlanModel;
use think\Db;
/**
 *  设计变更
 */
class Plan extends Admin
{
	//
	public function index()
	{
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('constructionsite_plan.id desc');

        $btn_detail = [
		    'title' => '查看详情',
		    'icon'  => 'fa fa-fw fa-search',
		    'href'  => url('detail', ['id' => '__id__'])
		];

		$data_list = PlanModel::getList($map,$order);
        return ZBuilder::make('table')
	        	 	->setSearch(['title'=>'方案名称'],'','',true) // 设置搜索框
	        	 	->addTimeFilter('constructionsite_plan.create_time') // 添加时间段筛选
	        	 	->addFilter(['xname'=>'tender_obj.name']) // 添加筛选
	        		->hideCheckbox()
	        		->addOrder('constructionsite_plan.id,constructionsite_plan.create_time') // 添加排序
                    ->addColumns([ // 批量添加列
				        ['id', '编号'],
				        ['title', '方案名称'],
                        ['xname', '项目名称'],
                        ['nickname', '填报人'],
				        ['create_time', '日期','date'],
				        ['content', '方案说明'],
                        ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
				        ['right_button','操作']
				    ])
				    ->setRowList($data_list) // 设置表格数据
				    ->addRightButton('btn', $btn_detail,true) // 添加授权按钮
				    ->addRightButton('delete') //添加删除按钮
	                ->fetch();
	        	
	}

	public function add(){

        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['wid'] = UID;
            $data['create_time'] = time();
            //验证
            $result = $this->validate($data, 'plan');
            //验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if ($res = PlanModel::create($data)) {
                // 记录行为
                flow_detail($data['title'],'constructionsite_plan','constructionsite_plan','constructionsite/plan/detail',$res['id']);
                action_log('constructionsite_plan_add', 'constructionsite_plan', $res['id'], UID, $res['id']);
                $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }
        }
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('技术交底')           
            ->addFormItems([
                ['text:6', 'title', '方案名称'],
                ['select:6','xid', '项目', '', db::name('tender_obj')->column('id,name')],
                ['static:6', 'wid', '填报人','',get_nickname(UID)],
                ['static:6', 'create_time', '日期','',date('Y-m-d',time())],
            	['wangeditor', 'content','方案说明'],
            ])           
            ->fetch();

	}
	//详情 
	public function detail($id=null){

		if($id==null)return $this->error('缺少参数');
		
		$plan = PlanModel::getTell($id);
		        return ZBuilder::make('form')
            ->setPageTitle('查看详情')           
            ->addFormItems([
                ['static:6', 'title', '方案名称'],
                ['static:6','xname', '项目'],
                ['static:6', 'nickname', '填报人'],
                ['wangeditor', 'content','方案说明'],
            ])      
            ->setFormData($plan)    
            ->hideBtn('submit') 
            ->fetch();

	}

	//删除
	public function delete($ids = null){		
		if($ids == null) $this->error('参数错误');
		$map['id'] = $ids;
		if($model = PlanModel::where($map)->delete()){	
			//记录行为
        	action_log('constructionsite_plan_delete', 'constructionsite_plan', $map['id'], UID,$map['id']);			
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}		
	}

}   
