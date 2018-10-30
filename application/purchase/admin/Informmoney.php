<?php
namespace app\purchase\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\admin\model\Access as AccessModel;
use app\purchase\model\Informmoney as InformmoneyModel;
use think\Db;
use app\supplier\model\Supplier as SupplierModel;
/**
 *  采购类型
 */
class Informmoney extends Admin
{
	//
	public function index()
	{
        $map = $this->getMap();
        // 排序
        $order = $this->getOrder('id desc');
         $btn_detail = [
            'title' => '查看详情',
            'icon'  => 'fa fa-fw fa-search',
            'href'  => url('detail', ['id' => '__id__'])
        ];

		$data_list = InformmoneyModel::getList($map,$order);
        return ZBuilder::make('table')
	        		->hideCheckbox()
                    ->addColumns([ // 批量添加列
				['date', '日期','date'],
        		['name','主题'],
        		['contract','采购合同'],
                ['supplier','供应商'],
                ['nickname','申请人'],
                ['money','金额'],
                ['status', '审批结果','status','',[0 =>'进行中:info', 2=>'否决:danger', 1=>'同意:success']],
                ['right_button','操作']
				    ])
				    ->addTopButton('add') // 添加顶部按钮
				    ->setRowList($data_list) // 设置表格数据
				    ->addRightButton('detail',$btn_detail,true) //添加删除按钮
	                ->fetch();
	        	
	}

	public function add(){

        if ($this->request->isPost()) {
            $data = $this->request->post();
            //验证
            $result = $this->validate($data, 'Informmoney');
            
            $data['maker']=UID;           
			$data['date'] = strtotime($data['date']);
            //$data['money'] = round($data['money'],2);
            //
          
            //验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            if ($res = InformmoneyModel::create($data)) {
                // 记录行为
               	flow_detail($data['name'],'purchase_informmoney','purchase_informmoney','purchase/informmoney/detail',$res['id']);
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
        
        
        $c = InformmoneyModel::getc();
       
        
        
        
        
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('采购请款')           
            ->addFormItems([
        	['date:3', 'date', '日期','', date('Y-m-d', time())],
        	['text:3','name','主题'],
        	['select:3','contract','采购合同','',$c],
            //['linkage:3','contract','采购合同','',$c,'',url('get_supplier'),'supplier'],
            //['select:3','supplier','供应商'],
            ['static:3','maker','申请人','',get_nickname(UID)],
            ['number:3','money','金额'],
        
            ['text:3','note','备注'],
            ['file','file','文件']  
            ])  
            ->js('chineseNumber')
            ->setExtraJs($js.outjs2())
            ->js('inform')
            ->fetch();

	}


    public function get_Detail($contract = ''){
         
            $data = InformmoneyModel::getAll($contract);
        return $data;
    }
	
	 public function get_supplier($contract = '')
    {
    	  
        $res= InformmoneyModel::getCa($contract);
        //dump($res);die;
        $array =array();
        foreach($res as $key=>$val){
            $array[] = ['key'=>$val['supplier_id'],'value'=>$val['name']];
        }
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        $arr['list'] =$array; //数据
        return json($arr);
    }
	
	
	

	
	
	
	public function detail($id = ''){
		
		if (null == $id) $this -> error('参数错误');
		$data_list = InformmoneyModel::getOne($id);
		$data_list['date'] = date('Y-m-d',$data_list['date']); 
		return ZBuilder::make('form')
		->addFormItems([
		// 批量添加表单项
			['static:3', 'date', '日期','','date', ],
        	['static:3','name','主题'],
        	['static:3','contract','采购合同'],
            ['static:3','supplier','供应商'],
            ['static:3','nickname','申请人'],
            ['static:3','money','金额'],
            ['static:3','big_money','金额大写'],
            ['static:3','note','备注'],
            ['archives','file','文件']  
		])
		-> setPageTitle('详情')
		->hideBtn('submit')
		-> setFormData($data_list)
		->fetch();
		
		
		
		
		}


}   
