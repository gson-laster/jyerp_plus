-- -----------------------------
-- 导出时间 `2018-02-05 10:39:48`
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
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;


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

