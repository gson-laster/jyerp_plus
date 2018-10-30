-- -----------------------------
-- 导出时间 `2018-02-05 10:41:20`
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
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

