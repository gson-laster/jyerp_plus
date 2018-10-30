-- -----------------------------
-- 导出时间 `2018-03-15 08:55:18`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_personnel_award`
-- -----------------------------
DROP TABLE IF EXISTS `dp_personnel_award`;
CREATE TABLE `dp_personnel_award` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `award_type` tinyint(1) unsigned DEFAULT NULL COMMENT '奖惩类型 1 慰问 2 惩罚 3  奖励',
  `award_cate` varchar(50) DEFAULT NULL COMMENT '奖励项目',
  `money` decimal(8,2) unsigned DEFAULT '0.00' COMMENT '奖励金额',
  `good` varchar(255) DEFAULT NULL COMMENT '奖励物品',
  `award_time` int(11) unsigned DEFAULT NULL COMMENT '奖惩日期',
  `code` varchar(255) DEFAULT NULL COMMENT '备注',
  `enclosure` varchar(255) DEFAULT NULL COMMENT '附件',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '0 待执行 1 已执行',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_personnel_award`
-- -----------------------------
INSERT INTO `dp_personnel_award` VALUES ('2', '2', '1', '4', '110.00', '', '1516291200', '', '', '1', '1516951895', '1516952640');
INSERT INTO `dp_personnel_award` VALUES ('3', '2', '3', '4', '0.00', '', '0', '', '', '0', '1516955598', '1516955624');

-- -----------------------------
-- 表结构 `dp_personnel_awardcate`
-- -----------------------------
DROP TABLE IF EXISTS `dp_personnel_awardcate`;
CREATE TABLE `dp_personnel_awardcate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '奖惩项目',
  `sort` smallint(4) unsigned DEFAULT '100' COMMENT '排序',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '0 禁用 1 启用',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_personnel_awardcate`
-- -----------------------------
INSERT INTO `dp_personnel_awardcate` VALUES ('3', '节日福利', '100', '1', '1516950884', '1516950884');
INSERT INTO `dp_personnel_awardcate` VALUES ('4', '生日福利', '100', '1', '1516950926', '1516951062');

-- -----------------------------
-- 表结构 `dp_personnel_care`
-- -----------------------------
DROP TABLE IF EXISTS `dp_personnel_care`;
CREATE TABLE `dp_personnel_care` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `care_type` tinyint(1) unsigned DEFAULT NULL COMMENT '关怀类型 1 节日关怀 2 生日关怀',
  `money` decimal(8,2) unsigned DEFAULT NULL COMMENT '关怀费用',
  `holiday` tinyint(2) unsigned DEFAULT '0' COMMENT '关怀假期',
  `good` varchar(255) DEFAULT NULL COMMENT '关怀物品',
  `care_time` int(11) unsigned DEFAULT NULL COMMENT '关怀日期',
  `code` varchar(255) DEFAULT NULL COMMENT '备注',
  `enclosure` varchar(255) DEFAULT NULL COMMENT '附件',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '0 待执行 1 已执行',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_personnel_care`
-- -----------------------------
INSERT INTO `dp_personnel_care` VALUES ('1', '2', '2', '100.00', '1', '', '1516896000', '', '', '1', '1516955409', '1516955437');
INSERT INTO `dp_personnel_care` VALUES ('2', '3', '2', '100.00', '0', '', '1516982400', '', '', '0', '1516955508', '1516955634');

-- -----------------------------
-- 表结构 `dp_personnel_column`
-- -----------------------------
DROP TABLE IF EXISTS `dp_personnel_column`;
CREATE TABLE `dp_personnel_column` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '栏目名称',
  `model` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文档模型id',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `target` varchar(16) NOT NULL DEFAULT '_self' COMMENT '链接打开方式',
  `content` text NOT NULL COMMENT '内容',
  `icon` varchar(64) NOT NULL DEFAULT '' COMMENT '字体图标',
  `index_template` varchar(32) NOT NULL DEFAULT '' COMMENT '封面模板',
  `list_template` varchar(32) NOT NULL DEFAULT '' COMMENT '列表页模板',
  `detail_template` varchar(32) NOT NULL DEFAULT '' COMMENT '详情页模板',
  `post_auth` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '投稿权限',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `hide` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `rank_auth` int(11) NOT NULL DEFAULT '0' COMMENT '浏览权限，-1待审核，0为开放浏览，大于0则为对应的用户角色id',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '栏目属性：0-最终列表栏目，1-外部链接，2-频道封面',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='栏目表';

-- -----------------------------
-- 表数据 `dp_personnel_column`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_personnel_contract`
-- -----------------------------
DROP TABLE IF EXISTS `dp_personnel_contract`;
CREATE TABLE `dp_personnel_contract` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `contract_code` varchar(50) DEFAULT NULL COMMENT '合同编号',
  `contract_type` tinyint(1) unsigned DEFAULT NULL COMMENT '合同类型 1 固定期限劳动合同 2 无固定期限劳动合同 3 劳务派遣合同 4 非全日制用工合同',
  `is_fixed` tinyint(1) unsigned DEFAULT '1' COMMENT '是否期限固定',
  `num` tinyint(2) unsigned DEFAULT '1' COMMENT '签约次数',
  `contract_time` int(11) unsigned DEFAULT NULL COMMENT '签约时间',
  `start_time` int(11) unsigned DEFAULT NULL COMMENT '开始时间',
  `end_time` int(11) unsigned DEFAULT NULL COMMENT '结束时间',
  `test_time` int(11) unsigned DEFAULT NULL COMMENT '试用结束时间',
  `pend_time` int(11) unsigned DEFAULT NULL COMMENT '提前终止时间',
  `code` varchar(255) DEFAULT NULL COMMENT '备注',
  `enclosure` varchar(255) DEFAULT NULL COMMENT '附件',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '合同状态 0 解除 1 有效  2 解除',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_personnel_contract`
-- -----------------------------
INSERT INTO `dp_personnel_contract` VALUES ('3', '4', '201801246722u4', '1', '1', '1', '1516723200', '1516723200', '1579795200', '1524499200', '0', '', '', '1', '1516786722', '1516786722');

-- -----------------------------
-- 表结构 `dp_personnel_daily`
-- -----------------------------
DROP TABLE IF EXISTS `dp_personnel_daily`;
CREATE TABLE `dp_personnel_daily` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `oid` smallint(4) unsigned NOT NULL COMMENT '部门id',
  `title` varchar(100) NOT NULL COMMENT '招聘标题',
  `info` varchar(255) NOT NULL COMMENT '日志详情',
  `note` varchar(255) NOT NULL COMMENT '备注',
  `daily_time` int(11) unsigned NOT NULL COMMENT '日志时间',
  `type` tinyint(1) unsigned NOT NULL COMMENT '日志类型  0 日志 1 周报 2 月报',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态  待阅 0 已阅 1 ',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '签到时间',
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_personnel_daily`
-- -----------------------------
INSERT INTO `dp_personnel_daily` VALUES ('1', '2', '0', '', '', '', '0', '0', '1', '1517382104', '0');
INSERT INTO `dp_personnel_daily` VALUES ('17', '2', '0', '', '', '', '0', '0', '1', '1517457746', '0');
INSERT INTO `dp_personnel_daily` VALUES ('18', '3', '0', '', '', '', '0', '0', '1', '1517561653', '1517561653');
INSERT INTO `dp_personnel_daily` VALUES ('19', '3', '0', '', '', '', '0', '0', '1', '1517561704', '1517561704');
INSERT INTO `dp_personnel_daily` VALUES ('20', '3', '0', '', '', '', '0', '0', '1', '1517561796', '1517561796');
INSERT INTO `dp_personnel_daily` VALUES ('21', '1', '0', '', '', '', '0', '0', '1', '1517563062', '1517563062');
INSERT INTO `dp_personnel_daily` VALUES ('22', '1', '0', '', '', '', '0', '0', '1', '1517563334', '1517563334');
INSERT INTO `dp_personnel_daily` VALUES ('23', '3', '0', '技术部招聘需求', '<p>技术部招聘需求</p>', '技术部招聘需求', '1519488000', '0', '1', '1518060161', '1518075345');
INSERT INTO `dp_personnel_daily` VALUES ('24', '1', '1', '2018年2月9日报告', '<p>2018年2月9日报告</p><p><br/></p><p>2018年2月9日报告</p><p>2018年2月9日报告</p>', '', '1519488000', '0', '0', '1518146852', '1518147668');

-- -----------------------------
-- 表结构 `dp_personnel_plan`
-- -----------------------------
DROP TABLE IF EXISTS `dp_personnel_plan`;
CREATE TABLE `dp_personnel_plan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `oid` smallint(4) unsigned NOT NULL COMMENT '部门id',
  `title` varchar(100) NOT NULL COMMENT '计划标题',
  `info` varchar(255) NOT NULL COMMENT '计划详情',
  `note` varchar(255) NOT NULL COMMENT '备注',
  `plan_time` int(11) unsigned NOT NULL COMMENT '计划时间',
  `type` tinyint(1) unsigned NOT NULL COMMENT '计划类型  0 日计划 1 周计划 2 月计划',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态  待阅 0 已阅 1 ',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '签到时间',
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_personnel_plan`
-- -----------------------------
INSERT INTO `dp_personnel_plan` VALUES ('1', '2', '0', '', '', '', '0', '0', '1', '1517382104', '0');
INSERT INTO `dp_personnel_plan` VALUES ('17', '2', '0', '', '', '', '0', '0', '1', '1517457746', '0');
INSERT INTO `dp_personnel_plan` VALUES ('18', '3', '0', '', '', '', '0', '0', '1', '1517561653', '1517561653');
INSERT INTO `dp_personnel_plan` VALUES ('19', '3', '0', '', '', '', '0', '0', '1', '1517561704', '1517561704');
INSERT INTO `dp_personnel_plan` VALUES ('20', '3', '0', '', '', '', '0', '0', '1', '1517561796', '1517561796');
INSERT INTO `dp_personnel_plan` VALUES ('21', '1', '0', '', '', '', '0', '0', '1', '1517563062', '1517563062');
INSERT INTO `dp_personnel_plan` VALUES ('22', '1', '0', '', '', '', '0', '0', '1', '1517563334', '1517563334');
INSERT INTO `dp_personnel_plan` VALUES ('23', '3', '0', '技术部招聘需求', '<p>技术部招聘需求</p>', '技术部招聘需求', '1519488000', '0', '1', '1518060161', '1518075345');
INSERT INTO `dp_personnel_plan` VALUES ('24', '1', '1', '2018年2月9日报告', '<p>2018年2月9日报告</p><p><br/></p><p>2018年2月9日报告</p><p>2018年2月9日报告</p>', '', '1519488000', '0', '0', '1518146852', '1518147668');

-- -----------------------------
-- 表结构 `dp_personnel_papercat`
-- -----------------------------
DROP TABLE IF EXISTS `dp_personnel_papercat`;
CREATE TABLE `dp_personnel_papercat` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '证件类型',
  `pid` smallint(54) unsigned NOT NULL DEFAULT '0',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='组织架构表';

-- -----------------------------
-- 表数据 `dp_personnel_papercat`
-- -----------------------------
INSERT INTO `dp_personnel_papercat` VALUES ('1', '二三奇其', '0', '1476065410', '1516935672', '1', '1');
INSERT INTO `dp_personnel_papercat` VALUES ('2', '技术部', '1', '1516005129', '1516935672', '1', '1');
INSERT INTO `dp_personnel_papercat` VALUES ('4', '销售部', '1', '1516005539', '1516935672', '2', '1');
INSERT INTO `dp_personnel_papercat` VALUES ('5', '网络部', '1', '1516005550', '1516935672', '3', '1');
INSERT INTO `dp_personnel_papercat` VALUES ('7', '金耀科技', '0', '1516093648', '1516935672', '2', '1');
INSERT INTO `dp_personnel_papercat` VALUES ('8', '英语', '0', '1516934662', '1516935672', '3', '1');

-- -----------------------------
-- 表结构 `dp_personnel_papers`
-- -----------------------------
DROP TABLE IF EXISTS `dp_personnel_papers`;
CREATE TABLE `dp_personnel_papers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `paper_type` tinyint(1) unsigned DEFAULT NULL COMMENT '证件类型',
  `paper_code` varchar(50) DEFAULT NULL COMMENT '证件编号',
  `paper_organization` varchar(50) DEFAULT NULL COMMENT '发证机构',
  `start_time` int(11) unsigned DEFAULT NULL COMMENT '生效日期',
  `end_time` int(11) unsigned DEFAULT NULL COMMENT '到期时间',
  `paper_time` int(11) unsigned DEFAULT NULL COMMENT '取证日期',
  `code` varchar(255) DEFAULT NULL COMMENT '备注',
  `enclosure` varchar(255) DEFAULT NULL COMMENT '附件',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_personnel_papers`
-- -----------------------------
INSERT INTO `dp_personnel_papers` VALUES ('3', '2', '8', '13513542123', '农大', '1514908800', '1577980800', '1516896000', '', '4', '1516939850', '1516939850');

-- -----------------------------
-- 表结构 `dp_personnel_record`
-- -----------------------------
DROP TABLE IF EXISTS `dp_personnel_record`;
CREATE TABLE `dp_personnel_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `record_code` varchar(20) NOT NULL COMMENT '档案编号',
  `con_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '员工类型 1 合同工 2 正式员工 3 临时工',
  `in_time` int(11) unsigned DEFAULT NULL COMMENT '入职时间',
  `zz_time` int(11) unsigned DEFAULT NULL COMMENT '转正时间',
  `lz_time` int(11) unsigned DEFAULT NULL COMMENT '离职时间',
  `person_card` varchar(30) DEFAULT NULL COMMENT '身份证号码',
  `hukou_type` tinyint(1) unsigned DEFAULT NULL COMMENT '户口类型  1 本地城镇 2 外地城镇 3 本地农村 4 外地农村',
  `hukou_address` varchar(120) DEFAULT NULL COMMENT '户口所在地',
  `old_name` varchar(50) DEFAULT NULL COMMENT '曾用名',
  `nation` varchar(50) DEFAULT NULL COMMENT '民族',
  `birth_address` varchar(120) DEFAULT NULL COMMENT '籍贯',
  `english_name` varchar(50) DEFAULT NULL COMMENT '英文名',
  `nationality` varchar(50) DEFAULT NULL COMMENT '国籍',
  `passport` varchar(50) DEFAULT NULL COMMENT '护照 ',
  `marriage` tinyint(1) unsigned DEFAULT NULL COMMENT '婚姻状态 0 未婚 1 已婚 2 离异 3 丧偶',
  `political` tinyint(1) unsigned DEFAULT NULL COMMENT '政治面貌 1 共青团员 2 预备党员 3 党员 4 民主党派 5 群众',
  `political_time` int(11) unsigned DEFAULT NULL COMMENT '政治面貌取得时间',
  `work_time` int(11) unsigned DEFAULT NULL COMMENT '参加工作时间',
  `professor_title` varchar(50) DEFAULT NULL COMMENT '职称',
  `professor_time` int(11) unsigned DEFAULT NULL COMMENT '职称取得时间',
  `education` smallint(2) unsigned DEFAULT NULL COMMENT '学历 1 小学 2 初中 3 中专 4 高中 5 大专 6 本科 7 硕士 8 博士 9 博士后',
  `degree` tinyint(1) unsigned DEFAULT NULL COMMENT '学位 1 学士 2 硕士 3 博士',
  `school` varchar(50) DEFAULT NULL COMMENT '毕业院校',
  `major` varchar(50) DEFAULT NULL COMMENT '所学专业',
  `graduation_time` int(11) unsigned DEFAULT NULL COMMENT '毕业时间',
  `hobby` varchar(120) DEFAULT NULL COMMENT '业余爱好',
  `health` varchar(50) DEFAULT NULL COMMENT '健康状况',
  `height` decimal(6,2) unsigned DEFAULT NULL COMMENT '身高 cm',
  `weight` decimal(6,2) unsigned DEFAULT NULL COMMENT '体重 kg',
  `bank` varchar(50) DEFAULT NULL COMMENT '银行',
  `bank_code` varchar(50) DEFAULT NULL COMMENT '银行卡号',
  `social_security` varchar(50) DEFAULT NULL COMMENT '社保卡号',
  `social_security_pc` varchar(50) DEFAULT NULL COMMENT '社保电脑号',
  `accumulation_fund` varchar(50) DEFAULT NULL COMMENT '公积金账号',
  `emergency_contact` varchar(50) DEFAULT NULL COMMENT '紧急联系人',
  `emergency_phone` varchar(20) DEFAULT NULL COMMENT '紧急联系人电话',
  `code` varchar(255) DEFAULT NULL COMMENT '备注',
  `enclosure` varchar(255) DEFAULT NULL COMMENT '附件',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_personnel_record`
-- -----------------------------
INSERT INTO `dp_personnel_record` VALUES ('1', '4', '20171010001', '1', '1515600000', '1516809600', '1516809600', '', '0', '', '', '', '', '', '', '', '0', '0', '1516809600', '1516723200', '', '1516723200', '0', '0', '', '', '1516723200', '', '', '0.00', '0.00', '', '', '', '', '', '', '', '', '1,2', '0', '1516763743');
INSERT INTO `dp_personnel_record` VALUES ('2', '3', '', '1', '0', '0', '0', '', '0', '', '', '', '', '', '', '', '0', '0', '0', '0', '', '0', '0', '0', '', '', '0', '', '', '0.00', '0.00', '', '', '', '', '', '', '', '', '', '0', '0');
INSERT INTO `dp_personnel_record` VALUES ('3', '2', '201801248479u2', '1', '0', '0', '0', '', '0', '', '', '', '', '', '', '', '0', '0', '0', '0', '', '0', '0', '0', '', '', '0', '', '', '0.00', '0.00', '', '', '', '', '', '', '', '', '', '1516778479', '1516778479');
INSERT INTO `dp_personnel_record` VALUES ('4', '1', '201801248676u1', '1', '0', '0', '0', '', '1', '', '', '', '', '', '', '', '0', '5', '0', '0', '', '0', '6', '1', '', '', '0', '', '', '0.00', '0.00', '', '', '', '', '', '', '', '', '', '1516778676', '1516778676');

-- -----------------------------
-- 表结构 `dp_personnel_recruit`
-- -----------------------------
DROP TABLE IF EXISTS `dp_personnel_recruit`;
CREATE TABLE `dp_personnel_recruit` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `title` varchar(100) NOT NULL COMMENT '招聘标题',
  `description` varchar(255) NOT NULL COMMENT '描述',
  `info` varchar(255) NOT NULL COMMENT '招聘主体',
  `note` varchar(255) NOT NULL COMMENT '备注',
  `recruit_time` int(11) unsigned NOT NULL COMMENT '期望到岗时间',
  `status` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '状态 -1 待审 0 拒绝 1 通过',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '签到时间',
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_personnel_recruit`
-- -----------------------------
INSERT INTO `dp_personnel_recruit` VALUES ('1', '2', '', '', '', '', '0', '1', '1517382104', '0');
INSERT INTO `dp_personnel_recruit` VALUES ('17', '2', '', '', '', '', '0', '1', '1517457746', '0');
INSERT INTO `dp_personnel_recruit` VALUES ('18', '3', '', '', '', '', '0', '1', '1517561653', '1517561653');
INSERT INTO `dp_personnel_recruit` VALUES ('19', '3', '', '', '', '', '0', '1', '1517561704', '1517561704');
INSERT INTO `dp_personnel_recruit` VALUES ('20', '3', '', '', '', '', '0', '1', '1517561796', '1517561796');
INSERT INTO `dp_personnel_recruit` VALUES ('21', '1', '', '', '', '', '0', '1', '1517563062', '1517563062');
INSERT INTO `dp_personnel_recruit` VALUES ('22', '1', '', '', '', '', '0', '1', '1517563334', '1517563334');
INSERT INTO `dp_personnel_recruit` VALUES ('23', '3', '技术部招聘需求', '技术部招聘需求', '<p>技术部招聘需求</p>', '技术部招聘需求', '1519488000', '1', '1518060161', '1518075345');

-- -----------------------------
-- 表结构 `dp_personnel_sign`
-- -----------------------------
DROP TABLE IF EXISTS `dp_personnel_sign`;
CREATE TABLE `dp_personnel_sign` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '签到时间',
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_personnel_sign`
-- -----------------------------
INSERT INTO `dp_personnel_sign` VALUES ('1', '2', '1517382104');
INSERT INTO `dp_personnel_sign` VALUES ('2', '2', '1517270400');
INSERT INTO `dp_personnel_sign` VALUES ('3', '1', '1517184000');
INSERT INTO `dp_personnel_sign` VALUES ('14', '1', '1517392512');
INSERT INTO `dp_personnel_sign` VALUES ('16', '1', '1517457746');
INSERT INTO `dp_personnel_sign` VALUES ('17', '2', '1517457746');
INSERT INTO `dp_personnel_sign` VALUES ('18', '1', '1517796301');
INSERT INTO `dp_personnel_sign` VALUES ('19', '1', '1520050327');
INSERT INTO `dp_personnel_sign` VALUES ('20', '1', '1520565352');

-- -----------------------------
-- 表结构 `dp_personnel_wage`
-- -----------------------------
DROP TABLE IF EXISTS `dp_personnel_wage`;
CREATE TABLE `dp_personnel_wage` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `wage_type` tinyint(1) unsigned NOT NULL COMMENT '工资类型  1 计时工资 2 计件 ',
  `base_pay` decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '基本工资',
  `merit_pay` decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '绩效工资',
  `piece_pay` decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '计件工资',
  `unit` varchar(20) NOT NULL COMMENT '单位',
  `total_pay` decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '工资总额',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '签到时间',
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_personnel_wage`
-- -----------------------------
INSERT INTO `dp_personnel_wage` VALUES ('1', '2', '1', '0.00', '0.00', '0.00', '', '0.00', '1517382104', '0');
INSERT INTO `dp_personnel_wage` VALUES ('2', '2', '1', '0.00', '0.00', '0.00', '', '0.00', '1517270400', '0');
INSERT INTO `dp_personnel_wage` VALUES ('3', '1', '2', '0.00', '0.00', '0.00', '', '0.00', '1517184000', '0');
INSERT INTO `dp_personnel_wage` VALUES ('14', '1', '2', '0.00', '0.00', '0.00', '', '0.00', '1517392512', '0');
INSERT INTO `dp_personnel_wage` VALUES ('16', '1', '2', '4000.00', '0.00', '15.00', '件', '0.00', '1517457746', '1517473840');
INSERT INTO `dp_personnel_wage` VALUES ('17', '2', '2', '0.00', '0.00', '0.00', '', '0.00', '1517457746', '0');
INSERT INTO `dp_personnel_wage` VALUES ('18', '3', '1', '4500.00', '1500.00', '0.00', '', '0.00', '1517556930', '1517556930');

-- -----------------------------
-- 表结构 `dp_personnel_wagecate`
-- -----------------------------
DROP TABLE IF EXISTS `dp_personnel_wagecate`;
CREATE TABLE `dp_personnel_wagecate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '奖惩项目',
  `sort` smallint(4) unsigned DEFAULT '100' COMMENT '排序',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '0 禁用 1 启用',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_personnel_wagecate`
-- -----------------------------
INSERT INTO `dp_personnel_wagecate` VALUES ('1', '计时工资', '100', '1', '1517551810', '1517556712');
INSERT INTO `dp_personnel_wagecate` VALUES ('2', '计件工资', '100', '1', '1517551810', '1517551810');

-- -----------------------------
-- 表结构 `dp_personnel_wagelist`
-- -----------------------------
DROP TABLE IF EXISTS `dp_personnel_wagelist`;
CREATE TABLE `dp_personnel_wagelist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `wage_type` tinyint(1) unsigned NOT NULL COMMENT '工资类型  1 计时工资 2 计件 ',
  `base_pay` decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '基本工资',
  `merit_pay` decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '绩效工资',
  `piece_pay` decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '计件工资',
  `extro_pay` decimal(6,2) unsigned NOT NULL COMMENT '额外工资 ',
  `total_pay` decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '工资总额',
  `wage_time` int(11) unsigned NOT NULL COMMENT '工资时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '签到时间',
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_personnel_wagelist`
-- -----------------------------
INSERT INTO `dp_personnel_wagelist` VALUES ('1', '2', '0', '0.00', '0.00', '0.00', '0.00', '0.00', '0', '1517382104', '0');
INSERT INTO `dp_personnel_wagelist` VALUES ('2', '2', '0', '0.00', '0.00', '0.00', '0.00', '0.00', '0', '1517270400', '0');
INSERT INTO `dp_personnel_wagelist` VALUES ('17', '2', '0', '0.00', '0.00', '0.00', '0.00', '0.00', '0', '1517457746', '0');
INSERT INTO `dp_personnel_wagelist` VALUES ('18', '3', '1', '4500.00', '1500.00', '0.00', '0.00', '6000.00', '1517414400', '1517561653', '1517561653');
INSERT INTO `dp_personnel_wagelist` VALUES ('19', '3', '1', '4500.00', '1500.00', '0.00', '0.00', '6000.00', '1517414400', '1517561704', '1517561704');
INSERT INTO `dp_personnel_wagelist` VALUES ('20', '3', '1', '4500.00', '1500.00', '0.00', '0.00', '6000.00', '1517414400', '1517561796', '1517561796');
INSERT INTO `dp_personnel_wagelist` VALUES ('21', '1', '2', '4500.00', '0.00', '1500.00', '0.00', '6000.00', '1517414400', '1517563062', '1517563062');
INSERT INTO `dp_personnel_wagelist` VALUES ('22', '1', '2', '5000.00', '0.00', '1600.00', '0.00', '6600.00', '1514736000', '1517563334', '1517563334');

-- -----------------------------
-- 表结构 `dp_personnel_work`
-- -----------------------------
DROP TABLE IF EXISTS `dp_personnel_work`;
CREATE TABLE `dp_personnel_work` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `on_time` int(11) unsigned NOT NULL COMMENT '上班时间',
  `off_time` int(11) unsigned NOT NULL COMMENT '下班时间',
  `create_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- -----------------------------
-- 表数据 `dp_personnel_work`
-- -----------------------------
INSERT INTO `dp_personnel_work` VALUES ('1', '2', '1517382104', '0', '0');
INSERT INTO `dp_personnel_work` VALUES ('2', '2', '1517270400', '0', '0');
INSERT INTO `dp_personnel_work` VALUES ('3', '1', '1517184000', '1520565357', '0');
INSERT INTO `dp_personnel_work` VALUES ('14', '1', '1517392512', '1520565357', '0');
INSERT INTO `dp_personnel_work` VALUES ('16', '1', '1517457746', '1520565357', '0');
INSERT INTO `dp_personnel_work` VALUES ('17', '2', '1517457746', '0', '0');
INSERT INTO `dp_personnel_work` VALUES ('18', '1', '1517796301', '1520565357', '0');
INSERT INTO `dp_personnel_work` VALUES ('20', '1', '1520050327', '1520565357', '0');
INSERT INTO `dp_personnel_work` VALUES ('21', '1', '1520050327', '1520565357', '0');
INSERT INTO `dp_personnel_work` VALUES ('30', '1', '1520240549', '1520565357', '1520240549');
INSERT INTO `dp_personnel_work` VALUES ('31', '1', '1520565354', '1520565357', '1520565354');
