-- -----------------------------
-- 导出时间 `2018-03-27 17:34:15`
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_flow_work`
-- -----------------------------
INSERT INTO `dp_flow_work` VALUES ('5', ' 春节提前回家', ' 买不到票,春节提前回家', '2-1-1', '6', '', '1', '超级管理员', '0', '', '1517802546', '1517802636', '40', '0', '{\"100\":1518105600,\"101\":1518192000}');
INSERT INTO `dp_flow_work` VALUES ('6', '肚子疼', '肚子疼', '2-1-1', '6', '', '1', '超级管理员', '0', '', '1517802725', '1517802936', '30', '0', '{\"100\":1517846400,\"101\":1517846400}');
INSERT INTO `dp_flow_work` VALUES ('7', '111111111', '11111111', '1-1', '7', '10', '1', '超级管理员', '0', '', '1520846535', '1520990482', '30', '0', '');
INSERT INTO `dp_flow_work` VALUES ('8', '哈哈哈哈', '哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈', '1-1', '7', '', '1', '超级管理员', '0', '', '1520992230', '1520998009', '40', '0', '');
INSERT INTO `dp_flow_work` VALUES ('9', '2', '2222222222', '2-1-1', '6', '', '1', '超级管理员', '0', '', '1520997991', '1520997991', '20', '0', '{\"100\":1522425600,\"101\":1522339200}');
INSERT INTO `dp_flow_work` VALUES ('10', '66666666666666', '6666666666', '2-1-1', '6', '', '1', '超级管理员', '0', '', '1520998071', '1520998071', '20', '0', '{\"100\":1519747200,\"101\":1521734400}');
INSERT INTO `dp_flow_work` VALUES ('11', '哈哈,请假啦', '哈哈,请假啦', '2-1-1', '6', '10', '1', '超级管理员', '0', '', '1521075578', '1521075578', '20', '0', '{\"100\":1521129600,\"101\":1521216000}');
INSERT INTO `dp_flow_work` VALUES ('12', '1111111111111', '12121', '2-1-1', '6', '13', '1', '超级管理员', '0', '', '1521076246', '1521076246', '20', '0', '{\"100\":1521734400,\"101\":1522944000}');
INSERT INTO `dp_flow_work` VALUES ('13', '啊啊454', '4545', '2-1-1', '6', '13', '1', '超级管理员', '0', '', '1521076269', '1521076269', '20', '0', '{\"100\":1522944000,\"101\":1523116800}');
INSERT INTO `dp_flow_work` VALUES ('14', '1212121', '212121', '1-1', '7', '13', '1', '超级管理员', '0', '', '1521076309', '1521100647', '30', '0', '');
INSERT INTO `dp_flow_work` VALUES ('15', '额问问', '恶趣味群翁', '2-1-1', '6', '', '1', '超级管理员', '0', '', '1522135288', '1522135288', '20', '0', '{\"100\":false,\"101\":false}');
INSERT INTO `dp_flow_work` VALUES ('16', '恶趣味群翁', '而为全额无群', '1-1', '7', '', '1', '超级管理员', '0', '', '1522135343', '1522135343', '20', '0', '');
INSERT INTO `dp_flow_work` VALUES ('17', '3232', '3232', '2-1-1', '6', '', '1', '超级管理员', '0', '', '1522137063', '1522137063', '20', '0', '{\"100\":false,\"101\":false}');
INSERT INTO `dp_flow_work` VALUES ('18', 'ewqewqewe', 'eqweqweqwe', '2-2-3', '7', '', '1', '超级管理员', '0', '', '1522137176', '1522137176', '20', '0', '');

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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_flow_log`
-- -----------------------------
INSERT INTO `dp_flow_log` VALUES ('5', '5', '2', '', '21', '1', '1517802546', '1517802579', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('6', '5', '1', '', '22', '1', '1517802579', '1517802617', '好,没问题', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('7', '5', '1', '', '23', '1', '1517802617', '1517802636', '好的', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('8', '6', '2', '', '21', '1', '1517802725', '1517802812', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('9', '6', '1', '', '22', '2', '1517802812', '1517802936', '我阑尾炎还加班,你肚子疼就想请假?   ', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('10', '7', '1', '', '21', '1', '1520846536', '1520990441', '4545454', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('11', '7', '1', '', '22', '2', '1520990441', '1520990482', '47846545', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('12', '8', '1', '', '21', '1', '1520992230', '1520997999', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('13', '9', '2', '', '21', '0', '1520997991', '1520997991', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('14', '8', '1', '', '22', '1', '1520997999', '1520998009', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('15', '10', '2', '', '21', '0', '1520998071', '1520998071', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('16', '11', '2', '', '21', '0', '1521075579', '1521075579', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('17', '12', '2', '', '21', '0', '1521076246', '1521076246', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('18', '13', '2', '', '21', '0', '1521076269', '1521076269', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('19', '14', '1', '', '21', '2', '1521076309', '1521100647', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('20', '15', '2', '', '21', '0', '1522135288', '1522135288', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('21', '16', '1', '', '21', '0', '1522135343', '1522135343', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('22', '17', '2', '', '21', '0', '1522137063', '1522137063', '', '0', '', '0');
INSERT INTO `dp_flow_log` VALUES ('23', '18', '2', '', '21', '0', '1522137176', '1522137176', '', '0', '', '0');
