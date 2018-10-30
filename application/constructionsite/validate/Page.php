<?php
namespace app\constructionsite\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\admin\model\Access as AccessModel;
use app\constructionsite\model\Log as LogModel;
use think\Db;
/**
 *  施工日志
 */
class Log extends Admin
{
	//
	public function index()
	{

        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('constructionsite_log.id desc');

        $btn_detail = [
		    'title' => '查看详情',
		    'icon'  => 'fa fa-fw fa-search',
		    'href'  => url('detail', ['lid' => '__id__'])
		];

		$data_list = LogModel::getList($map,$order);
        return ZBuilder::make('table')
	        	 	->setSearch(['nickname'=>'填报人'],'','',true) // 设置搜索框
	        	 	->addTimeFilter('constructionsite_log.create_time') // 添加时间段筛选
	        	 	->addFilter(['xname'=>'tender_obj.name']) // 添加筛选
	        		->hideCheckbox()
	        		->addOrder('constructionsite_log.id,constructionsite_log.create_time') // 添加排序
                    ->addColumns([ // 批量添加列
				        ['id', '编号'],
				        ['nickname', '填报人'],
				        ['create_time', '日期','date'],
				        ['xname', '项目名称'],
				        ['work_content', '施工进展'],
				        ['work_wrong','主要问题'],
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
            // 验证
            $result = $this->validate($data, 'Log');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if ($res = LogModel::create($data)) {
                // 记录行为
                $xname = db::name('tender_obj')->where('id',$data['xid'])->value('name');
                $details = "属于 ".$xname." 项目,日志ID(".$res['id'].")";
                action_log('constructionsite_log_add', 'constructionsite_log', $res['id'], UID, $details);
                $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }
        }
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('添加日志')           
            ->addFormItems([
                ['select:6', 'xid', '项目名称', '', db::name('tender_obj')->column('id,name')],
                ['static:6', 'wid', '填报人','',get_nickname(UID)],
            	['select:6', 'am_weather', '上午天气', '', [0=>'晴',1=>'阴',2=>'雨']],
            	['select:6', 'pm_weather', '下午天气', '', [0=>'晴',1=>'阴',2=>'雨']],
            	['number:6','max_warm','最高气温 （℃）','','10'],
            	['number:6','min_warm','最低气温 （℃）','','10'],
            	['select:6', 'cid', '车间','',db::name('produce_workcenter')->column('id,name')],
            	['number:6','work_num','工人人数','','20'],
            	['wangeditor', 'work_content', '施工内容'],
            	['wangeditor', 'work_wrong','施工遇到的问题'],
            ])           
            ->fetch();

	}
	//详情 
	public function detail($lid=null){

		if($lid==null)return $this->error('缺少参数');
		
		$log = LogModel::getLog($lid);
		        return ZBuilder::make('form')
            ->setPageTitle('日志详情')           
            ->addFormItems([
                ['static:6', 'xname', '项目名称', '', $log['xname']],
                ['static:6', 'wname', '填报人','',get_nickname($log['wid'])],
            	['static:6', 'am_weathers', '上午天气', '', self::weather($log['am_weather'])],
            	['static:6', 'pm_weathers', '下午天气', '', self::weather($log['pm_weather'])],
            	['static:6','max_warm','最高气温','','10'],
            	['static:6','min_warm','最低气温','','10'],
            	['static:6', 'name', '车间',''],
            	['static:6','work_num','工人人数',''],
            	['wangeditor', 'work_content', '施工内容'],
            	['wangeditor', 'work_wrong','施工遇到的问题',''],
            ])      
            ->setFormData($log)    
            ->hideBtn('submit') 
            ->fetch();

	}

	//删除
	public function delete($ids = null){		
		if($ids == null) $this->error('参数错误');
		$map['id'] = $ids;
		if($model = LogModel::where($map)->delete()){	
			//记录行为
        	action_log('constructionsite_log_delete', 'constructionsite_log', $map['id'], UID,$map['id']);			
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}		
	}


	//获取天气
	function weather($id=null){
		switch ($id) {
			case 1:
				return '阴';
				break;
			case 2:
				return '雨';
				break;
			default:
				return '晴';
				break;
		}
	}

}   
