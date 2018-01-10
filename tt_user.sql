/*
Navicat MySQL Data Transfer

Source Server         : 97.64.46.21
Source Server Version : 50556
Source Host           : 97.64.46.21:3306
Source Database       : qingyun

Target Server Type    : MYSQL
Target Server Version : 50556
File Encoding         : 65001

Date: 2018-01-10 13:55:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for qy_tt_user_all
-- ----------------------------
DROP TABLE IF EXISTS `tt_user`;
CREATE TABLE `tt_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志主键',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `reply_count` int(10) unsigned DEFAULT '0' COMMENT '评论数',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `offset` varchar(50) NOT NULL DEFAULT '' COMMENT 'offset',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT 'description',
  `bg_img_url` varchar(100) NOT NULL DEFAULT '' COMMENT 'bg_img_url',
  `share_url` varchar(100) NOT NULL DEFAULT '' COMMENT 'share_url',
  `mobile` varchar(30) NOT NULL DEFAULT '' COMMENT 'mobile',
  `score` decimal(12,11) NOT NULL DEFAULT '0.00000000000' COMMENT '分数',
  `is_pgc_author` int(2) DEFAULT '0' COMMENT 'is_pgc_author 不知什么意思',
  `user_profile_image_url` varchar(100) NOT NULL DEFAULT '' COMMENT '头像地址',
  `text` text COMMENT '评论内容',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '评论时间',
  `add_time` int(10) unsigned DEFAULT '0' COMMENT '记录添加时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `user_name` (`user_name`) USING BTREE,
  KEY `create_time` (`create_time`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='头条用户数据';
