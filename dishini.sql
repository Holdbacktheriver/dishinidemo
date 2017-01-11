/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50713
Source Host           : localhost:3306
Source Database       : dishini

Target Server Type    : MYSQL
Target Server Version : 50713
File Encoding         : 65001

Date: 2016-08-05 15:00:29
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `dsn_member`
-- ----------------------------
DROP TABLE IF EXISTS `dsn_member`;
CREATE TABLE `dsn_member` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL DEFAULT '' COMMENT '微信昵称',
  `truename` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `headimg` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `openid` varchar(50) NOT NULL DEFAULT '' COMMENT 'openid',
  `mobile` varchar(15) NOT NULL DEFAULT '' COMMENT '手机号码',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '性别（1：男；0：女）',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `province` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '省份',
  `city` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '城市',
  `area` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '地区',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  `brithday` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '生日',
  `papers` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '证件类型（1：身份证）',
  `paperscode` varchar(50) NOT NULL DEFAULT '' COMMENT '证件编号',
  `integral` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '积分',
  `grade` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '等级',
  `experience` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '经验值',
  `staffid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '推荐员工id',
  `cardnum` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '卡券数量',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态（0：删除；1：正常）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=89 DEFAULT CHARSET=utf8 COMMENT='会员表';

-- ----------------------------
-- Records of dsn_member
-- ----------------------------

-- ----------------------------
-- Table structure for `dsn_order`
-- ----------------------------
DROP TABLE IF EXISTS `dsn_order`;
CREATE TABLE `dsn_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL COMMENT '订单号',
  `order_wx_no` varchar(50) DEFAULT NULL COMMENT '微信订单号',
  `number` int(11) NOT NULL COMMENT '购买数量',
  `product` varchar(50) NOT NULL COMMENT '产品名称',
  `phone` varchar(15) NOT NULL COMMENT '手机号',
  `paytime` int(11) NOT NULL DEFAULT '0' COMMENT '付款时间',
  `datetime` int(11) DEFAULT NULL COMMENT '用票日期',
  `price` float(6,1) NOT NULL COMMENT '价格',
  `openid` varchar(50) DEFAULT NULL COMMENT '用户openid',
  `addr` varchar(225) NOT NULL COMMENT '收货地址',
  `remark` varchar(100) NOT NULL COMMENT '备注：',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '支付状态1=未支付，2=已支付,3=取消',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dsn_order
-- ----------------------------
INSERT INTO `dsn_order` VALUES ('1', '20160803153427\r\n20160803153427', '20160803153427', '0', '', '', '0', '0', '0.0', '20160803153427', '权威的期望', '', '1', '1470209741', '1470209741');
INSERT INTO `dsn_order` VALUES ('2', '201608031800010000002', null, '2', '成人票1张,儿童票1张', '13871312136', '0', '1470153600', '400.0', null, '3台调4股份4高4改4高4改4', '本产品不用送货', '1', '1470218401', '0');
INSERT INTO `dsn_order` VALUES ('3', '201608031803150000003', null, '2', '成人票1张,儿童票1张', '13871312136', '0', '1470153600', '400.0', null, '3台调4股份4高4改4高4改4', '本产品不用送货', '1', '1470218595', '0');
INSERT INTO `dsn_order` VALUES ('4', '201608031803450000004', null, '2', '成人票1张,儿童票1张', '13871312136', '0', '1470153600', '400.0', null, '违法未v我恩v我恩v我恩v ', '本产品不用送货', '1', '1470218625', '0');

-- ----------------------------
-- Table structure for `dsn_product`
-- ----------------------------
DROP TABLE IF EXISTS `dsn_product`;
CREATE TABLE `dsn_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticketname` varchar(40) NOT NULL COMMENT '票种名称',
  `ticketid` varchar(32) NOT NULL COMMENT '票id',
  `ticketprice` float(6,2) NOT NULL DEFAULT '0.00' COMMENT '票价格',
  `ticketcount` int(11) NOT NULL DEFAULT '0' COMMENT '应该是库存',
  `agentprice` float(6,2) NOT NULL DEFAULT '0.00' COMMENT '代理价格',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dsn_product
-- ----------------------------
INSERT INTO `dsn_product` VALUES ('1', '成人票（测试）', 'G1094174T19825', '1.00', '100', '1.00');
INSERT INTO `dsn_product` VALUES ('2', '儿童票（测试）', 'G1094174T19826', '1.00', '100', '1.00');
