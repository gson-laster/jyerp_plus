<?php

namespace plugins\advance;

use app\common\controller\Plugin;
use app\admin\controller\Admin;
use plugins\Advance\model\Advance as AModel;

class Advance extends Plugin {
    public $info = [
        // 插件名[必填]
        'name'        => 'Advance',
        // 插件标题[必填]
        'title'       => '首页显示项目进度',
        // 插件唯一标识[必填],格式：插件名.开发者标识.plugin
        'identifier'  => 'Advance.ming.Advance.w.plugin',
        // 插件作者[必填]
        'author'      => '金耀科技',
        // 插件版本[必填],格式采用三段式：主版本号.次版本号.修订版本号
        'version'     => '1.0.0',
        'admin'		  => 1
    ];
    public function adminIndex()
    {			
    	
        $data = AModel::getObj(); 
        $config = $this->getConfigValue();
        if ($config['display']) {
            $this->assign('data',$data);
            $this->fetch('index', $config);      
        }
  

       
    }
    public $hooks = [
        'admin_index',

    ];
    public function myHook(&$params)
    {
        $config = $this->getConfigValue();
        if ($config['display']) {
            $this->fetch('index', $config);
        }
    }
    /**
     * 安装方法必须实现
     */
    
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
