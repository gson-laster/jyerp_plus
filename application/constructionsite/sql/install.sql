-- -----------------------------
-- 导出时间 `2018-04-23 16:25:45`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_constructionsite_change`
-- -----------------------------
DROP TABLE IF EXISTS `dp_constructionsite_change`;
CREATE TABLE `dp_constructionsite_change` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '名称',
  `wid` int(10) unsigned NOT NULL COMMENT '填报人',
  `xid` int(10) unsigned NOT NULL COMMENT '项目id',
  `hid` int(10) unsigned NOT NULL COMMENT '合同id',
  `ti_username` varchar(50) NOT NULL COMMENT '提出变更者',
  `old_imgs` varchar(255) NOT NULL COMMENT '原图纸',
  `old_file` varchar(255) NOT NULL COMMENT '原附件',
  `new_imgs` varchar(255) NOT NULL COMMENT '变更后图纸',
  `new_file` varchar(255) NOT NULL COMMENT '变更后附件',
  `cause` text NOT NULL COMMENT '变更原因',
  `content` text NOT NULL COMMENT '变更内容',
  `money` int(10) unsigned NOT NULL COMMENT '变更金额',
  `create_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_constructionsite_change`
-- -----------------------------
INSERT INTO `dp_constructionsite_change` VALUES ('1', '金耀科技运动场地设施变更', '1', '1', '1', '金耀老板', '7', '9', '4', '8', '场地太小,原先设计太大', '1. 乒乓球台最小号2.跑步机5台', '500000', '1547895474');

-- -----------------------------
-- 表结构 `dp_constructionsite_log`
-- -----------------------------
DROP TABLE IF EXISTS `dp_constructionsite_log`;
CREATE TABLE `dp_constructionsite_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `xid` int(10) unsigned zerofill NOT NULL COMMENT '项目id',
  `wid` int(10) unsigned zerofill NOT NULL COMMENT '填报人',
  `create_time` int(10) unsigned zerofill NOT NULL COMMENT '填写时间',
  `am_weather` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0' COMMENT '上午天气 0 晴 1阴 2雨',
  `pm_weather` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0' COMMENT '下午天气',
  `max_warm` int(10) unsigned zerofill NOT NULL COMMENT '最高温度',
  `min_warm` int(10) unsigned zerofill NOT NULL COMMENT '最低温度',
  `cid` int(10) unsigned zerofill NOT NULL COMMENT '车间id',
  `work_num` int(10) unsigned zerofill NOT NULL COMMENT '工人人数',
  `work_content` text NOT NULL COMMENT '施工内容',
  `work_wrong` text NOT NULL COMMENT '施工遇到的问题',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_constructionsite_log`
-- -----------------------------
INSERT INTO `dp_constructionsite_log` VALUES ('5', '1', '1', '1524208080', '1', '1', '30', '18', '30', '30', '1. 制作桌子50\r\n2. 制作椅子800', '材料浪费严重');

-- -----------------------------
-- 表结构 `dp_constructionsite_plan`
-- -----------------------------
DROP TABLE IF EXISTS `dp_constructionsite_plan`;
CREATE TABLE `dp_constructionsite_plan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '方案名称',
  `wid` int(10) unsigned NOT NULL COMMENT '填报人',
  `xid` int(11) unsigned NOT NULL COMMENT '项目',
  `content` text NOT NULL COMMENT '方案说明',
  `create_time` int(11) unsigned NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_constructionsite_plan`
-- -----------------------------
INSERT INTO `dp_constructionsite_plan` VALUES ('2', '嘎嘎', '1', '1', '<p>1.哈哈哈</p><p>2.哈哈哈</p><p>3.哈哈哈</p>', '1524455984');

-- -----------------------------
-- 表结构 `dp_constructionsite_tell`
-- -----------------------------
DROP TABLE IF EXISTS `dp_constructionsite_tell`;
CREATE TABLE `dp_constructionsite_tell` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '交底名称',
  `xid` int(10) unsigned NOT NULL COMMENT '项目id',
  `cid` int(10) unsigned NOT NULL COMMENT '施工车间',
  `wid` int(11) NOT NULL COMMENT '填报人',
  `tell_site` varchar(255) NOT NULL COMMENT '交底地点',
  `tell_user` varchar(255) NOT NULL COMMENT '交底人',
  `tell_receive_user` varchar(255) NOT NULL COMMENT '被交底人',
  `tell_content` text NOT NULL COMMENT '交底内容',
  `create_time` int(11) unsigned NOT NULL COMMENT '交底时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_constructionsite_tell`
-- -----------------------------
