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

namespace app\admin\controller;

use app\common\builder\ZBuilder;
use app\admin\model\Messagelog as MessagelogModel;
use think\Db;

/**
 * 系统日志控制器
 * @package app\admin\controller
 */
class Messagelog extends Admin
{

    /**
     * 日志列表
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function index()
    {
        


        // 查询
        $map = $this->getMap();
        if (empty($map['admin_message_log.status']) || !isset($map['admin_message_log.status'])) {
            $map['admin_message_log.status'] = ['=',0];
        }
        if ($map['admin_message_log.status'] == 2) {
            unset($map['admin_message_log.status']);
        }
        // 排序
        $order = $this->getOrder('admin_message_log.id desc');
        // 数据列表
        $data_list = MessagelogModel::getAll($map, $order);
        // 分页数据
        $page = $data_list->render();

        $thisu = url('setmessagestatus');
    $js = <<<EOF
            <script type="text/javascript">
               function url_go(id,obj){
                        var url = $(obj).attr('u');
                        $.post('{$thisu}', {id: id}, function(data) {
                            if(url==0){
                                layer.msg('此消息不用处理', {icon: 1});
                                 $(obj).parent('td').next('td').html('<span class="label label-success">已读</span>');
                                return false;

                             }else{
                                window.location.href = url;
                            }
                        });
                    
                    
               }
            </script>
EOF;
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setPageTitle('我的消息') // 设置页面标题
            ->setSearch(['admin_message_action.title' => '消息类型', 'admin_user.nickname' => '发送人'],'','',true) // 设置搜索框
            ->addColumns([ // 批量添加数据列
                ['title', '消息类型'],
                ['nickname', '发送人'],
                ['create_time', '发送时间', 'datetime', '', 'Y-m-d H:i:s'],
                ['remark', '内容 (点击处理)','callback',function($value, $data){return '<a href="javascript:;" onclick="url_go('.$data['id'].',this);" u="'.$data['url'].'">'.mb_substr($value,0,40,'utf-8').'...'.'</a>';}, '__data__'],
                ['status', '状态', 'status','',['未读','已读']]
            ])
            ->addOrder(['create_time' => 'admin_message_log'])
            ->addFilter(['admin_message_action.title'])
            ->addTopButtons('delete') // 批量添加顶部按钮
            ->addTopButton('enable', ['title'=>'标记已读','class'=>'btn btn-success ajax-post',])
            ->addTopButton('disable', ['title'=>'标记未读','class'=>'btn btn-info ajax-post',])
            ->addTopSelect('admin_message_log.status', '未读消息', [2=>'全部消息',0=>'未读消息',1=>'已读消息'], 0) 
            ->setRowList($data_list) // 设置表格数据
            ->setTableName('admin_message_log') // 指定数据表名
            ->setPages($page) // 设置分页数据
            ->setExtraJS($js)
            ->fetch(); // 渲染模板
           
    }

    //设置已读
    public function setmessagestatus($id=null){
        if($id==null){
            return false;
        }
        $status = MessagelogModel::where('id',$id)->value('status');
        if($status==1){
            return true;
        }else{
            MessagelogModel::update(['id'=>$id,'status'=>1]);
        }
        return true;
    }

   

}