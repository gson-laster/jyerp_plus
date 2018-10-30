-- -----------------------------
-- 导出时间 `2018-03-16 09:20:08`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_notice_list`
-- -----------------------------
DROP TABLE IF EXISTS `dp_notice_list`;
CREATE TABLE `dp_notice_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '发布人',
  `title` varchar(255) NOT NULL COMMENT '公告标题',
  `cate` smallint(5) unsigned NOT NULL COMMENT '公告类型',
  `to_user` varchar(255) DEFAULT NULL COMMENT '公告通知人',
  `noticer` varchar(255) DEFAULT NULL COMMENT '公告通知人',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `info` varchar(255) NOT NULL COMMENT '公告主体',
  `note` varchar(255) DEFAULT NULL COMMENT '备注',
  `enclosure` varchar(255) DEFAULT NULL COMMENT '附件',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态  0 待发布 1 发布 2 撤销',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_notice_list`
-- -----------------------------
INSERT INTO `dp_notice_list` VALUES ('9', '1', '公司公告', '1', '', '', '公司公告', '<p>公司公告</p>', '公司公告', '4', '1', '1521104523', '1521163129');
INSERT INTO `dp_notice_list` VALUES ('7', '1', '部门公告', '2', '1,2,4,5', '二三奇其,技术部,网络部,销售部', '部门公告', '<p>部门公告</p>', '', '4', '1', '1521099712', '1521163124');
INSERT INTO `dp_notice_list` VALUES ('8', '1', '个人公告', '3', '1,2,3,4', '张三,李四,网络,超级管理员', '个人公告', '<p>个人公告</p>', '个人公告', '11', '1', '1521099793', '1521163126');

-- -----------------------------
-- 表结构 `dp_notice_cate`
-- -----------------------------
DROP TABLE IF EXISTS `dp_notice_cate`;
CREATE TABLE `dp_notice_cate` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '公告类型',
  `pid` smallint(5) NOT NULL,
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='组织架构表';

-- -----------------------------
-- 表数据 `dp_notice_cate`
-- -----------------------------
INSERT INTO `dp_notice_cate` VALUES ('1', '公司公告', '0', '1521013267', '1521013267', '100', '1');
INSERT INTO `dp_notice_cate` VALUES ('2', '部门公告', '0', '1521013307', '1521013307', '100', '1');
INSERT INTO `dp_notice_cate` VALUES ('3', '个人公告', '0', '1521013337', '1521013337', '100', '1');

-- -----------------------------
-- 表结构 `dp_notice_user`
-- -----------------------------
DROP TABLE IF EXISTS `dp_notice_user`;
CREATE TABLE `dp_notice_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lid` int(11) unsigned NOT NULL COMMENT '公告id',
  `cate` smallint(5) unsigned NOT NULL COMMENT '公告类型',
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `is_read` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已阅读',
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_notice_user`
-- -----------------------------
INSERT INTO `dp_notice_user` VALUES ('22', '8', '3', '3', '0', '1521163126', '1521163126');
INSERT INTO `dp_notice_user` VALUES ('17', '7', '2', '2', '0', '1521163124', '1521163124');
INSERT INTO `dp_notice_user` VALUES ('18', '7', '2', '3', '0', '1521163124', '1521163124');
INSERT INTO `dp_notice_user` VALUES ('23', '8', '3', '4', '0', '1521163126', '1521163126');
INSERT INTO `dp_notice_user` VALUES ('19', '7', '2', '4', '0', '1521163124', '1521163124');
INSERT INTO `dp_notice_user` VALUES ('21', '8', '3', '2', '0', '1521163126', '1521163126');
INSERT INTO `dp_notice_user` VALUES ('27', '9', '1', '4', '0', '1521163129', '1521163129');
INSERT INTO `dp_notice_user` VALUES ('26', '9', '1', '3', '0', '1521163129', '1521163129');
INSERT INTO `dp_notice_user` VALUES ('25', '9', '1', '2', '0', '1521163129', '1521163129');
