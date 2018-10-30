<?php

namespace plugins\SectorChart;

use app\common\controller\Plugin;
use plugins\SectorChart\model\SectorChart as SectorChartModel;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
class SectorChart extends Plugin {
    public $info = [
        // 插件名[必填]
        'name'        => 'SectorChart',
        // 插件标题[必填]
        'title'       => '饼形图表',
        // 插件唯一标识[必填],格式：插件名.开发者标识.plugin
        'identifier'  => 'SectorChart.ming.plugin',
        //简介
        'description' => '首页------在后台首页显示数据',
        
        // 插件作者[必填]
        'author'      => '金耀科技',
        // 插件版本[必填],格式采用三段式：主版本号.次版本号.修订版本号
        'version'     => '1.0.0',
        'admin'		  => 1
    ];






    public function adminIndex()
    {
        // 查询
        // 排序
        $data_list = SectorChartModel::getList();
        
        //dump($data_list);
        $config = $this->getConfigValue();
        
        //dump($data_list);die;
        if ($config['display']) {
            $this->assign('data',$data_list);
            $this->fetch('index', $config);
        }
    }
    public $hooks = [
        'admin_index'
    ];

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