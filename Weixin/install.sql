SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `lab_access_token`
-- ----------------------------
DROP TABLE IF EXISTS `lab_access_token`;
CREATE TABLE `lab_access_token` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(512) NOT NULL COMMENT 'token',
  `expires_in` int(10) DEFAULT NULL COMMENT '有效时间',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=utf8 COMMENT='access_token表';

-- ----------------------------
--  Table structure for `lab_auto_reply`
-- ----------------------------
DROP TABLE IF EXISTS `lab_auto_reply`;
CREATE TABLE `lab_auto_reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键词',
  `msg_type` char(50) DEFAULT 'text' COMMENT '消息类型',
  `content` text COMMENT '文本内容',
  `group_id` int(10) DEFAULT NULL COMMENT '图文',
  `image_id` int(10) unsigned DEFAULT NULL COMMENT '上传图片',
  `image_material` int(10) DEFAULT NULL COMMENT '素材图片id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COMMENT='关键词自动回复表';

-- ----------------------------
--  Table structure for `lab_custom_menu`
-- ----------------------------
DROP TABLE IF EXISTS `lab_custom_menu`;
CREATE TABLE `lab_custom_menu` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id(uuid)',
  `pid` int(10) DEFAULT '0' COMMENT '一级菜单',
  `title` varchar(50) NOT NULL COMMENT '菜单名',
  `type` varchar(30) DEFAULT 'click' COMMENT '类型',
  `sort` tinyint(4) DEFAULT '0' COMMENT '排序号',
  `keyword` varchar(100) DEFAULT NULL COMMENT '关联关键词',
  `url` varchar(255) DEFAULT NULL COMMENT '关联URL',
  `jump_type` char(10) DEFAULT '0' COMMENT '推送类型',
  `material_type` varchar(50) DEFAULT NULL COMMENT '素材类型',
  `createtime` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='微信自定义菜单表';

-- ----------------------------
--  Table structure for `lab_material_image`
-- ----------------------------
DROP TABLE IF EXISTS `lab_material_image`;
CREATE TABLE `lab_material_image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cover_id` int(10) DEFAULT NULL COMMENT '图片在本地的ID',
  `cover_url` varchar(255) DEFAULT NULL COMMENT '本地URL',
  `media_id` varchar(100) DEFAULT '0' COMMENT '微信端图文消息素材的media_id',
  `wechat_url` varchar(255) DEFAULT NULL COMMENT '微信端的图片地址',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `is_use` int(10) DEFAULT '1' COMMENT '可否使用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4617 DEFAULT CHARSET=utf8 COMMENT='微信图片素材表';

-- ----------------------------
--  Table structure for `lab_material_news`
-- ----------------------------
DROP TABLE IF EXISTS `lab_material_news`;
CREATE TABLE `lab_material_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(100) DEFAULT NULL COMMENT '标题',
  `author` varchar(30) DEFAULT NULL COMMENT '作者',
  `cover_id` int(10) unsigned DEFAULT NULL COMMENT '封面',
  `intro` varchar(255) DEFAULT NULL COMMENT '摘要',
  `content` longtext COMMENT '内容',
  `link` varchar(255) DEFAULT NULL COMMENT '外链',
  `group_id` int(10) DEFAULT '0' COMMENT '多图文组的ID',
  `thumb_media_id` varchar(100) DEFAULT NULL COMMENT '图文消息的封面图片素材id（必须是永久mediaID）',
  `media_id` varchar(100) DEFAULT '0' COMMENT '微信端图文消息素材的media_id',
  `ctime` int(10) DEFAULT NULL COMMENT '发布时间',
  `url` varchar(255) DEFAULT NULL COMMENT '图文页url',
  `is_use` int(10) DEFAULT '1' COMMENT '可否使用',
  `update_time` int(10) DEFAULT '0' COMMENT 'update_time',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=733 DEFAULT CHARSET=utf8 COMMENT='微信图文素材表';

-- ----------------------------
--  Table structure for `lab_picture`
-- ----------------------------
DROP TABLE IF EXISTS `lab_picture`;
CREATE TABLE `lab_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id自增',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `media_id` char(64) DEFAULT NULL,
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片链接',
  `category_id` int(255) DEFAULT '0',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `system` tinyint(10) DEFAULT '0' COMMENT '素材ID',
  PRIMARY KEY (`id`),
  KEY `status` (`id`,`status`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='图片素材表';


SET FOREIGN_KEY_CHECKS = 1;
