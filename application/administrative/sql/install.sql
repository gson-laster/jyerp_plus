-- -----------------------------
-- 导出时间 `2018-03-09 16:19:13`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_administrative_customer`
-- -----------------------------
DROP TABLE IF EXISTS `dp_administrative_customer`;
CREATE TABLE `dp_administrative_customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '公司名称',
  `short` varchar(20) NOT NULL DEFAULT '' COMMENT '简称',
  `contact` varchar(20) NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮件地址',
  `office_tel` varchar(20) NOT NULL DEFAULT '' COMMENT '办公电话',
  `mobile_tel` varchar(20) NOT NULL DEFAULT '' COMMENT '移动电话',
  `fax` varchar(20) NOT NULL DEFAULT '' COMMENT '传真',
  `qq` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'qq',
  `wechat` varchar(255) NOT NULL DEFAULT '' COMMENT '微信',
  `address` varchar(50) NOT NULL DEFAULT '' COMMENT '地址',
  `add_user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `remark` text NOT NULL COMMENT '备注',
  `is_open` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0自己可见  1 公开',
  PRIMARY KEY (`id`),
  KEY `contact` (`contact`) USING BTREE,
  KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_administrative_customer`
-- -----------------------------
INSERT INTO `dp_administrative_customer` VALUES ('1', '南昌金耀科技有限公司', '金耀科技', '何总', '123@qq.com', '0791-7894645', '13778984564', '', '123456789', 'hezong', '湾里九州名城', '1', '重要客户', '1');
INSERT INTO `dp_administrative_customer` VALUES ('2', '南昌二三其奇网络科技有限公司', '二三其奇', '王总', '739712704@qq.com', '0791-464646464', '15898989898', '', '789456123', '7464646472ee', '湾里区九州名城', '1', '重要的客户,二三奇其大项目', '1');

-- -----------------------------
-- 表结构 `dp_administrative_staffwhere`
-- -----------------------------
DROP TABLE IF EXISTS `dp_administrative_staffwhere`;
CREATE TABLE `dp_administrative_staffwhere` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '员工id',
  `user_name` varchar(255) NOT NULL DEFAULT '' COMMENT '员工name',
  `oid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '部门id',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `staff_where` varchar(255) NOT NULL DEFAULT '' COMMENT '去向',
  `is_open` int(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否公开',
  `open_user` text COMMENT '不公开时的查看人员',
  PRIMARY KEY (`id`),
  KEY `user_name` (`user_name`) USING BTREE,
  FULLTEXT KEY `open_user` (`open_user`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_administrative_staffwhere`
-- -----------------------------
INSERT INTO `dp_administrative_staffwhere` VALUES ('3', '2', '张三', '2', '1517850060', '1519146120', '日本学习', '1', '-1-4-3-');
INSERT INTO `dp_administrative_staffwhere` VALUES ('4', '4', '网络', '5', '1519408860', '1519765320', '丁丁家学习', '0', '-1-3-');
INSERT INTO `dp_administrative_staffwhere` VALUES ('5', '2', '张三', '2', '1517882400', '1518624000', '泰国学习', '0', '-1-4-3-2-');
