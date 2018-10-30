-- -----------------------------
-- 导出时间 `2018-06-06 18:15:44`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_tender_obj`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_obj`;
CREATE TABLE `dp_tender_obj` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '投标项目填写',
  `name` varchar(255) NOT NULL COMMENT '投标项目名称',
  `start_time` varchar(50) NOT NULL COMMENT '计划开始日期',
  `end_time` varchar(50) NOT NULL COMMENT '计划结束日期',
  `address` varchar(255) NOT NULL COMMENT '项目地址',
  `info` varchar(255) NOT NULL COMMENT '项目简介',
  `obj_time` varchar(255) NOT NULL COMMENT '工程工期',
  `estimate` varchar(255) NOT NULL COMMENT '工程量估算',
  `cost` double(50,2) NOT NULL COMMENT '工程造价（元）',
  `profit` double(50,2) NOT NULL COMMENT '预期利润（元）',
  `type` int(11) NOT NULL COMMENT '项目类型',
  `zrid` int(11) NOT NULL COMMENT '项目跟踪人UID',
  `bmid` int(11) NOT NULL COMMENT '所属部门',
  `tender_time` datetime NOT NULL COMMENT '日期',
  `unit` varchar(255) NOT NULL COMMENT '建设单位',
  `contact` varchar(255) NOT NULL COMMENT '联系人',
  `phone` varchar(255) NOT NULL COMMENT '联系人电话',
  `lxaddrss` varchar(255) NOT NULL COMMENT '联系地址',
  `lxid` int(11) NOT NULL COMMENT '立项人',
  `note` varchar(255) NOT NULL COMMENT '备注',
  `fileid` int(11) NOT NULL COMMENT '上传文件id',
  `path` varchar(255) NOT NULL COMMENT '上传附件路径',
  `status` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `dp_tender_hire`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_hire`;
CREATE TABLE `dp_tender_hire` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '租赁计划',
  `name` varchar(255) NOT NULL COMMENT '租赁计划名称',
  `obj_id` int(11) NOT NULL COMMENT '项目名称',
  `authorized` int(11) NOT NULL COMMENT '填报人ID',
  `remark` varchar(255) DEFAULT NULL COMMENT '用途',
  `fileid` varchar(255) DEFAULT NULL COMMENT '上传文件ID',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  `path` varchar(255) DEFAULT NULL COMMENT '上传附件路径',
  `create_time` int(11) NOT NULL COMMENT '日期',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `dp_tender_hire_detail`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_hire_detail`;
CREATE TABLE `dp_tender_hire_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL COMMENT '租赁id',
  `itemsid` int(11) unsigned NOT NULL COMMENT '租赁明细id',
  `xysl` int(50) NOT NULL COMMENT '需用数量',
  `ckjg` float(20,2) NOT NULL COMMENT '单价',
  `sdate` date NOT NULL COMMENT '计划进场日期',
  `edate` date NOT NULL COMMENT '计划出场日期',
  `hire_day` varchar(255) NOT NULL COMMENT '计划租赁天数',
  `xj` varchar(255) NOT NULL COMMENT '小计',
  `bz` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `dp_tender_lease`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_lease`;
CREATE TABLE `dp_tender_lease` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '租赁',
  `name` varchar(255) NOT NULL COMMENT '租赁计划名称',
  `obj_id` int(11) NOT NULL COMMENT '项目名称',
  `fileid` int(11) NOT NULL COMMENT '上传文件id',
  `path` varchar(255) NOT NULL COMMENT '上传附件路径',
  `note` text COMMENT '用途',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `dp_tender_lease_detail`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_lease_detail`;
CREATE TABLE `dp_tender_lease_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '采购入库id',
  `itemsid` int(11) unsigned NOT NULL COMMENT '入库明细id',
  `sl` int(50) NOT NULL COMMENT '需用数量',
  `jc_time` datetime NOT NULL COMMENT '计划进场日期',
  `tc_time` datetime NOT NULL COMMENT '计划退场日期',
  `zlts` varchar(255) NOT NULL COMMENT '租赁天数',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COMMENT='签到分享表';


-- -----------------------------
-- 表结构 `dp_tender_margin`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_margin`;
CREATE TABLE `dp_tender_margin` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '投标保证金',
  `applicant` int(11) NOT NULL COMMENT '申请人',
  `name` int(11) NOT NULL COMMENT '项目名称，选',
  `type` varchar(255) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `bank` varchar(255) NOT NULL COMMENT '开户行',
  `account` varchar(255) NOT NULL COMMENT '账户',
  `money` double(11,0) NOT NULL COMMENT '保证金金额',
  `item_time` varchar(50) NOT NULL COMMENT '交款日期',
  `back_time` varchar(50) NOT NULL COMMENT '预计退回日期',
  `note` varchar(255) NOT NULL COMMENT '备注',
  `fileid` int(11) NOT NULL,
  `path` varchar(255) NOT NULL COMMENT '文件路径',
  `status` int(11) NOT NULL,
  `creata_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `dp_tender_materials`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_materials`;
CREATE TABLE `dp_tender_materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '材料需用计划',
  `name` varchar(255) NOT NULL COMMENT '计划名称',
  `obj_id` int(11) NOT NULL COMMENT '项目名称',
  `authorized` int(11) NOT NULL COMMENT '编制人UID',
  `fileid` int(11) NOT NULL COMMENT '上传文件id',
  `path` varchar(255) NOT NULL COMMENT '上传附件路径',
  `note` varchar(255) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `dp_tender_materials_detail`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_materials_detail`;
CREATE TABLE `dp_tender_materials_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '采购入库id',
  `itemsid` int(11) unsigned NOT NULL COMMENT '入库明细id',
  `xysl` int(50) NOT NULL COMMENT '需用数量',
  `ckjg` double(50,2) NOT NULL COMMENT '参考价格',
  `xj` int(50) NOT NULL COMMENT '小计',
  `bz` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COMMENT='签到分享表';


-- -----------------------------
-- 表结构 `dp_tender_plan`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_plan`;
CREATE TABLE `dp_tender_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '招标文件购买申请',
  `applicant` int(11) NOT NULL COMMENT '申请人',
  `name` int(11) NOT NULL COMMENT '项目名称-从投标项目里选',
  `type` varchar(255) NOT NULL COMMENT '选完名称自动显示，不可写',
  `unit` varchar(255) NOT NULL,
  `money` double(255,0) NOT NULL COMMENT '招标文件费用',
  `time` varchar(20) NOT NULL COMMENT '申请日期',
  `note` varchar(255) NOT NULL COMMENT '备注',
  `fileid` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `status` int(11) NOT NULL COMMENT '审批状态：1-完成0-失败',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `dp_tender_type`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_type`;
CREATE TABLE `dp_tender_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '项目类型',
  `name` varchar(255) NOT NULL COMMENT '类型名称',
  `status` int(11) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

