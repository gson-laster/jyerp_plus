<?php
namespace app\purchase\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\admin\model\Access as AccessModel;
use app\tender\model\Materials as MaterialsModel;
use think\Db;
/**
 *  施工日志
 */
class Ask extends Admin
{
	//
	public function lists()
	{

        $map = $this->getMap();
        $order = $this->getOrder('purchase_ask.id desc');

        $btn_detail = [
            'title' => '查看详情',
            'icon'  => 'fa fa-fw fa-search',
            'href'  => url('detail', ['id' => '__id__'])
        ];

        $data_list = AskModel::getList($map,$order);
        $type = TypeModel::where('status=1')->column('id,name');
        return ZBuilder::make('table')
                    ->setSearch(['purchase_ask.name'=>'主题','admin_user.nickname'=>'申请人'],'','',true) // 设置搜索框
                    ->addTimeFilter('purchase_ask.atime') // 添加时间段筛选
                    ->addFilter('purchase_ask.tid',$type) // 添加筛选
                    ->hideCheckbox()
                    ->addOrder('purchase_ask.number,purchase_ask.atime') // 添加排序
                    ->addColumns([ // 批量添加列
                        ['number', '编号'],
                        ['name', '主题'],
                        ['tid', '采购类型',$type],
                        ['nickname', '申请人'],
                        ['oname', '申请部门'],
                        ['atime', '申请日期','date'],
                        ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                        ['address','到货地址'],
                        ['right_button','操作']
                    ])
                    ->setRowList($data_list) // 设置表格数据
                    ->addRightButton('btn', $btn_detail,true) // 添加授权按钮
                    ->fetch();
	        	
	        	
	}

	
    //采购通知
	public function inform()
	{
        // 查询
        $map = $this->getMap();
        //只查询status为1的数据
        $map['tender_materials.status']='1';
        // 排序
        $order = $this->getOrder('tender_materials.create_time desc');

        // 数据列表
        $data_list = MaterialsModel::getList($map,$order);
        $task_list = [
            'title' => '查看详情',
            'icon' => 'fa fa-fw fa-eye',
            'href' => url('task_list',['id'=>'__id__'])
        ];
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->addTimeFilter('tender_materials.create_time') // 添加时间段筛选
            ->hideCheckbox()
            ->addOrder(['code','create_time']) // 添加排序
            ->setSearch(['tender_materials.name' => '计划名称','tender_obj.name' => '项目名称'], '', '', true) // 设置搜索参数
            ->addColumns([ // 批量添加数据列
                ['code', '编号'],
                ['name', '计划名称'],
                ['obj_id', '项目名称'],
                ['authorizedname', '编制人'],
                ['create_time', '日期','datetime'],
                ['right_button', '操作', 'btn']
            ])
            ->addRightButton('task_list',$task_list,true) // 查看右侧按钮
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板
	}
	//详情 
	 public function task_list($id = null){
    	if($id == null) $this->error('参数错误');		
		$info = MaterialsModel::getOne($id);
		$info['materials_list'] = implode(MaterialsModel::getMaterials($id),',');
		return ZBuilder::make('form')
		->addGroup([
		'材料计划'=>[
			['hidden','id'],
			['hidden','authorized'],
			['static:4','name','计划名称'],
			['static:4','obj_id','项目名称'],
			['static:4','authorizedname','编制人'],
			['archives','file','附件'],
			['static:4','note','备注'],										
		],
          '材料计划明细' =>[
            ['hidden', 'materials_list'],
            ['hidden', 'old_plan_list'],
          ]			
		])
		->setExtraJs(outjs2())
		->setFormData($info)
		->HideBtn('submit')
		->js('ask')
		->fetch();
    }
     //明细
     public function tech($pid = '', $materials_list = '')
    {
        if ($materials_list == '' || $materials_list == 'undefined') {
            $html = '';
        } else {
            $map = ['tender_materials_detail.pid' => $pid, 'stock_material.id' => ['in', ($materials_list)]];
            $data = MaterialsModel::getDetail($map);
//            dump($data);die;
            $html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>仓库</td><td>单位</td><td>规格</td><td>需用数量</td><td>备注</td></tr>';
            foreach ($data as $k => $v) {
                $html .= '<tr>
                	<input type="hidden" name="mid[]" value="' . $v['itemsid'] . '">
                		<input type="hidden" name="mlid[]" value="' . $v['id'] . '">
                			<td>' . $v['name'] . '</td><td>'.$v['ckname'].'</td>
                			<td>' . $v['unit'] . '</td>
                			<td>' . $v['version'] . '</td>
                			<td>' . $v['xysl'] . '</td>
                			<td>' . $v['bz'] . '</td>
                			</tr>';
            }
            $html .= '</tbody></table></div>';

        }
        return $html;
    }


}   
