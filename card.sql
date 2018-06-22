/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50540
Source Host           : 127.0.0.1:53342
Source Database       : card

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2018-01-03 10:42:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cards
-- ----------------------------
DROP TABLE IF EXISTS `cards`;
CREATE TABLE `cards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `good_id` int(11) NOT NULL,
  `card` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of cards
-- ----------------------------
INSERT INTO `cards` VALUES ('1', '1', '123456', '1', '0', '2018-01-03 10:39:28', '2018-01-03 10:39:28');

-- ----------------------------
-- Table structure for card_order
-- ----------------------------
DROP TABLE IF EXISTS `card_order`;
CREATE TABLE `card_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of card_order
-- ----------------------------

-- ----------------------------
-- Table structure for goods
-- ----------------------------
DROP TABLE IF EXISTS `goods`;
CREATE TABLE `goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `sold_count` int(11) NOT NULL DEFAULT '0',
  `all_count` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of goods
-- ----------------------------
INSERT INTO `goods` VALUES ('1', '1', '测试商品', '这里是测试商品的一段描述, 可以插入html文本', '0', '10', '1', '1', '2018-01-03 10:39:28', '2018-01-03 10:39:28');

-- ----------------------------
-- Table structure for groups
-- ----------------------------
DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of groups
-- ----------------------------
INSERT INTO `groups` VALUES ('1', '测试分组', '1', '2018-01-03 10:39:28', '2018-01-03 10:39:28');

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES ('1', '2014_10_12_000000_create_users_table', '1');
INSERT INTO `migrations` VALUES ('2', '2017_12_23_223031_create_groups_table', '1');
INSERT INTO `migrations` VALUES ('3', '2017_12_23_223124_create_goods_table', '1');
INSERT INTO `migrations` VALUES ('4', '2017_12_23_223252_create_cards_table', '1');
INSERT INTO `migrations` VALUES ('5', '2017_12_23_223508_create_orders_table', '1');
INSERT INTO `migrations` VALUES ('6', '2017_12_23_223755_create_pays_table', '1');
INSERT INTO `migrations` VALUES ('7', '2018_01_02_142012_create_card_order', '1');

-- ----------------------------
-- Table structure for orders
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `good_id` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `email` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_sent` tinyint(1) NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `amount` int(11) NOT NULL,
  `pay_id` int(11) NOT NULL,
  `pay_trade_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid` tinyint(1) NOT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_order_no_index` (`order_no`(250))
) ENGINE=MyISAM AUTO_INCREMENT=1001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of orders
-- ----------------------------

-- ----------------------------
-- Table structure for pays
-- ----------------------------
DROP TABLE IF EXISTS `pays`;
CREATE TABLE `pays` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `img` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `way` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `config` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `enabled` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of pays
-- ----------------------------
INSERT INTO `pays` VALUES ('1', '支付宝', '/plugins/images/ali.png', 'Alipay', 'Alipay', '{\n  \"partner\": \"partner\",\n  \"key\": \"key\"\n}', null, '1', '2018-01-03 10:39:28', '2018-01-03 10:39:28');
INSERT INTO `pays` VALUES ('2', '手机支付宝', '/plugins/images/ali.png', 'Aliwap', 'Aliwap', '{\n  \"partner\": \"partner\",\n  \"key\": \"key\"\n}', null, '2', '2018-01-03 10:39:28', '2018-01-03 10:39:28');
INSERT INTO `pays` VALUES ('3', '支付宝扫码(当面付)', '/plugins/images/ali.png', 'Aliqr', 'Aliqr', '{\n  \"app_id\": \"app_id\",\n  \"alipay_public_key\": \"alipay_public_key\",\n  \"merchant_private_key\": \"merchant_private_key\"\n}', null, '3', '2018-01-03 10:39:28', '2018-01-03 10:39:28');
INSERT INTO `pays` VALUES ('4', '微信扫码支付', '/plugins/images/wx.png', 'Wechat', 'Wechat', '{\n  \"APPID\": \"APPID\",\n  \"MCHID\": \"商户ID\",\n  \"KEY\": \"KEY\",\n  \"APPSECRET\": \"APPSECRET\"\n}', null, '3', '2018-01-03 10:39:28', '2018-01-03 10:39:28');
INSERT INTO `pays` (`id`, `name`, `img`, `driver`, `way`, `config`, `comment`, `enabled`, `created_at`, `updated_at`) VALUES
(5, '支付宝', '/plugins/images/ali.png', 'Fakala', 'alipay', '{\n  "api_id": "api_id",\n  "api_key": "api_key"\n}', 'alipay', 3, '2018-06-17 06:20:24', '2018-06-21 12:28:07'),
(6, '手机支付宝', '/plugins/images/ali.png', 'Fakala', 'alipaywap', '{\n  "api_id": "api_id",\n  "api_key": "api_key"\n}', 'alipaywap', 3, '2018-06-17 07:09:27', '2018-06-21 12:30:22'),
(7, '手机微信', '/plugins/images/wx.png', 'Fakala', 'wxwap', '{\n  "api_id": "api_id",\n  "api_key": "api_key"\n}', 'wxwap', 3, '2018-06-17 07:10:09', '2018-06-21 12:28:29'),
(8, '微信', '/plugins/images/wx.png', 'Fakala', 'wx', '{\n  "api_id": "api_id",\n  "api_key": "api_key"\n}', 'wx', 3, '2018-06-17 07:13:56', '2018-06-21 12:30:30');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'admin', '$2y$10$uc/Fk0Q2vlonDA.jOgTFeeQv5uD20syRJm2msbTL0RgIMMigbIji6', 'UEJG8y8YgTw0C7mTkLHfMKL6FPnzaW8nDleBAIgFkLQdmmWYV5vhHZkrvSSX', '2018-01-03 10:39:28', '2018-01-03 10:39:28');
SET FOREIGN_KEY_CHECKS=1;
