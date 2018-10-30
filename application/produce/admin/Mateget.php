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

namespace app\produce\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\produce\model\Plan as PlanModel;
use app\produce\model\PlanList as PlanListModel;
use app\produce\model\Production as ProductionModel;
use app\produce\model\ProductionList as ProductionListModel;
use app\user\model\User as UserModel;
use app\user\model\Role as RoleModel;
use app\user\model\Organization as OrganizationModel;
use app\user\model\Position as PositionModel;
use app\produce\model\Mateget as MategetModel;
use app\produce\model\MategetList as MategetListModel;
use app\stock\model\Material as MaterialModel;
use app\stock\model\MaterialType as MaterialTypeModel;
use app\stock\model\House as HouseModel;
use app\tender\model\Materials as MaterialsModel;
use app\tender\model\Materialsdetail as MaterialsdetailModel;
use app\stock\model\Stock as StockModel;
/**
 * 领料单控制器
 * @package app\produce\admin
 */
class Mateget extends Admin
{
    /**
     * 物料需求计划列表
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {
        // 查询
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('produce_mateget.id desc');
        // 数据列表
        $data_list = MategetModel::getList($map,$order);
        $btn_detail = [
            'title' => '查看详情',
            'icon'  => 'fa fa-fw fa-search',
            'href'  => url('detail', ['id' => '__id__'])
        ];
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->hideCheckbox()
            ->setSearch(['produce_mateget.number' => '单据编号']) // 设置搜索框
            ->addColumns([ // 批量添加数据列
                ['__INDEX__', '序号'], 
            	['number', '单据编号'],
            	['name', '主题'],
            	['plan_name', '生产计划'],
            	['org_name', '生产部门'],  	
                ['get_username', '领料人'],
                ['out_username', '发料人'],
            	['bname', '制单人'],
            	['create_time', '制单时间','date'],
                ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
            	['right_button', '操作', 'btn']
            ])
            ->addTopButtons('add') // 批量添加顶部按钮
            ->addRightButton('btn', $btn_detail,true) // 添加授权按钮
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
            $data['out_user_id'] = $data['zrid'];
            $data['number'] = 'SCLL'.date('YmdHis',time());
            $data['create_time'] = time();
            $data['out_time'] = strtotime($data['out_time']);
            $data['organization_id'] = $data['org_id'];
            $data['wid'] = UID;
            $info = array();
            $kc = StockModel::where(['materialid'=>['in',implode($data['mid'], ',')]])->column('materialid,number');
            // dump($kc);
            // die;
            if(empty($kc)) $this->error('材料不存在');

            foreach ($data['mid'] as $key => $value) {
                if($data['lysl'][$key] == 0){
                    $this->error($data['clname'][$key].'领取数量不能为0');
                }
                if($data['lysl'][$key] > $kc[$value]){
                    $this->error($data['clname'][$key].'库存不足,请先发起物料需求计划');
                }
            }
            // 验证
            $result = $this->validate($data, 'Mateget');
            if (true !== $result) $this->error($result);
			$data['uid']=UID;
            if ($results = MategetModel::create($data)) {
            	foreach($data['mid'] as $k => $v){
            		$info = array();
            		$info = [
            				'mid'=>$v,
            				'pid'=>$results['id'],
            				'lysl'=>$data['lysl'][$k],
                            'price_sum'=>$data['price_sum'][$k],
							'dj'=>$data['dj'][$k],
                            'ckid'=>$data['ckid'][$k],
            				'note'=>$data['note'][$k],
            		];
            		MategetListModel::create($info);            	
            	}
                flow_detail($data['name'],'produce_mateget','produce_mateget','produce/mateget/detail',$results['id']);
                $this->success('新增成功', 'index');
            }else{
                $this->error('新增失败');
            }
        }  
          $js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
    $('#plan_name').click(function(){
            //iframe窗
            layer.open({
              type: 2,
              title: '选择生产计划',
              shadeClose: true,
              shade: 0.3,
              maxmin: true, //开启最大化最小化按钮
              area: ['70%', '70%'],
              content: '/admin.php/produce/mateget/choose_plan'
            });      
    });
    
    $('#get_user_name').click(function(){
            //iframe窗
            layer.open({
              type: 2,
              title: '选择领料人',
              shadeClose: true,
              shade: 0.3,
              maxmin: true, //开启最大化最小化按钮
              area: ['70%', '70%'],
              content: '/admin.php/produce/mateget/get_user_name'
            });      
    }); 
    
    

});

EOF;
        $house = HouseModel::where('status',1)->column('id,name');
        $html = 'var house_select = \'<select name="house[]">';
        foreach ($house as $key => $value) {
            $html.='<option value="'.$key.'">'.$value.'</option>';
            
        }
        $html.='</select>\';
        </script>';

        // 显示添加页面
        return ZBuilder::make('form')
        	->setPageTitle('领料管理')
        	->addGroup(
        			[
        				'添加领料' =>[

                                    ['hidden', 'plan_id'],
                                    ['hidden', 'zrid'],
        							['hidden', 'get_user_id'],
			            			['text:6', 'name','主题'],
        							['text:6','plan_name', '主生产计划'],
        							['select:6','org_id', '生产部门','',OrganizationModel::getTree()],
                                    ['text:6','get_user_name', '领料人'],                                    
                                    ['text:6','zrname', '发料人'],                                    
			            			['date:6','out_time', '领料时间'],        							
        							['files','file', '附件'],
			            			['textarea', 'remark', '备注'],    
        				],
        				'领料明细' =>[
        					['hidden', 'materials_list'],
        					['hidden','is_detail',0]
        				]
        			])
			->setExtraJs($js.$html.outjs2())
            ->setExtraHtml(outhtml2())
            ->js('mateget')
            ->fetch();
    } 
    
    //详情   
    public function detail($id=null){

        if($id==null){
            $this->error('请选择要查看的详情');
        }
        $data = MategetModel::getOne($id);
        $data['out_time'] = date('Y-m-d',$data['out_time']);
        $data['materials_list'] = implode(MategetListModel::where('pid',$data['id'])->column('id'),',');
        $data['is_detail'] = 1; 
        // 显示详情页面
        return ZBuilder::make('form')
            ->setPageTitle('领料管理')
            ->addGroup(
                    [
                        '领料详情' =>[
                                    ['hidden','id'],
                                    ['static:6', 'name','主题'],
                                    ['static:6','plan_name', '主生产计划'],
                                    ['static:6','org_name','生产部门'],
                                    ['static:6','get_username', '领料人'],                                    
                                    ['static:6','out_username', '发料人'],                                    
                                    ['static:6','out_time', '领料时间'],                                  
                                    ['archives','file', '附件'],
                                    ['static', 'remark', '备注'],    
                        ],
                        '领料明细' =>[
                            ['hidden', 'materials_list'],
                            ['hidden','is_detail']
                            
                        ]
                    ])
            // ->setExtraJs($js.$html.outjs2())
            // ->setExtraHtml(outhtml2())
            ->setFormData($data)    
            ->hideBtn('submit') 
            ->js('mateget')
            ->fetch();
    }
    
    /**
     * 编辑生成工艺表格
     * @param array $record 行为日志
     * @author 黄远东<641435071@qq.com>
     * @return mixed
     */
    public function tech($pid = '')
    {
        if($pid==null) return '';
        $obj_id = PlanModel::where('id',$pid)->value('obj_id');
        $cljx = MaterialsModel::where('obj_id',$obj_id)->column('id');
        $map = ['pid'=>['in',implode($cljx, ',')]];
        $cl = MaterialsModel::getDetail2($map);
        $html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>领料数量</td><td>库存</td><td>所在仓库</td><td>单价</td><td>金额</td><td>备注</td></tr>';
            foreach ($cl as $k => $v){ 
                $html.='<tr><input type="hidden" name="mid[]" value="'.$v['itemsid'].'"><input type="hidden" name="ckid[]" value="'.$v['ckid'].'"><input type="hidden" name="clname[]" value="'.$v['name'].'"><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td><input type="number" name="lysl[]" value="'.$v['xysl'].'"></td><td>'.$v['number'].'</td><td>'.$v['ckname'].'</td><td><input type="number" name="dj[]" value="'.$v['price'].'" readonly></td><td><input type="number" name="price_sum[]" value="'.$v['xysl']*$v['price'].'" readonly></td><td><input type="text" name="note[]"></td></tr>';
            }           
            $html .= '</tbody></table></div>';
        return $html;
    }
    
    public function tech2($materials_list='')
    {
        if($materials_list==null) return '';
        $map = ['produce_mateget_list.id'=>['in',$materials_list]];
        $cl = MategetModel::getmdetail($map);
        if(empty($cl)) return '';
        $html = '<span class="btn btn-success" onclick="dddd();" style="margin:10px">打印明细</span><!--startprint--><div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><div id="toplist"></div><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>领料数量</td><td>目前库存</td><td>所在仓库</td><td>单价</td><td>金额</td><td>备注</td></tr>';
            foreach ($cl as $k => $v){ 
                $html.='<tr><td>'.$v['name'].'</td><td>'.$v['unit'].'</td><td>'.$v['version'].'</td><td>'.$v['lysl'].'</td><td>'.$v['number'].'</td><td>'.$v['ckname'].'</td><td>'.$v['dj'].'</td><td>'.$v['price_sum'].'</td><td>'.$v['note'].'</td></tr>';
            }           
        $html .= '</tbody></table></div><!--endprint-->';
        return $html;
    }
       
    /**
     * 选择主生产计划
     * @author 黄远东 <641435071@qq.com>
     */
    public function choose_plan()
    {
        // 查询
        $map = $this->getMap();
        $map['produce_plan.status'] = 1;
        // 排序
        $order = $this->getOrder('produce_plan.create_time desc');
        // 数据列表
        $data_list = PlanModel::getList($map,$order);
    
        $js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
    $('.table-builder input:checkbox').click(function(){
            var pid = $(this).val();
            var plan_name = $.trim($(this).parents('tr').find('td').eq(3).text());
            
            $("#plan_id",parent.document).val(pid);
            $("#plan_name",parent.document).val(plan_name);
            
            //当你在iframe页面关闭自身时
            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
            parent.layer.close(index); //再执行关闭
    });
});
            </script>
EOF;
    
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch(['produce_plan.code' => '单据编号']) // 设置搜索框
            ->addFilter('produce_plan.org_id',OrganizationModel::getTree())
            ->addColumns([ // 批量添加数据列
                ['id', '序号'], 
                ['code', '单据编号'],
                ['name', '主题'],
                ['bname', '负责人'],
                ['org_id', '部门',OrganizationModel::getTree()],
                ['nickname', '制单人'],
                ['create_time', '制单时间','datetime'],             
            ])
        ->setRowList($data_list) // 设置表格数据
        ->setExtraJs($js)
        ->assign('empty_tips', '暂无数据')
        ->setTableName('produce_plan')
        ->fetch('choose'); // 渲染页面
    }
       
    /**
     * 选择负责人
     * @author 黄远东 <641435071@qq.com>
     */
    public function get_user_name()
    {
    	// 获取查询条件
    	$map = $this->getMap();
    	$order = $this->getOrder();
    	// 数据列表   	 
    	$data_list = UserModel::view('admin_user', true)    	
    	->view("admin_organization", ['title'], 'admin_organization.id=admin_user.organization', 'left')   
    	->where($map)
    	->order($order)
    	->paginate();   
    	// 分页数据
    	$page = $data_list->render();
    
    	$js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
	$('.table-builder input:checkbox').click(function(){
			var uid = $(this).val();
        	var nickname = $.trim($(this).parents('tr').find('td').eq(3).text());   		
			$("#get_user_id",parent.document).val(uid);
        	$("#get_user_name",parent.document).val(nickname);   		
			//当你在iframe页面关闭自身时
			var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
			parent.layer.close(index); //再执行关闭
	});
});
            </script>
EOF;
    
    	// 使用ZBuilder快速创建数据表格
    	return ZBuilder::make('table')
    	->setTableName('admin_user') // 设置数据表名
    	->setSearch(['admin_user.id' => 'ID', 'admin_user.username' => '用户名', 'admin_user.nickname' => '姓名']) // 设置搜索参数
    	->addOrder('admin_user.id,admin_user.organization,admin_user.position,admin_user.is_on')
    	->addFilter('admin_user.role', RoleModel::getTree2())
    	->addFilter('admin_organization.title')
    	->addFilter('admin_user.position', PositionModel::getTree(null, false))
    	->addFilter('admin_user.is_on', ['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职'])
    	->addColumns([ // 批量添加列
    			['id', 'ID'],
    			['username', '用户名'],
    			['nickname', '姓名'],
    			['role', '角色', RoleModel::getTree2()],
    			['organization', '部门编号'],
    			['title', '部门名称'],
    			['position', '职位', PositionModel::getTree()],
    			['create_time', '创建时间', 'datetime'],
    			['is_on', '在职状态',['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职']],
    	])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js)
    	->assign('empty_tips', '暂无需要添加证件的用户')
    	->fetch('choose'); // 渲染页面
    }
    
    /**
     * 选择部门
     * @author 黄远东 <641435071@qq.com>
     */
    public function choose_org()
    {
    	// 获取查询条件
    	$map = $this->getMap();
    	$order = $this->getOrder();
    	// 数据列表
    	$data_list = OrganizationModel::where($map)->order($order)->paginate();

    	$js = <<<EOF
            <script type="text/javascript">
                jQuery(function () {
	$('.table-builder input:checkbox').click(function(){
			var oid = $(this).val();
        	var title = $.trim($(this).parents('tr').find('td').eq(2).text());
			$("#org_id",parent.document).val(oid);
        	$("#org_name",parent.document).val(title);
			//当你在iframe页面关闭自身时
			var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
			parent.layer.close(index); //再执行关闭
	});
});
            </script>
EOF;
    
    	// 使用ZBuilder快速创建数据表格
    	return ZBuilder::make('table')
    	->setTableName('admin_organization') // 设置数据表名
    	->setSearch(['title' => '部门名称']) // 设置搜索参数
    	->addOrder('id')
    	->addColumns([ // 批量添加列
    			['id', 'ID'],
    			['title', '部门名称'],	
    	])
    	->setRowList($data_list) // 设置表格数据
    	->setExtraJs($js)
    	->assign('empty_tips', '暂无数据')
    	->fetch('choose'); // 渲染页面
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
    	if (ProductionModel::destroy($ids)) {
    		// 记录行为
    		$ids = is_array($ids)?implode(',',$ids):$ids;
    		$details = '生产任务ID('.$ids.'),操作人ID('.UID.')';
    		action_log('produce_production_delete', 'produce_production', $ids, UID, $details);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }



}