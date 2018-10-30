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

namespace app\personnel\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\personnel\model\Awardcate as AwardcateModel;

/**
 * 奖惩控制器
 * @package app\cms\admin
 */
class Awardcate extends Admin
{
    /**
     * 文档列表
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('update_time desc,sort asc');
        // 数据列表
        $data_list = AwardcateModel::where($map)->order($order)->paginate();;

        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')         
            ->addOrder('id,create_time,sort') // 添加排序
            ->addColumns([ // 批量添加数据列
                ['id', 'ID'],            	
            	['name', '奖惩项目'],            	
            	['create_time', '创建日期','datetime'],
            	['sort', '排序'],
            	['status', '状态','switch'],            	
            	['right_button', '操作', 'btn']
            ])
            ->addTopButton('add','',true)
            ->addTopButtons('delete,enable,disable') // 批量添加顶部按钮
            ->addRightButton('edit','',true)
            ->addRightButton('delete')
            ->setRowList($data_list) // 设置表格数据          
            ->setTableName('personnel_awardcate')
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
            $result = $this->validate($data, 'Awardcate');
            if (true !== $result) $this->error($result);

            if ($awardcate = AwardcateModel::create($data)) {
                // 记录行为
            	$details    = '详情：奖惩项目('.$awardcate['name'].'),项目ID('.$awardcate['id'].')';
                action_log('personnel_awardcate_add', 'personnel_awardcate', $awardcate['id'], UID, $details);
                $this->success('新增成功', null, '_parent_reload');
            } else {
                $this->error('新增失败');
            }
        }
        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([
                ['text', 'name', '奖惩项目'],
                ['text', 'sort', '排序', '', 100],
                ['radio', 'status', '立即启用', '', ['否', '是'], 1]
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
            $result = $this->validate($data, 'Awardcate');
            if (true !== $result) $this->error($result);

            if (AwardcateModel::update($data)) {
                // 记录行为
            	$details    = '详情：奖惩项目('.$data['name'].'),项目ID('.$data['id'].')';
                action_log('personnel_awardcate_edit', 'personnel_awardcate', $id, UID, $details);
                $this->success('编辑成功', null,'_parent_reload');
            } else {
                $this->error('编辑失败');
            }
        }
        

        $data_list = AwardcateModel::get($id);
        // 显示编辑页面
        return ZBuilder::make('form')           
			->addFormItems([
					['hidden', 'id'],
					['text', 'name', '奖惩项目'],
					['text', 'sort', '排序', '', 100],
					['radio', 'status', '立即启用', '', ['否', '是'], 1]
			])
            ->setFormData($data_list)
            ->fetch();
    }
    
    /**
     * 删除奖惩项目
     * @param array $record 行为日志
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function delete($record = [])
    {
    	return $this->setStatus('delete');
    }
    
    /**
     * 启用奖惩项目
     * @param array $record 行为日志
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function enable($record = [])
    {
    	return $this->setStatus('enable');
    }
    
    /**
     * 禁用奖惩项目
     * @param array $record 行为日志
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function disable($record = [])
    {
    	return $this->setStatus('disable');
    }
    
    /**
     * 设置奖惩项目状态：删除、禁用、启用
     * @param string $type 类型：delete/enable/disable
     * @param array $record
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function setStatus($type = '', $record = [])
    {
    	$ids        = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    	$link_title = AwardcateModel::where('id', 'in', $ids)->column('name');
    	return parent::setStatus($type, ['personnel_awardcate_'.$type, 'personnel_awardcate', 0, UID, implode('、', $link_title)]);
    }
   
}