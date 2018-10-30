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
use app\personnel\model\Sign as SignModel;
use app\personnel\model\Work as WorkModel;

/**
 * 签到控制器
 * @package app\cms\admin
 */
class Sign extends Admin
{
    /**
     * 文档列表
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function index()
    {
        $map = $this->getMap();
        $order = $this->getOrder();
        // 数据列表
        $data_list = UserModel::where($map)->order($order)->paginate();
      
        $btn_sign = [
        		'title' => '签到详情',
        		'icon'  => 'fa fa-fw fa-search',
        		'href'  => url('view', ['uid' => '__id__'])
        ];
        
        $btn_work = [
        		'title' => '打卡详情',
        		'icon'  => 'fa fa-fw fa-check-square-o',
        		'href'  => url('detail', ['uid' => '__id__'])
        ];
 
        if($this->todayData()){
        	$html = '<button title="签到" type="button" class="btn btn-success" id="sign">已签到</button>';
        }else{
        	$html = '<button title="签到" type="button" class="btn btn-primary" id="sign">签到</button>';        
        }
        
        $html .= '<button title="打卡" class="btn btn-primary" id="work" style="margin-left:5px;"><i class="fa fa-fw fa-check-square-o"></i>打卡</button>'; 
        
        $js = <<<EOF
            <script>
    $(document).ready(function(){        
        // 保存节点
        $('#sign').click(function(){                    
            $.post("/admin.php/personnel/sign/sign",{},function(data) {
        		//console.log(data);
                if (data.code) {  
        			$('#sign').removeClass('btn-primary').addClass('btn-success'); 
        			$('#sign').html('已签到');                  
                    Dolphin.notify(data.msg, 'success');
                } else {
                    Dolphin.notify(data.msg, 'danger');
                }
            });
        });
        		
        $('#work').click(function(){                    
            $.post("/admin.php/personnel/sign/work",{},function(data) {
        		console.log(data);
                if (data.code) {         
                    Dolphin.notify(data.msg, 'danger');
                }else{
                    Dolphin.notify(data.msg, 'success');
                }
            });
        });
        		 		
    });
</script>
EOF;
        
        
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setTableName('admin_user') // 设置数据表名
            ->setSearch(['id' => 'ID', 'username' => '用户名', 'nickname' => '姓名']) // 设置搜索参数  
            ->addOrder('id,role,organization,position,is_on')
            ->addFilter('role', RoleModel::getTree2())
            ->addFilter('organization', OrganizationModel::getTree(null, false))
            ->addFilter('position', PositionModel::getTree(null, false))
            ->addFilter('is_on', ['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职'])
            ->addColumns([ // 批量添加列
                ['id', 'ID'],
                ['username', '用户名'],
                ['nickname', '姓名'],
                ['role', '角色',  RoleModel::getTree2()],
            	['organization', '部门', OrganizationModel::getTree()],
            	['position', '职位', PositionModel::getTree()],
                ['sign_days', '签到天数'],
            	['is_on', '在职状态',['0'=>'定编','1'=>'在职','2'=>'缺职','3'=>'超编','4'=>'兼职']],  
            	['right_button', '操作', 'btn']
            ])
            ->addRightButton('sign', $btn_sign) 
            ->addRightButton('work', $btn_work)
            ->setExtraHtml($html, 'toolbar_top')
            ->setExtraJs($js)
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染页面
    }

    /**
     * 签到详情
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function view($uid = null,$year = '',$month = '')
    {    	
        if($uid == null) $uid = UID;
        $days = $this->getMonthSign($uid,$year,$month);
        //$sign_days = $this->showDays($days);
        $sign_days =  $this->SimCalendar($days,$year,$month);
        $this->assign('sign_days', $sign_days);
        return $this->fetch('view');
    }
    
    /**
     * 打卡详情
     * @author 黄远东 <641435071@qq.com>
     * @return mixed
     */
    public function detail($uid = null)
    {
    	if($uid == null) $uid = UID;    	    	
    	$map = $this->getMap();
    	$map['uid'] = $uid;
    	$order = $this->getOrder('personnel_work.create_time desc');
    	// 数据列表
    	$data_list = WorkModel::getList($map, $order);
    	
    	// 返回按钮
    	$btn_history = [
    			'title' => '返回',
    			'icon'  => 'fa fa-fw fa-mail-reply',
    			'class' => 'btn btn-warning',
    			'href'  => url('index')
    	];
    	return ZBuilder::make('table')
            ->setTableName('admin_user') // 设置数据表名
            ->addColumns([ // 批量添加列
                ['username', '用户名'],
                ['nickname', '姓名'],
            	['organization', '部门', OrganizationModel::getTree()],
            	['position', '职位', PositionModel::getTree()],
                ['create_time', '日期','date'],
            	['on_time', '上班时间','datetime'],
            	['off_time', '下班时间','datetime'],            	
            ])
            ->hideCheckbox() 
            ->addTopButton('history', $btn_history)
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染页面
    }
    
    /**
	  * 执行当天签到
	  */
	  public function sign(){
	  	if($this->todayData()){
	  		return ['code'=>1,'msg'=>'您已签到'];	  	
	  	}else{
	  		if(SignModel::create(['uid'=>UID])){
	  			$data = $this->getInsertData();
	  			if(UserModel::update($data)){
	  				return ['code'=>1,'msg'=>'签到成功'];
	  			}else{
	  				return ['code'=>0,'msg'=>'连续签到，签到天数更新失败'];
	  			}
	  		}else{
	  			return ['code'=>0,'msg'=>'签到失败'];
	  		}	  		
	  	}	    
	  }
	  
	  /**
	   * 执行当天打卡
	   */
	  public function work(){
	  	//判断是否是内网访问	
	  	$filter_var = module_config('personnel.filter_var');  	
	  	if($filter_var){
	  		$ip = get_client_ip(0);
	  		$result = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	  		if($result){
	  			return ['code'=>1,'msg'=>'请在公司局域网内打卡上班'];
	  		}
	  	}
	  	
	  	$work = $this->todayWork();
	  	if($work){
	  		if($work['off_time']){
	  			return ['code'=>2,'msg'=>'超过每日打卡上限'];	  			
	  		}else{
	  			$data = ['off_time'=>time()];
	  			if(WorkModel::where(['uid'=>UID])->update($data)){
	  				return ['code'=>0,'msg'=>'打卡下班'];
	  			}else{
	  				return ['code'=>1,'msg'=>'打卡下班失败'];
	  			}
	  		}	  			  				
	  	}else{	    	  
	  		$data = ['on_time'=>time(),'uid'=>UID];
	  		if(WorkModel::create($data)){
	  			return ['code'=>0,'msg'=>'打卡上班'];
	  		}else{
	  			return ['code'=>1,'msg'=>'打卡上班失败'];
	  		}	 
	  	}	  	 
	  }
	  
	  /**
	  * 返回每次签到要插入的数据
	  *
	  * @param int $uid 用户id
	  * @return array(
	  *  'days'   =>  '天数',
	  *  'is_sign'  =>  '是否签到,用1表示已经签到',
	  *  'stime'   =>  '签到时间',
	  * );
	  */
	  protected function getInsertData(){
	    // 昨天的连续签到天数
	    $start_time = strtotime(date('Y-m-d 0:0:0',time()-86400))-1;
	    $end_time  = strtotime(date('Y-m-d 23:59:59',time()-86400))+1;
	    $sign = SignModel::where("uid = ".UID." and create_time > $start_time and create_time < $end_time")->find();
	    $user = UserModel::where(['id'=>UID])->find();
	    if($sign){
	      $days = $user['days']+1;
	      if($days > 30){
	        $days = 1;
	      }
	    }else{
	      $days = 1;
	    }
	    
	    $sign_days = $user['sign_days']+1;
	    return array(
	      'id'=>UID,	
	      'days'    => $days,
	      'sign_days'    => $sign_days,
	    );
	  }
	  
	  /**
	   * 用户当天是否签到
	   * @return boolean
	   */
	  protected function todayData(){
	  	$time = time();
	  	$start_stime  = strtotime(date('Y-m-d 0:0:0',$time))-1;
	  	$end_stime = strtotime(date('Y-m-d 23:59:59',$time))+1;
	  	$sign = SignModel::where("uid = ".UID." and create_time > $start_stime and create_time < $end_stime")->find();
	  	if($sign){	  		
	  		return true;
	  	}else{
	  		return false;
	  	}	  	
	  }
	  
	  /**
	   * 用户当天打卡情况
	   * @return boolean
	   */
	  protected function todayWork(){
	  	$time = time();
	  	$start_stime  = strtotime(date('Y-m-d 0:0:0',$time))-1;
	  	$end_stime = strtotime(date('Y-m-d 23:59:59',$time))+1;
	  	$work = WorkModel::where("uid = ".UID." and create_time > $start_stime and create_time < $end_stime")->find();  		  	
	  	return $work;	  	 
	  }
	  
	  /**
	  * 显示签到列表
	  *
	  * @param array  $signDays 某月签到的日期 array(1,2,3,4,5,12,13)
	  * @param int $year    可选，年份
	  * @param int $month   可选，月份
	  * @return string 日期列表<li>1</li>....
	  */
	  protected function showDays($signDays,$year = '',$month = ''){
	    $time = time();
	    $year = $year ? $year : date('Y',$time);
	    $month = $month ? $month : date('m',$time);
	    $daysTotal = date('t', mktime(0, 0, 0, $month, 1, $year));
	    $now = date('Y-m-d',$time);
	    $str = '';
	    for ($j = 1; $j <= $daysTotal; $j++) {
	      
	      $someDay = date('Y-m-d',strtotime("$year-$month-$j"));
	      // 小于今天的日期样式
	      if ($someDay <= $now){
	        // 当天日期样式 tdc = todayColor
	        if($someDay == $now){
	          // 当天签到过的
	          if(in_array($j,$signDays)){
	            $str .= '<li class="current fw tdc">'.$j.'</li>';
	          }else{
	            $str .= '<li class="today fw tdc">'.$j.'</li>';
	          }
	        }else{
	          // 签到过的日期样式 current bfc = beforeColor , fw = font-weight
	          if(in_array($j,$signDays)){
	            $str .= '<li class="current fw bfc">'.$j.'</li>';
	          }else{
	            $str .= '<li class="fw bfc">'.$j.'</li>';
	          }
	        }
	      }else{
	        $str .= '<li>'.$j.'</li>';
	      }
	    }
	    return $str;
	  }
	  
	  /**
	   * 简单日历输出,本函数需要cal_days_in_month的支持
	   * @param $date Y-m 要输出的日期
	   */	 
	  function SimCalendar($days = [],$year = '',$month = '')
	  {	  		  
	  	$time = time();
	  	$year = $year ? intval($year) : intval(date('Y',$time));
	  	$month = $month ? intval($month) : intval(date('m',$time));	  	
	  	$start_week = 0;//从星期天开始为0
	  	$monthdays = cal_days_in_month(CAL_GREGORIAN, $month, $year);//当月的天数	  	
	  	$wstar = date('w', strtotime($year . '-' . $month . '-01'));//当月从星期几天始
	  	$rows = ceil(($wstar + $monthdays) / 7);//总行数
	  	$mday = 1;//第几天	  	
	  	$html = '';
	  	for ($i = 0; $i < $rows; $i++) {	  		
	  		for ($d = 0; $d < 7; $d++) {
	  			$nowday = 7 * $i + $d + $start_week;
	  			if ($nowday >= $wstar && $mday <= $monthdays) {	  				
	  				$temp = date('d', strtotime($year . '-' . $month . '-' . $mday));	  				
	  				if(in_array($temp,$days)){
	  					$html .= '<div class="cell on" style="left: 0px; top: 2px;">
	  	<div class="so" style="color: rgb(198, 11, 2);">'.$temp . '</div></div>';
	  					
	  				}else{
	  					$now = date('Y-m-d',$time);
	  					$someDay = date('Y-m-d',strtotime($year . '-' . $month . '-' . $mday));
	  					if($now == $someDay){
	  						$html .= '<div class="cell today" style="left: 0px; top: 2px;">
	  	<div class="so" style="color: rgb(198, 11, 2);">'.$temp . '</div></div>';
	  						
	  					}else{
	  						$html .= '<div class="cell" style="left: 0px; top: 2px;">
	  	<div class="so" style="color: rgb(198, 11, 2);">'.$temp . '</div></div>';
	  					}
	  					
	  				}
	  				
	  				$mday++;
	  			} else {
	  				$html .= '<div class="cell" style="left: 0px; top: 2px;">
	  	<div class="so" style="color: rgb(198, 11, 2);"></div></div>';
	  			}
	  		}
	  		
	  	}
	  	return $html;
	  }
	 
	  /**
	  * 获取当月签到的天数，与 $this->showDays() 配合使用
	  * $uid 用户id
	  * $year 年份
	  * $month 月份
	  * @return 当月签到日期 array(1,2,3,4,5,12,13)
	  */
	  protected function getMonthSign($uid = null, $year = '',$month = ''){
	    $time  = time();
	    $year = $year ? $year: date('Y',$time) ;
	    $month = $month ? $month : date('m',$time);
	    $day  = date("t",strtotime("$year-$month"));
	    $start_stime  = strtotime("$year-$month-1 0:0:0")-1;
	    $end_stime = strtotime("$year-$month-$day 23:59:59")+1;

	    $list = SignModel::where("uid = $uid and create_time > $start_stime and create_time < $end_stime")->column('create_time');
	    foreach ($list as $key => $value){
	      $list[$key] = date('j',$value);
	    }
	    return $list;
	  }

}