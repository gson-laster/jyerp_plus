-- -----------------------------
-- 导出时间 `2018-06-07 09:07:08`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_contract_hire`
-- -----------------------------
DROP TABLE IF EXISTS `dp_contract_hire`;
CREATE TABLE `dp_contract_hire` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '租赁合同',
  `number` char(50) NOT NULL COMMENT '合同编号',
  `name` varchar(255) NOT NULL COMMENT '合同名称',
  `plan` int(10) NOT NULL COMMENT '租赁计划',
  `obj_id` int(10) NOT NULL COMMENT '所属项目',
  `money` varchar(12) NOT NULL COMMENT '合同金额',
  `ctype` int(10) NOT NULL COMMENT '合同类型 1租赁合同 2总承包租赁合同',
  `sdate` date NOT NULL COMMENT '开始日期',
  `edate` date NOT NULL COMMENT '结束日期',
  `supplier` int(10) NOT NULL COMMENT '供应商',
  `ftype` int(10) NOT NULL COMMENT '结算方式 1分段结算 2竣工后一次结算 3进度款结算',
  `paytype` int(10) NOT NULL COMMENT '1按合同付款  2按进度付款',
  `premoney` float(12,2) DEFAULT NULL COMMENT '预付款',
  `bzmoney` float(12,2) DEFAULT NULL COMMENT '保证金',
  `people` int(10) DEFAULT NULL COMMENT '参与人员',
  `authorized` int(255) DEFAULT NULL COMMENT '签订人',
  `fileid` int(11) NOT NULL COMMENT '上传文件ID',
  `path` varchar(255) NOT NULL COMMENT '上传文件路径',
  `note` varchar(255) DEFAULT NULL COMMENT '付款条件',
  `notes` varchar(255) DEFAULT NULL COMMENT '主要条款',
  `create_time` int(11) NOT NULL,
  `date` date NOT NULL COMMENT '日期',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_contract_hire`
-- -----------------------------
INSERT INTO `dp_contract_hire` VALUES ('26', '51463', 'dasdas', '14', '23', '320,000,000', '1', '0000-00-00', '0000-00-00', '3', '3', '2', '20000', '200', '300', '1', '0', '/static/admin/img/none.png', '', '', '1528164824', '2018-06-05');
INSERT INTO `dp_contract_hire` VALUES ('25', 'ZLHT11223344', '租赁遮阳伞', '15', '23', '52，000，000', '1', '0000-00-00', '0000-00-00', '4', '2', '2', '200000', '100000', '0', '1', '0', '/static/admin/img/none.png', '竣工', '竣工结款', '1528164134', '2018-06-05');

-- -----------------------------
-- 表结构 `dp_contract_hire_detail`
-- -----------------------------
DROP TABLE IF EXISTS `dp_contract_hire_detail`;
CREATE TABLE `dp_contract_hire_detail` (
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
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_contract_hire_detail`
-- -----------------------------
INSERT INTO `dp_contract_hire_detail` VALUES ('1', '13', '9', '30', '10', '2018-05-31', '2018-06-03', '4', '1200', '', '0', '0');
INSERT INTO `dp_contract_hire_detail` VALUES ('2', '7', '9', '100', '10', '2018-05-31', '2018-06-03', '4', '4000', '', '0', '0');
INSERT INTO `dp_contract_hire_detail` VALUES ('3', '8', '9', '100', '100', '2018-05-31', '2018-06-10', '11', '11000000', '', '0', '0');
INSERT INTO `dp_contract_hire_detail` VALUES ('4', '21', '9', '3213', '321321', '0000-00-00', '0000-00-00', '321', '3213213', '', '0', '0');
INSERT INTO `dp_contract_hire_detail` VALUES ('5', '22', '9', '211', '111', '2018-06-04', '2018-06-10', '7', '111111', '', '0', '0');
INSERT INTO `dp_contract_hire_detail` VALUES ('6', '23', '10', '700', '3', '2018-06-04', '2018-06-14', '10', '210000', '', '0', '0');
INSERT INTO `dp_contract_hire_detail` VALUES ('7', '24', '9', '100', '100', '2018-06-04', '2018-06-09', '5', '50000', '', '0', '0');
INSERT INTO `dp_contract_hire_detail` VALUES ('8', '25', '9', '100', '10', '2018-06-05', '2018-06-09', '5', '5000', '', '0', '0');
INSERT INTO `dp_contract_hire_detail` VALUES ('9', '26', '9', '100', '100', '2018-05-30', '2018-05-04', '5', '100', '', '0', '0');

-- -----------------------------
-- 表结构 `dp_contract_income`
-- -----------------------------
DROP TABLE IF EXISTS `dp_contract_income`;
CREATE TABLE `dp_contract_income` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `pid` int(11) NOT NULL COMMENT '收入id',
  `itemsid` int(11) unsigned NOT NULL COMMENT '工程明细id',
  `qdzm` varchar(50) NOT NULL COMMENT '清单子目',
  `dw` double(50,2) NOT NULL COMMENT '单位',
  `test_num` float(50,2) NOT NULL COMMENT '工程量',
  `zhdj` float(50,2) NOT NULL COMMENT '综合单价',
  `sum` float(50,2) DEFAULT NULL COMMENT '合价',
  `bz` varchar(255) DEFAULT NULL COMMENT '备注',
  `fileid` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_contract_income`
-- -----------------------------
INSERT INTO `dp_contract_income` VALUES ('1', '0', '0', '', '0', '0', '0', '0', '', '0', '1527845017');
INSERT INTO `dp_contract_income` VALUES ('2', '0', '0', '', '0', '0', '0', '0', '', '0', '1527845037');
INSERT INTO `dp_contract_income` VALUES ('3', '0', '0', '', '0', '0', '0', '0', '', '0', '1527845053');
INSERT INTO `dp_contract_income` VALUES ('4', '0', '0', '', '0', '0', '0', '0', '', '0', '1527845150');
INSERT INTO `dp_contract_income` VALUES ('5', '0', '0', '', '0', '0', '0', '0', '', '0', '1527845195');
INSERT INTO `dp_contract_income` VALUES ('6', '0', '0', '', '0', '0', '0', '0', '', '0', '1527845205');
INSERT INTO `dp_contract_income` VALUES ('7', '0', '0', '', '0', '0', '0', '0', '', '0', '1527845236');

-- -----------------------------
-- 表结构 `dp_contract_list`
-- -----------------------------
DROP TABLE IF EXISTS `dp_contract_list`;
CREATE TABLE `dp_contract_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL COMMENT '日期',
  `number` varchar(255) NOT NULL COMMENT '合同编号',
  `title` varchar(255) NOT NULL COMMENT '合同名称',
  `attach_item` int(10) NOT NULL COMMENT '归属项目',
  `type` varchar(255) NOT NULL COMMENT '类型',
  `begin_date` int(11) NOT NULL COMMENT '开始时间',
  `end_date` int(11) NOT NULL COMMENT '结束时间',
  `money` varchar(255) NOT NULL COMMENT '合同金额',
  `big` varchar(255) NOT NULL COMMENT '金额(大写)',
  `nail` varchar(255) NOT NULL COMMENT '甲方',
  `second_party` varchar(255) NOT NULL COMMENT '乙方',
  `operator` varchar(255) NOT NULL COMMENT '签订人',
  `pay_type` int(255) NOT NULL COMMENT '付款方式',
  `balance` int(255) NOT NULL COMMENT '结算方式',
  `advances_received` float(255,0) NOT NULL COMMENT '预收款',
  `bail` float(255,0) NOT NULL COMMENT '保证金',
  `collection_terms` text COMMENT '收款条件',
  `main_requirements` text,
  `note` text COMMENT '备注',
  `path` varchar(255) DEFAULT NULL COMMENT '文件路径',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_contract_list`
-- -----------------------------
INSERT INTO `dp_contract_list` VALUES ('2', '1527696000', 'CGXJ201805310531391', '21', '12', '12', '1527696000', '1527523200', '21', '12', '12', '12', '12', '21', '12', '21', '21', '12', '12', '12', '');
INSERT INTO `dp_contract_list` VALUES ('3', '1527696000', 'CGXJ201805310609231', '23', '32', '32', '1527696000', '1527696000', '23', '23', '32', '23', '23', '32', '23', '32', '32', '23', '32', '32', '');

-- -----------------------------
-- 表结构 `dp_contract_materials`
-- -----------------------------
DROP TABLE IF EXISTS `dp_contract_materials`;
CREATE TABLE `dp_contract_materials` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '材料合同',
  `number` char(50) NOT NULL COMMENT '合同编号',
  `name` varchar(255) NOT NULL COMMENT '合同名称',
  `stype` int(10) NOT NULL COMMENT '源单类型',
  `snumber` int(10) NOT NULL COMMENT '源单号',
  `ctype` int(10) NOT NULL COMMENT '合同类型',
  `obj_id` int(10) NOT NULL COMMENT '所属项目',
  `sdate` date NOT NULL COMMENT '开始日期',
  `edate` date NOT NULL COMMENT '结束日期',
  `money` float(12,2) NOT NULL COMMENT '合同金额',
  `premoney` float(12,2) DEFAULT NULL COMMENT '预付款',
  `bzmoney` float(12,2) DEFAULT NULL COMMENT '保证金',
  `supplier` int(10) NOT NULL COMMENT '供应商',
  `supplier_w` varchar(255) DEFAULT NULL COMMENT '供应商签约人',
  `is_add` int(10) DEFAULT NULL COMMENT '是否为增值税',
  `paytype` int(10) NOT NULL COMMENT '支付方式1现金 2转账 3支票 4微信',
  `ftype` int(10) NOT NULL COMMENT '结算方式 1分段结算 2合同结算3进度结算 4竣工后一次结算 ',
  `handle_type` int(10) DEFAULT NULL COMMENT '交货方式 0分批 1一次性',
  `place` varchar(255) DEFAULT NULL COMMENT '签约地点',
  `people` int(255) DEFAULT NULL COMMENT '我方签订人',
  `authorized` int(10) NOT NULL COMMENT '录入人',
  `fileid` int(11) NOT NULL COMMENT '上传文件ID',
  `path` varchar(255) NOT NULL COMMENT '上传文件路径',
  `note` varchar(255) DEFAULT NULL COMMENT '付款条件',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `notes` varchar(255) DEFAULT NULL COMMENT '主要条款',
  `create_time` int(11) NOT NULL,
  `date` date NOT NULL COMMENT '日期',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_contract_materials`
-- -----------------------------
INSERT INTO `dp_contract_materials` VALUES ('2', 'dsadsad', 'dsadassa', '2', '0', '1', '23', '2018-06-01', '2018-06-01', '1000000', '10000', '10000', '4', 'dsad', '0', '3', '2', '1', 'dasdas', '0', '0', '0', '/static/admin/img/none.png', '', '', '', '1527825708', '2018-06-01');
INSERT INTO `dp_contract_materials` VALUES ('3', 'dsddsa', '深圳前海找家电信息科技有限公司', '2', '0', '1', '23', '2018-06-04', '2018-06-10', '100000', '1000', '100', '4', '大大', '0', '3', '2', '1', '大大', '0', '1', '0', '/static/admin/img/none.png', '', '', '', '1528092979', '2018-06-04');

-- -----------------------------
-- 表结构 `dp_contract_materials_detail`
-- -----------------------------
DROP TABLE IF EXISTS `dp_contract_materials_detail`;
CREATE TABLE `dp_contract_materials_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '采购入库id',
  `itemsid` int(11) unsigned NOT NULL COMMENT '入库明细id',
  `xysl` int(50) NOT NULL COMMENT '采购数量数量',
  `ckjg` double(50,2) NOT NULL COMMENT '采购价格',
  `tax` float(5,2) DEFAULT NULL COMMENT '税率',
  `havetax` float(50,2) DEFAULT NULL COMMENT '含税金额',
  `notax` float(50,2) DEFAULT NULL COMMENT '不含税金额',
  `xj` int(50) NOT NULL COMMENT '小计',
  `bz` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_contract_materials_detail`
-- -----------------------------
INSERT INTO `dp_contract_materials_detail` VALUES ('1', '11', '9', '100', '10', '0', '0', '0', '1000', '', '0', '0');
INSERT INTO `dp_contract_materials_detail` VALUES ('2', '1', '9', '100', '100', '0', '0', '0', '1000', '', '0', '0');
INSERT INTO `dp_contract_materials_detail` VALUES ('3', '2', '9', '1', '10', '0', '0', '0', '10', '', '0', '0');
INSERT INTO `dp_contract_materials_detail` VALUES ('4', '3', '9', '321', '321', '321', '312', '312', '2147483647', '', '0', '0');
