/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50724
Source Host           : localhost:3306
Source Database       : tsubasa

Target Server Type    : MYSQL
Target Server Version : 50724
File Encoding         : 65001

Date: 2020-02-02 17:27:33
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for zc_admin
-- ----------------------------
DROP TABLE IF EXISTS `zc_admin`;
CREATE TABLE `zc_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 0.禁用 1.启用',
  `is_delete` tinyint(4) NOT NULL DEFAULT '1' COMMENT '删除 0.已删除 1.未删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COMMENT='管理员列表';

-- ----------------------------
-- Records of zc_admin
-- ----------------------------
INSERT INTO `zc_admin` VALUES ('1', 'admin', 'admin@qq.com', 'aafa61d9714223da1111ea2eef30e312', '1575622635', '1578110571', '1', '1');
INSERT INTO `zc_admin` VALUES ('10', 'test', 'test@qq.com', 'aafa61d9714223da1111ea2eef30e312', '1578110606', '1578110606', '1', '1');

-- ----------------------------
-- Table structure for zc_admin_role
-- ----------------------------
DROP TABLE IF EXISTS `zc_admin_role`;
CREATE TABLE `zc_admin_role` (
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '角色ID'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='管理员角色表';

-- ----------------------------
-- Records of zc_admin_role
-- ----------------------------
INSERT INTO `zc_admin_role` VALUES ('1', '6');
INSERT INTO `zc_admin_role` VALUES ('1', '5');
INSERT INTO `zc_admin_role` VALUES ('10', '6');

-- ----------------------------
-- Table structure for zc_permission
-- ----------------------------
DROP TABLE IF EXISTS `zc_permission`;
CREATE TABLE `zc_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父级ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '访问地址',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `display` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否显示 0否 1.是',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序 正序',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 0.禁用 1.启用',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否删除 0.已删除 1.未删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COMMENT='权限表';

-- ----------------------------
-- Records of zc_permission
-- ----------------------------
INSERT INTO `zc_permission` VALUES ('9', '0', '权限管理', '', 'fa fa-lock', '1', '1', '1', '1578104547', '1578104547', '1');
INSERT INTO `zc_permission` VALUES ('10', '9', '管理员列表', 'admin/admin/index', '', '1', '1', '1', '1578104576', '1578104677', '1');
INSERT INTO `zc_permission` VALUES ('11', '9', '角色列表', 'admin/role/index', '', '1', '2', '1', '1578104595', '1578104687', '1');
INSERT INTO `zc_permission` VALUES ('12', '9', '权限列表', 'admin/permission/index', '', '1', '3', '1', '1578104613', '1578104697', '1');
INSERT INTO `zc_permission` VALUES ('15', '10', '添加管理员', 'admin/admin/create', '', '0', '1', '1', '1578110253', '1578110253', '1');
INSERT INTO `zc_permission` VALUES ('16', '0', '图片上传', '', 'fa fa-file-image-o', '1', '2', '1', '1578143346', '1578143346', '1');
INSERT INTO `zc_permission` VALUES ('17', '16', '单张图片', 'admin/image/single', '', '1', '1', '1', '1578143371', '1578143371', '1');

-- ----------------------------
-- Table structure for zc_role
-- ----------------------------
DROP TABLE IF EXISTS `zc_role`;
CREATE TABLE `zc_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 0.禁用 1.启用',
  `is_delete` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否删除 0.已删除 1.未删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COMMENT='角色表';

-- ----------------------------
-- Records of zc_role
-- ----------------------------
INSERT INTO `zc_role` VALUES ('5', '超级管理员', '1578104635', '1578104635', '1', '1');
INSERT INTO `zc_role` VALUES ('6', '权限管理', '1578104661', '1578104661', '1', '1');

-- ----------------------------
-- Table structure for zc_role_permission
-- ----------------------------
DROP TABLE IF EXISTS `zc_role_permission`;
CREATE TABLE `zc_role_permission` (
  `role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '角色ID',
  `permission_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '权限ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色权限表';

-- ----------------------------
-- Records of zc_role_permission
-- ----------------------------
INSERT INTO `zc_role_permission` VALUES ('6', '9');
INSERT INTO `zc_role_permission` VALUES ('6', '12');
INSERT INTO `zc_role_permission` VALUES ('5', '9');
INSERT INTO `zc_role_permission` VALUES ('5', '10');
INSERT INTO `zc_role_permission` VALUES ('5', '15');
INSERT INTO `zc_role_permission` VALUES ('5', '11');
INSERT INTO `zc_role_permission` VALUES ('5', '12');
INSERT INTO `zc_role_permission` VALUES ('5', '16');
INSERT INTO `zc_role_permission` VALUES ('5', '17');
