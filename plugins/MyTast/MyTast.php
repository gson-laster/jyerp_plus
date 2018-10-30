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

namespace plugins\MyTast;
use think\Db;
use app\common\controller\Plugin;


/**
 * 公司信息
 * @package plugins\DevTeam
 * @author 蔡伟明 <314013107@qq.com>
 */
class MyTast extends Plugin
{
    /**
     * @var array 插件信息
     */
    public $info = [
        // 插件名[必填]
        'name'        => 'MyTast',
        // 插件标题[必填]
        'title'       => '我的任务',
        // 插件唯一标识[必填],格式：插件名.开发者标识.plugin
        'identifier'  => 'my_tast.wang.plugin',
        // 插件图标[选填]
        'icon'        => 'fa fa-fw fa-folder-open',
        // 插件描述[选填]
        'description' => '首页------在后台首页显示我的任务',
        // 插件作者[必填]
        'author'      => '金耀科技',
        // 作者主页[选填]
        'author_url'  => '',
        // 插件版本[必填],格式采用三段式：主版本号.次版本号.修订版本号
        'version'     => '1.0.0',
        // 是否有后台管理功能[选填]
        'admin'       => '0',
    ];

    /**
     * @var array 插件钩子
     */
    public $hooks = [
        'admin_index'
    ];


    /**
     * 后台首页钩子
     * @author 蔡伟明 <314013107@qq.com>
     */
    public function adminIndex()
    {
        $config = $this->getConfigValue();

        if(module_exist('task')){
            $config['task'] = db::name('task_detail')->where(['zrid'=>UID])->whereOr("locate(',".UID.",',`helpid`)>0")->limit($config['task_num'])->column('name','id');
        }else{
            $config['display']=0;
        }
        if ($config['display']) {
            $this->fetch('index', $config);
        }
    }

    /**
     * 安装方法
     * @author 蔡伟明 <314013107@qq.com>
     * @return bool
     */
    public function install(){
         
         if(module_exist('task')){
            return true;
         }else{
            $this->error = '未安装或已禁用任务模块';
            return false;
         }
    }

    /**
     * 卸载方法必
     * @author 蔡伟明 <314013107@qq.com>
     * @return bool
     */
    public function uninstall(){
        return true;
    }
}