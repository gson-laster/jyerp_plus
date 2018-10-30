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

// 为方便系统核心升级，二次开发中需要用到的公共函数请写在这个文件，不要去修改common.php文件

use think\Db;
use think\View;
use app\user\model\User;
use app\document\model\Document_list as Document_listModel;
use app\task\model\Task_detail as Task_detailModel;

if (!function_exists('message_log')) {
    /**
     * 记录消息日志，并执行该行为的规则
     * @param null $action 行为标识
     * @param null $send_user_id 发送的用户id
     * @param null $receive_user_id 接收的用户id  可多个  例如  '1,2,3,4,5'  一个用int 多个string
     * @param string $url 点击通知跳转的url  可为空
     * @param string $details 详情
     * 王永吉 <739712704@qq.com>
     * message_log('行为','发送人','接收人','跳转url','行为变量[detail]')  -----
     * @return bool|string
     */
    function message_log($action = null, $send_user_id = null, $receive_user_id=null, $url=null, $details = '')
    {
		
        // 判断是否开启系统日志功能
        if (config('message_log')) {

            // 参数检查
            if(empty($action)){
                return '参数不能为空';
            }
            if(empty($url)){
                $url = "0";
            }
            if(empty($receive_user_id)){
                return '接收人不仅能空';
            }

            if(empty($send_user_id)){
                $send_user_id = is_signin();
            }

            if (strpos($action, '.')) {
                list($module, $action) = explode('.', $action);
            } else {
                $module = request()->module();
            }

		
            // 查询行为,判断是否执行
            $action_info = model('admin/messageaction')->where('module', $module)->getByName($action);
            if($action_info['status'] != 1){
                return '该行为被禁用或删除';
            }
	
            if(is_numeric($receive_user_id)){
                // 插入行为日志
                $data = [
                    'action_id'   => $action_info['id'],
                    'send_user_id'     => $send_user_id,
                    'receive_user_id' => $receive_user_id,
                    'url' => $url,
                    'create_time' => request()->time()
                ];
                // 解析日志规则,生成日志备注
                if(!empty($action_info['log'])){
                    if(preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)){
                        $log = [
                            'send_user'    => $send_user_id,
                            'receive_user'    => $receive_user_id,
                            'details' => $details
                        ];

                        $replace = [];
                        foreach ($match[1] as $value){
                            $param = explode('|', $value);
                            if(isset($param[1])){
                                $replace[] = call_user_func($param[1], $log[$param[0]]);
                            }else{
                                $replace[] = $log[$param[0]];
                            }
                        }

                        $data['remark'] = str_replace($match[0], $replace, $action_info['log']);
                    }else{
                        $data['remark'] = $action_info['log'];
                    }
                }else{
                    // 未定义日志规则，记录操作url
                    $data['remark'] = $action_info['title'];
                }

                // 保存日志
                model('admin/messagelog')->insert($data);
            }else{
				$receive_user_id = trim($receive_user_id,',');
                foreach (explode(',', $receive_user_id) as $key => $value) {
                    $tmp_data = [
                        'action_id'   => $action_info['id'],
                        'send_user_id'     => $send_user_id,
                        'receive_user_id' => $value,
                        'url' => $url,
                        'create_time' => request()->time()
                    ];
                     // 解析日志规则,生成日志备注
                    if(!empty($action_info['log'])){
                        if(preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)){
                            $log = [
                                'send_user'    => $send_user_id,
                                'receive_user'    => $value,
                                'details' => $details
                            ];

                            $replace = [];
                            foreach ($match[1] as $value){
                                $param = explode('|', $value);
                                if(isset($param[1])){
                                    $replace[] = call_user_func($param[1], $log[$param[0]]);
                                }else{
                                    $replace[] = $log[$param[0]];
                                }
                            }
                            $tmp_data['remark'] = str_replace($match[0], $replace, $action_info['log']);
                        }else{
                            $tmp_data['remark'] = $action_info['log'];
                        }
                    }else{
                        // 未定义日志规则，记录操作url
                        $tmp_data['remark'] = $action_info['title'];
                    }
                    $data[] = $tmp_data;
                }
                model('admin/messagelog')->insertAll($data);
            }
        }

        return true;
    }
}

if (!function_exists('outhtml')) {
	/*
	 * 文档外链html
	 * @author HJP
	 */
	function outhtml(){
		$html = <<<EOF
			//弹框html
			//选择目录
            <div class="add" style="display: none;height: 100%;overflow: auto;">
		    	<div class="block-content" style="height: 100%;overflow: auto;">
		            <div class="col-sm-6" style="height: 100%;overflow: auto;">
			        	<div class="block">
				        	<div class="block-header bg-gray-lighter">
				        		<h2 class="block-title">父级目录</h2>
				        	</div>
				          <div class="block-content">				          	
					          	<li class="list-group-item node-treeview1 blockli" data-id="0" style="color:undefined;background-color:undefined;cursor:pointer;">顶级目录</li>				          	
				          <div id="treeview1" class="">				          	
				          </div>
				          </div>
			          	</div>
		        	</div>  
		        	<div class="col-sm-6" style="height: 100%;overflow: auto;">
			        	<div class="block">
				        	<div class="block-header bg-gray-lighter">
				        		<h2 class="block-title">已选目录</h2>
				        	</div>
				          <div class="block-content">
				          	<p data-id=""></p>
				          </div>
			          	</div>
		        	</div>  
		        </div>
    		</div>
EOF;
return $html;
	}
}
if (!function_exists('outhtml2')) {
	/*
	 * 部门外链html
	 * @author HJP
	 */
	function outhtml2(){
		$html = <<<EOF
            <div class="add" style="display: none;height: 100%;overflow: auto;">
		    	<div class="block-content" style="height: 100%;overflow: auto;">
		            <div class="col-sm-6" style="height: 100%;overflow: auto;">
			        	<div class="block">
				        	<div class="block-header bg-gray-lighter">
				        		<h2 class="block-title">部门</h2>
				        	</div>
				          <div class="block-content">				          						          					          	
				          <div id="treeview1" class="">				          	
				          </div>
				          </div>
			          	</div>
		        	</div>  
		        	<div class="col-sm-6" style="height: 100%;overflow: auto;">
			        	<div class="block">
				        	<div class="block-header bg-gray-lighter">
				        		<h2 class="block-title">已选人员</h2>
				        	</div>
				          <div class="block-content">
				          	<p class="p_name" data-id=""></p>
				          </div>
			          	</div>
		        	</div>  
		        </div>
    		</div>
    		<div class="add2" style="display: none;height: 100%;overflow: auto;">
		    	<div class="block-content" style="height: 100%;overflow: auto;">
		            <div class="col-sm-6" style="height: 100%;overflow: auto;">
			        	<div class="block">
				        	<div class="block-header bg-gray-lighter">
				        		<h2 class="block-title">部门</h2>
				        	</div>
				          <div class="block-content">				          						          					          	
				          <div id="treeview2" class="">				          	
				          </div>
				          </div>
			          	</div>
		        	</div>  
		        	<div class="col-sm-6" style="height: 100%;overflow: auto;">
			        	<div class="block">
				        	<div class="block-header bg-gray-lighter">
				        		<h2 class="block-title">已选人员</h2>
				        	</div>
				          <div class="block-content helpname"></div>
			          	</div>
		        	</div>  
		        </div>
    		</div>
EOF;
return $html;
	}
}
if (!function_exists('getOrgan')) {
	/*
	 * 部门结构
	 * @author HJP
	 */
	function getOrgan(){
		$tree = Task_detailModel::getTree(0);
		$resulet = json_encode($tree);
		return 	$resulet;
	}
}
if (!function_exists('outjs')) {
	/*
	 * 文档外链js
	 * @author HJP
	 */
	function outjs($url = '',$resulet=''){					
		$js = <<<EOF
			<script src="__LIBS__/bootstrap-treeview/bootstrap-treeview.js"></script>
            <script type="text/javascript">  
            	//上传文件id 
            	var dp_file_upload_success = function () {
  					$('#fileid').val($('#file').val());
				};	
				//光标触发弹框         	
                $('#pidname').focus(function(){
                	//点击顶级
                	$('.blockli').click(function(){
		        		$('.block-content p').text($(this).text());
		        		$('.block-content p').attr('data-id',$(this).attr('data-id'));
		        	});	
                	layer.open({		
				        type:1,
				        title:'选择目录',
				        fixed: false, //不固定
						maxmin: true,
						scrollbar: false,
				        area:['700px', '450px'],
				        content:$('.add'),
				        btn:['确定','取消'],
				        yes:function (index,layero) {				        	
				        	$('#pidname').val($('.block-content p').text());
				        	$('#pid').val($('.block-content p').attr('data-id'));
				        	layer.close(index);	        					        					        			    
					   	 }				        
				        })
                })
                //无限分级
                $(function() {			
			        var resulet = {$resulet}; 
			        //目录      
			        $('#treeview1').treeview({
			          	levels: 1,
			        	data: resulet,
			        	onNodeSelected: function(event, node) {
			            var nodeid = node.id;
			            $.ajax({
			            	type:"post",
			            	url:"$url",
			            	data:{id:nodeid},
			            	success:function(data){
			            		if(data.type == 0){
			            			$('.block-content p').text(data.name);
			            			$('.block-content p').attr('data-id',data.id);
			            		}else{
			            			$('.block-content p').text('');
			            			layer.msg('文件不能作为目录!', function(){});
			            		}
			            	}
			            });
			         },
			        });  
	  			});
            </script>
EOF;
return $js;
	}
}
if (!function_exists('outjs2')) {
	/*
	 * 选择部门人员外链js
	 * @author HJP
	 */
	function outjs2(){
		$resulet = getOrgan();			
		$js = <<<EOF
			<script src="/static/libs/bootstrap-treeview/bootstrap-treeview.js"></script>
            <script type="text/javascript">              	
				//光标触发弹框
				//选择责任人         	
                $('#zrname').focus(function(){
                	layer.open({		
				        type:1,
				        title:'选择人员',
				        fixed: false, //不固定
						maxmin: true,
						scrollbar: false,
				        area:['700px', '450px'],
				        content:$('.add'),
				        btn:['确定','取消'],
				        yes:function (index,layero) {				        	
				        	$('#zrname').val($('.block-content .p_name').text());
				        	$('#zrid').val($('.block-content .p_name').attr('data-id'));
				        	layer.close(index);	        					        					        			    
					   	 }				        
				        })
                });
                //批量选择人员
                $('#helpname').focus(function(){
                	layer.open({		
				        type:1,
				        title:'选择人员',
				        fixed: false, //不固定
						maxmin: true,
						scrollbar: false,
				        area:['700px', '450px'],
				        content:$('.add2'),
				        btn:['确定','取消'],
				        yes:function (index,layero) {
				        	var helpdata = '';
				        	var helpids = '';
				        	var helptext = $('.helpname p');
				        	helptext.each(function(){
				        		helpdata = helpdata + $(this).text() + ',';
				        		helpids = helpids + $(this).attr('data-id') + ',';			        		
				        	})
				        	helpdata = helpdata.slice(0,-1);				        					        	
				        	$('#helpname').val(helpdata);
							if(helpids){
								$('#helpid').val(','+helpids);
							}				        	
				        	layer.close(index);	        					        					        			    
					   	 }				        
				        })
                });               
                //无限分级
                $(function() {
                	//树形数据	               	
			        var resulet = {$resulet}; 
			        //责任人     
			        $('#treeview1').treeview({
			          	levels: 99,
			        	data: resulet,
			        	onNodeSelected: function(event, node) {			           			            		
		            		if(node.nodes == ''){
		            			$('.block-content .p_name').text(node.text);
		            			$('.block-content .p_name').attr('data-id',node.uid);
		            		}else{
		            			$('.block-content .p_name').text('');
		            			$('.block-content .p_name').attr('data-id','');
		            		}			            
			         },
			        });
			        //部门人员  
			        $('#treeview2').treeview({
			          	levels: 99,
			          	showIcon: false,
          				showCheckbox: true,
			        	data: resulet,
		        		onNodeChecked: function(event, node) {//选中
		        			var helpname = $('.helpname');          		
		            		if(node.nodes == ''){//没有子级		            			
		            			helpname.prepend('<p class="helpremove'+node.nodeId+'" data-id="'+node.uid+'">' + node.text + '</p>');		            			      					            				            			
		            		}else{//有子级，选中所有子级
		            			function checkAllSon(node){  
								    $('#treeview2').treeview('checkNode',node.nodeId,{silent:true});  
								    if(node.nodes!=null&&node.nodes.length>0){  
								        for(var i in node.nodes){ 
								            checkAllSon(node.nodes[i]); 
console.log(1)											
								        }  
								    }  
								} 
								checkAllSon(node);    										            				            					            			 
		            			}			            
			         	},
			         	onNodeUnchecked: function(event, node) {//取消选中			         		
			         		var addw = '.helpremove'+node.nodeId;			                					                           				            			            			       			
							$(addw).remove();
							function uncheckAllSon(node){  
								    $('#treeview2').treeview('uncheckNode',node.nodeId,{silent:true});  
								    if(node.nodes!=null&&node.nodes.length>0){  
								        for(var i in node.nodes){  
								            uncheckAllSon(node.nodes[i]);  
								        }  
								    }  
								}  
							uncheckAllSon(node);	            					            				            
			         	},
			        });			        			        
	  		});	  				
            </script>
            
EOF;
return $js;
	}
}



/** 
 * URL跳转 
 * @param string $url 跳转地址 
 */  
if (!function_exists('go_url')) {

    function go_url($url) {  
        $url = str_replace(array("\n", "\r"), '', $url); // 多行URL地址支持  
        if (headers_sent()) {  
            $str = "<meta http-equiv='Refresh' content='0,URL={$url}'>";  
            exit($str);  
        } else {  
                header("Location: " . $url);  
            exit();  
        }  
    }  
}

//判断某个模块是否安装
if (!function_exists('module_exist')) {
    function module_exist($module) {  
        return db::name('admin_module')->where('name',$module)->value('status');
    }  
}

/*
*添加审批
*@ $标题 $title
*@ $action 行为名称
*@ $table 表名
*@ $url  详情页url
*@ $id 触发行为的id
*/
if(!function_exists('flow_detail')){
	function flow_detail($title=null,$action=null,$table=null,$url = null,$id=null){
            if(empty($title) || empty($table) || empty($action) || empty($id) || empty($url)){
                return '参数不能为空';
            }
            $module = request()->module();

            $action_info = model('admin/itemflow')->where('module', $module)->getByName($action);
            if($action_info['status'] != 1){
                return '该流程被禁用或删除';
            }
            $data = [
            	'title' => $title,
            	'table' => $table,
            	'action_id' => $action_info['id'],
            	'url' => $url,
            	'trigger_id' => $id,
            	'step' => 20,
            	'create_time' => time(),
            	'update_time' => time(),
            	'uid' => UID,
            ];
        	$confirm = str_replace("-",",",ltrim($action_info['flow'],'form-'));

        // 员工id拼接
       

            if($confirm==null)  return "找不到审批人";
            $confirm_data = model('user/user')->where('position','in',$confirm)->column('id','position');
            $confirm_str = '';
            foreach (explode(",",$confirm) as $key => $value) {

                if($value==0){
                    $position=model('user/user')->where('id',UID)->value('position');
                    $pid=model('user/position')->where('id',$position)->value('pid');
                    if($pid==0){
                        $confirm_str.='-'.UID;
                    }else{
                        $confirm_str.='-'.model('user/user')->where('position',$pid)->value('id');
                    }
                }else{
                    foreach ($confirm_data as $k => $v) {
                        if($value==$k){
                            $confirm_str.='-'.$v;
                        }
                    }
                }
            }

            $data['confirm'] = trim($confirm_str,'-');
            model('flow/itemdetail')->save($data);
            $flow = model('flow/itemdetail')->where(['action_id'=>$action_info['id'],'trigger_id' => $id])->find();
            if(!empty($flow) && ($flowid = $flow['id'])){
            	next_step($flowid,20);
            	
            }
            return true;
	}
}

/*
*添加审批步骤
*@ action 行为名称
*@ 触发行为的id
*/
// if(!function_exists('next_step')){
// 	function next_step($action=null,$id=null){

// 	}
// }
function next_step($wid, $step) {

            if (substr($step, 0, 1) == 2) {
                    if(is_last_confirm($wid)){

                        model('flow/itemdetail')->where('id',$wid)->update(['step'=>40]);
                        $thisflow = model('flow/itemdetail')->where('id',$wid)->field('trigger_id,table')->find();
                        db::name($thisflow['table'])->where('id',$thisflow['trigger_id'])->update(['status'=>1]);
						
                        $add_stock_table = ['stock_purchase','stock_produce','stock_otherin','stock_restore'];//加库存
                        $del_stock_table = ['stock_sell','stock_sell','stock_borrow','produce_mateget'];//减库存
                        $update_stock_table = ['stock_allot'];//调拨
						$account_status_table = ['tender_obj', 'tender_budget'];
						$obj_status = ['tender_obj','purchase_hetong','constructionsite_finish'];
						
						if(in_array($thisflow['table'], $account_status_table)){//项目结款完成
							$action_id = model('flow/itemdetail')->where('id',$wid)->value('action_id');
							$action_name = db::name('admin_itemflow')->where('id',$action_id)->value('name');
							if($action_name=='tender_confirm'){
								//$thisflow['table']
								db::name('tender_obj')->where('id',$thisflow['trigger_id'])->update(['account_status'=>1]);
							}
							if($action_name == 'tender_budget') {
								
								Db::name('tender_obj') -> where(['id' => db::name('tender_budget') -> where(['id' => $thisflow['trigger_id']]) ->  value('obj_id')]) -> update(['pre_status'=> 1]);
							}
							if($action_name == 'tender_prebudget'){
								Db::name('sales_opport')->where('id', db::name('tender_prebudget') -> where(['id' => $thisflow['trigger_id']]) -> value('item'))->update(['status_pre' => 1]);
							}
							
						}
						if(in_array($thisflow['table'], $obj_status)){//项目状态
							$action_id = model('flow/itemdetail')->where('id',$wid)->value('action_id');
							$action_name = db::name('admin_itemflow')->where('id',$action_id)->value('name');
							if($action_name=='sales_tender'){
								db::name('tender_obj')->where('id',$thisflow['trigger_id'])->update(['real_status'=>1]);
							}
							if($action_name == 'purchase_hetong') {
								$source_id = Db::name('purchase_hetong')->where('id',$thisflow['trigger_id'])->value('source_id');
								$prate = Db::name('purchase_plan')->where('id',$source_id)->value('prate');
								$objid = Db::name('tender_materials')->where('id',$prate)->value('obj_id');
								//dump($objid);die;
								Db::name('tender_obj')->where('id',$objid)->update(['real_status'=>2]);
							}
				

							if($action_name == 'constructionsite_finish'){
								$item = Db::name('constructionsite_finish')->where('id',$thisflow['trigger_id'])->value('item');
								Db::name('tender_obj')->where('id',$item)->update(['real_status'=>3]);
							}
							
						}
                        if(in_array($thisflow['table'], $add_stock_table)){//加库存
                            add_stock($thisflow['table'],$thisflow['trigger_id']);
                        }elseif(in_array($thisflow['table'], $del_stock_table)){//减库存
                            del_stock($thisflow['table'],$thisflow['trigger_id']);
                        }elseif(in_array($thisflow['table'], $update_stock_table)){//调拨
                            get_allot($thisflow['trigger_id']);
                        }
                        
                        return true;
                    }else{
                         $step++;
                    }
            }
            $data['itemdetail_id'] = $wid;
            $data['step'] = $step;
            $data['user_id'] = duty_id($wid, $step);
            $data['create_time'] = time();

            if (strpos($data['user_id'], ",") !== false) {

                $emp_list = explode(",", $data['user_id']);
                foreach ($emp_list as $emp) {

                    $data['user_id'] = $emp;
                     if(model('flow/itemdetailstep')->insert($data)){
                        return true;
                     }else{
                        return false;
                     }
                }

            } else {
                 if(model('flow/itemdetailstep')->insert($data)){
                        return true;
                     }else{
                        return false;
                 }
            }
        
    }

    //判断是否为最后一位审批人
    function is_last_confirm($flow_id) {
        $confirm = model('flow/itemdetail')->where(array('id' => $flow_id)) -> value("confirm");

        if (empty($confirm)) {
            return true;
        }
        $count = count(explode("-", $confirm));
        $log_count = model('flow/itemdetailstep')->where(['itemdetail_id'=>$flow_id,'result'=>1])->count();
        $last_confirm = array_filter(explode("-", $confirm));
        $last_confirm_user_id = end($last_confirm);

        return (($last_confirm_user_id == UID) && ($count==$log_count));
    }

    //获取下一位审批人的user_id
    function duty_id($wid, $step) {
        if (substr($step, 0, 1) == 2) {
            $confirm = model('flow/itemdetail')->where(array('id' => $wid))->value("confirm");

            $arr_confirm = array_filter(explode("-", $confirm));

            return $arr_confirm[fmod($step, 10) - 1];
        }
    }
	//基础物资入库加减$meter-入库表名,$id-表名id
	
	if(!function_exists('add_stock')){
	function add_stock($meter,$id) {		
		$data = db::name($meter.'_detail')->where('pid',$id)->column('itemsid,rksl,dj,type,ck');
		$date = time();
		foreach($data as $k=>$v){			
			if(empty(db::name('stock_stock')->where(['materialid'=>$v['itemsid'],'ckid'=>$v['ck']])->find()))
			{
				$list['ckid'] = $v['ck'];
				$list['number'] = $v['rksl'];
				$list['materialid'] = $v['itemsid'];
				$list['price'] = $v['dj'];
				$list['material_type'] = $v['type'];
				$list['update_time'] = $date;
				db::name('stock_stock')->insert($list);
			}else{
				$price = $v['dj'];
				$num = db::name('stock_stock')->where(['materialid'=>$v['itemsid'],'ckid'=>$v['ck']])->value('number');
				$new_num = $num+$v['rksl'];
				db::name('stock_stock')->where(['materialid'=>$v['itemsid'],'ckid'=>$v['ck']])->update(['number'=>$new_num,'update_time'=>$date,'price'=>$price]);
			}					
		}
	}
}
if(!function_exists('del_stock')){
	function del_stock($meter,$id) {
		if($meter == "produce_mateget"){
			$data = db::name($meter.'_list')->where('pid',$id)->column('mid,lysl,ckid');
			$date = time();		
			foreach($data as $k=>$v){								
				$num = db::name('stock_stock')->where(['materialid'=>$v['mid'],'ckid'=>$v['ckid']])->value('number');
				$new_num = $num-$v['lysl'];
				db::name('stock_stock')->where(['materialid'=>$v['mid'],'ckid'=>$v['ckid']])->update(['number'=>$new_num,'update_time'=>$date]);
			}
		}else{
			$data = db::name($meter.'_detail')->where('pid',$id)->column('itemsid,cksl,ck');
			$date = time();		
			foreach($data as $k=>$v){								
				$num = db::name('stock_stock')->where(['materialid'=>$v['itemsid'],'ckid'=>$v['ck']])->value('number');
				$new_num = $num-$v['cksl'];
				db::name('stock_stock')->where(['materialid'=>$v['itemsid'],'ckid'=>$v['ck']])->update(['number'=>$new_num,'update_time'=>$date]);
			}
		}
	}
}
//基础物资库存调拨加减$meter-入库表名
if(!function_exists('get_allot')){
	function get_allot($id){		
		$data = db::name('stock_allot_detail')->where('pid',$id)->column('itemsid,tbsl');			
		foreach($data as $k=>$v){								
			$num = db::name('stock_stock')->where(['materialid'=>$v['itemsid'],'ckid'=>$v['ck']])->value('number');
			$new_num = $num-$v['tbsl'];
			db::name('stock_stock')->where(['materialid'=>$v['itemsid'],'ckid'=>$v['ck']])->update(['number'=>$new_num]);
			$num1 = db::name('stock_stock')->where(['materialid'=>$v['itemsid'],'ckid'=>$v['ck']])->value('number');
			$new_num1 = $num1+$v['tbsl'];
			db::name('stock_stock')->where(['materialid'=>$v['itemsid'],'ckid'=>$v['ck']])->update(['number'=>$new_num1]);
		}
	}
}
//可查看人员
if(!function_exists('get_helpname')){
	function get_helpname($str = ''){
		$str = trim($str, ',');
		$data = explode(',', $str);
		$helpname = db::name('admin_user')->where('status',1)->column('id,nickname');
		$result = '';
		if($str == 0){
			$result = '暂无';
			return $result;
		}
		foreach($data as $k => $v){			
			$result .= $helpname[$v].",";			
		}
		$result = rtrim($result, ',');
		return $result;
	}
}
if(!function_exists('isMobile')){
	function isMobile()
	    {
	        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
	        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
	        {
	            return true;
	        }
	        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
	        if (isset ($_SERVER['HTTP_VIA']))
	        {
	            // 找不到为flase,否则为true
	            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
	        }
	        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
	        if (isset ($_SERVER['HTTP_USER_AGENT']))
	        {
	            $clientkeywords = array ('nokia',
	                'sony',
	                'ericsson',
	                'mot',
	                'samsung',
	                'htc',
	                'sgh',
	                'lg',
	                'sharp',
	                'sie-',
	                'philips',
	                'panasonic',
	                'alcatel',
	                'lenovo',
	                'iphone',
	                'ipod',
	                'blackberry',
	                'meizu',
	                'android',
	                'netfront',
	                'symbian',
	                'ucweb',
	                'windowsce',
	                'palm',
	                'operamini',
	                'operamobi',
	                'openwave',
	                'nexusone',
	                'cldc',
	                'midp',
	                'wap',
	                'mobile'
	            );

	            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
	            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
	            {
	                return true;
	            }
	        }
	        // 协议法，因为有可能不准确，放到最后判断
	        if (isset ($_SERVER['HTTP_ACCEPT']))
	        {
	            // 如果只支持wml并且不支持html那一定是移动设备
	            // 如果支持wml和html但是wml在html之前则是移动设备
	            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
	            {
	                return true;
	            }
	        }

	        return false;
	    }
}
