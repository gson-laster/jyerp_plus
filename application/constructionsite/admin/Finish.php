<?php
namespace app\constructionsite\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\admin\model\Access as AccessModel;
use app\constructionsite\model\Finish as FinishModel;
use think\Db;
use app\tender\model\Obj;
use app\constructionsite\model\Log as LogModel;
/**
 *  施工日志
 */
class Finish extends Admin
{
	//
	public function index()
	{

        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('constructionsite_finish.id desc');

        $task_list = [
		    'title' => '查看详情',
		    'icon'  => 'fa fa-fw fa-search',
		    'href'  => url('task_list', ['id' => '__id__'])
		];

		$data_list = FinishModel::getList($map,$order);

		
        return ZBuilder::make('table')
	        	
	        	 	->addTimeFilter('constructionsite_finish.date') // 添加时间段筛选
	        	 	->addFilter(['item'=>'tender_obj.name']) // 添加筛选
	        		->hideCheckbox()
	        		->addOrder('constructionsite_finish.e_time,constructionsite_finish.s_time') // 添加排序
                    ->addColumns([ // 批量添加列
				        ['__INDEX__', '编号'],
				        ['item', '完工项目'],
				        ['s_time', '开工日期','date'],
				        ['e_time', '竣工日期','date'],				   
				        ['maker','申请人'],				      
				        ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
				        ['right_button','操作'],
				    ])
				    ->setRowList($data_list) // 设置表格数据
				    ->addRightButton('task_list',$task_list,true) // 添加授权按钮
				    
				    ->setTableName('constructionsite_finish')
	          ->fetch();
	        	
	}


	public function add()
	{
		 if ($this->request->isPost()) {
            $data = $this->request->post();
    				$data['maker'] = UID;
    				$data['s_time'] = strtotime($data['s_time']);
            $data['e_time'] = strtotime($data['e_time']);
            $data['date'] = strtotime($data['date']);
           	//Dump($data);die;
            // 验证
            $result = $this->validate($data, 'Finish');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            if ($res = FinishModel::create($data)) {
                // 记录行为
                flow_detail( FinishModel::getName($data['item']).'的竣工申请','constructionsite_finish','constructionsite_finish','constructionsite/finish/task_list',$res['id']);     
                $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }
        }
	// 使用ZBuilder快速创建表单
$js = <<<EOF
            <script type="text/javascript">			
				$('input[name="e_time"]').change(function(){
					fn($(this));
				});
				$('input[name="s_time"]').change(function(){
					fn($(this));
				});
				function fn(o) {
					var e_t = new Date($('input[name="e_time"]').val()).getTime();
					var s_t = new Date($('input[name="s_time"]').val()).getTime();
					if (s_t > e_t) {
						layer.msg('结束日期不得早于开始日期', {time: 3000})
						o.val('')
						$('input[name="obj_time"]').val('')
						
					}
                      else{
      						var tianshu = (e_t - s_t) / (24 * 60 * 60 * 1000);
      						if(!isNaN(tianshu)){
								$('input[name="obj_time"]').val(tianshu)
							}
                      }
				
			}
            </script>
EOF;


        return ZBuilder::make('form')
            ->setPageTitle('竣工申请')           
            ->addFormItems([
              ['date:3','date','日期','',date('Y-m-d')],
              ['select:5', 'item', '完工项目', '', FinishModel::getItem()],
            	['date:4', 's_time', '开工日期'],
            	['date:4', 'e_time', '竣工日期'],
            	['number:3','obj_time','工期(天)'],
            	['text:6', 'maker', '申请人','',get_nickname(UID)],
            	['wangeditor','note','备注'],
            	['files:6','file','竣工图'],
            ])
           	->setExtraJs($js)           
            ->fetch();
	}
	
	
	
	
	public function task_list($id){
		$data = FinishModel::getOne($id);
		$data['date'] = date("Y-m-d",$data['date']);
		$data['s_time'] = date("Y-m-d",$data['s_time']);
		$data['e_time'] = date("Y-m-d",$data['e_time']);
		
				 return ZBuilder::make('form')
            ->setPageTitle('竣工申请')           
            ->addFormItems([
              ['static:3','date','日期'],
              ['static:5', 'item', '完工项目'],
            	['static:4', 's_time', '开工日期'],
            	['static:4', 'e_time', '竣工日期'],
            	['static:3','obj_time','工期(天)'],
            	['static:6', 'maker', '申请人'],
            	['static','note','备注'],
            	['archives','file',' 竣工图'],
            ])
            ->setFormData($data)
            ->HideBtn('submit')
            ->fetch();		
		}
	
		
		
}   
