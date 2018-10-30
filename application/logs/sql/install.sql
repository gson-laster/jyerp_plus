/*
Navicat MySQL Data Transfer

Source Server         : 192.168.0.109_3306
Source Server Version : 50553
Source Host           : 192.168.3.118:3306
Source Database       : jyerp_plus

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-07-06 17:28:46
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for dp_personnel_daily
-- ----------------------------
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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- ----------------------------
-- Records of dp_personnel_daily
-- ----------------------------
INSERT INTO `dp_personnel_daily` VALUES ('1', '2', '0', '', '', '', '0', '0', '1', '1517382104', '0');
INSERT INTO `dp_personnel_daily` VALUES ('17', '2', '0', '', '', '', '0', '0', '1', '1517457746', '0');
INSERT INTO `dp_personnel_daily` VALUES ('18', '3', '0', '', '', '', '0', '0', '1', '1517561653', '1517561653');
INSERT INTO `dp_personnel_daily` VALUES ('19', '3', '0', '', '', '', '0', '0', '1', '1517561704', '1517561704');
INSERT INTO `dp_personnel_daily` VALUES ('20', '3', '0', '', '', '', '0', '0', '1', '1517561796', '1517561796');
INSERT INTO `dp_personnel_daily` VALUES ('21', '1', '0', '', '', '', '0', '0', '1', '1517563062', '1517563062');
INSERT INTO `dp_personnel_daily` VALUES ('22', '1', '0', '', '', '', '0', '0', '1', '1517563334', '1517563334');
INSERT INTO `dp_personnel_daily` VALUES ('23', '3', '0', '技术部招聘需求', '<p>技术部招聘需求</p>', '技术部招聘需求', '1519488000', '0', '1', '1518060161', '1518075345');
INSERT INTO `dp_personnel_daily` VALUES ('24', '1', '1', '2018年2月9日报告', '<p>2018年2月9日报告</p><p><br/></p><p>2018年2月9日报告</p><p>2018年2月9日报告</p>', '', '1519488000', '0', '1', '1518146852', '1529399560');
INSERT INTO `dp_personnel_daily` VALUES ('25', '1', '1', 'test', '', '', '1530028800', '1', '1', '1529920856', '1530861780');

-- ----------------------------
-- Table structure for dp_personnel_plan
-- ----------------------------
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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='签到分享表';

-- ----------------------------
-- Records of dp_personnel_plan
-- ----------------------------
INSERT INTO `dp_personnel_plan` VALUES ('1', '2', '0', '', '', '', '0', '0', '1', '1517382104', '0');
INSERT INTO `dp_personnel_plan` VALUES ('17', '2', '0', '', '', '', '0', '0', '1', '1517457746', '0');
INSERT INTO `dp_personnel_plan` VALUES ('18', '3', '0', '', '', '', '0', '0', '1', '1517561653', '1517561653');
INSERT INTO `dp_personnel_plan` VALUES ('19', '3', '0', '', '', '', '0', '0', '1', '1517561704', '1517561704');
INSERT INTO `dp_personnel_plan` VALUES ('20', '3', '0', '', '', '', '0', '0', '1', '1517561796', '1517561796');
INSERT INTO `dp_personnel_plan` VALUES ('21', '1', '0', '', '', '', '0', '0', '1', '1517563062', '1517563062');
INSERT INTO `dp_personnel_plan` VALUES ('22', '1', '0', '', '', '', '0', '0', '1', '1517563334', '1517563334');
INSERT INTO `dp_personnel_plan` VALUES ('23', '3', '0', '技术部招聘需求', '<p>技术部招聘需求</p>', '技术部招聘需求', '1519488000', '0', '1', '1518060161', '1530861950');
INSERT INTO `dp_personnel_plan` VALUES ('24', '1', '1', '2018年2月9日报告', '<p>2018年2月9日报告</p><p><br/></p><p>2018年2月9日报告</p><p>2018年2月9日报告</p>', '', '1519488000', '0', '1', '1518146852', '1530785032');
INSERT INTO `dp_personnel_plan` VALUES ('25', '1', '1', 'test', '<p>test</p>', '', '1530028800', '1', '0', '1529920877', '1529920877');
