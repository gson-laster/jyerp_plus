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
use app\notice\model\Cate as CateModel;
use app\notice\model\Nuser as NuserModel;

/**
 * 公告类型
 * @package app\notice\admin
 */
class Cate extends Admin
{
    /**
     * 组织部门首页    
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('update_time asc,sort asc');
        // 数据列表
        $data_list = CateModel::where($map)->order($order)->paginate();;

        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')         
            ->addOrder('id,create_time,sort') // 添加排序
            ->addColumns([ // 批量添加数据列
                ['id', 'ID'],            	
            	['title', '公告类型'],            	
            	['create_time', '创建日期','datetime'],
            	['status', '状态','switch'],            	
            	['right_button', '操作', 'btn']
            ])
            ->addTopButton('add','',true)
            ->addTopButtons('enable,disable') // 批量添加顶部按钮
            ->addRightButton('edit','',true)  
            ->addRightButton('delete')
            ->setRowList($data_list) // 设置表格数据          
            ->setTableName('notice_cate')
            ->fetch(); // 渲染模板
    }

    /**
     * 新增公告类型
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function add()
    {
        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post('', null, 'trim');
            // 验证
            $result = $this->validate($data, 'Cate');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if ($cate = CateModel::create($data)) {
                // 记录行为
                $details = '类型ID('.$cate['id'].'),类型名称('.$cate['title'].')';
                action_log('notice_cate_add', 'notice_cate', $cate['id'], UID, $details);
                $this->success('新增成功', null,'_parent_reload');
            } else {
                $this->error('新增失败');
            }
        }

        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('新增类型')           
            ->addFormItems([              
                ['text', 'title', '类型名称','<span class="text-danger">必填</span>'],
            	['number', 'sort', '排序', '', 100],
            ])           
            ->fetch();
    }

    /**
     * 编辑节点
     * @param int $id 节点ID
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function edit($id = 0)
    {
        if ($id === 0) $this->error('缺少参数');
        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post('', null, 'trim');
            // 验证
            $result = $this->validate($data, 'Cate');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if (CateModel::update($data)) {               
                // 记录行为
                $details = '公告类型ID('.$id.')';
                action_log('notice_cate_edit', 'notice_cate_edit', $id, UID, $details);
                $this->success('编辑成功', null,'_parent_reload');
            } else {
                $this->error('编辑失败');
            }
        }
        // 获取数据
        $info = CateModel::get($id);

        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('编辑类型')
            ->addFormItems([
            		['hidden','id'],            	
            		['text', 'title', '类型名称','<span class="text-danger">必填</span>'],
            		['number', 'sort', '排序', '', 100],
            ])
            ->setFormData($info)
            ->fetch();
    }
    
    /**
     * 删除公告类型
     * @param array $record 行为日志
     * @author 黄远东 <64143571@qq.com>
     * @return mixed
     */
    public function delete($record = [])
    {
    	return $this->setStatus('delete');
    }
    
    /**
     * 启用公告类型
     * @param array $record 行为日志
     * @author 黄远东 <64143571@qq.com>
     * @return mixed
     */
    public function enable($record = [])
    {
    	return $this->setStatus('enable');
    }
    
    /**
     * 禁用公告类型
     * @param array $record 行为日志
     * @author 黄远东 <64143571@qq.com>
     * @return mixed
     */
    public function disable($record = [])
    {
    	return $this->setStatus('disable');
    }
    
    /**
     * 设置公告类型状态：删除、禁用、启用
     * @param string $type 类型：delete/enable/disable
     * @param array $record
     * @author 黄远东 <64143571@qq.com>
     * @return mixed
     */
    public function setStatus($type = '', $record = [])
    {
    	$ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
    	if(in_array($ids, ['1','2','3'])) $this->error('基本类型无法删除');
    	NuserModel::where(['cate'=>$ids])->delete();
    	$link_title = CateModel::where('id', 'in', $ids)->column('title');
    	return parent::setStatus($type, ['notice_cate_'.$type, 'notice_cate', 0, UID, implode('、', $link_title)]);
    }
}
