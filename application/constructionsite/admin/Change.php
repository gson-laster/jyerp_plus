<?php
namespace app\constructionsite\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\admin\model\Access as AccessModel;
use app\constructionsite\model\Change as ChangeModel;
use think\Db;
/**
 *  设计变更
 */
class Change extends Admin
{
	//
	public function index()
	{
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('constructionsite_change.id desc');

        $btn_detail = [
		    'title' => '查看详情',
		    'icon'  => 'fa fa-fw fa-search',
		    'href'  => url('detail', ['id' => '__id__'])
		];

		$data_list = ChangeModel::getList($map,$order);
        return ZBuilder::make('table')
	        	 	->setSearch(['title'=>'变更名称','nickname'=>'填报人'],'','',true) // 设置搜索框
	        	 	->addTimeFilter('constructionsite_change.create_time') // 添加时间段筛选
	        	 	->addFilter(['xname'=>'tender_obj.name']) // 添加筛选
	        		->hideCheckbox()
	        		->addOrder('constructionsite_change.id,constructionsite_change.create_time') // 添加排序
                    ->addColumns([ // 批量添加列
				        ['__INDEX__', '编号'],
				        ['title', '变更名称'],
				        ['nickname', '填报人'],
				        ['ti_username', '提出变更'],
				        ['create_time', '日期','date'],
				        ['xname', '项目名称'],
				        ['content', '变更内容'],
				        ['money','变更金额'],
                        ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
				        ['right_button','操作']
				    ])
				    ->setRowList($data_list) // 设置表格数据
				    ->addRightButton('btn', $btn_detail,true) // 添加授权按钮
				  
	                ->fetch();
	        	
	}

	public function add(){

        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['wid'] = UID;
            $data['create_time'] = time();
            //验证
            $result = $this->validate($data, 'Change');
            //验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if ($res = ChangeModel::create($data)) {
                // 记录行为
                flow_detail($data['title'],'constructionsite_change','constructionsite_change','constructionsite/change/detail',$res['id']);
                action_log('constructionsite_change_add', 'constructionsite_change', $res['id'], UID, $res['id']);
                $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }
        }
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('设计变更')           
            ->addFormItems([
                ['text:6', 'title', '设计变更名称'],
                ['static:6', 'wid', '填报人','',get_nickname(UID)],
 				['linkage:6','xid', '选择项目', '', db::name('tender_obj')->column('id,name'), '', url('get_ht'), 'hid'],
 				['select:6','hid','选择合同'],
                ['text:6', 'ti_username', '提出变更者'],
                ['number:6','money','变更金额'],
                ['images', 'old_imgs', '原图片'],
                ['files', 'old_file', '原文件'],
                ['images', 'new_imgs', '更换后图片'],
                ['files', 'new_file', '更换后文件'],
            	['textarea', 'cause', '变更原因'],
            	['wangeditor', 'content','变更内容'],
            ])           
            ->fetch();

	}
	//详情 
	public function detail($id=null){

		if($id==null) $this->error('缺少参数');
		
		$change = ChangeModel::getChange($id);
		        return ZBuilder::make('form')
            ->setPageTitle('查看详情')           
            ->addFormItems([
                ['static:6', 'title', '设计变更名称'],
                ['static:6', 'nickname', '填报人'],
 				['static:6','xname', '项目'],
 				
                ['static:6', 'ti_username', '提出变更者'],
                ['static:6','money','变更金额'],
                ['gallery', 'old_imgs', '原图片'],
                ['archives', 'old_file', '原文件'],
                ['gallery', 'new_imgs', '更换后图片'],
                ['archives', 'new_file', '更换后文件'],
            	['static', 'cause', '变更原因'],
            	['wangeditor', 'content','变更内容'],
            ])      
            ->setFormData($change)    
            ->hideBtn('submit') 
            ->fetch();

	}

	//删除
	public function delete($ids = null){		
		if($ids == null) $this->error('参数错误');
		$map['id'] = $ids;
		if($model = ChangeModel::where($map)->delete()){	
			//记录行为
        	action_log('constructionsite_change_delete', 'constructionsite_change', $map['id'], UID,$map['id']);			
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}		
	}


	//获取合同
    public function get_ht($xid = '')
    {
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        $ht = db::name('contract_income')->where('attach_item',$xid)->column('id,title');
        foreach ($ht as $key => $value) {
        	 $arr['list'][] = ['key'=>$key,'value'=>$value];
        }
        return json($arr);
    }

}   
