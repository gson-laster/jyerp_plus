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

namespace app\stock\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\stock\model\HouseType as HouseTypeModel;


/**
 * 仓库类型控制器
 * @package app\produce\admin
 */
class HouseType extends Admin
{
    /**
     * 仓库列表
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder();
        // 数据列表
        $data_list = HouseTypeModel::where($map)->order($order)->paginate();

        // 类型按钮
        $btn_back = [
        		'title' => '返回',
        		'icon'  => 'fa fa-fw fa-mail-reply',
        		'href'  => url('house/index')
        ];
        
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['name' => '仓库名称']) // 设置搜索框
            ->addOrder('create_time') // 添加排序                     
            ->addColumns([ // 批量添加数据列
                ['__INDEX__', '序号'], 
            	['name', '类型名称'],
            	['sort', '排序'],
            	['create_time', '添加时间','datetime'],
            	['status', '启用状态状态','switch','',['0'=>'关闭','1'=>'启用']],
            	['right_button', '操作', 'btn']
            ]) 
            ->addTopButton('back',$btn_back)
            ->addTopButtons('add,delete') // 批量添加顶部按钮
            ->addRightButtons('edit,delete')         
            ->setRowList($data_list) // 设置表格数据            
            ->fetch(); // 渲染模板
    }

    /**
     * 新增
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function add()
    {
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            
            // 验证
            $result = $this->validate($data, 'HouseType');
            if (true !== $result) $this->error($result);
			
            if ($result = HouseTypeModel::create($data)) {
                // 记录行为
            	$details    = '详情：类型ID('.$result['id'].')';
                action_log('stock_house_type_add', 'stock_house_type', $result['id'], UID, $details);
                $this->success('新增成功', 'index');
            } else {
                $this->error('新增失败');
            }
        }
             
        // 显示添加页面
        return ZBuilder::make('form')
        	->setPageTitle('添加仓库类型')
            ->addFormItems([        		
            			['text', 'name','仓库类型'],
            			['number', 'sort','排序'],
						['radio', 'status', '启用状态','',['0'=>'关闭','1'=>'启用'],1],
				])			
            ->fetch();
    }   

    /**
     * 编辑
     * @param null $id id
     * @author 黄远东<6414350717@qq.com>
     * @return mixed
     */
    public function edit($id = null)
    {
        if ($id === null) $this->error('缺少参数');

        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();            
            // 验证
            $result = $this->validate($data, 'HouseType');
            if (true !== $result) $this->error($result);

            if (HouseTypeModel::update($data)) {
                // 记录行为
            	$details    = '详情：类型ID('.$data['id'].'),修改人ID('.UID.')';
                action_log('stock_house_type_edit', 'stock_house_type', $id, UID, $details);
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }
        
        $data_list = HouseTypeModel::get($id);
        // 显示编辑页面
        return ZBuilder::make('form')   
        	->setPageTitle('修改仓库类型')
            ->addFormItems([
						['hidden', 'id'],       		
            			['text', 'name','类型名称'],
            			['text', 'sort','排序'],
						['radio', 'status', '启用状态','',['0'=>'关闭','1'=>'启用'],1],								
				])
            ->setFormData($data_list)
            ->fetch();
    }
        
    /**
     * 删除
     * @param array $record 行为日志
     * @author 黄远东<641435071@qq.com>
     * @return mixed
     */
    public function delete($record = [])
    {
   		$ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    	// 删除节点
    	if (HouseTypeModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		$details = '类型ID('.$ids.'),操作人ID('.UID.')';
    		action_log('stock_house_type_delete', 'stock_house_type', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }
    
    /**
     * 启用
     * @param array $record 行为日志
     * @author 黄远东<641435071@qq.com>
     * @return mixed
     */
    public function enable($record = [])
    {
    	return $this->setStatus('enable');
    }
    
    /**
     * 禁用
     * @param array $record 行为日志
     * @author 黄远东<641435071@qq.com>
     * @return mixed
     */
    public function disable($record = [])
    {
    	return $this->setStatus('disable');
    }
    
    public function setStatus($type = '', $record = [])
    {
    	$ids        = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    	$link_title = HouseTypeModel::where('id', 'in', $ids)->column('name');
    	return parent::setStatus($type, ['stock_house_type_'.$type, 'stock_house_type', 0, UID, implode('、', $link_title)]);
    }
}