-- -----------------------------
-- 导出时间 `2018-03-27 17:34:33`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_assets_category`
-- -----------------------------
DROP TABLE IF EXISTS `dp_assets_category`;
CREATE TABLE `dp_assets_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_assets_category`
-- -----------------------------
INSERT INTO `dp_assets_category` VALUES ('3', '笔记本', '1', '1516159197', '1516159197');
INSERT INTO `dp_assets_category` VALUES ('2', '办公桌', '1', '1516152924', '1516152924');

-- -----------------------------
-- 表结构 `dp_assets_dateil`
-- -----------------------------
DROP TABLE IF EXISTS `dp_assets_dateil`;
CREATE TABLE `dp_assets_dateil` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '领用人id',
  `selectid` int(11) NOT NULL COMMENT '资产id',
  `returnid` tinyint(2) NOT NULL DEFAULT '0' COMMENT '1-已归还',
  `status` tinyint(2) NOT NULL COMMENT '状态：-1-申请失败，0-申请中，1-申请成功',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_assets_dateil`
-- -----------------------------
INSERT INTO `dp_assets_dateil` VALUES ('27', '2', '6', '1', '3', '1516330179', '1516330179');
INSERT INTO `dp_assets_dateil` VALUES ('28', '2', '6', '1', '3', '1516330200', '1516330200');
INSERT INTO `dp_assets_dateil` VALUES ('29', '2', '6', '1', '3', '1516330238', '1516330238');
INSERT INTO `dp_assets_dateil` VALUES ('31', '2', '6', '0', '-1', '1516330431', '1516330431');
INSERT INTO `dp_assets_dateil` VALUES ('32', '2', '6', '1', '3', '1516330918', '1516330918');
INSERT INTO `dp_assets_dateil` VALUES ('33', '2', '4', '0', '-1', '1516331317', '1516331317');
INSERT INTO `dp_assets_dateil` VALUES ('34', '2', '4', '0', '-1', '1516331412', '1516331412');
INSERT INTO `dp_assets_dateil` VALUES ('35', '2', '4', '0', '-1', '1516331476', '1516331476');

-- -----------------------------
-- 表结构 `dp_assets_select`
-- -----------------------------
DROP TABLE IF EXISTS `dp_assets_select`;
CREATE TABLE `dp_assets_select` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '领用人',
  `categoryid` int(11) NOT NULL COMMENT '类别',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `specifications` varchar(255) NOT NULL COMMENT '规格',
  `departmentid` int(11) NOT NULL COMMENT '所属部门',
  `procurement` varchar(255) NOT NULL COMMENT '采购人',
  `money` float(50,0) NOT NULL COMMENT '价钱',
  `invoice_time` varchar(50) NOT NULL COMMENT '发票日期',
  `note` varchar(255) NOT NULL COMMENT '备注',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态：-2停用，-1申请失败，0申请中，1可用，2申请成功',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_assets_select`
-- -----------------------------
INSERT INTO `dp_assets_select` VALUES ('4', '2', '3', '华硕', 'a450', '2', '贺建平', '5000', '2018-01-16 00:00', '', '-1', '1516091209', '1516158849');
INSERT INTO `dp_assets_select` VALUES ('5', '0', '2', '电脑桌', '啊大大', '1', '黄远东', '2000', '2018-01-17 00:00', '', '1', '1516158996', '1516158996');
INSERT INTO `dp_assets_select` VALUES ('6', '0', '3', 'Apple MacBook 12英寸笔记本电脑 ', '2018新款Core i5 处理器/8GB内存/512GB闪存 MNYG2CH/A', '1', '王永吉', '12288', '2018-01-18 00:00', '', '1', '1516256118', '1516330715');
INSERT INTO `dp_assets_select` VALUES ('7', '0', '2', '2222', '2222', '1', '姜俊', '5000', '2018-01-18 00:00', '', '1', '1516259882', '1516259882');
