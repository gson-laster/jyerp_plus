<?php

namespace plugins\chart;

use app\common\controller\Plugin;
use app\admin\controller\Admin;
use plugins\chart\model\Chart as ChartModel;
class chart extends Plugin {
    public $info = [
        // 插件名[必填]
        'name'        => 'chart',
        // 插件标题[必填]
        'title'       => '柱形图表',
        // 插件唯一标识[必填],格式：插件名.开发者标识.plugin
        'identifier'  => 'chart.ming.chart.w.plugin',
        // 插件作者[必填]
        'author'      => '金耀科技',
        // 插件版本[必填],格式采用三段式：主版本号.次版本号.修订版本号
        'version'     => '1.0.0',
        'admin'		  => 1
    ];
    public function adminIndex()
    {


        $data_list = ChartModel::getList();
        $config = $this->getConfigValue();
        if ($config['display']) {
            $this->assign('data',$data_list);
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
