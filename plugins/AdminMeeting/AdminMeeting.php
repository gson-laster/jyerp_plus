<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/14 0014
 * Time: 15:44
 */

namespace plugins\AdminMeeting;


use app\common\controller\Plugin;
use think\Db;
use app\admin\controller\Admin;

class AdminMeeting extends Plugin
{

    public $info = [
        // 插件名[必填]
        'name'        => 'AdminMeeting',
        // 插件标题[必填]
        'title'       => '首页展示会议信息',
        // 插件唯一标识[必填],格式：插件名.开发者标识.plugin
        'identifier'  => 'helloworld.ming.plugin',
        // 插件图标[选填]
        'icon'        => 'fa fa-fw fa-globe',
        // 插件描述[选填]
        'description' => '首页----后台首页展示会议信息',
        // 插件作者[必填]
        'author'      => '金耀',
        // 插件版本[必填],格式采用三段式：主版本号.次版本号.修订版本号
        'version'     => '1.0.0',
        // 是否有后台管理功能
        'admin'       => '0',
    ];


    /**
     * @var array 插件钩子
     */
    public $hooks = [
        'admin_index'
    ];


    public function adminIndex()
    {
        $config = $this->getConfigValue();
            if(module_exist('task')){
            $config['task'] = Db::name('meeting_list')->where('id','>',0)->limit($config['task_num'])->select();
        }else{
            $config['display']=0;
        }
        if ($config['display']) {
            $this->fetch('table', $config);
        }
    
    }




    public function install(){
        return true;
    }

    /**
     * 卸载方法必须实现
     */
    public function uninstall(){
        return true;
    }

}