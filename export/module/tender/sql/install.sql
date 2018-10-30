-- -----------------------------
-- 导出时间 `2018-06-20 13:57:13`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_tender_obj`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_obj`;
CREATE TABLE `dp_tender_obj` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '投标项目填写',
  `name` varchar(255) NOT NULL COMMENT '投标项目名称',
  `start_time` int(10) NOT NULL COMMENT '计划开始日期',
  `end_time` int(10) NOT NULL COMMENT '计划结束日期',
  `address` varchar(255) NOT NULL COMMENT '项目地址',
  `info` varchar(255) NOT NULL COMMENT '项目简介',
  `obj_time` varchar(255) NOT NULL COMMENT '工程工期',
  `estimate` varchar(255) NOT NULL COMMENT '工程量估算',
  `cost` double(50,2) NOT NULL COMMENT '工程造价（元）',
  `profit` double(50,2) NOT NULL COMMENT '预期利润（元）',
  `type` int(11) NOT NULL COMMENT '项目类型',
  `zrid` int(11) NOT NULL COMMENT '项目跟踪人UID',
  `bmid` int(11) NOT NULL COMMENT '所属部门',
  `tender_time` int(10) NOT NULL COMMENT '日期',
  `unit` varchar(255) NOT NULL COMMENT '建设单位',
  `contact` varchar(255) NOT NULL COMMENT '联系人',
  `phone` varchar(255) NOT NULL COMMENT '联系人电话',
  `lxaddrss` varchar(255) NOT NULL COMMENT '联系地址',
  `lxid` int(11) NOT NULL COMMENT '立项人',
  `note` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `file` varchar(255) DEFAULT NULL COMMENT '附件',
  `code` char(50) NOT NULL COMMENT '编号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '启用状态  0 关闭 1 启用',
  `sale` int(12) NOT NULL COMMENT '销售合同',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_tender_obj`
-- -----------------------------
INSERT INTO `dp_tender_obj` VALUES ('29', '金耀公司大楼建设', '1529164800', '1528214400', '湾里区', '金耀公司大楼建设', '13', '2000000', '2000000', '2000000', '8', '3', '2', '1528992000', '建设单位', '小王', '15979138888', '湾里', '3', '嘎嘎', '1529059420', '1529397214', '30', 'XMGL20180615184340', '1', '0');
INSERT INTO `dp_tender_obj` VALUES ('26', '金耀erp', '1528819200', '1534608000', '金耀科技总公司', '啊飒飒大师', '60', '20000', '500000', '20000', '7', '2', '2', '1528819200', '大师傅的方法的发生的', '啊实打实大', '18679199741', '阿萨德地方地方', '1', '', '1528857197', '1528857197', '', 'XMGL20180613103317', '1', '0');
INSERT INTO `dp_tender_obj` VALUES ('30', '2377', '1368201600', '1368806400', '北京', '被in个', '40', '900000', '900000', '900000', '7', '6', '4', '0', '北京', '北京', '18777777777', '北京', '1', '', '1529112249', '1529112249', '', 'XMGL20180616092409', '1', '0');
INSERT INTO `dp_tender_obj` VALUES ('31', '乌拉拉的项目', '1529337600', '1529510400', '江西省南昌市湾里行政中心', '金耀科技二期项目建设', '60', '100000', '100000', '200000', '8', '1', '1', '0', '擎天柱施工队', '张三', '15270894381', '金耀科技', '1', '', '1529389005', '1529389005', '', 'XMGL20180619141645', '2', '11');

-- -----------------------------
-- 表结构 `dp_tender_hire`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_hire`;
CREATE TABLE `dp_tender_hire` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '租赁计划',
  `name` varchar(255) NOT NULL COMMENT '租赁计划名称',
  `obj_id` int(11) NOT NULL COMMENT '项目名称',
  `authorized` int(11) NOT NULL COMMENT '填报人ID',
  `note` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL COMMENT '日期',
  `file` varchar(255) DEFAULT NULL COMMENT '附件',
  `code` char(50) NOT NULL COMMENT '编号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '启用状态  0 关闭 1 启用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_tender_hire`
-- -----------------------------
INSERT INTO `dp_tender_hire` VALUES ('16', '项目工程一期租赁', '26', '1', '', '1528437917', '', '', '0');
INSERT INTO `dp_tender_hire` VALUES ('17', '金耀大楼', '29', '1', '嘎嘎', '1529059634', '', 'XMGL20180615184714', '1');

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
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_tender_hire_detail`
-- -----------------------------
INSERT INTO `dp_tender_hire_detail` VALUES ('7', '16', '9', '2', '2', '0000-00-00', '0000-00-00', '5', '6', '', '0', '0');
INSERT INTO `dp_tender_hire_detail` VALUES ('8', '17', '9', '50', '50', '0000-00-00', '0000-00-00', '50', '50', '', '0', '0');
INSERT INTO `dp_tender_hire_detail` VALUES ('9', '17', '10', '50', '50', '0000-00-00', '0000-00-00', '5', '50', '', '0', '0');
INSERT INTO `dp_tender_hire_detail` VALUES ('10', '17', '11', '50', '50', '0000-00-00', '0000-00-00', '50', '50', '', '0', '0');

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
-- 表数据 `dp_tender_lease`
-- -----------------------------

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_tender_lease_detail`
-- -----------------------------

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
-- 表数据 `dp_tender_margin`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_tender_materials`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_materials`;
CREATE TABLE `dp_tender_materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '材料需用计划',
  `name` varchar(255) NOT NULL COMMENT '计划名称',
  `obj_id` int(11) NOT NULL COMMENT '项目名称',
  `authorized` int(11) NOT NULL COMMENT '编制人UID',
  `note` varchar(255) NOT NULL,
  `create_time` int(11) NOT NULL,
  `file` varchar(255) DEFAULT NULL COMMENT '附件',
  `code` char(50) NOT NULL COMMENT '编号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '启用状态  0 关闭 1 启用',
  `status_type` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_tender_materials`
-- -----------------------------
INSERT INTO `dp_tender_materials` VALUES ('16', '工程一期钢板材料', '26', '1', '啊实打实大', '1528436100', '', '', '0', '1');
INSERT INTO `dp_tender_materials` VALUES ('17', '金耀大楼建设材料需求', '29', '1', '嘎嘎嘎', '1529059591', '', 'XMGL20180615184631', '1', '1');
INSERT INTO `dp_tender_materials` VALUES ('18', '2377', '30', '1', '2377材料需求计划', '1529112342', '', 'XMGL20180616092542', '1', '1');

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
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_tender_materials_detail`
-- -----------------------------
INSERT INTO `dp_tender_materials_detail` VALUES ('53', '16', '9', '3', '3', '6', '33', '1528436100', '1528436100');
INSERT INTO `dp_tender_materials_detail` VALUES ('54', '17', '11', '50', '50', '50', '50', '1529059591', '1529059591');
INSERT INTO `dp_tender_materials_detail` VALUES ('55', '17', '10', '50', '50', '50', '50', '1529059591', '1529059591');
INSERT INTO `dp_tender_materials_detail` VALUES ('56', '17', '9', '50', '50', '50', '50', '1529059591', '1529059591');
INSERT INTO `dp_tender_materials_detail` VALUES ('57', '18', '11', '50', '50', '50', '50', '1529112342', '1529112342');
INSERT INTO `dp_tender_materials_detail` VALUES ('58', '18', '10', '50', '50', '50', '50', '1529112342', '1529112342');
INSERT INTO `dp_tender_materials_detail` VALUES ('59', '18', '9', '50', '50', '50', '50', '1529112342', '1529112342');

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
-- 表数据 `dp_tender_plan`
-- -----------------------------

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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_tender_type`
-- -----------------------------
INSERT INTO `dp_tender_type` VALUES ('7', '金耀科技', '1', '1528366529', '1528366529');
INSERT INTO `dp_tender_type` VALUES ('8', '大型工程', '1', '1529059278', '1529059278');

-- -----------------------------
-- 表结构 `dp_tender_already_salary`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_already_salary`;
CREATE TABLE `dp_tender_already_salary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int(11) NOT NULL COMMENT '项目',
  `zdid` int(11) NOT NULL COMMENT '制单人id',
  `already` float(14,2) NOT NULL COMMENT '计划工资',
  `create_time` int(11) NOT NULL COMMENT '制单时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0待审批 1通过 2不通过 默认0',
  `s_time` varchar(50) NOT NULL COMMENT '开始时间',
  `e_time` varchar(50) NOT NULL COMMENT '结束时间',
  `big_money` varchar(255) DEFAULT NULL COMMENT '大写金额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_tender_already_salary`
-- -----------------------------
INSERT INTO `dp_tender_already_salary` VALUES ('5', '29', '1', '12000', '1529467106', '0', '2018-07-20', '2018-08-20', '壹万贰仟圆');
INSERT INTO `dp_tender_already_salary` VALUES ('4', '29', '1', '10000', '1529467016', '0', '2018-06-20', '2018-07-20', '壹万');
INSERT INTO `dp_tender_already_salary` VALUES ('6', '26', '1', '10000', '1529470083', '0', '2018-06-20', '2018-07-20', '壹万');

-- -----------------------------
-- 表结构 `dp_tender_contract_hire`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_contract_hire`;
CREATE TABLE `dp_tender_contract_hire` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '租赁合同',
  `number` char(50) NOT NULL COMMENT '合同编号',
  `name` varchar(255) NOT NULL COMMENT '合同名称',
  `plan` int(10) NOT NULL COMMENT '租赁计划',
  `obj_id` int(10) NOT NULL COMMENT '所属项目',
  `money` varchar(12) NOT NULL COMMENT '合同金额',
  `ctype` int(10) NOT NULL COMMENT '合同类型 1租赁合同 2总承包租赁合同',
  `sdate` varchar(20) NOT NULL COMMENT '开始日期',
  `edate` varchar(20) NOT NULL COMMENT '结束日期',
  `supplier` int(10) NOT NULL COMMENT '供应商',
  `ftype` int(10) NOT NULL COMMENT '结算方式 1分段结算 2竣工后一次结算 3进度款结算',
  `paytype` int(10) NOT NULL COMMENT '1按合同付款  2按进度付款',
  `premoney` float(12,2) DEFAULT NULL COMMENT '预付款',
  `bzmoney` float(12,2) DEFAULT NULL COMMENT '保证金',
  `people` int(10) DEFAULT NULL COMMENT '参与人员',
  `authorized` int(255) DEFAULT NULL COMMENT '签订人',
  `path` int(11) DEFAULT NULL COMMENT '上传文件路径',
  `note` varchar(255) DEFAULT NULL COMMENT '付款条件',
  `notes` varchar(255) DEFAULT NULL COMMENT '主要条款',
  `create_time` int(11) NOT NULL,
  `create_uid` int(11) unsigned NOT NULL,
  `start_time` varchar(20) NOT NULL,
  `end_time` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `big_money` varchar(255) DEFAULT NULL COMMENT '金额大写',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_tender_contract_hire`
-- -----------------------------
INSERT INTO `dp_tender_contract_hire` VALUES ('45', 'HT1122333', '哇啦啦', '16', '29', '1,000,000', '1', '', '', '5', '2', '2', '100000', '100000', '1', '1', '', '', '', '1529393966', '1', '2018-06-19', '2018-07-07', '1', '壹佰万');

-- -----------------------------
-- 表结构 `dp_tender_contract_hire_detail`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_contract_hire_detail`;
CREATE TABLE `dp_tender_contract_hire_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
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
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_tender_contract_hire_detail`
-- -----------------------------
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('1', '13', '9', '30', '10', '2018-05-31', '2018-06-03', '4', '1200', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('2', '7', '9', '100', '10', '2018-05-31', '2018-06-03', '4', '4000', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('3', '8', '9', '100', '100', '2018-05-31', '2018-06-10', '11', '11000000', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('4', '21', '9', '3213', '321321', '0000-00-00', '0000-00-00', '321', '3213213', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('5', '22', '9', '211', '111', '2018-06-04', '2018-06-10', '7', '111111', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('6', '23', '10', '700', '3', '2018-06-04', '2018-06-14', '10', '210000', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('7', '24', '9', '100', '100', '2018-06-04', '2018-06-09', '5', '50000', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('8', '25', '9', '100', '10', '2018-06-05', '2018-06-09', '5', '5000', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('9', '26', '9', '100', '100', '2018-05-30', '2018-05-04', '5', '100', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('10', '27', '9', '0', '0', '0000-00-00', '0000-00-00', '', '', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('11', '27', '10', '0', '0', '0000-00-00', '0000-00-00', '', '', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('12', '28', '9', '12', '12', '2018-06-05', '2018-06-06', '12', '12', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('13', '29', '9', '32', '23', '2018-06-07', '2018-06-15', '32', '32', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('14', '30', '9', '0', '0', '0000-00-00', '0000-00-00', '', '', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('15', '31', '9', '12', '12', '2018-06-07', '2018-06-14', '21', '12', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('16', '32', '9', '21', '21', '2018-06-04', '2018-06-05', '21', '21', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('17', '36', '9', '12', '21', '2018-06-14', '2018-06-07', '21', '21', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('18', '37', '9', '2', '21', '2018-06-06', '2018-06-14', '2', '21', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('19', '38', '9', '55', '55', '2018-06-11', '2018-06-12', '55', '54', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('20', '39', '9', '12', '12', '2018-06-11', '2018-06-12', '1', '12', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('21', '40', '9', '3213', '312312', '2018-06-15', '2018-06-22', '32', '2322', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('22', '41', '10', '312', '2131', '2018-06-05', '2018-06-13', '231', '321', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('23', '42', '9', '111', '111', '2018-06-04', '2018-06-20', '11', '111', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('24', '43', '9', '3213', '321', '2018-06-06', '2018-06-28', '22', '10000', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('25', '44', '10', '323', '321312', '2018-06-19', '2018-06-20', '1', '11111', '', '0', '0');
INSERT INTO `dp_tender_contract_hire_detail` VALUES ('26', '45', '9', '3213', '3123', '2018-06-28', '2018-06-30', '3', '111111', '', '0', '0');

-- -----------------------------
-- 表结构 `dp_tender_fact_salary`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_fact_salary`;
CREATE TABLE `dp_tender_fact_salary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int(11) NOT NULL COMMENT '项目',
  `zdid` int(11) NOT NULL COMMENT '制单人id',
  `fact` double(14,2) NOT NULL COMMENT '实发工资',
  `create_time` int(11) NOT NULL COMMENT '制单时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0待审批 1通过 2不通过 默认0',
  `s_time` varchar(50) NOT NULL COMMENT '开始时间',
  `e_time` varchar(50) NOT NULL COMMENT '结束时间',
  `big_money` varchar(255) DEFAULT NULL COMMENT '大写金额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_tender_fact_salary`
-- -----------------------------
INSERT INTO `dp_tender_fact_salary` VALUES ('4', '29', '1', '10000', '1529467076', '0', '2018-06-20', '2018-07-20', '壹万');
INSERT INTO `dp_tender_fact_salary` VALUES ('5', '26', '1', '0', '1529470096', '0', '2018-06-20', '2018-07-20', '零');

-- -----------------------------
-- 表结构 `dp_tender_salary`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_salary`;
CREATE TABLE `dp_tender_salary` (
  `id` int(11) NOT NULL,
  `obj_id` int(11) NOT NULL COMMENT '项目',
  `zdid` int(11) NOT NULL COMMENT '制单人id',
  `already` float(14,2) NOT NULL COMMENT '计划工资',
  `fact` float(14,2) NOT NULL COMMENT '实际工资',
  `create_time` int(11) NOT NULL COMMENT '制单时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0待审批 1通过 2不通过 默认0',
  `s_time` int(11) NOT NULL COMMENT '开始时间',
  `e_time` int(11) NOT NULL COMMENT '结束时间',
  `big_money` varchar(255) DEFAULT NULL COMMENT '大写金额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_tender_salary`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_tender_schedule`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_schedule`;
CREATE TABLE `dp_tender_schedule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int(10) unsigned NOT NULL COMMENT '项目id',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_tender_schedule`
-- -----------------------------
INSERT INTO `dp_tender_schedule` VALUES ('3', '26', '0');
INSERT INTO `dp_tender_schedule` VALUES ('2', '26', '0');

-- -----------------------------
-- 表结构 `dp_tender_schedule_detail`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_schedule_detail`;
CREATE TABLE `dp_tender_schedule_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `schedule_id` int(10) unsigned NOT NULL COMMENT '项目id',
  `bh` varchar(50) DEFAULT NULL COMMENT '编号',
  `gcmx` varchar(100) DEFAULT NULL COMMENT '工程明细',
  `dw` varchar(20) DEFAULT NULL COMMENT '单位',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '工程量',
  `dj` double(50,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '单价',
  `sum` double(50,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总和总价',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_tender_schedule_detail`
-- -----------------------------
INSERT INTO `dp_tender_schedule_detail` VALUES ('1', '2', '50', '50', '50', '50', '50', '2500');
INSERT INTO `dp_tender_schedule_detail` VALUES ('2', '3', '1.1', '桥墩建设', '个', '4', '200000', '800000');
INSERT INTO `dp_tender_schedule_detail` VALUES ('3', '3', '', '', '', '0', '0', '0');

-- -----------------------------
-- 表结构 `dp_tender_schedule_over`
-- -----------------------------
DROP TABLE IF EXISTS `dp_tender_schedule_over`;
CREATE TABLE `dp_tender_schedule_over` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int(10) unsigned NOT NULL COMMENT '项目id',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL COMMENT '名称',
  `wid` int(10) unsigned NOT NULL COMMENT '填报人',
  `create_time` int(10) unsigned NOT NULL,
  `number` varchar(50) NOT NULL COMMENT '编号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_tender_schedule_over`
-- -----------------------------
INSERT INTO `dp_tender_schedule_over` VALUES ('3', '26', '0', '', '0', '0', '');
INSERT INTO `dp_tender_schedule_over` VALUES ('2', '26', '0', '', '0', '0', '');
