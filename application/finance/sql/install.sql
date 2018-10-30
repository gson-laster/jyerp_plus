-- -----------------------------
-- 导出时间 `2018-06-07 15:14:02`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_finance_accmount`
-- -----------------------------
DROP TABLE IF EXISTS `dp_finance_accmount`;
CREATE TABLE `dp_finance_accmount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '账户名称',
  `accmount` varchar(255) NOT NULL COMMENT '账户',
  `bank` varchar(255) NOT NULL COMMENT '开户银行',
  `first_money` varchar(255) NOT NULL COMMENT '期初金额',
  `big_money` varchar(255) NOT NULL COMMENT '金额大写',
  `operator` varchar(255) NOT NULL COMMENT '录入人',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='财务管理-账户期初';

-- -----------------------------
-- 表数据 `dp_finance_accmount`
-- -----------------------------
INSERT INTO `dp_finance_accmount` VALUES ('4', '4', '12', '21', '21', '21', '4', '1528161808', '1528161808');
INSERT INTO `dp_finance_accmount` VALUES ('5', '2', '12', '12', '12', '12', '2', '1528161829', '1528161829');
INSERT INTO `dp_finance_accmount` VALUES ('6', '4', '12', '12', '12', '12', '2', '1528272889', '1528272889');

-- -----------------------------
-- 表结构 `dp_finance_gather`
-- -----------------------------
DROP TABLE IF EXISTS `dp_finance_gather`;
CREATE TABLE `dp_finance_gather` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `number` char(20) NOT NULL COMMENT '收款编号',
  `pact` int(10) NOT NULL COMMENT '合同名称',
  `name` int(10) NOT NULL COMMENT '收款人',
  `money` float(12,0) NOT NULL COMMENT '收款金额',
  `supplier` int(10) NOT NULL COMMENT '供货商',
  `gtype` int(255) NOT NULL COMMENT '收款类型',
  `account` int(255) NOT NULL COMMENT '公司账户',
  `maker` varchar(255) NOT NULL COMMENT '录入人',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `file` varchar(255) DEFAULT NULL COMMENT '附件',
  `date` date NOT NULL COMMENT '收款日期',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- -----------------------------
-- 表数据 `dp_finance_gather`
-- -----------------------------
INSERT INTO `dp_finance_gather` VALUES ('3', 'SKD20180529150615', '2', '4', '1000', '3', '3', '2', '超级管理员', '', '', '2018-05-29');
INSERT INTO `dp_finance_gather` VALUES ('2', 'SKD20180529112942', '3', '3', '15550', '3', '3', '2', '超级管理员', '', '', '2018-05-30');
INSERT INTO `dp_finance_gather` VALUES ('4', 'SKD20180607121439', '24', '0', '21', '0', '1', '4', '1', '21', '', '2018-06-07');

-- -----------------------------
-- 表结构 `dp_finance_hire`
-- -----------------------------
DROP TABLE IF EXISTS `dp_finance_hire`;
CREATE TABLE `dp_finance_hire` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `number` char(20) NOT NULL COMMENT '付款编号',
  `name` varchar(255) NOT NULL COMMENT '付款名称',
  `pact` int(10) NOT NULL COMMENT '租赁合同',
  `item` int(10) NOT NULL COMMENT '所属项目',
  `supplier` int(10) NOT NULL COMMENT '供应商',
  `bank_name` int(255) NOT NULL COMMENT '开户行名称',
  `accmount` varchar(255) NOT NULL COMMENT '银行账户',
  `money` float(12,0) NOT NULL COMMENT '付款金额',
  `maker` varchar(255) NOT NULL COMMENT '填报人',
  `remark` varchar(255) DEFAULT NULL COMMENT '付款说明',
  `file` varchar(255) DEFAULT NULL COMMENT '附件',
  `date` date NOT NULL COMMENT '日期',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- -----------------------------
-- 表数据 `dp_finance_hire`
-- -----------------------------
INSERT INTO `dp_finance_hire` VALUES ('1', 'ZL20180528102457', '租借器材', '4', '1', '2', '3', '2222200002', '1000', '超级管理员', '支付租借器材', '', '2018-05-28');
INSERT INTO `dp_finance_hire` VALUES ('2', 'ZL20180528105149', '租借遮阳伞', '2', '3', '2', '2', '12011101232416', '1000', '超级管理员', '', '', '2018-05-30');
INSERT INTO `dp_finance_hire` VALUES ('3', 'ZL20180606111200', '12', '2', '24', '24', '4', '4', '12', '超级管理员', '12', '', '2018-06-06');
INSERT INTO `dp_finance_hire` VALUES ('4', 'ZL20180606145216', '12', '2', '24', '24', '10', '10', '12', '超级管理员', '12', '', '2018-06-06');
INSERT INTO `dp_finance_hire` VALUES ('5', 'ZL20180607091037', '32', '4', '24', '24', '11', '11', '32', '超级管理员', '32', '', '2018-06-07');
INSERT INTO `dp_finance_hire` VALUES ('6', 'ZL20180607113411', '21', '4', '24', '24', '10', '10', '21', '1', '21', '', '2018-06-07');

-- -----------------------------
-- 表结构 `dp_finance_info`
-- -----------------------------
DROP TABLE IF EXISTS `dp_finance_info`;
CREATE TABLE `dp_finance_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `number` char(20) NOT NULL COMMENT '报销编号',
  `title` varchar(255) NOT NULL COMMENT '报销名称',
  `name` int(10) NOT NULL COMMENT '报销人',
  `item` int(10) NOT NULL COMMENT '所属项目',
  `depot` int(10) NOT NULL COMMENT '部门',
  `work` int(10) NOT NULL COMMENT '职位',
  `project` int(10) NOT NULL COMMENT '报销科目',
  `money` int(10) NOT NULL COMMENT '报销金额',
  `bx_time` date NOT NULL COMMENT '报销时间',
  `sum` char(255) NOT NULL COMMENT '累计报销',
  `maker` varchar(255) NOT NULL COMMENT '填报人',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `file` varchar(255) DEFAULT NULL COMMENT '附件',
  `time` date NOT NULL COMMENT '开单日期',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- -----------------------------
-- 表数据 `dp_finance_info`
-- -----------------------------
INSERT INTO `dp_finance_info` VALUES ('2', 'FYBX201805', '出差', '3', '2', '2', '4', '2', '100', '2018-05-26', '100', '超级管理员', '', '', '2018-05-25');
INSERT INTO `dp_finance_info` VALUES ('3', 'FYBX20180525162445', '出公差修改', '2', '1', '4', '12', '2', '100000', '2018-06-21', '1000000', '超级管理员', '', '', '2018-05-25');
INSERT INTO `dp_finance_info` VALUES ('4', 'FYBX20180601102329', '12', '3', '24', '2', '7', '5', '21', '2018-06-08', '2', '超级管理员', '21', '', '2018-06-01');
INSERT INTO `dp_finance_info` VALUES ('5', 'FYBX20180601102352', '21', '3', '24', '5', '5', '3', '12', '2018-06-14', '12', '超级管理员', '12', '', '2018-06-01');

-- -----------------------------
-- 表结构 `dp_finance_manager`
-- -----------------------------
DROP TABLE IF EXISTS `dp_finance_manager`;
CREATE TABLE `dp_finance_manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '账户昵称',
  `accmount` varchar(255) NOT NULL COMMENT '账户',
  `bank` varchar(255) NOT NULL COMMENT '开户银行',
  `address` varchar(255) NOT NULL COMMENT '地址',
  `date` int(11) NOT NULL COMMENT '开户日期',
  `operator` varchar(255) NOT NULL COMMENT '经办人',
  `ismoneyaccount` int(11) NOT NULL COMMENT '是否现金账户 0 不是, 1 是',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '是否停用',
  `note` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='财务管理-账户信息';

-- -----------------------------
-- 表数据 `dp_finance_manager`
-- -----------------------------
INSERT INTO `dp_finance_manager` VALUES ('4', '12', '12', '12', '12', '1528905600', '3', '1', '1', '12', '1528103358', '1528103358');
INSERT INTO `dp_finance_manager` VALUES ('11', '12', '12', '21', '12', '1528819200', '3', '1', '1', '12', '1528272876', '1528272876');
INSERT INTO `dp_finance_manager` VALUES ('10', '213', '32', '32', '32', '1529424000', '3', '1', '1', '32', '1528250556', '1528250556');
INSERT INTO `dp_finance_manager` VALUES ('9', '32', '32', '32', '32', '1528819200', '7', '1', '1', '32', '1528249111', '1528249111');

-- -----------------------------
-- 表结构 `dp_finance_other`
-- -----------------------------
DROP TABLE IF EXISTS `dp_finance_other`;
CREATE TABLE `dp_finance_other` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `number` char(20) NOT NULL COMMENT '付款编号',
  `payer` int(10) NOT NULL COMMENT '付款人',
  `part` int(10) NOT NULL COMMENT '部门',
  `supplier` int(10) NOT NULL COMMENT '供应商',
  `account` int(10) NOT NULL COMMENT '公司账户',
  `money` float(12,0) NOT NULL COMMENT '付款金额',
  `ptype` int(10) NOT NULL COMMENT '付款类型',
  `pway` int(10) NOT NULL COMMENT '支付方式',
  `maker` varchar(255) NOT NULL COMMENT '经办人',
  `item` int(10) NOT NULL COMMENT '项目',
  `date` date NOT NULL COMMENT '付款日期',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `file` varchar(255) DEFAULT NULL COMMENT '附件',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- -----------------------------
-- 表数据 `dp_finance_other`
-- -----------------------------
INSERT INTO `dp_finance_other` VALUES ('5', 'FKD20180607103548', '1', '24', '24', '10', '21', '1', '1', '1', '24', '2018-06-07', '21', '');
INSERT INTO `dp_finance_other` VALUES ('4', 'FKD20180605143127', '2', '2', '24', '2', '21', '4', '3', '超级管理员', '24', '2018-06-05', '55', '');

-- -----------------------------
-- 表结构 `dp_finance_ptype`
-- -----------------------------
DROP TABLE IF EXISTS `dp_finance_ptype`;
CREATE TABLE `dp_finance_ptype` (
  `id` int(10) NOT NULL COMMENT '编号',
  `name` varchar(255) NOT NULL COMMENT '付款类型',
  `status` tinyint(4) DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- -----------------------------
-- 表数据 `dp_finance_ptype`
-- -----------------------------
INSERT INTO `dp_finance_ptype` VALUES ('1', '材料付款', '1');
INSERT INTO `dp_finance_ptype` VALUES ('2', '劳务付款', '1');
INSERT INTO `dp_finance_ptype` VALUES ('3', '工资发放', '1');
INSERT INTO `dp_finance_ptype` VALUES ('4', '租赁付款', '1');
INSERT INTO `dp_finance_ptype` VALUES ('5', '机械设备', '1');
INSERT INTO `dp_finance_ptype` VALUES ('6', '采购付款', '1');
INSERT INTO `dp_finance_ptype` VALUES ('7', '预付款', '1');
INSERT INTO `dp_finance_ptype` VALUES ('8', '保证金', '1');
INSERT INTO `dp_finance_ptype` VALUES ('9', '办公用品', '1');
INSERT INTO `dp_finance_ptype` VALUES ('10', '其他', '1');

-- -----------------------------
-- 表结构 `dp_finance_pway`
-- -----------------------------
DROP TABLE IF EXISTS `dp_finance_pway`;
CREATE TABLE `dp_finance_pway` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT '支付方式',
  `status` tinyint(4) DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- -----------------------------
-- 表数据 `dp_finance_pway`
-- -----------------------------
INSERT INTO `dp_finance_pway` VALUES ('1', '现金', '1');
INSERT INTO `dp_finance_pway` VALUES ('2', '网银', '1');
INSERT INTO `dp_finance_pway` VALUES ('3', '支付宝', '1');
INSERT INTO `dp_finance_pway` VALUES ('4', '承兑汇票', '1');
INSERT INTO `dp_finance_pway` VALUES ('5', '转账支票', '1');
INSERT INTO `dp_finance_pway` VALUES ('6', '汇票', '1');
INSERT INTO `dp_finance_pway` VALUES ('7', '一次性支付', '1');
INSERT INTO `dp_finance_pway` VALUES ('8', '分期支付', '1');
INSERT INTO `dp_finance_pway` VALUES ('9', '其他', '1');

-- -----------------------------
-- 表结构 `dp_finance_stuff`
-- -----------------------------
DROP TABLE IF EXISTS `dp_finance_stuff`;
CREATE TABLE `dp_finance_stuff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL COMMENT '日期',
  `number` varchar(225) NOT NULL COMMENT '付款编号',
  `name` varchar(255) NOT NULL COMMENT '付款名称',
  `type` varchar(255) NOT NULL COMMENT '源单类型',
  `source_number` varchar(255) NOT NULL COMMENT '源单号',
  `item` varchar(255) NOT NULL COMMENT '所属项目',
  `supplier` varchar(255) NOT NULL COMMENT '供应商',
  `account` varchar(255) NOT NULL COMMENT '银行账户',
  `moneyed` varchar(255) NOT NULL COMMENT '已结算金额',
  `payed` varchar(255) NOT NULL COMMENT '已支付金额',
  `stock` varchar(255) NOT NULL COMMENT '累计入库金额',
  `allpay` varchar(255) NOT NULL COMMENT '累计付款金额',
  `notpay` varchar(255) NOT NULL COMMENT '未付金额',
  `operator` varchar(255) NOT NULL COMMENT '经办人',
  `pay` varchar(255) NOT NULL COMMENT '付款金额',
  `info` varchar(255) NOT NULL COMMENT '(供)账户信息',
  `note` text NOT NULL COMMENT '付款说明',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='财务管理-材料付款';

-- -----------------------------
-- 表数据 `dp_finance_stuff`
-- -----------------------------
INSERT INTO `dp_finance_stuff` VALUES ('9', '1528300800', 'CGXJ201806071104321', '32', '2', '23', '24', '32', '11', '32', '32', '32', '32', '32', '1', '32', '32', '23', '1528340672', '1528340672');
INSERT INTO `dp_finance_stuff` VALUES ('7', '1528300800', 'CGXJ201806061035301', '12', '1', '21', '0', '0', '4', '21', '21', '21', '21', '21', '1', '21', '21', '2', '1528252530', '1528252530');
INSERT INTO `dp_finance_stuff` VALUES ('8', '1528214400', 'CGXJ201806061047081', '3', '0', '32', '23', '32', '9', '23', '32', '32', '32', '23', '5', '32', '23', '32', '1528253228', '1528253228');
INSERT INTO `dp_finance_stuff` VALUES ('10', '1528300800', 'CGXJ201806071109531', '21aaa', '2', '12', '24', '21', '11', '21', '12', '21', '21', '12', '1', '12', '12', '12', '1528340993', '1528340993');

-- -----------------------------
-- 表结构 `dp_finance_receipts`
-- -----------------------------
DROP TABLE IF EXISTS `dp_finance_receipts`;
CREATE TABLE `dp_finance_receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL COMMENT '日期',
  `number` varchar(255) NOT NULL COMMENT '收款编号',
  `name` varchar(255) NOT NULL COMMENT '收款名称',
  `item` varchar(255) NOT NULL COMMENT '项目',
  `contract_title` varchar(255) NOT NULL COMMENT '合同名称',
  `contract_money` varchar(255) NOT NULL COMMENT '合同金额',
  `type` varchar(255) NOT NULL COMMENT '收款类型',
  `nail` varchar(255) NOT NULL COMMENT '甲方单位',
  `gathering_type` varchar(255) NOT NULL COMMENT '收款类型,0,按进度付款,1,按合同付款',
  `fine` varchar(255) NOT NULL COMMENT '罚款',
  `withhold` varchar(255) NOT NULL COMMENT '扣款',
  `gathering` varchar(255) NOT NULL COMMENT '收款金额',
  `big` varchar(255) NOT NULL COMMENT '金额大写',
  `operator` varchar(255) NOT NULL COMMENT '填报人',
  `note` text NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='财务管理-合同收款';

-- -----------------------------
-- 表数据 `dp_finance_receipts`
-- -----------------------------
INSERT INTO `dp_finance_receipts` VALUES ('10', '1527696000', 'CGXJ201806050920411', '12', '12', '12', '12', '', '12', '0', '12', '12', '21', '12', '4', '21', '1528161641', '1528161641');
INSERT INTO `dp_finance_receipts` VALUES ('11', '1527696000', 'CGXJ201806071138401', '12', '24', '1', '12', '', '21', '1', '21', '12', '12', '12', '3', '12', '1528342720', '1528342720');
INSERT INTO `dp_finance_receipts` VALUES ('12', '1528300800', 'CGXJ201806071144041', '12', '24', '21', '12', '', '21', '0', '12', '12', '12', '12', '1', '12', '1528343044', '1528343044');
