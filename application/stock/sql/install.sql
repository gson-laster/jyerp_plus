/*
Navicat MySQL Data Transfer

Source Server         : 4r54o54o
Source Server Version : 50547
Source Host           : localhost:3306
Source Database       : dolphin

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2018-06-05 10:24:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for dp_stock_allot
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_allot`;
CREATE TABLE `dp_stock_allot` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '库存调拨',
  `name` varchar(255) NOT NULL COMMENT '主题',
  `zrid` int(11) NOT NULL COMMENT '调拨申请人',
  `yhid` int(11) NOT NULL COMMENT '要货部门',
  `dhid` int(11) NOT NULL COMMENT '调货部门',
  `drid` int(11) NOT NULL COMMENT '调入仓库',
  `dcid` int(11) NOT NULL COMMENT '调出仓库',
  `dh_time` varchar(50) NOT NULL COMMENT '要求到货日期',
  `zd_time` varchar(50) NOT NULL COMMENT '制单日期',
  `zdid` int(11) NOT NULL COMMENT '制单人（登录id）',
  `why` tinyint(2) NOT NULL COMMENT '调拨原因0-平衡需要1-货物短缺',
  `note` varchar(255) NOT NULL COMMENT '摘要',
  `goodid` varchar(255) NOT NULL COMMENT '物资id,',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `fileid` int(11) NOT NULL COMMENT '上传文件id',
  `helpid` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL COMMENT '上传附件路径',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dp_stock_allot
-- ----------------------------
INSERT INTO `dp_stock_allot` VALUES ('1', '案发地方', '6', '2', '2', '35', '35', '2018-06-04', '', '1', '0', '阿斯顿发顺丰的', '', '1528107028', '1528107028', '6', '', '/uploads/files/20180529/5931c8e544807e014f5a906b6559dcac.txt');
INSERT INTO `dp_stock_allot` VALUES ('2', '案发地方', '6', '2', '2', '35', '35', '2018-06-04', '', '1', '0', '阿斯顿发顺丰的', '', '1528107084', '1528107084', '6', '', '/uploads/files/20180529/5931c8e544807e014f5a906b6559dcac.txt');
INSERT INTO `dp_stock_allot` VALUES ('3', '阿萨斯', '6', '4', '2', '35', '35', '2018-06-04', '', '1', '0', '奥术大师多', '', '1528107125', '1528107125', '6', '', '/uploads/files/20180529/5931c8e544807e014f5a906b6559dcac.txt');
INSERT INTO `dp_stock_allot` VALUES ('4', '阿萨斯', '6', '4', '2', '35', '35', '2018-06-04', '', '1', '0', '奥术大师多', '', '1528107145', '1528107145', '6', '', '/uploads/files/20180529/5931c8e544807e014f5a906b6559dcac.txt');

-- ----------------------------
-- Table structure for dp_stock_allot_detail
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_allot_detail`;
CREATE TABLE `dp_stock_allot_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '采购入库id',
  `itemsid` int(11) unsigned NOT NULL COMMENT '入库明细id',
  `tbsl` int(11) NOT NULL COMMENT '调拨数量',
  `tbdj` int(50) NOT NULL COMMENT '调拨单价',
  `tbje` int(50) NOT NULL COMMENT '调拨金额',
  `cksl` double(50,2) NOT NULL COMMENT '已出库数量',
  `rksl` double(50,2) NOT NULL COMMENT '已入库数量',
  `bz` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- ----------------------------
-- Records of dp_stock_allot_detail
-- ----------------------------
INSERT INTO `dp_stock_allot_detail` VALUES ('41', '8', '10', '4', '4', '0', '4.00', '4.00', '4', '1527837079', '1527837079');
INSERT INTO `dp_stock_allot_detail` VALUES ('42', '1', '10', '234', '0', '0', '0.00', '0.00', '4', '1528077578', '1528077578');
INSERT INTO `dp_stock_allot_detail` VALUES ('43', '2', '10', '5', '0', '0', '0.00', '0.00', '是v', '1528078166', '1528078166');
INSERT INTO `dp_stock_allot_detail` VALUES ('44', '3', '9', '4', '0', '0', '0.00', '0.00', '是的发个', '1528078981', '1528078981');
INSERT INTO `dp_stock_allot_detail` VALUES ('45', '1', '10', '3', '3', '3', '3.00', '3.00', '3', '1528094012', '1528094012');
INSERT INTO `dp_stock_allot_detail` VALUES ('46', '2', '9', '4', '4', '3', '3.00', '3.00', '3', '1528094513', '1528094513');
INSERT INTO `dp_stock_allot_detail` VALUES ('47', '4', '9', '2', '2', '2', '2.00', '2.00', '2', '1528107145', '1528107145');

-- ----------------------------
-- Table structure for dp_stock_bad
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_bad`;
CREATE TABLE `dp_stock_bad` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '库存报损',
  `name` varchar(255) NOT NULL COMMENT '报损主题',
  `zrid` int(11) NOT NULL COMMENT '经办人',
  `bsbm` int(11) NOT NULL COMMENT '报损部门',
  `ck` int(11) NOT NULL COMMENT '仓库',
  `bstype` int(11) NOT NULL COMMENT '报损原因，0-物品折旧1-物品损坏',
  `bs_time` datetime NOT NULL COMMENT '报损日期',
  `zdid` int(11) NOT NULL COMMENT '制单人UID',
  `note` text,
  `create_time` int(11) NOT NULL,
  `file` varchar(255) DEFAULT NULL COMMENT '附件',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dp_stock_bad
-- ----------------------------
INSERT INTO `dp_stock_bad` VALUES ('5', '阿萨德', '6', '2', '35', '1', '2018-06-06 00:00:00', '1', '奥术大师多', '1528165296', '6');

-- ----------------------------
-- Table structure for dp_stock_bad_detail
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_bad_detail`;
CREATE TABLE `dp_stock_bad_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '采购入库id',
  `itemsid` int(11) unsigned NOT NULL COMMENT '入库明细id',
  `cbjg` int(11) NOT NULL COMMENT '成本单价',
  `shsl` int(50) NOT NULL COMMENT '损坏数量',
  `bsje` int(50) NOT NULL COMMENT '报损金额',
  `bz` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- ----------------------------
-- Records of dp_stock_bad_detail
-- ----------------------------
INSERT INTO `dp_stock_bad_detail` VALUES ('49', '5', '9', '3', '3', '3', '3', '1528165296', '1528165296');

-- ----------------------------
-- Table structure for dp_stock_borrow
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_borrow`;
CREATE TABLE `dp_stock_borrow` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '借货表',
  `name` varchar(255) NOT NULL COMMENT '借货主题',
  `zrid` int(11) NOT NULL COMMENT '借货人',
  `jhbm` int(11) NOT NULL COMMENT '借货部门',
  `why` int(11) NOT NULL COMMENT '借货原因',
  `jh_time` varchar(50) NOT NULL COMMENT '借货日期',
  `jcck` int(11) NOT NULL COMMENT '借出仓库',
  `ck_time` varchar(50) NOT NULL COMMENT '出库日期',
  `ckid` int(11) NOT NULL COMMENT '出库人',
  `jcbm` int(11) NOT NULL COMMENT '借出部门',
  `zdid` int(11) NOT NULL COMMENT '制单人UID',
  `helpid` varchar(255) NOT NULL COMMENT '可查看人员',
  `note` text COMMENT '摘要',
  `create_time` int(11) NOT NULL,
  `path` varchar(255) NOT NULL COMMENT '上传附件路径',
  `fileid` int(11) NOT NULL COMMENT '上传文件id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dp_stock_borrow
-- ----------------------------
INSERT INTO `dp_stock_borrow` VALUES ('1', '阿道夫', '8', '4', '-1', '2018-06-04 00:00:00', '0', '2018-06-04 00:00:00', '1', '2', '1', '', '安抚', '1528094012', '/uploads/files/20180529/5931c8e544807e014f5a906b6559dcac.txt', '6');
INSERT INTO `dp_stock_borrow` VALUES ('2', '发发呆过', '6', '4', '-1', '2018-06-04', '35', '2018-06-04', '1', '4', '1', '', '阿萨手多', '1528094513', '/uploads/files/20180529/5931c8e544807e014f5a906b6559dcac.txt', '6');

-- ----------------------------
-- Table structure for dp_stock_borrow_detail
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_borrow_detail`;
CREATE TABLE `dp_stock_borrow_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '采购入库id',
  `itemsid` int(11) unsigned NOT NULL COMMENT '入库明细id',
  `ck` int(11) NOT NULL COMMENT '仓库',
  `xycl` int(50) NOT NULL COMMENT '现有存量',
  `jhsl` int(50) NOT NULL COMMENT '借货数量',
  `jcdj` double(50,2) NOT NULL COMMENT '借出单价',
  `jcje` double(50,2) NOT NULL COMMENT '借出金额',
  `fhtime` varchar(50) NOT NULL COMMENT '返还时间',
  `fhsl` int(50) NOT NULL COMMENT '返还数量',
  `bz` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- ----------------------------
-- Records of dp_stock_borrow_detail
-- ----------------------------
INSERT INTO `dp_stock_borrow_detail` VALUES ('41', '8', '10', '4', '4', '0', '4.00', '4.00', '', '0', '4', '1527837079', '1527837079');
INSERT INTO `dp_stock_borrow_detail` VALUES ('42', '1', '10', '234', '0', '0', '0.00', '0.00', '', '0', '4', '1528077578', '1528077578');
INSERT INTO `dp_stock_borrow_detail` VALUES ('43', '2', '10', '5', '0', '0', '0.00', '0.00', '', '0', '是v', '1528078166', '1528078166');
INSERT INTO `dp_stock_borrow_detail` VALUES ('44', '3', '9', '4', '0', '0', '0.00', '0.00', '', '0', '是的发个', '1528078981', '1528078981');
INSERT INTO `dp_stock_borrow_detail` VALUES ('45', '1', '10', '3', '3', '3', '3.00', '3.00', '3', '3', '3', '1528094012', '1528094012');
INSERT INTO `dp_stock_borrow_detail` VALUES ('46', '2', '9', '4', '4', '3', '3.00', '3.00', '3', '3', '3', '1528094513', '1528094513');

-- ----------------------------
-- Table structure for dp_stock_check
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_check`;
CREATE TABLE `dp_stock_check` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '期末盘点',
  `name` varchar(255) NOT NULL COMMENT '盘点主题',
  `zrid` int(11) NOT NULL COMMENT '经办人',
  `pdbm` int(11) NOT NULL COMMENT '盘点部门',
  `pdck` int(11) NOT NULL COMMENT '盘点仓库',
  `pdtype` int(11) NOT NULL COMMENT '盘点类型',
  `start_time` varchar(50) NOT NULL COMMENT '开始日期',
  `end_time` varchar(50) NOT NULL COMMENT '结束日期',
  `tzid` int(11) NOT NULL COMMENT '库存调整人',
  `tz_time` datetime NOT NULL COMMENT '库存调整日期',
  `zdid` int(11) NOT NULL COMMENT '制单人UID',
  `note` text,
  `create_time` int(11) NOT NULL,
  `file` varchar(255) DEFAULT NULL COMMENT '附件',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dp_stock_check
-- ----------------------------
INSERT INTO `dp_stock_check` VALUES ('1', '阿萨德发的', '6', '4', '35', '0', '2018-06-05', '2018-06-06', '1', '2018-06-05 00:00:00', '1', '阿达', '1528163468', '6');

-- ----------------------------
-- Table structure for dp_stock_check_detail
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_check_detail`;
CREATE TABLE `dp_stock_check_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '采购入库id',
  `itemsid` int(11) unsigned NOT NULL COMMENT '入库明细id',
  `cbjg` int(11) NOT NULL COMMENT '成本价格',
  `xcsl` int(50) NOT NULL COMMENT '现存数量',
  `spsl` int(50) NOT NULL COMMENT '实盘数量',
  `cyl` double(50,2) NOT NULL COMMENT '差异量',
  `bz` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- ----------------------------
-- Records of dp_stock_check_detail
-- ----------------------------
INSERT INTO `dp_stock_check_detail` VALUES ('41', '8', '10', '4', '4', '0', '4.00', '4', '1527837079', '1527837079');
INSERT INTO `dp_stock_check_detail` VALUES ('42', '1', '10', '234', '0', '0', '0.00', '4', '1528077578', '1528077578');
INSERT INTO `dp_stock_check_detail` VALUES ('43', '2', '10', '5', '0', '0', '0.00', '是v', '1528078166', '1528078166');
INSERT INTO `dp_stock_check_detail` VALUES ('44', '3', '9', '4', '0', '0', '0.00', '是的发个', '1528078981', '1528078981');
INSERT INTO `dp_stock_check_detail` VALUES ('45', '1', '10', '3', '3', '3', '3.00', '3', '1528094012', '1528094012');
INSERT INTO `dp_stock_check_detail` VALUES ('46', '2', '9', '4', '4', '3', '3.00', '3', '1528094513', '1528094513');
INSERT INTO `dp_stock_check_detail` VALUES ('47', '1', '10', '3', '3', '3', '3.00', '3', '1528163468', '1528163468');

-- ----------------------------
-- Table structure for dp_stock_house
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_house`;
CREATE TABLE `dp_stock_house` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '仓库名称',
  `uid` int(11) unsigned NOT NULL COMMENT '建档人',
  `zrid` int(11) unsigned DEFAULT NULL COMMENT '负责人',
  `code` varchar(50) DEFAULT '' COMMENT '仓库代码',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态  0 关闭 1 启用',
  `type` tinyint(1) unsigned NOT NULL COMMENT '仓库类型',
  `description` text COMMENT '仓库描述',
  `helpid` varchar(255) DEFAULT '' COMMENT '可查看该仓库的人员',
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- ----------------------------
-- Records of dp_stock_house
-- ----------------------------
INSERT INTO `dp_stock_house` VALUES ('35', '一号仓库', '1', '6', '', '1', '11', '<p>呵呵呵呵呵</p>', '4,6,10,', '1527218144', '1527218144');

-- ----------------------------
-- Table structure for dp_stock_house_type
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_house_type`;
CREATE TABLE `dp_stock_house_type` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '仓库类型',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='组织架构表';

-- ----------------------------
-- Records of dp_stock_house_type
-- ----------------------------
INSERT INTO `dp_stock_house_type` VALUES ('1', '二三奇其', '1476065410', '1524473409', '2', '1');
INSERT INTO `dp_stock_house_type` VALUES ('2', '技术部', '1516005129', '1516935672', '1', '1');
INSERT INTO `dp_stock_house_type` VALUES ('4', '销售部', '1516005539', '1516935672', '2', '1');
INSERT INTO `dp_stock_house_type` VALUES ('5', '网络部', '1516005550', '1516935672', '3', '1');
INSERT INTO `dp_stock_house_type` VALUES ('11', '材料', '1527218117', '1527218117', '0', '1');

-- ----------------------------
-- Table structure for dp_stock_material
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_material`;
CREATE TABLE `dp_stock_material` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '物品名称',
  `short_name` varchar(32) DEFAULT NULL COMMENT '简称',
  `code` varchar(32) NOT NULL DEFAULT '' COMMENT '物品编码',
  `bar` varchar(32) DEFAULT NULL COMMENT '条码',
  `type` smallint(5) unsigned NOT NULL COMMENT '物品类型',
  `unit` varchar(10) NOT NULL COMMENT '单位',
  `version` varchar(32) NOT NULL DEFAULT '' COMMENT '规格型号',
  `color` varchar(64) NOT NULL DEFAULT '' COMMENT '颜色',
  `brand` varchar(20) NOT NULL DEFAULT '' COMMENT '品牌',
  `rate` varchar(20) NOT NULL DEFAULT '' COMMENT '税率',
  `level` varchar(20) DEFAULT NULL COMMENT '档次级别',
  `size` varchar(50) DEFAULT NULL COMMENT '尺寸',
  `weight` decimal(8,2) NOT NULL COMMENT '重量',
  `quality` varchar(10) NOT NULL COMMENT '质量',
  `explain` text COMMENT '说明',
  `note` text COMMENT '备注',
  `enclosure` varchar(255) DEFAULT NULL COMMENT '附件',
  `abc` varchar(5) NOT NULL COMMENT 'abc分类',
  `cost_way` tinyint(1) NOT NULL COMMENT '成本核算方法  0 无  1 加权平均',
  `cost` decimal(8,2) unsigned NOT NULL COMMENT '标准成本',
  `price_tax` decimal(8,2) unsigned NOT NULL COMMENT '含税售价',
  `price_no_tax` decimal(8,2) unsigned DEFAULT NULL COMMENT '去税售价',
  `price` decimal(8,2) unsigned DEFAULT NULL COMMENT '零售价',
  `sales_rate` decimal(5,2) DEFAULT NULL COMMENT '销项税率',
  `price_discount` decimal(5,2) unsigned DEFAULT NULL COMMENT '销售折扣',
  `allot_price` decimal(8,2) unsigned DEFAULT NULL COMMENT '调拨单价',
  `bid_no_tax` decimal(8,2) unsigned DEFAULT NULL COMMENT '去税进价',
  `bid_tax` decimal(8,2) unsigned DEFAULT NULL COMMENT '含税进价',
  `input_tax` decimal(5,2) DEFAULT NULL COMMENT '进项税率',
  `trade_price_tax` decimal(8,2) DEFAULT NULL COMMENT '含税批发价',
  `trade_price_no_tax` decimal(8,2) DEFAULT NULL COMMENT '去税批发价',
  `is_stock` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否计入库存  0 否 1 是',
  `is_negative` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许负库存  0 否 1 是',
  `house_id` int(11) unsigned NOT NULL COMMENT '主放仓库',
  `safe_stock` int(11) NOT NULL COMMENT '安全库存',
  `max_stock` int(11) DEFAULT NULL COMMENT '最高库存量',
  `min_stock` int(11) DEFAULT NULL COMMENT '最低库存量',
  `source` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '来源  0 自制  1 外购  2  委外 3 虚拟件 4  其他',
  `produce_place` varchar(255) DEFAULT NULL COMMENT '产地',
  `figure_number` varchar(255) DEFAULT NULL COMMENT '图号',
  `status` tinyint(1) unsigned NOT NULL COMMENT '启用状态  0  停用  1 启用',
  `license_number` varchar(255) NOT NULL COMMENT '批准文号',
  `substitute_good` varchar(50) DEFAULT NULL COMMENT '替代品名称',
  `vender` varchar(50) NOT NULL COMMENT '厂家',
  `description` varchar(255) DEFAULT NULL COMMENT '替代品描述',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='组织架构表';

-- ----------------------------
-- Records of dp_stock_material
-- ----------------------------
INSERT INTO `dp_stock_material` VALUES ('9', '钢板', '钢板', 'wz1001', '2156213', '10', '吨', '20*1650', '黑色', '香奈儿', '0', '', '', '0.00', '', '', '', '', '1', '1', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1', '0', '0', '0', '0', '0', '0', '', '', '1', '', '', '', '', '1524819647', '1527755442');
INSERT INTO `dp_stock_material` VALUES ('10', '白菜', '', 'wz2001', '', '12', '斤', '10*201', '红色', '花花公子', '0', '', '', '0.00', '', '', '', '', '', '0', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1', '0', '0', '0', '0', '0', '0', '', '', '1', '', '', '', '', '1524819752', '1527755394');

-- ----------------------------
-- Table structure for dp_stock_material_type
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_material_type`;
CREATE TABLE `dp_stock_material_type` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '类型名称',
  `code` varchar(32) NOT NULL DEFAULT '' COMMENT '类型编码',
  `pid` smallint(54) unsigned NOT NULL DEFAULT '0',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='组织架构表';

-- ----------------------------
-- Records of dp_stock_material_type
-- ----------------------------
INSERT INTO `dp_stock_material_type` VALUES ('8', '钢材', '', '0', '1524713221', '1524713221', '100', '1');
INSERT INTO `dp_stock_material_type` VALUES ('10', '轻钢', '', '8', '1524713546', '1524713632', '100', '1');
INSERT INTO `dp_stock_material_type` VALUES ('11', '食物', '', '0', '1524714002', '1524714002', '100', '1');
INSERT INTO `dp_stock_material_type` VALUES ('12', '白菜', '', '11', '1524714014', '1524714014', '100', '1');
INSERT INTO `dp_stock_material_type` VALUES ('13', '中钢', '', '8', '1524714096', '1524714096', '100', '1');

-- ----------------------------
-- Table structure for dp_stock_other
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_other`;
CREATE TABLE `dp_stock_other` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '仓库名称',
  `uid` int(11) unsigned NOT NULL COMMENT '建档人',
  `header` int(11) unsigned DEFAULT NULL COMMENT '负责人',
  `code` varchar(50) DEFAULT '' COMMENT '仓库代码',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态  0 关闭 1 启用',
  `type` tinyint(1) unsigned NOT NULL COMMENT '仓库类型',
  `description` text COMMENT '仓库描述',
  `access` varchar(255) DEFAULT '' COMMENT '可查看该仓库的人员',
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- ----------------------------
-- Records of dp_stock_other
-- ----------------------------
INSERT INTO `dp_stock_other` VALUES ('30', '车间4', '0', '1', '1520240549', '1', '0', null, null, '1520240549', '0');
INSERT INTO `dp_stock_other` VALUES ('31', '车间5', '1', '1', '1520565354', '1', '0', null, null, '1520565354', '0');
INSERT INTO `dp_stock_other` VALUES ('32', '浇筑中心', '1', '3', '12313', '1', '1', '<p>3123</p>', '3213213', '1524119632', '1524125565');
INSERT INTO `dp_stock_other` VALUES ('33', '呵呵', '1', '1', '213123', '1', '2', '', '1,2,3,4', '1524533084', '1524534530');

-- ----------------------------
-- Table structure for dp_stock_other_detail
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_other_detail`;
CREATE TABLE `dp_stock_other_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '入库单名称',
  `code` varchar(50) DEFAULT '' COMMENT '入库单编号',
  `uid` int(11) unsigned NOT NULL COMMENT '建档人',
  `order_id` int(11) unsigned NOT NULL COMMENT '源单编号',
  `type` tinyint(1) unsigned NOT NULL COMMENT '源单类型  1 采购入库  2 生产入库  3 其他入库',
  `description` text COMMENT '仓库描述',
  `access` varchar(255) DEFAULT '' COMMENT '可查看该仓库的人员',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态  0 关闭 1 启用',
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- ----------------------------
-- Records of dp_stock_other_detail
-- ----------------------------
INSERT INTO `dp_stock_other_detail` VALUES ('30', '车间4', '1520240549', '0', '0', '0', null, null, '1', '1520240549', '0');
INSERT INTO `dp_stock_other_detail` VALUES ('31', '车间5', '1520565354', '1', '0', '0', null, null, '1', '1520565354', '0');
INSERT INTO `dp_stock_other_detail` VALUES ('32', '浇筑中心', '12313', '1', '0', '1', '<p>3123</p>', '3213213', '1', '1524119632', '1524125565');
INSERT INTO `dp_stock_other_detail` VALUES ('33', '呵呵', '213123', '1', '0', '2', '', '1,2,3,4', '1', '1524533084', '1524534530');

-- ----------------------------
-- Table structure for dp_stock_otherin
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_otherin`;
CREATE TABLE `dp_stock_otherin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '入库主题',
  `why` int(11) NOT NULL COMMENT '入库原因',
  `helpid` varchar(255) NOT NULL COMMENT '可查看人员',
  `zdid` int(255) NOT NULL COMMENT '制单人UID',
  `note` text COMMENT '摘要',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `putinid` int(11) NOT NULL COMMENT '入库部门',
  `deliverer` int(11) NOT NULL COMMENT '交货人',
  `warehouses` int(11) NOT NULL COMMENT '入库人',
  `zrid` int(11) NOT NULL COMMENT '验收人',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dp_stock_otherin
-- ----------------------------
INSERT INTO `dp_stock_otherin` VALUES ('8', '阿萨德', '0', '4,6,10,', '1', '阿萨德', '1527837079', '1527837079', '4', '1', '1', '9');

-- ----------------------------
-- Table structure for dp_stock_otherin_detail
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_otherin_detail`;
CREATE TABLE `dp_stock_otherin_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '采购入库id',
  `itemsid` int(11) unsigned NOT NULL COMMENT '入库明细id',
  `ck` int(11) NOT NULL COMMENT '仓库',
  `rksl` int(50) NOT NULL COMMENT '实收数量',
  `dj` double(50,2) NOT NULL COMMENT '单价',
  `je` double(50,2) NOT NULL COMMENT '金额',
  `bz` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- ----------------------------
-- Records of dp_stock_otherin_detail
-- ----------------------------
INSERT INTO `dp_stock_otherin_detail` VALUES ('41', '8', '10', '4', '4', '4.00', '4.00', '4', '1527837079', '1527837079');
INSERT INTO `dp_stock_otherin_detail` VALUES ('42', '1', '10', '234', '0', '0.00', '0.00', '4', '1528077578', '1528077578');
INSERT INTO `dp_stock_otherin_detail` VALUES ('43', '2', '10', '5', '0', '0.00', '0.00', '是v', '1528078166', '1528078166');
INSERT INTO `dp_stock_otherin_detail` VALUES ('44', '3', '9', '4', '0', '0.00', '0.00', '是的发个', '1528078981', '1528078981');

-- ----------------------------
-- Table structure for dp_stock_otherout
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_otherout`;
CREATE TABLE `dp_stock_otherout` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '其他出库',
  `name` varchar(255) NOT NULL COMMENT '出库主题',
  `goodaddrss` varchar(255) NOT NULL COMMENT '发货地址',
  `addrss` varchar(255) NOT NULL COMMENT '收货地址',
  `zrid` int(11) NOT NULL COMMENT '经办人',
  `ckid` int(11) NOT NULL COMMENT '出库人',
  `ckbm` int(11) NOT NULL COMMENT '出库部门',
  `ck_time` varchar(50) NOT NULL COMMENT '出库时间',
  `why` int(11) NOT NULL COMMENT '出库原因',
  `zdid` int(11) NOT NULL COMMENT '制单人UID',
  `helpid` int(11) NOT NULL COMMENT '可查看人员',
  `note` text COMMENT '摘要',
  `create_time` int(11) NOT NULL,
  `path` varchar(255) NOT NULL COMMENT '上传附件路径',
  `fileid` int(11) NOT NULL COMMENT '上传文件id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dp_stock_otherout
-- ----------------------------
INSERT INTO `dp_stock_otherout` VALUES ('4', '电饭锅快捷键', '撒地方刚好够', '顺丰到付', '5', '1', '4', '2018-06-05', '0', '1', '2', '是的发个', '1528079489', '/uploads/files/20180529/5931c8e544807e014f5a906b6559dcac.txt', '6');

-- ----------------------------
-- Table structure for dp_stock_otherout_detail
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_otherout_detail`;
CREATE TABLE `dp_stock_otherout_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '采购入库id',
  `itemsid` int(11) unsigned NOT NULL COMMENT '入库明细id',
  `thsl` int(50) NOT NULL COMMENT '退货数量',
  `ck` int(11) NOT NULL COMMENT '仓库',
  `cksl` int(50) NOT NULL COMMENT '出库数量',
  `ckdj` double(50,2) NOT NULL COMMENT '出库单价',
  `ckje` double(50,2) NOT NULL COMMENT '金额',
  `bz` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- ----------------------------
-- Records of dp_stock_otherout_detail
-- ----------------------------
INSERT INTO `dp_stock_otherout_detail` VALUES ('40', '4', '9', '6', '4', '7', '98.00', '7.00', '丰东股份', '1528079489', '1528079489');

-- ----------------------------
-- Table structure for dp_stock_produce
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_produce`;
CREATE TABLE `dp_stock_produce` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '入库单名称',
  `code` varchar(50) DEFAULT '' COMMENT '入库单编号',
  `uid` int(11) unsigned NOT NULL COMMENT '建档人',
  `order_id` int(11) unsigned NOT NULL COMMENT '生产任务单',
  `putinid` int(11) NOT NULL COMMENT '入库部门',
  `deliverer` int(11) NOT NULL COMMENT '交货人',
  `warehouses` int(11) NOT NULL COMMENT '入库人',
  `zrid` int(11) NOT NULL COMMENT '验收人',
  `helpid` varchar(255) DEFAULT '' COMMENT '可查看该仓库的人员',
  `zdid` int(11) NOT NULL COMMENT '制单人',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态  0 关闭 1 启用',
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  `path` varchar(255) NOT NULL COMMENT '上传附件路径',
  `fileid` int(11) NOT NULL COMMENT '上传文件id',
  `note` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- ----------------------------
-- Records of dp_stock_produce
-- ----------------------------
INSERT INTO `dp_stock_produce` VALUES ('37', '阿萨德', '', '0', '41', '1', '1', '1', '6', '4,6,10,', '1', '1', '1527832298', '1527832298', '6', '6', '阿萨德');
INSERT INTO `dp_stock_produce` VALUES ('38', '阿斯蒂芬', '', '0', '40', '4', '1', '1', '6', '4,6,10,', '1', '1', '1527844693', '1527844693', '6', '6', '阿萨德');

-- ----------------------------
-- Table structure for dp_stock_produce_detail
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_produce_detail`;
CREATE TABLE `dp_stock_produce_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '采购入库id',
  `itemsid` int(11) unsigned NOT NULL COMMENT '入库明细id',
  `ck` int(11) NOT NULL COMMENT '仓库',
  `rksl` int(50) NOT NULL COMMENT '实收数量',
  `dj` double(50,2) NOT NULL COMMENT '单价',
  `je` double(50,2) NOT NULL COMMENT '金额',
  `bz` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- ----------------------------
-- Records of dp_stock_produce_detail
-- ----------------------------
INSERT INTO `dp_stock_produce_detail` VALUES ('38', '37', '10', '3', '4', '5.00', '6.00', '撒地方', '1527832298', '1527832298');
INSERT INTO `dp_stock_produce_detail` VALUES ('39', '38', '9', '6', '6', '6.00', '6.00', '6', '1527844693', '1527844693');

-- ----------------------------
-- Table structure for dp_stock_purchase
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_purchase`;
CREATE TABLE `dp_stock_purchase` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '入库单名称',
  `code` varchar(50) DEFAULT '' COMMENT '入库单编号',
  `uid` int(11) unsigned NOT NULL COMMENT '建档人',
  `order_id` int(11) unsigned NOT NULL COMMENT '源单编号',
  `deliverer` int(11) unsigned NOT NULL COMMENT '交货人',
  `zrid` int(11) unsigned NOT NULL COMMENT '验收人',
  `putinid` int(11) unsigned NOT NULL COMMENT '入库部门id',
  `warehouses` int(11) unsigned NOT NULL COMMENT '入库人',
  `note` text COMMENT '摘要',
  `fileid` int(11) NOT NULL COMMENT '上传文件id',
  `path` varchar(255) NOT NULL COMMENT '上传附件路径',
  `zdid` int(11) NOT NULL COMMENT '制单人',
  `helpid` varchar(255) NOT NULL COMMENT '可查看该入库人员',
  `create_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- ----------------------------
-- Records of dp_stock_purchase
-- ----------------------------
INSERT INTO `dp_stock_purchase` VALUES ('47', '火影', '', '0', '1', '1', '6', '4', '1', '阿萨德', '6', '/uploads/files/20180529/5931c8e544807e014f5a906b6559dcac.txt', '1', '4,6,10,', '1527820224');
INSERT INTO `dp_stock_purchase` VALUES ('48', 'a爱上的对方', '', '0', '1', '1', '6', '5', '1', '阿萨德奥术大师多', '6', '/uploads/files/20180529/5931c8e544807e014f5a906b6559dcac.txt', '1', '4,6,10,', '1527844879');

-- ----------------------------
-- Table structure for dp_stock_purchase_detail
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_purchase_detail`;
CREATE TABLE `dp_stock_purchase_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '采购入库id',
  `itemsid` int(11) unsigned NOT NULL COMMENT '入库明细id',
  `dhsl` int(50) unsigned NOT NULL COMMENT '到货数量',
  `ck` int(11) NOT NULL COMMENT '仓库',
  `sssl` int(50) NOT NULL COMMENT '实收数量',
  `dj` double(50,2) NOT NULL COMMENT '单价',
  `je` double(50,2) NOT NULL COMMENT '金额',
  `sl` double(50,2) NOT NULL COMMENT '税率',
  `bz` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- ----------------------------
-- Records of dp_stock_purchase_detail
-- ----------------------------
INSERT INTO `dp_stock_purchase_detail` VALUES ('41', '47', '9', '2', '2', '2', '3.00', '50.30', '2.00', '第三方', '1527820224', '1527820224');
INSERT INTO `dp_stock_purchase_detail` VALUES ('42', '48', '9', '4', '5', '5', '6.00', '5.00', '5.00', '4', '1527844879', '1527844879');

-- ----------------------------
-- Table structure for dp_stock_restore
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_restore`;
CREATE TABLE `dp_stock_restore` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '借货返还',
  `name` varchar(255) NOT NULL COMMENT '返还主题',
  `borrowid` int(11) NOT NULL COMMENT '借货单',
  `zrid` int(11) NOT NULL COMMENT '返还人',
  `fhbm` int(11) NOT NULL COMMENT '返还部门',
  `fh_time` varchar(50) NOT NULL COMMENT '返还时间',
  `rkid` int(11) NOT NULL COMMENT '入库人',
  `zdid` int(11) NOT NULL COMMENT '制单人UID',
  `note` text COMMENT '摘要',
  `create_time` int(11) NOT NULL,
  `fileid` int(11) NOT NULL COMMENT '上传文件id',
  `helpid` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL COMMENT '上传附件路径',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dp_stock_restore
-- ----------------------------
INSERT INTO `dp_stock_restore` VALUES ('1', '奥术大师多', '2', '6', '4', '2018-06-04 00:00:00', '1', '1', '法国人郭', '1528104412', '0', '', '');

-- ----------------------------
-- Table structure for dp_stock_restore_detail
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_restore_detail`;
CREATE TABLE `dp_stock_restore_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '采购入库id',
  `itemsid` int(11) unsigned NOT NULL COMMENT '入库明细id',
  `yhsl` int(11) NOT NULL COMMENT '应还数量',
  `shsl` int(50) NOT NULL COMMENT '实还数量',
  `fhdj` int(50) NOT NULL COMMENT '返还单价',
  `fhje` double(50,2) NOT NULL COMMENT '返还金额',
  `bz` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- ----------------------------
-- Records of dp_stock_restore_detail
-- ----------------------------
INSERT INTO `dp_stock_restore_detail` VALUES ('47', '1', '9', '3', '3', '3', '3.00', '3', '1528104412', '1528104412');

-- ----------------------------
-- Table structure for dp_stock_sell
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_sell`;
CREATE TABLE `dp_stock_sell` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '销售出库',
  `name` varchar(255) NOT NULL COMMENT '出库主题',
  `deliveryid` int(11) NOT NULL COMMENT '销售发货单',
  `zrid` int(11) NOT NULL COMMENT '经办人',
  `ckid` int(11) NOT NULL COMMENT '出库人',
  `ckbm` int(11) NOT NULL COMMENT '出库部门',
  `helpid` varchar(11) NOT NULL COMMENT '可查看人员',
  `zdid` int(11) NOT NULL COMMENT '制单人UID',
  `note` text COMMENT '摘要',
  `ck_time` varchar(50) NOT NULL COMMENT '出库时间',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `path` varchar(255) NOT NULL COMMENT '上传附件路径',
  `fileid` int(11) NOT NULL COMMENT '上传文件id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dp_stock_sell
-- ----------------------------
INSERT INTO `dp_stock_sell` VALUES ('6', '暗示法规和', '32', '6', '1', '2', '4,6,10,', '1', '撒地方撒地方', '2018-06-01', '1527845034', '1527845034', '/uploads/files/20180529/5931c8e544807e014f5a906b6559dcac.txt', '6');

-- ----------------------------
-- Table structure for dp_stock_sell_detail
-- ----------------------------
DROP TABLE IF EXISTS `dp_stock_sell_detail`;
CREATE TABLE `dp_stock_sell_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '采购入库id',
  `itemsid` int(11) unsigned NOT NULL COMMENT '入库明细id',
  `bzxq` varchar(255) NOT NULL COMMENT '包装需求',
  `ck` int(11) NOT NULL COMMENT '仓库',
  `cksl` int(50) NOT NULL COMMENT '出库数量',
  `dj` double(50,2) NOT NULL COMMENT '单价',
  `je` double(50,2) NOT NULL COMMENT '金额',
  `bz` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- ----------------------------
-- Records of dp_stock_sell_detail
-- ----------------------------
INSERT INTO `dp_stock_sell_detail` VALUES ('41', '6', '9', '订单', '4', '5', '6.00', '7.00', '8', '1527845034', '1527845034');
