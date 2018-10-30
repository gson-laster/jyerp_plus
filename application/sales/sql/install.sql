-- -----------------------------
-- 导出时间 `2018-06-07 16:29:12`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_sales_contract`
-- -----------------------------
DROP TABLE IF EXISTS `dp_sales_contract`;
CREATE TABLE `dp_sales_contract` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '合同名称',
  `monophyletic` int(11) NOT NULL COMMENT '单源类型',
  `document_time` int(10) NOT NULL COMMENT '开始时间',
  `goodtype` int(11) NOT NULL COMMENT '交货方式',
  `transport` int(11) NOT NULL COMMENT '运货方式',
  `paytype` int(11) NOT NULL COMMENT '支付方式',
  `end_time` int(11) NOT NULL COMMENT '截止日期',
  `helpid` varchar(255) NOT NULL COMMENT '可查看人',
  `zrid` int(11) NOT NULL COMMENT '业务员',
  `adderss` varchar(255) NOT NULL COMMENT '签约地点',
  `oid` int(11) NOT NULL COMMENT '部门',
  `why` varchar(255) NOT NULL COMMENT '终止原因',
  `note` varchar(255) NOT NULL COMMENT '备注',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态（执行中，已执行）',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `file` varchar(255) DEFAULT NULL COMMENT '附件',
  `code` char(50) NOT NULL COMMENT '编号',
  `zdid` int(10) NOT NULL COMMENT '制单人',
  `monophycode` int(11) NOT NULL COMMENT '单据编号',
  `currency` tinyint(2) NOT NULL COMMENT '币种',
  `parities` int(10) NOT NULL COMMENT '汇率',
  `customer_name` varchar(255) NOT NULL COMMENT '客户名称',
  `phone` varchar(255) NOT NULL COMMENT '客户联系方式',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `dp_sales_offer`
-- -----------------------------
DROP TABLE IF EXISTS `dp_sales_offer`;
CREATE TABLE `dp_sales_offer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '报价名称',
  `monophyletic` int(11) NOT NULL COMMENT '单源类型（无来源，销售机会）',
  `monophycode` int(11) NOT NULL COMMENT '单据编号',
  `document_time` int(10) NOT NULL COMMENT '开始日期',
  `goodtype` int(11) NOT NULL COMMENT '交货方式',
  `transport` int(11) NOT NULL COMMENT '运送方式',
  `paytype` int(50) NOT NULL COMMENT '支付方式',
  `end_time` int(10) NOT NULL COMMENT '有效截止期',
  `zrid` int(11) NOT NULL COMMENT '业务员',
  `helpid` varchar(255) NOT NULL COMMENT '可查看人员',
  `department` int(11) NOT NULL,
  `note` varchar(255) NOT NULL COMMENT '备注',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `file` varchar(255) DEFAULT NULL COMMENT '附件',
  `code` char(50) NOT NULL COMMENT '编号',
  `zdid` int(10) NOT NULL COMMENT '制单人',
  `currency` tinyint(2) NOT NULL COMMENT '币种',
  `parities` int(10) NOT NULL COMMENT '汇率',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `dp_sales_opport`
-- -----------------------------
DROP TABLE IF EXISTS `dp_sales_opport`;
CREATE TABLE `dp_sales_opport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '机会名称',
  `customer_name` int(10) NOT NULL COMMENT '客户名称,供应商id',
  `type` int(11) NOT NULL COMMENT '机会类型',
  `found_time` int(10) NOT NULL COMMENT '发现时间',
  `zrid` int(11) NOT NULL COMMENT '业务员',
  `department` int(11) NOT NULL COMMENT '部门',
  `status` int(11) NOT NULL COMMENT '阶段（状态）',
  `zdid` int(10) NOT NULL COMMENT '制单人',
  `helpid` varchar(255) NOT NULL COMMENT '可查看人员',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `code` char(50) NOT NULL COMMENT '编号',
  `note` text COMMENT '备注',
  `file` varchar(255) DEFAULT NULL COMMENT '附件',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `dp_sales_order`
-- -----------------------------
DROP TABLE IF EXISTS `dp_sales_order`;
CREATE TABLE `dp_sales_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '订单名称',
  `monophyletic` int(11) NOT NULL COMMENT '单源类型',
  `customer_name` varchar(255) NOT NULL COMMENT '客户名称',
  `phone` varchar(255) NOT NULL COMMENT '客户电话',
  `document_time` int(10) NOT NULL COMMENT '开始时间',
  `goodtype` int(11) NOT NULL COMMENT '交货方式',
  `transport` int(11) NOT NULL COMMENT '运货方式',
  `paytype` int(11) NOT NULL COMMENT '支付方式',
  `end_time` int(11) NOT NULL COMMENT '截止日期',
  `latest_time` int(10) NOT NULL COMMENT '最迟发货时间',
  `helpid` varchar(255) NOT NULL COMMENT '可查看人',
  `zrid` int(11) NOT NULL COMMENT '业务员',
  `oid` int(11) NOT NULL COMMENT '部门',
  `why` varchar(255) NOT NULL COMMENT '终止原因',
  `note` varchar(255) NOT NULL COMMENT '备注',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态（执行中，已执行）',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `code` char(50) NOT NULL COMMENT '编号',
  `zdid` int(10) NOT NULL COMMENT '制单人',
  `monophycode` int(11) NOT NULL COMMENT '单据编号',
  `currency` tinyint(2) NOT NULL COMMENT '币种',
  `parities` int(10) NOT NULL COMMENT '汇率',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `dp_sales_plan`
-- -----------------------------
DROP TABLE IF EXISTS `dp_sales_plan`;
CREATE TABLE `dp_sales_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` char(50) NOT NULL COMMENT '编号',
  `name` varchar(255) NOT NULL COMMENT '计划名称',
  `type` int(11) NOT NULL COMMENT '计划类型',
  `start_time` int(10) NOT NULL COMMENT '开始时间',
  `end_time` int(10) NOT NULL COMMENT '结束时间',
  `low_money` double(20,0) NOT NULL COMMENT '最低金额',
  `total_money` double(20,0) NOT NULL COMMENT '计划总金额',
  `zrid` int(11) NOT NULL COMMENT '业务员',
  `department` int(11) NOT NULL COMMENT '部门',
  `helpid` varchar(255) NOT NULL COMMENT '可查看人员',
  `zdid` int(11) NOT NULL COMMENT '制单人',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态',
  `note` varchar(255) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `file` varchar(255) DEFAULT NULL COMMENT '附件',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `dp_sales_delivery`
-- -----------------------------
DROP TABLE IF EXISTS `dp_sales_delivery`;
CREATE TABLE `dp_sales_delivery` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '销售发货',
  `uid` int(11) NOT NULL COMMENT '业务员',
  `name` varchar(255) NOT NULL COMMENT '发货名称',
  `monophyletic` int(11) NOT NULL COMMENT '单源类型',
  `customer_name` varchar(255) NOT NULL COMMENT '客户名称',
  `phone` varchar(255) NOT NULL COMMENT '客户电话',
  `document_time` varchar(255) NOT NULL COMMENT '预计发货时间',
  `goodtype` int(11) NOT NULL COMMENT '交货方式',
  `transport` int(11) NOT NULL COMMENT '运货方式',
  `paytype` int(11) NOT NULL COMMENT '支付方式',
  `deliveryman` varchar(255) NOT NULL COMMENT '收货人',
  `deliveryphone` varchar(11) NOT NULL COMMENT '收货人电话',
  `addrss` varchar(255) NOT NULL COMMENT '收货地址',
  `goodaddrss` varchar(255) NOT NULL COMMENT '发货地址',
  `helpid` varchar(255) NOT NULL COMMENT '可查看人',
  `zrid` int(11) NOT NULL COMMENT '发货人',
  `oid` int(11) NOT NULL COMMENT '部门',
  `money` double(255,0) NOT NULL COMMENT '运费金额',
  `note` varchar(255) NOT NULL COMMENT '备注',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态（执行中，已执行）',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `code` char(50) NOT NULL COMMENT '编号',
  `zdid` int(10) NOT NULL COMMENT '制单人',
  `monophycode` int(11) NOT NULL COMMENT '单据编号',
  `currency` tinyint(2) NOT NULL COMMENT '币种',
  `parities` int(10) NOT NULL COMMENT '汇率',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

