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
use app\user\model\User as UserModel;
use app\user\model\Role as RoleModel;
use app\user\model\Organization as OrganizationModel;
use app\user\model\Position as PositionModel;
use app\personnel\model\Wage as WageModel;
use app\personnel\model\Wagecate as WagecateModel;
use app\personnel\model\Wagelist as WagelistModel;

/**
 * 薪资控制器
 * @package app\cms\admin
 */
class Wagelist extends Admin
{    
    /**
     * 新增
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function add($uid = null,$type = null)
    {
    	if($uid == null) $this->error('缺少参数');
    	if($type == null) $this->error('缺少参数');
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            $data['wage_type'] = $type;
            if($type == 1){
            	$data['total_pay'] = $data['base_pay'] + $data['merit_pay'] + $data['extro_pay'];
            }
            if($type == 2){
            	$data['total_pay'] = $data['base_pay'] + $data['piece_pay'] + $data['extro_pay'];
            }
            
            // 验证
            $result = $this->validate($data, 'Wagelist');
            if (true !== $result) $this->error($result);
 
            if ($wagelist = WagelistModel::create($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.$wagelist['uid'].'),档案ID('.$wagelist['id'].')';
                action_log('personnel_wagelist_add', 'personnel_wagelist', $wagelist['id'], UID, $details);
                $this->success('新增成功', url('wage/view',['uid'=>$uid,'type'=>$type]));
            } else {
                $this->error('新增失败');
            }
        }
        
        $user = UserModel::get($uid);
        $wage_type = WagecateModel::getTree();
        $typename = $wage_type[$type];
        $date = [
        			['hidden', 'uid',$uid],
	        		['static', 'nickname','姓名','',$user['nickname']],
	        		['static', 'wage_type', '工资类型', '', $typename],
        			['date', 'wage_time', '工资时间', '', '', 'yyyy-mm','data-start-view=1 data-max-view-mode=2 data-min-view-mode=1']        		        		
        ];
       
        if($type == 1){
        	$info = [	        			
	        			['text', 'base_pay','基本工资','单位/元','0.00'],
	        			['text', 'merit_pay','绩效工资','单位/元','0.00'],  
        			    ['text', 'extro_pay','其他工资','单位/元','0.00'],
        	];
        	
        }
        if($type == 2){        	
        	$info = [	        			
	        			['text', 'base_pay','基本工资','单位/元','0.00'],
	        			['text', 'piece_pay','计件工资','每单位工资','0.00'],
        			    ['text', 'extro_pay','其他工资','单位/元','0.00'],
        	];
        }
              
        $date = array_merge($date,$info);
        // 显示添加页面
        return ZBuilder::make('form')
             ->addFormItems($date)	            
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
            $result = $this->validate($data, 'Wagelist');
            if (true !== $result) $this->error($result);

            if (WagecateModel::update($data)) {
                // 记录行为
            	$details    = '详情：用户ID('.$data['uid'].'),档案ID('.$data['id'].')';
                action_log('personnel_wagelist_edit', 'personnel_wagelist', $id, UID, $details);
                $this->success('编辑成功', 'index');
            } else {
                $this->error('编辑失败');
            }
        }
                       
        $data = WagelistModel::getOne($id);
       
        
        $trigger = [
        		['wage_type', '1', 'merit_pay'],   
        		['wage_type', '2', 'piece_pay'],
        ];
        	
        
        // 显示编辑页面
        return ZBuilder::make('form')           
           ->addFormItems([
           		['hidden', 'uid'],
           		['hidden', 'wage_type'],
           		['static', 'nickname','姓名'],
           		['select', 'wage_type', '工资类型','',WagecateModel::getTree(),'','disabled'],
           		['date', 'wage_time', '工资时间', '', '', 'yyyy-mm'],
           		['text', 'base_pay','基本工资','单位/元','0.00'],
           		['text', 'merit_pay','绩效工资','单位/元','0.00'],           		
           		['text', 'piece_pay','计件工资','每单位工资','0.00'],
           		['text', 'extro_pay','其他工资','每单位工资','0.00'],
           		['text', 'total_pay','总工资','单位/元','0.00'],
						
			])
			->setTrigger($trigger)
            ->setFormData($data)
            ->fetch();
    }

}