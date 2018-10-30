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

namespace app\admin\model;

use think\Model as ThinkModel;

/**
 * 字段模型
 * @package app\cms\model
 */
class FlowField extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__ADMIN_FLOW_FIELD__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    // 当前表名
    protected $_table_name = '';

    /**
     * 创建字段
     * @param null $field 字段数据
     * @author 蔡伟明 <314013107@qq.com>
     * @return bool
     */
    public function newField($field = null)
    {
        if ($field === null) {
            $this->error = '缺少参数';
            return false;
        }

        if ($this->tableExist($field['user_type'])) {
        	
            $sql = <<<EOF
            ALTER TABLE `{$this->_table_name}`
            ADD COLUMN `{$field['name']}` {$field['define']} COMMENT '{$field['title']}';
EOF;
        } else {
            $type_title = get_type_title($field['user_type']);

            // 新建扩展表
            $sql = <<<EOF
                CREATE TABLE IF NOT EXISTS `{$this->_table_name}` (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id' ,
        `mid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员id' ,
        `tid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '模型id' ,
        `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间' ,
        `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间' ,
        `add_status` tinyint(2) NOT NULL DEFAULT 0 COMMENT '状态 -10 资料未审核  -1 待审核 1 审核不通过  2审核通过' ,
        `code` varchar(128) NOT NULL DEFAULT '' COMMENT '备注' ,
        `admin` varchar(128) NOT NULL DEFAULT '' COMMENT '操作人' ,
        `{$field['name']}` {$field['define']} COMMENT '{$field['title']}' ,
                PRIMARY KEY (`id`)
                )
                ENGINE=MyISAM
                DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
                CHECKSUM=0
                ROW_FORMAT=DYNAMIC
                DELAY_KEY_WRITE=0
                COMMENT='{$type_title}模型扩展表'
                ;
EOF;
        }
        try {
            Db::execute($sql);
        } catch(\Exception $e) {
            $this->error = '字段添加失败';
            return false;
        }

        return true;
    }

    /**
     * 更新字段
     * @param null $field 字段数据
     * @author 蔡伟明 <314013107@qq.com>
     * @return bool
     */
    public function updateField($field = null)
    {
        if ($field === null) {
            return false;
        }

        // 获取原字段名
        $field_name = $this->where('id', $field['id'])->value('name');

        if ($this->tableExist($field['user_type'])) {
            $sql = <<<EOF
            ALTER TABLE `{$this->_table_name}`
            CHANGE COLUMN `{$field_name}` `{$field['name']}` {$field['define']} COMMENT '{$field['title']}';
EOF;
            try {
                Db::execute($sql);
            } catch(\Exception $e) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 删除字段
     * @param null $field 字段数据
     * @author 蔡伟明 <314013107@qq.com>
     * @return bool
     */
    public function deleteField($field = null)
    {
        if ($field === null) {
            return false;
        }

        if ($this->tableExist($field['user_type'])) {
            $sql = <<<EOF
            ALTER TABLE `{$this->_table_name}`
            DROP COLUMN `{$field['name']}`;
EOF;
            try {
                Db::execute($sql);
            } catch(\Exception $e) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 检查表是否存在
     * @param string $model 文档模型id
     * @author 蔡伟明 <314013107@qq.com>
     * @return bool
     */
    private function tableExist($user_type = '')
    {
        $this->_table_name = strtolower(get_user_table($user_type));
        return true == Db::query("SHOW TABLES LIKE '{$this->_table_name}'");
    }
}