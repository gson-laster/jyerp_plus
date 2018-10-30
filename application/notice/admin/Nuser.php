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

namespace app\notice\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\user\model\Role as RoleModel;
use app\user\model\Organization as OrganizationModel;
use app\user\model\Position as PositionModel;
use app\notice\model\Nlist as NlistModel;
use app\notice\model\Cate as CateModel;
use app\notice\model\Nuser as NuserModel;

/**
 * 我的公告控制器
 * @package app\cms\admin
 */
class Nuser extends Admin
{
    /**
     * 我的公告
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {
        // 查询
        $map = $this->getMap();
        $map['notice_user.uid'] = UID;
        // 排序
        $order = $this->getOrder('notice_user.create_time desc');
        // 数据列表
        $data_list = NuserModel::getList($map,$order);

     
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['notice_list.title' => '公告标题']) // 设置搜索框
            ->addOrder('notice_user.create_time') // 添加排序
            ->addFilter('notice_user.cate', CateModel::getTree())
            ->addColumns([ // 批量添加数据列
                ['__INDEX__', '序号'],            	
            	['cate', '公告类型',CateModel::getTree()],         	
            	['title', '公告标题'],
            	['create_time', '发布时间','datetime'],  
            	['is_read', '查阅状态','status', '',['待阅','已阅']],
            	['right_button', '操作', 'btn']
            ])
            //->addTopButton('delete')
            ->addRightButton('edit',['title' => '查阅','icon'  => 'fa fa-fw fa-search'],true)
            //->addRightButton('delete')
            ->setRowList($data_list) // 设置表格数据            
            ->fetch(); // 渲染模板
    }
  
    /**
     * 查阅
     * @param null $id id
     * @author 黄远东<6414350717@qq.com>
     * @return mixed
     */
    public function edit($id = null)
    {
        if ($id === null) $this->error('缺少参数');

        NuserModel::update(['id'=>$id,'is_read'=>1]);       
        $data_list = NuserModel::getOne($id);
        // 显示编辑页面
        return ZBuilder::make('form')           
            ->addFormItems([
						['hidden', 'id'],
						['hidden', 'uid'],
						['select', 'cate','公告类型','',CateModel::getTree(),1,'disabled'],
						['text', 'title','标题','','','','readonly'],	
            			['textarea', 'description', '公告描述','','','readonly'],
            			['ueditor', 'info', '公告详情'],
            			['archives', 'enclosure', '附件'],
						['textarea', 'note', '备注','','','readonly'],							
				])
            ->setFormData($data_list)
            ->hideBtn('submit')
            ->fetch();
    }
    
    /**
     * 删除我的公告
     * @param array $record 行为日志
     * @author 黄远东<641435071@qq.com>
     * @return mixed
     */
    public function delete($record = [])
    {
   		$ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    	// 删除
    	if (NuserModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids) ? implode(',', $ids) : $ids;
    		$details = '我的公告ID('.$ids.')';
    		action_log('notice_user_delete', 'notice_user', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }   
}