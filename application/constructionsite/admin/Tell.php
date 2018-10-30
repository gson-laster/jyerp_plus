<?php
namespace app\constructionsite\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\admin\model\Access as AccessModel;
use app\constructionsite\model\Tell as TellModel;
use think\Db;
/**
 *  设计变更
 */
class Tell extends Admin
{
	//
	public function index()
	{
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('constructionsite_tell.id desc');

        $btn_detail = [
		    'title' => '查看详情',
		    'icon'  => 'fa fa-fw fa-search',
		    'href'  => url('detail', ['id' => '__id__'])
		];

		$data_list = TellModel::getList($map,$order);
        return ZBuilder::make('table')
	        	 	->setSearch(['title'=>'交底名称','tell_user'=>'交底人','tell_receive_user'=>'被交底人'],'','',true) // 设置搜索框
	        	 	->addTimeFilter('constructionsite_tell.create_time') // 添加时间段筛选
	        	 	->addFilter(['xname'=>'tender_obj.name']) // 添加筛选
	        		->hideCheckbox()
	        		->addOrder('constructionsite_tell.id,constructionsite_tell.create_time') // 添加排序
                    ->addColumns([ // 批量添加列
				        ['id', '编号'],
				        ['title', '交底名称'],
				        ['tell_user', '交底人'],
				        ['tell_receive_user', '被交底人'],
                        ['name', '施工车间'],
				        ['create_time', '日期','date'],
				        ['xname', '项目名称'],
				        ['tell_content', '交底内容'],
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
            $result = $this->validate($data, 'Tell');
            //验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if ($res = TellModel::create($data)) {
                // 记录行为
                flow_detail($data['title'],'constructionsite_tell','constructionsite_tell','constructionsite/tell/detail',$res['id']);
                action_log('constructionsite_tell_add', 'constructionsite_tell', $res['id'], UID, $res['id']);
                $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }
        }
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('技术交底')           
            ->addFormItems([
                ['text:6', 'title', '技术交底名称'],
                ['static:6', 'wid', '填报人','',get_nickname(UID)],
 				['select:6','xid', '项目', '', db::name('tender_obj')->column('id,name')],
 				['select:6', 'cid', '车间','',db::name('produce_workcenter')->column('id,name')],
                ['text:6', 'tell_user', '交底人'],
                ['text:6', 'tell_receive_user', '被交底人'],
                ['text', 'tell_site', '交底地点'],
            	['wangeditor', 'tell_content','交底内容'],
            ])           
            ->fetch();

	}
	//详情 
	public function detail($id=null){

		if($id==null)return $this->error('缺少参数');
		
		$tell = TellModel::getTell($id);
		        return ZBuilder::make('form')
            ->setPageTitle('查看详情')           
            ->addFormItems([
                ['static:6', 'title', '技术交底名称'],
                ['static:6', 'nickname', '填报人','',get_nickname($tell['wid'])],
                ['static:6','xname', '项目'],
                ['static:6', 'name', '车间'],
                ['static:6', 'tell_user', '交底人'],
                ['static:6', 'tell_receive_user', '被交底人'],
                ['static', 'tell_site', '交底地点'],
                ['wangeditor', 'tell_content','交底内容'],
            ])      
            ->setFormData($tell)    
            ->hideBtn('submit') 
            ->fetch();

	}

	//删除
	public function delete($ids = null){		
		if($ids == null) $this->error('参数错误');
		$map['id'] = $ids;
		if($model = TellModel::where($map)->delete()){	
			//记录行为
        	action_log('constructionsite_tell_delete', 'constructionsite_tell', $map['id'], UID,$map['id']);			
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}		
	}

}   
