-- -----------------------------
-- 导出时间 `2018-06-04 14:40:19`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_produce_cost`
-- -----------------------------
DROP TABLE IF EXISTS `dp_produce_cost`;
CREATE TABLE `dp_produce_cost` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL COMMENT '编号',
  `month` int(2) unsigned NOT NULL COMMENT '月份',
  `good_id` int(10) unsigned NOT NULL COMMENT '产品名称',
  `completion_ratio` int(3) unsigned NOT NULL COMMENT '完工比例',
  `is_disposable` tinyint(1) unsigned NOT NULL COMMENT '是否一次性投入',
  `good_num` int(10) unsigned NOT NULL COMMENT '完工数量',
  `zai_good_monthcl_money` int(10) unsigned NOT NULL COMMENT '月末在产品直接材料定额',
  `zai_good_cl_money` int(10) unsigned NOT NULL COMMENT '在产品直接材料定额',
  `zai_good_monthgs_money` int(10) unsigned NOT NULL COMMENT ' 在产品工时消耗定额',
  `good_clxh_money` int(10) unsigned NOT NULL COMMENT '产品材料消耗定额 ',
  `good_gsxh_money` int(10) unsigned NOT NULL COMMENT '工时消耗定额',
  `zai_wages` int(10) unsigned NOT NULL COMMENT '在产品工资定额',
  `zai_zhizao` int(10) unsigned NOT NULL COMMENT '在产品制造费用定额',
  `zai_good_num` int(10) unsigned NOT NULL COMMENT '在产品数量',
  `money_type` tinyint(1) unsigned NOT NULL COMMENT '币种 0美元  1人民币 2欧元',
  `rate` int(10) unsigned NOT NULL COMMENT '汇率',
  `wid` int(11) unsigned NOT NULL COMMENT '制表人',
  `create_time` int(10) unsigned NOT NULL COMMENT '制作日期',
  `remark` text COMMENT '备注',
  `file` varchar(255) DEFAULT NULL COMMENT '附件',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_produce_cost`
-- -----------------------------
INSERT INTO `dp_produce_cost` VALUES ('1', 'CPCB20181254125478', '5', '9', '50', '1', '2000', '1000000', '42324', '2423023020', '3213232', '3244232', '424224242', '2442232323', '10000', '1', '1', '1', '1574548746', '哈哈哈', '');
INSERT INTO `dp_produce_cost` VALUES ('2', 'CPCB20181254125478', '4', '10', '50', '0', '2000', '1000000', '42324', '2423023020', '3213232', '3244232', '424224242', '2442232323', '10000', '1', '1', '1', '1574548746', '哈哈哈', '');

-- -----------------------------
-- 表结构 `dp_produce_mateplan`
-- -----------------------------
DROP TABLE IF EXISTS `dp_produce_mateplan`;
CREATE TABLE `dp_produce_mateplan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '生产主题',
  `uid` int(11) unsigned NOT NULL COMMENT '建档人',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '单据编号',
  `plan_id` int(11) unsigned NOT NULL COMMENT '主生产计划id',
  `header` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '负责人id',
  `org_id` int(11) unsigned NOT NULL COMMENT '所属部门',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态  0 关闭 1 启用',
  `note` varchar(255) DEFAULT '' COMMENT '备注',
  `enclosure` varchar(255) DEFAULT NULL COMMENT '附件',
  `mateplan_list` varchar(255) NOT NULL COMMENT '物料需求明细',
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_produce_mateplan`
-- -----------------------------
INSERT INTO `dp_produce_mateplan` VALUES ('37', 'eqweqwe', '1', 'sc1001', '37', '5', '2', '1', '', '', '10', '1524926009', '1524927770');
INSERT INTO `dp_produce_mateplan` VALUES ('39', '测试', '1', 'wl1001', '37', '5', '2', '1', '恶趣味', '17', '10,9', '1525184828', '1525184828');
INSERT INTO `dp_produce_mateplan` VALUES ('40', 'eqeqweqwe', '1', 'wl1002', '37', '3', '4', '1', 'eqwe', '17', '10,9', '1525185611', '1525186653');

-- -----------------------------
-- 表结构 `dp_produce_mateplan_list`
-- -----------------------------
DROP TABLE IF EXISTS `dp_produce_mateplan_list`;
CREATE TABLE `dp_produce_mateplan_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pmid` int(11) unsigned NOT NULL COMMENT '生产计划id',
  `smid` int(11) unsigned NOT NULL COMMENT '物资id',
  `mao_num` decimal(11,2) unsigned NOT NULL COMMENT '毛需求量',
  `plan_num` decimal(11,2) unsigned NOT NULL COMMENT '应计划数量',
  `plan_time` datetime NOT NULL COMMENT '计划供料日期',
  `source` datetime NOT NULL COMMENT '物料来源',
  `note` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`pmid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_produce_mateplan_list`
-- -----------------------------
INSERT INTO `dp_produce_mateplan_list` VALUES ('36', '37', '10', '200.00', '150.00', '2018-05-24 00:00:00', '2018-05-30 00:00:00', 'wewqe', '1524926009', '1524927770');
INSERT INTO `dp_produce_mateplan_list` VALUES ('37', '39', '10', '200.00', '150.00', '2018-05-24 00:00:00', '2018-05-30 00:00:00', '', '1525184828', '1525184828');
INSERT INTO `dp_produce_mateplan_list` VALUES ('39', '40', '10', '111.00', '111.00', '2018-05-24 00:00:00', '2018-05-30 00:00:00', '', '1525185611', '1525186654');
INSERT INTO `dp_produce_mateplan_list` VALUES ('40', '40', '9', '999.99', '11.00', '2018-05-24 00:00:00', '2018-05-30 00:00:00', '', '1525185611', '1525186654');

-- -----------------------------
-- 表结构 `dp_produce_materials`
-- -----------------------------
DROP TABLE IF EXISTS `dp_produce_materials`;
CREATE TABLE `dp_produce_materials` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '清单名称',
  `uid` int(11) unsigned NOT NULL COMMENT '建档人',
  `version` varchar(50) DEFAULT NULL COMMENT '版本',
  `code` varchar(50) DEFAULT '' COMMENT 'BOM编号',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'BOM类型  0 工程BOM 1 生产BOM  2 销售BOM 3 成本BOM',
  `technology_line` int(11) unsigned NOT NULL COMMENT '工艺路线',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态  0 关闭 1 启用',
  `pid` int(11) unsigned NOT NULL COMMENT '父件id',
  `note` varchar(255) DEFAULT '' COMMENT '备注',
  `enclosure` varchar(255) DEFAULT NULL COMMENT '附件',
  `materials_list` varchar(255) NOT NULL COMMENT '子件明细',
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_produce_materials`
-- -----------------------------
INSERT INTO `dp_produce_materials` VALUES ('14', '车间1', '0', '1', '1517392512', '1', '32', '1', '0', '', '', '', '1520240549', '0');
INSERT INTO `dp_produce_materials` VALUES ('16', '车间2', '1', '0', '1517457746', '1', '35', '1', '0', '', '', '', '1520240549', '0');
INSERT INTO `dp_produce_materials` VALUES ('17', '车间3', '0', '1', '1517457746', '1', '34', '1', '0', '', '', '', '1520240549', '0');
INSERT INTO `dp_produce_materials` VALUES ('30', '车间4', '0', '1', '1520240549', '1', '32', '1', '0', '', '', '', '1520240549', '0');
INSERT INTO `dp_produce_materials` VALUES ('31', '车间5', '1', '1', '1520565354', '1', '35', '1', '0', '', '', '', '1520565354', '0');
INSERT INTO `dp_produce_materials` VALUES ('32', '浇筑中心', '1', '3', '12313', '2', '34', '1', '0', '3213213', '', '', '1524119632', '1524125565');
INSERT INTO `dp_produce_materials` VALUES ('34', '而为全额', '1', '1', 'wl2110121', '0', '35', '1', '9', '恶趣味二位群', '', '10,9', '1524882791', '1524888246');
INSERT INTO `dp_produce_materials` VALUES ('35', 'sd', '1', '1', '123', '2', '35', '1', '10', '', '', '10,9', '1524900737', '1524900737');

-- -----------------------------
-- 表结构 `dp_produce_materials_list`
-- -----------------------------
DROP TABLE IF EXISTS `dp_produce_materials_list`;
CREATE TABLE `dp_produce_materials_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pmid` int(11) unsigned NOT NULL COMMENT '清单id',
  `smid` int(11) unsigned NOT NULL COMMENT '子件id',
  `loss_rate` decimal(5,2) unsigned NOT NULL COMMENT '损耗率',
  `quota` varchar(50) NOT NULL COMMENT '定额',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '使用状态  0 关闭 1 启用',
  `is_key` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为关键件  0 否 1 是',
  `note` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`pmid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_produce_materials_list`
-- -----------------------------
INSERT INTO `dp_produce_materials_list` VALUES ('33', '34', '10', '3.00', '100', '1', '0', '发的所', '1524882791', '1524888246');
INSERT INTO `dp_produce_materials_list` VALUES ('35', '34', '9', '4.00', '200', '1', '1', '委屈额', '1524888246', '1524888246');
INSERT INTO `dp_produce_materials_list` VALUES ('36', '35', '10', '2.00', '20', '1', '1', '21321', '1524900737', '1524900737');
INSERT INTO `dp_produce_materials_list` VALUES ('37', '35', '9', '0.00', '23213', '1', '1', '', '1524900737', '1524900737');

-- -----------------------------
-- 表结构 `dp_produce_plan`
-- -----------------------------
DROP TABLE IF EXISTS `dp_produce_plan`;
CREATE TABLE `dp_produce_plan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '生产主题',
  `uid` int(11) unsigned NOT NULL COMMENT '建档人',
  `code` varchar(50) DEFAULT '' COMMENT '单据编号',
  `header` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '负责人id',
  `org_id` int(11) unsigned NOT NULL COMMENT '所属部门',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态  0 关闭 1 启用',
  `note` varchar(255) DEFAULT '' COMMENT '备注',
  `enclosure` varchar(255) DEFAULT NULL COMMENT '附件',
  `plan_list` varchar(255) NOT NULL COMMENT '生产计划明细',
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_produce_plan`
-- -----------------------------
INSERT INTO `dp_produce_plan` VALUES ('37', 'eqweqwe', '1', 'sc1001', '5', '2', '1', '', '', '10,9', '1524926009', '1526520606');

-- -----------------------------
-- 表结构 `dp_produce_plan_list`
-- -----------------------------
DROP TABLE IF EXISTS `dp_produce_plan_list`;
CREATE TABLE `dp_produce_plan_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ppid` int(11) unsigned NOT NULL COMMENT '生产计划id',
  `smid` int(11) unsigned NOT NULL COMMENT '物资id',
  `plan_num` decimal(11,2) unsigned NOT NULL COMMENT '计划需求量',
  `require_num` decimal(11,2) unsigned NOT NULL COMMENT '应计划数量',
  `start_time` datetime NOT NULL COMMENT '开工时间',
  `end_time` datetime NOT NULL COMMENT '完工时间',
  `note` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`ppid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_produce_plan_list`
-- -----------------------------
INSERT INTO `dp_produce_plan_list` VALUES ('36', '37', '10', '200.00', '150.00', '2018-05-24 00:00:00', '2018-05-30 00:00:00', 'wewqe', '1524926009', '1526520606');
INSERT INTO `dp_produce_plan_list` VALUES ('37', '37', '9', '2.00', '1.00', '2018-05-24 00:00:00', '2018-05-30 00:00:00', '454', '1526009268', '1526520606');

-- -----------------------------
-- 表结构 `dp_produce_procedure`
-- -----------------------------
DROP TABLE IF EXISTS `dp_produce_procedure`;
CREATE TABLE `dp_produce_procedure` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '工序名称',
  `uid` int(11) unsigned NOT NULL COMMENT '建档人',
  `code` varchar(50) DEFAULT '' COMMENT '工序代码',
  `wc_id` int(11) unsigned NOT NULL COMMENT '所属车间',
  `is_other` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否外协  0  否 1 是',
  `technology` varchar(255) DEFAULT NULL COMMENT '包含工艺',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态  0 关闭 1 启用',
  `description` text COMMENT '工序描述',
  `note` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_produce_procedure`
-- -----------------------------
INSERT INTO `dp_produce_procedure` VALUES ('32', '浇筑中心', '1', '12313', '16', '0', '', '1', '<p>3123</p>', '3213213', '1524119632', '1524123256');
INSERT INTO `dp_produce_procedure` VALUES ('34', '呵呵', '1', '123213', '30', '0', '32', '1', '', '', '1524130171', '1524212735');
INSERT INTO `dp_produce_procedure` VALUES ('35', 'edd', '1', '1233', '31', '0', '31,32', '1', '<p>eqwe</p>', '', '1524900571', '1524900571');

-- -----------------------------
-- 表结构 `dp_produce_production`
-- -----------------------------
DROP TABLE IF EXISTS `dp_produce_production`;
CREATE TABLE `dp_produce_production` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '生产主题',
  `uid` int(11) unsigned NOT NULL COMMENT '建档人',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '单据编号',
  `plan_id` int(11) unsigned NOT NULL COMMENT '主生产计划id',
  `header` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '负责人id',
  `org_id` int(11) unsigned NOT NULL COMMENT '生产部门',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态  0 关闭 1 启用',
  `note` varchar(255) DEFAULT '' COMMENT '备注',
  `enclosure` varchar(255) DEFAULT NULL COMMENT '附件',
  `production_list` varchar(255) NOT NULL COMMENT '生产任务明细明细',
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_produce_production`
-- -----------------------------
INSERT INTO `dp_produce_production` VALUES ('40', 'eqeqweqwe', '1', 'wl1002', '37', '3', '4', '1', 'eqwe', '17', '10,9', '1525185611', '1525186653');
INSERT INTO `dp_produce_production` VALUES ('41', 'eqeqwe', '1', 'sc1001', '37', '5', '4', '1', '', '', '10,9', '1525935815', '1525935815');

-- -----------------------------
-- 表结构 `dp_produce_production_list`
-- -----------------------------
DROP TABLE IF EXISTS `dp_produce_production_list`;
CREATE TABLE `dp_produce_production_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ppid` int(11) unsigned NOT NULL COMMENT '生产计划id',
  `smid` int(11) unsigned NOT NULL COMMENT '物资id',
  `produce_num` decimal(11,2) unsigned NOT NULL COMMENT '生产数量',
  `plan_time` datetime NOT NULL COMMENT '计划开工日期',
  `end_time` datetime NOT NULL COMMENT '完工日期',
  `ysc_num` decimal(11,2) unsigned NOT NULL COMMENT '已生产数量',
  `yrk_num` decimal(11,2) unsigned NOT NULL COMMENT '已入库数量',
  `yb_num` decimal(11,2) unsigned NOT NULL COMMENT '已报数量',
  `sj_num` decimal(11,2) unsigned NOT NULL COMMENT '实检数量',
  `hg_num` decimal(11,2) unsigned NOT NULL COMMENT '合格数量',
  `bhg_num` decimal(11,2) unsigned NOT NULL COMMENT '不合格数量',
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`ppid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_produce_production_list`
-- -----------------------------
INSERT INTO `dp_produce_production_list` VALUES ('36', '37', '10', '200.00', '2018-05-24 00:00:00', '2018-05-30 00:00:00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1524926009', '1525938544');
INSERT INTO `dp_produce_production_list` VALUES ('37', '39', '10', '0.00', '2018-05-24 00:00:00', '2018-05-30 00:00:00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1525184828', '1525184828');
INSERT INTO `dp_produce_production_list` VALUES ('39', '40', '10', '111.00', '2018-05-24 00:00:00', '2018-05-30 00:00:00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1525185611', '1525186654');
INSERT INTO `dp_produce_production_list` VALUES ('40', '40', '9', '999.99', '2018-05-24 00:00:00', '2018-05-30 00:00:00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1525185611', '1525186654');
INSERT INTO `dp_produce_production_list` VALUES ('41', '41', '10', '200.00', '2018-05-25 00:00:00', '2018-05-25 00:00:00', '500.00', '500.00', '500.00', '500.00', '500.00', '500.00', '1525935815', '1525935815');
INSERT INTO `dp_produce_production_list` VALUES ('42', '41', '9', '300.00', '2018-05-25 00:00:00', '2018-05-25 00:00:00', '500.00', '500.00', '500.00', '500.00', '500.00', '500.00', '1525935815', '1525935815');
INSERT INTO `dp_produce_production_list` VALUES ('43', '37', '9', '300.00', '2018-05-24 00:00:00', '2018-05-24 00:00:00', '300.00', '300.00', '300.00', '300.00', '300.00', '300.00', '1525938544', '1525938544');

-- -----------------------------
-- 表结构 `dp_produce_technology`
-- -----------------------------
DROP TABLE IF EXISTS `dp_produce_technology`;
CREATE TABLE `dp_produce_technology` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '工艺名称',
  `uid` int(11) unsigned NOT NULL COMMENT '建档人',
  `code` varchar(50) DEFAULT '' COMMENT '工艺代码',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态  0 关闭 1 启用',
  `description` text COMMENT '工艺描述',
  `note` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_produce_technology`
-- -----------------------------
INSERT INTO `dp_produce_technology` VALUES ('30', '1', '0', '1520240549', '0', '', '', '1520240549', '0');
INSERT INTO `dp_produce_technology` VALUES ('31', '1', '1', '1520565354', '1', '', '', '1520565354', '0');
INSERT INTO `dp_produce_technology` VALUES ('32', '浇筑中心', '1', '12313', '1', '', '', '1524119632', '1524120016');

-- -----------------------------
-- 表结构 `dp_produce_technology_line`
-- -----------------------------
DROP TABLE IF EXISTS `dp_produce_technology_line`;
CREATE TABLE `dp_produce_technology_line` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '工艺路线名称',
  `uid` int(11) unsigned NOT NULL COMMENT '建档人',
  `code` varchar(50) DEFAULT '' COMMENT '工艺路线代码',
  `good_name` varchar(50) NOT NULL,
  `procedure` varchar(255) DEFAULT NULL COMMENT '包含工序',
  `is_main` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否为主打工艺  0 否 1 是',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态  0 关闭 1 启用',
  `description` text COMMENT '工艺路线描述',
  `note` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_produce_technology_line`
-- -----------------------------
INSERT INTO `dp_produce_technology_line` VALUES ('32', '水杯', '1', '12313', '我的去问', '34', '1', '1', '<p>3123</p>', '3213213', '1524119632', '1524123256');
INSERT INTO `dp_produce_technology_line` VALUES ('34', '汽车', '1', '123213', '恶趣味', '32', '1', '1', '', '', '1524130171', '1524212735');
INSERT INTO `dp_produce_technology_line` VALUES ('35', '椅子工艺', '1', '', '椅子', '32,34', '1', '1', '', '', '1524447046', '1524447046');
INSERT INTO `dp_produce_technology_line` VALUES ('36', 'zhuozi ', '1', '2323', 'zhuozi', '32,34', '1', '1', '<p>ewq</p>', 'eqwe', '1524900640', '1524900640');

-- -----------------------------
-- 表结构 `dp_produce_workcenter`
-- -----------------------------
DROP TABLE IF EXISTS `dp_produce_workcenter`;
CREATE TABLE `dp_produce_workcenter` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '车间名称',
  `uid` int(11) unsigned NOT NULL COMMENT '建档人',
  `header` int(11) unsigned DEFAULT NULL COMMENT '负责人',
  `code` varchar(50) DEFAULT '' COMMENT '车间代码',
  `org_id` int(11) unsigned NOT NULL COMMENT '所属部门',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态  0 关闭 1 启用',
  `is_key` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为关键车间  0 否 1 是',
  `description` text COMMENT '车间描述',
  `note` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_produce_workcenter`
-- -----------------------------
INSERT INTO `dp_produce_workcenter` VALUES ('14', '车间1', '0', '1', '1517392512', '1', '1', '0', '', '', '0', '0');
INSERT INTO `dp_produce_workcenter` VALUES ('16', '车间2', '1', '0', '1517457746', '1', '1', '0', '', '', '0', '0');
INSERT INTO `dp_produce_workcenter` VALUES ('17', '车间3', '0', '1', '1517457746', '1', '1', '0', '', '', '0', '0');
INSERT INTO `dp_produce_workcenter` VALUES ('30', '车间4', '0', '1', '1520240549', '1', '1', '0', '', '', '1520240549', '0');
INSERT INTO `dp_produce_workcenter` VALUES ('31', '车间5', '1', '1', '1520565354', '1', '0', '0', '', '', '1520565354', '0');
INSERT INTO `dp_produce_workcenter` VALUES ('32', '浇筑中心', '1', '3', '12313', '2', '1', '1', '<p>3123</p>', '3213213', '1524119632', '1524125565');
INSERT INTO `dp_produce_workcenter` VALUES ('33', '544545', '1', '0', '54545', '2', '1', '0', '', '', '1526874622', '1526874622');
INSERT INTO `dp_produce_workcenter` VALUES ('34', '877878', '1', '0', '7878', '2', '1', '0', '<p>8787</p>', '', '1526874649', '1526874649');
INSERT INTO `dp_produce_workcenter` VALUES ('35', '52656', '1', '0', '56546', '2', '1', '1', '<p>2323</p>', '', '1526874730', '1526874730');
