-- -----------------------------
-- 导出时间 `2018-03-27 17:34:22`
-- -----------------------------

-- -----------------------------
-- 表结构 `dp_document_list`
-- -----------------------------
DROP TABLE IF EXISTS `dp_document_list`;
CREATE TABLE `dp_document_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL COMMENT '父级',
  `fileid` varchar(255) NOT NULL COMMENT '文档id',
  `name` varchar(255) NOT NULL COMMENT '文档名字',
  `type` varchar(255) NOT NULL DEFAULT '0' COMMENT '文件类型',
  `path` varchar(255) NOT NULL DEFAULT '0' COMMENT '文件路径',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;

-- -----------------------------
-- 表数据 `dp_document_list`
-- -----------------------------
INSERT INTO `dp_document_list` VALUES ('50', '1', '49', '6', '新建文本文档.txt', 'txt', '/uploads/files/20180309/8c496bf6abcb2a327553cd48e7d25222.txt', '1', '1520587623', '1520587623');
INSERT INTO `dp_document_list` VALUES ('49', '1', '0', '', '金耀科技规章制度', '0', '0', '1', '1520586519', '1520586519');
