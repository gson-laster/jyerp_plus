-- -----------------------------
-- 导出时间 `2018-05-31 17:11:26`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_supplier_phone`
-- -----------------------------
DROP TABLE IF EXISTS `dp_supplier_phone`;
CREATE TABLE `dp_supplier_phone` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sid` int(10) unsigned NOT NULL COMMENT '供应商id',
  `name` varchar(255) NOT NULL COMMENT '主题',
  `susername` varchar(255) NOT NULL COMMENT '供应商联络人',
  `stime` int(10) unsigned NOT NULL COMMENT '联络时间',
  `cause` tinyint(1) unsigned NOT NULL COMMENT '1寻找新客户 2老客户跟踪 3 电话回访',
  `type` tinyint(1) unsigned NOT NULL COMMENT '联络方式 1手机 2电话  3微信 4qq 5邮件 6传真',
  `uid` int(10) unsigned NOT NULL COMMENT '我方联络人',
  `content` text NOT NULL COMMENT '联络内容',
  `file` varchar(255) DEFAULT NULL COMMENT '附加',
  `number` varchar(255) NOT NULL COMMENT '编号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_supplier_phone`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_supplier_list`
-- -----------------------------
DROP TABLE IF EXISTS `dp_supplier_list`;
CREATE TABLE `dp_supplier_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(255) NOT NULL COMMENT '供应商编号',
  `name` varchar(255) NOT NULL COMMENT '供应商名称',
  `type` int(10) unsigned NOT NULL COMMENT '供应商类型id',
  `susername` varchar(255) NOT NULL COMMENT '供应商联络人',
  `phone` varchar(50) DEFAULT NULL COMMENT '手机号',
  `tel` varchar(50) DEFAULT NULL COMMENT '电话',
  `wechat` varchar(255) DEFAULT '' COMMENT '微信',
  `qq` varchar(255) DEFAULT NULL COMMENT 'qq',
  `email` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `chuanzhen` varchar(255) DEFAULT NULL COMMENT '传真',
  `address` varchar(255) DEFAULT NULL COMMENT '公司地址',
  `content` text COMMENT '经营范围',
  `bankname` varchar(255) DEFAULT NULL COMMENT '开户行',
  `bankuser` varchar(255) DEFAULT NULL COMMENT '户名',
  `banknumber` varchar(255) DEFAULT NULL COMMENT '卡号',
  `stime` int(11) DEFAULT NULL COMMENT '成立时间',
  `suser` varchar(255) DEFAULT NULL COMMENT '法人代表',
  `yingye` varchar(255) DEFAULT NULL COMMENT '营业执照号',
  `wid` int(11) unsigned NOT NULL COMMENT '建档人',
  `remark` text COMMENT '备注',
  `create_time` int(10) unsigned NOT NULL COMMENT '建档时间',
  `file` varchar(255) DEFAULT NULL COMMENT '附件',
  `purchas_user` int(10) unsigned NOT NULL COMMENT '联络人',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1 可见 0不可见',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_supplier_list`
-- -----------------------------
INSERT INTO `dp_supplier_list` VALUES ('3', 'GYS20180521150030', '一级金耀钢板供应', '1', '小王', '18756468546', '0233213213', '2312321312', '145874586', '21321@qq.com', '321321321321321', '湾里', '一级钢板制作', '中国银行', '小王', '64816464151515154545', '1525968000', '小王', '151564654156465156', '1', '', '1526886131', '', '2', '1');
INSERT INTO `dp_supplier_list` VALUES ('4', 'GYS20180521150354', '二三科技', '3', '小李', '18777777777', '4546545645', '45465454', '45454545445', '454545454@qq.com', '45465456465465', '湾里', '二级钢板制作', '中国建行', '小王', '456415465154651234651', '1525276800', '小王', '1654534651534351', '1', '', '1526886234', '', '3', '1');

-- -----------------------------
-- 表结构 `dp_supplier_res`
-- -----------------------------
DROP TABLE IF EXISTS `dp_supplier_res`;
CREATE TABLE `dp_supplier_res` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sid` int(10) unsigned NOT NULL COMMENT '供应商id',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0 低 1中 2高',
  `uid` int(10) unsigned NOT NULL COMMENT '推荐人',
  `cause` text NOT NULL COMMENT '推荐理由',
  `file` varchar(255) DEFAULT NULL COMMENT '附件',
  `res` varchar(255) NOT NULL COMMENT '物品名称',
  `create_time` int(10) unsigned NOT NULL COMMENT '推荐时间',
  `number` varchar(255) NOT NULL COMMENT '编号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_supplier_res`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_supplier_type`
-- -----------------------------
DROP TABLE IF EXISTS `dp_supplier_type`;
CREATE TABLE `dp_supplier_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '供应商类型',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_supplier_type`
-- -----------------------------
INSERT INTO `dp_supplier_type` VALUES ('1', '一级供应商');
INSERT INTO `dp_supplier_type` VALUES ('3', '二级供应商');
