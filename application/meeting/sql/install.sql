-- -----------------------------
-- 导出时间 `2018-03-09 16:24:06`
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
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_meeting_list`
-- -----------------------------
INSERT INTO `dp_meeting_list` VALUES ('1', '', '1516157016', '1516160616', '如何演绎塞蛋的传奇', '1', '1', '3');
INSERT INTO `dp_meeting_list` VALUES ('2', '', '09:00', '10:00', '塞蛋少年', '0', '2', '3');
INSERT INTO `dp_meeting_list` VALUES ('3', '', '09:00', '10:00', '塞蛋少年', '0', '5', '3');
INSERT INTO `dp_meeting_list` VALUES ('4', '1516592586', '09:00', '13:00', '测试测试测试', '0', '4', '3');
INSERT INTO `dp_meeting_list` VALUES ('5', '1516636800', '1516586400', '1516590000', '塞蛋少年', '0', '2', '3');
INSERT INTO `dp_meeting_list` VALUES ('6', '1516723200', '1516579200', '1516582800', 'asd ', '0', '5', '3');
INSERT INTO `dp_meeting_list` VALUES ('7', '1516636800', '1516615200', '1516611600', '阿斯达的', '0', '4', '3');
INSERT INTO `dp_meeting_list` VALUES ('8', '2018-01-25', '09:00', '17:00', 'fdgfd', '0', '7', '1');
INSERT INTO `dp_meeting_list` VALUES ('9', '1517414400', '1516626000', '1516629600', 'dsdsfd', '0', '2,4,5,7', '3');
INSERT INTO `dp_meeting_list` VALUES ('10', '1516636800', '1516581300', '1516615200', 'asdasd', '0', '1,2,4,5,7,8,12,13', '3');
INSERT INTO `dp_meeting_list` VALUES ('11', '1516809600', '1516665600', '1516687200', '似懂非懂是', '0', '2,4,5', '3');
INSERT INTO `dp_meeting_list` VALUES ('12', '2018-01-24', '05:00', '06:00', 'asd', '0', '2', '2');

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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_meeting_rooms`
-- -----------------------------
INSERT INTO `dp_meeting_rooms` VALUES ('1', '大会议室', '80', '投影仪,音响,话筒', '1');
INSERT INTO `dp_meeting_rooms` VALUES ('2', '中会议室', '60', '投影仪,电脑', '2');
