<?php
namespace app\mobile\controller;

use app\user\model\User;
use think\Cache;
/*
 
 * 用户中心控制器*/
class My extends Base{
	
	public function index() {
		
		
//		halt(session('user_auth'));
		
		 //取得所有数据
        $Lists=user::getList(['admin_user.id'=>session('user_auth.uid')])->toArray();

		

        $List=$Lists['data'][0];
        
        
        foreach($List as $key=>&$value){
			if($key=='zid' && $value==null)
				$value=config('tytytytyty');
		}	
		
        $this->assign('a',$List);
		return $this -> fetch();
	}
	
	/*
	 * 个人信息编辑*/
	public function edit($id = null) {
		 $Lists=user::getList(['admin_user.id'=>session('user_auth.uid')])->toArray();
        $List=$Lists['data'][0];
//        halt($List);
        $this->assign('a',$List);
		return $this -> fetch();
	}
	/*
	 
	 * 我的考勤*/
	public function  attendance(){
		return $this -> fetch();
	}
	
	public function my_attendance(){
		return $this -> fetch();
	}
	
	/*
	 * 修改密码*/
	public function  change_password(){
		return $this -> fetch();
	}
	
	/*
	 * 清空缓存
	 */
	public function wipeCache(){
		
		 if (!empty(config('wipe_cache_type'))) {
            foreach (config('wipe_cache_type') as $item) {
                if ($item == 'LOG_PATH') {
                    $dirs = (array) glob(constant($item) . '*');
                    foreach ($dirs as $dir) {
                        array_map('unlink', glob($dir . '/*.log'));
                    }
                    array_map('rmdir', $dirs);
                } else {
                    array_map('unlink', glob(constant($item) . '/*.*'));
                }
            }
            Cache::clear();
            $this->success('清空成功');
        } else {
            $this->error('请在系统设置中选择需要清除的缓存类型');
        }
	}

}
?>