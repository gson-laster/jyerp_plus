<?php
namespace app\purchase\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\admin\model\Access as AccessModel;
use app\purchase\model\Type as TypeModel;
use think\Db;
/**
 *  采购类型
 */
class Type extends Admin
{
	//
	public function index()
	{
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('id desc');

		$data_list = TypeModel::getList($map,$order);
        return ZBuilder::make('table')
	        		->hideCheckbox()
                    ->addColumns([ // 批量添加列
				        ['id', '编号'],
				        ['name', '类型名称'],
				        ['status', '是否开启','switch'],
				        ['right_button','操作']
				    ])
				    ->addTopButton('add') // 添加顶部按钮
				    ->setRowList($data_list) // 设置表格数据
				    ->addRightButton('delete') //添加删除按钮
	                ->fetch();
	        	
	}

	public function add(){

        if ($this->request->isPost()) {
            $data = $this->request->post();
            //验证
            $result = $this->validate($data, 'Type');
            //验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            if ($res = TypeModel::create($data)) {
                // 记录行为
                action_log('purchase_type_add', 'purchase_type', $res['id'], UID, $res['id']);
                $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }
        }
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('采购类型添加')           
            ->addFormItems([
                ['text:6', 'name', '类型名称'],
            ])           
            ->fetch();

	}

	//删除
	public function delete($ids = null){		
		if($ids == null) $this->error('参数错误');
		$map['id'] = $ids;
		if($model = TypeModel::where($map)->delete()){	
			//记录行为
        	action_log('purchase_type_delete', 'purchase_type', $map['id'], UID,$map['id']);			
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}		
	}


}   
