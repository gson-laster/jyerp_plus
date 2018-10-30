<?php
namespace app\tender\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\admin\model\Access as AccessModel;
use app\user\model\Organization as OrganizationModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\tender\model\Obj as ObjModel;
use app\tender\model\Scheduledetail as ScheduledetailModel;
use app\tender\model\Schedule as ScheduleModel;
use think\Db;
/**
 * 项目进度 
 */
class Scheduleover extends Admin
{
	// public function index()
	// {
        
  //       $map = $this->getMap();
  //       $order = $this->getOrder('purchase_ask.id desc');

  //    	$btn_detail = [
		//     'title' => '查看详情',
		//     'icon'  => 'fa fa-fw fa-search',
		//     'href'  => url('detail', ['id' => '__id__'])
		// ];

		// $data_list = AskModel::getList($map,$order);
  //       $type = TypeModel::where('status=1')->column('id,name');
        // return ZBuilder::make('table')
	       //  	 	->setSearch(['purchase_ask.name'=>'主题','admin_user.nickname'=>'申请人'],'','',true) // 设置搜索框
	       //  	 	->addTimeFilter('purchase_ask.atime') // 添加时间段筛选
	       //  	 	// ->addFilter('purchase_ask.tid',$type) // 添加筛选
	       //  		->hideCheckbox()
	       //  		->addOrder('purchase_ask.number,purchase_ask.atime') // 添加排序
        //             ->addColumns([ // 批量添加列
				    //     ['number', '编号'],
				    //     ['name', '主题'],
				    //     // ['tid', '采购类型',$type],
				    //     ['nickname', '申请人'],
				    //     ['oname', '申请部门'],
				    //     ['atime', '申请日期','date'],
        //                 ['step', '审批结果','status','',[20 =>'进行中:info', 30=>'否决:danger', 40=>'同意:success']],
				    //     ['address','到货地址'],
				    //     ['right_button','操作']
				    // ])
				    // // ->setRowList($data_list) // 设置表格数据
				    // // ->addRightButton('btn', $btn_detail,true) // 添加授权按钮
				    // ->addRightButton('delete') //添加删除按钮
				    // ->addTopButton('add') //添加删除按钮
	       //          ->fetch();
	        	
	// }

	public function add(){

        if ($this->request->isPost()) {
            $data = $this->request->post();
            if(empty($data['obj_id'])) $this->error('请选择项目');
            if(!empty($data['gcmx'])){
                $res = ScheduleModel::create(array('obj_id'=>$data['obj_id']));
                $gc = array();
                foreach($data['gcmx'] as $k => $v){
                    $gc[] = [
                            'schedule_id' =>$res['id'],
                            'bh'=>$data['bhs'][$k],
                            'gcmx'=>$v,
                            'dw'=>$data['dw'][$k],
                            'num'=>$data['num'][$k],
                            'dj'=>$data['dj'][$k],
                            'sum'=>$data['num'][$k]*$data['dj'][$k],
                    ];  
                }  
                
                $Scheduledetail = new ScheduledetailModel;
                $Scheduledetail->saveAll($gc);
                flow_detail(ObjModel::where('id',$data['obj_id'])->value('name').'添加进度','tender_schedule','tender_schedule','tender/schedule/detail',$res['id']);
                $this->success('新增成功',url('add'));
            }else{
                $this->error('请点击增行后继续操作');
            }

        }
        return ZBuilder::make('form')
        ->setPageTitle('添加')
        ->addFormItems([['select:6', 'obj_id', '项目名称','',ObjModel::where('status=1')->column('id,name')]])
        ->js('jd')
        ->fetch();
	}
	//详情 
	public function detail($id=null){

		if($id==null)return $this->error('缺少参数');
		
        $obj_id = ScheduleModel::where('id',$id)->value('obj_id');
        return ZBuilder::make('form')
        ->setPageTitle('添加')
        ->addFormItems([['hidden','id',$id],['static:6', 'obj', '项目名称','',ObjModel::where('id',$obj_id)->value('name')]])
        ->js('jd2')
        ->hideBtn('submit') 
        ->fetch();
	}

     //编辑生成物品表格
    public function tech($id = '')
    {
        if(empty($id)) return false;
        $detail = ScheduledetailModel::where('schedule_id',$id)->select();
        if(empty($detail)) return false;

        $html = '<table class="table table-builder table-hover table-bordered table-striped js-table-checkable" style="margin-left:30px"><thead><tr><th class="column-id ">编号</th><th class="column-name ">清单子目</th><th class="column-address ">单位<span></span></th><th class="column-zrid ">工程量<span></span></th><th class="column-tender_time">综合单价 </th><th class="column-unit ">合价<span></span></th></tr></thead><tbody>';
        foreach ($detail as $key => $value) {
            $html .= '<tr class="detail_list"><td class="">'.$value['bh'].'</td><td class="">'.$value['gcmx'].'  </td><td class="">'.$value['dw'].' </td><td class="">'.$value['num'].' </td><td class="">'.$value['dj'].' </td><td class="">'.$value['sum'].' </td></tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }


}   
