-- -----------------------------
-- 导出时间 `2018-06-06 18:18:06`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_flow_work`
-- -----------------------------
DROP TABLE IF EXISTS `dp_flow_work`;
CREATE TABLE `dp_flow_work` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL COMMENT '内容',
  `confirm` varchar(200) NOT NULL DEFAULT '' COMMENT '裁决数据',
  `fid` varchar(20) NOT NULL DEFAULT '' COMMENT '流程类型',
  `add_file` varchar(200) NOT NULL DEFAULT '' COMMENT '附件',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `user_name` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名称',
  `dept_id` int(11) NOT NULL DEFAULT '0' COMMENT '部门ID',
  `create_date` varchar(10) NOT NULL DEFAULT '' COMMENT '创建日期',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `step` int(11) NOT NULL DEFAULT '0' COMMENT '目前阶段状态',
  `is_del` tinyint(3) NOT NULL DEFAULT '0' COMMENT '删除标记',
  `udf_data` text COMMENT '用户自定义数据',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_flow_work`
-- -----------------------------
INSERT INTO `dp_flow_work` VALUES ('1', '肚子疼', '肚子疼肚子疼', '4', '1', '', '1', '超级管理员', '0', '', '1520996567', '1520996567', '20', '0', '');
INSERT INTO `dp_flow_work` VALUES ('2', '1111', '1', '4', '1', '', '1', '超级管理员', '0', '', '1520996666', '1520996666', '20', '0', '');
INSERT INTO `dp_flow_work` VALUES ('3', '11111111111', '1111111111111111', '4', '1', '', '1', '超级管理员', '0', '', '1520996820', '1520996820', '20', '0', '');
INSERT INTO `dp_flow_work` VALUES ('4', '11111111111111', '11111111111111111111', '1-1', '1', '', '1', '超级管理员', '0', '', '1520996911', '1520996926', '30', '0', '');
INSERT INTO `dp_flow_work` VALUES ('5', '222222', '22222222222222', '1-1', '1', '', '1', '超级管理员', '0', '', '1520997028', '1520997028', '20', '0', '');
INSERT INTO `dp_flow_work` VALUES ('6', '33333', '33333333333', '1-1', '1', '', '1', '超级管理员', '0', '', '1520997467', '1520997467', '20', '0', '');
INSERT INTO `dp_flow_work` VALUES ('7', '1111111', '456', '1-1', '1', '8', '1', '超级管理员', '0', '', '1521078924', '1521078924', '20', '0', '');
INSERT INTO `dp_flow_work` VALUES ('8', '哈哈', '哈哈哈', '1-1', '1', '9', '1', '超级管理员', '0', '', '1521707332', '1521707332', '20', '0', '');

-- -----------------------------
-- 表结构 `dp_flow_log`
-- -----------------------------
DROP TABLE IF EXISTS `dp_flow_log`;
CREATE TABLE `dp_flow_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wid` int(11) NOT NULL DEFAULT '0' COMMENT '流程ID',
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `user_name` varchar(20) DEFAULT '' COMMENT '用户名称',
  `step` int(11) NOT NULL DEFAULT '0' COMMENT '当前步骤',
  `result` int(11) unsigned DEFAULT '0' COMMENT '审批结果',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `comment` text COMMENT '意见',
  `is_read` tinyint(3) NOT NULL DEFAULT '0' COMMENT '已读',
  `from` varchar(20) DEFAULT NULL COMMENT '传阅人',
  `is_del` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_flow_log`
-- -----------------------------
INSERT INTO `dp_flow_log` VALUES ('1', '1', '4', '', '21', '0', '1520996567', '1520996567', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('2', '2', '4', '', '21', '0', '1520996667', '1520996667', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('3', '3', '4', '', '21', '0', '1520996820', '1520996820', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('4', '4', '1', '', '21', '2', '1520996911', '1520996926', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('5', '5', '1', '', '21', '0', '1520997028', '1520997028', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('6', '6', '1', '', '21', '0', '1520997467', '1520997467', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('7', '7', '1', '', '21', '0', '1521078924', '1521078924', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('8', '8', '1', '', '21', '0', '1521707332', '1521707332', '', '0', '', '0');

-- -----------------------------
-- 表结构 `dp_flow_itemdetail_step`
-- -----------------------------
DROP TABLE IF EXISTS `dp_flow_itemdetail_step`;
CREATE TABLE `dp_flow_itemdetail_step` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `itemdetail_id` int(11) NOT NULL COMMENT '行为id',
  `step` int(11) unsigned NOT NULL COMMENT '触发行为的id',
  `user_id` int(10) unsigned NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `result` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `update_time` char(10) DEFAULT NULL COMMENT '审批时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_flow_itemdetail_step`
-- -----------------------------
INSERT INTO `dp_flow_itemdetail_step` VALUES ('26', '26', '21', '1', '1527824528', '0', '', '');
INSERT INTO `dp_flow_itemdetail_step` VALUES ('25', '25', '21', '1', '1527759885', '2', '1527760550', '不行');
INSERT INTO `dp_flow_itemdetail_step` VALUES ('27', '27', '21', '1', '1528163682', '0', '', '');
INSERT INTO `dp_flow_itemdetail_step` VALUES ('28', '28', '21', '1', '1528169801', '1', '1528171649', '');
INSERT INTO `dp_flow_itemdetail_step` VALUES ('29', '28', '22', '1', '1528171649', '0', '', '');
INSERT INTO `dp_flow_itemdetail_step` VALUES ('30', '29', '21', '1', '1528254674', '0', '', '');
INSERT INTO `dp_flow_itemdetail_step` VALUES ('31', '30', '21', '2', '1528256991', '0', '', '');

-- -----------------------------
-- 表结构 `dp_flow_itemdetail`
-- -----------------------------
DROP TABLE IF EXISTS `dp_flow_itemdetail`;
CREATE TABLE `dp_flow_itemdetail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `action_id` int(11) NOT NULL COMMENT '行为id',
  `trigger_id` int(11) unsigned NOT NULL COMMENT '触发行为的id',
  `url` varchar(255) NOT NULL COMMENT '跳转的url',
  `uid` int(10) unsigned NOT NULL,
  `confirm` varchar(255) NOT NULL COMMENT '审批步骤',
  `step` tinyint(2) unsigned NOT NULL DEFAULT '20' COMMENT '审批结果',
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL COMMENT '最后审批时间',
  `table` varchar(50) NOT NULL COMMENT '表名',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_flow_itemdetail`
-- -----------------------------
INSERT INTO `dp_flow_itemdetail` VALUES ('25', '金耀场地建设建设', '273', '7', 'purchase/plan/detail', '1', '1-1-2', '30', '1527759885', '1527760550', 'purchase_plan');
INSERT INTO `dp_flow_itemdetail` VALUES ('26', '金耀钢材询价', '274', '16', 'purchase/money/detail', '1', '1-1-2', '20', '1527824528', '1527824528', 'purchase_money');
INSERT INTO `dp_flow_itemdetail` VALUES ('27', '金耀场地建设合同', '275', '3', 'purchase/hetong/detail', '1', '1-1-2', '20', '1528163682', '1528163682', 'purchase_hetong');
INSERT INTO `dp_flow_itemdetail` VALUES ('28', '金耀场地订单', '276', '4', 'purchase/order/detail', '1', '1-1-2', '20', '1528169801', '1528171649', 'purchase_order');
INSERT INTO `dp_flow_itemdetail` VALUES ('29', '金耀场地建设到货', '277', '5', 'purchase/arrival/detail', '1', '1-1-2', '20', '1528254674', '1528254674', 'purchase_arrival');
INSERT INTO `dp_flow_itemdetail` VALUES ('30', '金耀场地建设', '272', '5', 'purchase/ask/detail', '1', '2-1-1', '20', '1528256991', '1528256991', 'purchase_ask');
