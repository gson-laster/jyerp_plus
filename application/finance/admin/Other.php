<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28 0028
 * Time: 14:44
 */

namespace app\finance\admin;


use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\finance\model\Organization;
use app\finance\model\Other as OModel;
//use app\finance\model\User as UModel;
use app\tender\model\Obj as IModel;
use app\finance\model\Bank as BModel;
use app\finance\model\Pway;
use app\finance\model\Ptype;
use think\Db;
use think\config;
use app\supplier\model\Supplier;
use app\user\model\Organization as OrganizationModel;
class Other extends Admin
{
    public function index(){
        $map = $this->getMap();
        $order = $this->getOrder('o.id desc');
        $data_list = OModel::getList($map,$order);
        
        $task_list = [
						'title' => '查看详情',
						'icon' => 'fa fa-fw fa-eye',
						'href' => url('edit',['id'=>'__id__'])
						];
        $pname = OModel::getP();
        //dump($pname);die;
        //dump($data_list);die;
        //dump($data_list);die;
        //使用ZBuilder构建表格展示数据
        return ZBuilder::make('table')
            ->setSearch(['obj.name'=>'项目','o1.name'=>'供应商'],'','',true) // 设置搜索框
            ->addTimeFilter('o.date') // 添加时间段筛选
            ->addFilter(['ptype'=>'finance_ptype.name'])
            ->addFilter(['ortitle'=>'admin_organization.title'])
            ->addOrder() // 添加排序
            ->addColumns([ // 批量添加列
                ['number', '付款编号'],
                ['objname', '项目'],
                ['date', '付款日期'],
                ['unickname', '付款人'],
                ['ortitle','部门'],
                ['o1rtitle', '供应商'],
                ['macc', '公司账户'],
                ['money', '付款金额'],
                ['ptype', '付款类型'],
                ['pwname', '付款方式'],
                ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                ['right_button','操作']
            ])
            ->setRowList($data_list) // 设置表格数据
            ->addTopButton('add') //添加删除按钮
            ->addTopButton('delete') //添加删除按钮
            ->addRightButton('edit',$task_list,true) // 添加授权按钮
            ->addRightButton('delete') //添加删除按钮
            ->setTableName('finance_other')
            ->fetch();


    }


    public function add(){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['maker'] = UID;
          
            $result = $this->validate($data, 'Other');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            $data['number'] = 'FKD'.date('YmdHis',time());
            if ($res = OModel::create($data)) {
            
            flow_detail(OModel::getSname($data['item']).'项目的付款','finance_other','finance_other','finance/other/edit',$res['id']);
                $this->success('新增成功',url('index'));
            } else {
                $this->error('新增失败');
            }
        }
        
        
        $js = <<<EOF
            <script type="text/javascript">
                $(function(){
                   $('#money').attr('oninput','return Edit1Change();');				   					
                });
				var j=chineseNumber(document.getElementById("money").value);
				document.getElementById("big_money").value=j;		
				function Edit1Change(){			
					document.getElementById("big_money").value=chineseNumber(document.getElementById("money").value);
				}
					
            </script>
EOF;



       //   $list_province= UModel::where('status=1')->column('id,nickname');
        // 使用ZBuilder快速创建表单
        $user = Db::name('admin_user') -> column('id,nickname');
        return ZBuilder::make('form')
            ->setPageTitle('付款单')
            ->addFormItems([
            	['hidden', 'payer', UID],
                ['date:3','date','日期','',date('Y-m-d')],
                ['text:3','zrid', '付款人','',get_nickname(UID),'', ''],
                ['select:3','part', '部门', '', OrganizationModel::getMenuTree2()],
                ['select:3','supplier','供应商','',Supplier::getName()],
                ['select:3','account','公司账户','',BModel::where('status_1=1')->column('id,name')],
                ['number:3','money','付款金额'],
                ['text:3','big_money','金额大写'],
                ['select:3','ptype','付款类型','',Ptype::getName()],
                ['select:3','pway','支付方式','',Config::get('pay_way')],
                ['select:3','maker', '经办人','',$user, UID, 'disabled'],
                ['select:3','item','项目','',IModel::get_nameid()],
                ['textarea','remark','付款说明'],
                ['file','file','附件'],
            ])
        	->setExtraHtml(outhtml2())
			    ->setExtraJs($js.outjs2())
			    ->js('chineseNumber')
          ->fetch();
    }

    public function get_part($payer=''){
        $res= UModel::where('id',$payer)->select();
        //dump($res);die;
        $array =array();
        foreach($res as $key=>$val){
            $array[] = ['key'=>$val['id'],'value'=>UModel::getOne(($val['organization']))];
        }
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        $arr['list'] =$array; //数据
        return json($arr);
    }


		public function edit($id=''){
 				$data_list = OModel::getOne($id);
 				
 				//dump($data_list);die;
 				
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('供应商添加') 
            ->addHidden('id')          
            ->addFormItems([
                ['static:3', 'date', '日期'],
								['static:3', 'number', '付款编号'],
								['static:3', 'unickname', '付款人'],
								['static:3', 'ortitle', '部门'],
								['static:3', 'o1rtitle','供应商'],
								['static:3', 'macc','公司账户'],
								['static:3', 'money','付款金额'],
								['static:3', "big_money", '大写金额'],			
								['static:3', 'pname','付款类型'],	
								['static:3', 'pwname','付款方式','',Config::get('pay_way')],	
								['static:3', 'money','付款金额'],			
								['static:3', 'usnickname', '经办人'],			
								['static:3', 'objname','项目'],			
								['static:3', 'remark','付款说明'],
								['static','file','附件'],	
            ])
           	->hideBtn('submit')     
            ->setFormData($data_list[0])       
            ->fetch();
            
          }

    public function delete($record = [])
    {
        return $this->setStatus('delete');
    }

}