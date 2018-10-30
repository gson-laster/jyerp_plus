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

namespace app\mobile\controller;

use app\common\controller\Common;
use app\admin\model\Mobilemenu as MobilemenuModel;

/**
 * 前台公共控制器
 * @package app\index\controller
 */
class Base extends Common
{
    public function _initialize(){
    	parent::_initialize();
        
		// 判断是否登录，并定义用户ID常量
        defined('UID') or define('UID', $this->isLogin());
        //判断是否是是pc访问
        if (!$this->isMobile()) {
            $this->redirect('/');
        }
         

        //判断是否ajax提交
        if (!$this->request->isAjax()) {
            // 读取顶部菜单
            $menulocation = MobilemenuModel::getLocation('', true);
            $this->assign('_top_menus', MobilemenuModel::getTopMenu(5, '_mobile_footer_menus'));//底部菜单
            $this->assign('_location', $menulocation);//当前节点位置
            $this->assign('_sidebar_menus', MobilemenuModel::getSidebarMenu());//当前节点的父级节点下所有节点
            $this->assign('_this_link',$menulocation[count($menulocation)-1]);
        }

//      dump(MobilemenuModel::getLocation('', true));
//      die;
    }
   /**
     * 检查是否登录，没有登录则跳转到登录页面
     * @author 蔡伟明 <314013107@qq.com>
     * @return int
     */
    final protected function isLogin()
    {

        // 判断是否登录
        if ($uid = is_signin()) {

            // 已登录
            return $uid;
        } else {
            // 未登录
           
            $this->redirect('user/publics/signin');
        }
    }
   
   private function isMobile()
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
