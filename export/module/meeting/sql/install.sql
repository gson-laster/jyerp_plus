-- -----------------------------
-- 导出时间 `2018-03-27 17:33:49`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_meeting_list`
-- -----------------------------
DROP TABLE IF EXISTS `dp_meeting_list`;
CREATE TABLE `dp_meeting_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `m_time` varchar(11) NOT NULL COMMENT '会议日期',
  `s_time` varchar(11) NOT NULL COMMENT '开始时间',
  `e_time` varchar(11) NOT NULL COMMENT '结束时间',
  `title` varchar(255) NOT NULL COMMENT '会议主题',
  `room_id` int(10) NOT NULL COMMENT '开会地点',
  `user_id` varchar(255) NOT NULL COMMENT '参会人员',
  `compare` int(10) NOT NULL COMMENT '主持人',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_meeting_list`
-- -----------------------------
INSERT INTO `dp_meeting_list` VALUES ('1', '1521216000', '1522117800', '1522121400', '开会测', '1', '1', '3');

-- -----------------------------
-- 表结构 `dp_meeting_rooms`
-- -----------------------------
DROP TABLE IF EXISTS `dp_meeting_rooms`;
CREATE TABLE `dp_meeting_rooms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '会议室名',
  `r_number` int(10) NOT NULL COMMENT '容纳人数',
  `r_resource` varchar(255) NOT NULL COMMENT '配置,资源',
  `r_sort` tinyint(1) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_meeting_rooms`
-- -----------------------------
INSERT INTO `dp_meeting_rooms` VALUES ('1', '大会议室', '80', '投影仪,音响,话筒', '1');
INSERT INTO `dp_meeting_rooms` VALUES ('2', '中会议室', '60', '投影仪,电脑', '2');
