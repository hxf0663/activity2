SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for act_config
-- ----------------------------
DROP TABLE IF EXISTS `act_config`;
CREATE TABLE `act_config` (
  `item` varchar(30) NOT NULL,
  `conf` text,
  PRIMARY KEY (`item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of act_config
-- ----------------------------
INSERT INTO `act_config` VALUES ('adminid', 'admin');
INSERT INTO `act_config` VALUES ('adminpwd', 'admin');
INSERT INTO `act_config` VALUES ('appid', '0');
INSERT INTO `act_config` VALUES ('appsecret', '0');
INSERT INTO `act_config` VALUES ('access_token', '');
INSERT INTO `act_config` VALUES ('access_token_expire', '0');
INSERT INTO `act_config` VALUES ('access_token_cache', '0');
INSERT INTO `act_config` VALUES ('jsapi_ticket', '');
INSERT INTO `act_config` VALUES ('jsapi_ticket_expire', '0');
INSERT INTO `act_config` VALUES ('jsapi_ticket_cache', '0');
INSERT INTO `act_config` VALUES ('pcnt', '5');
INSERT INTO `act_config` VALUES ('prize_name1', '一等奖品');
INSERT INTO `act_config` VALUES ('prize_angle1', '');
INSERT INTO `act_config` VALUES ('prize_chance1', '1000');
INSERT INTO `act_config` VALUES ('prize_cnt1', '100');
INSERT INTO `act_config` VALUES ('prize_daycnt1', '10');
INSERT INTO `act_config` VALUES ('prize_name2', '二等奖品');
INSERT INTO `act_config` VALUES ('prize_angle2', '');
INSERT INTO `act_config` VALUES ('prize_chance2', '1000');
INSERT INTO `act_config` VALUES ('prize_cnt2', '100');
INSERT INTO `act_config` VALUES ('prize_daycnt2', '10');
INSERT INTO `act_config` VALUES ('prize_name3', '三等奖品');
INSERT INTO `act_config` VALUES ('prize_angle3', '');
INSERT INTO `act_config` VALUES ('prize_chance3', '1000');
INSERT INTO `act_config` VALUES ('prize_cnt3', '100');
INSERT INTO `act_config` VALUES ('prize_daycnt3', '10');
INSERT INTO `act_config` VALUES ('prize_name4', '四等奖品');
INSERT INTO `act_config` VALUES ('prize_angle4', '');
INSERT INTO `act_config` VALUES ('prize_chance4', '1000');
INSERT INTO `act_config` VALUES ('prize_cnt4', '100');
INSERT INTO `act_config` VALUES ('prize_daycnt4', '10');
INSERT INTO `act_config` VALUES ('prize_name5', '五等奖品');
INSERT INTO `act_config` VALUES ('prize_angle5', '');
INSERT INTO `act_config` VALUES ('prize_chance5', '1000');
INSERT INTO `act_config` VALUES ('prize_cnt5', '100');
INSERT INTO `act_config` VALUES ('prize_daycnt5', '10');
INSERT INTO `act_config` VALUES ('prize_name6', '六等奖品');
INSERT INTO `act_config` VALUES ('prize_angle6', '');
INSERT INTO `act_config` VALUES ('prize_chance6', '1000');
INSERT INTO `act_config` VALUES ('prize_cnt6', '100');
INSERT INTO `act_config` VALUES ('prize_daycnt6', '10');
INSERT INTO `act_config` VALUES ('prize_name7', '七等奖品');
INSERT INTO `act_config` VALUES ('prize_angle7', '');
INSERT INTO `act_config` VALUES ('prize_chance7', '1000');
INSERT INTO `act_config` VALUES ('prize_cnt7', '100');
INSERT INTO `act_config` VALUES ('prize_daycnt7', '10');
INSERT INTO `act_config` VALUES ('prize_name8', '八等奖品');
INSERT INTO `act_config` VALUES ('prize_angle8', '');
INSERT INTO `act_config` VALUES ('prize_chance8', '1000');
INSERT INTO `act_config` VALUES ('prize_cnt8', '100');
INSERT INTO `act_config` VALUES ('prize_daycnt8', '10');
INSERT INTO `act_config` VALUES ('prize_name9', '九等奖品');
INSERT INTO `act_config` VALUES ('prize_angle9', '');
INSERT INTO `act_config` VALUES ('prize_chance9', '1000');
INSERT INTO `act_config` VALUES ('prize_cnt9', '100');
INSERT INTO `act_config` VALUES ('prize_daycnt9', '10');
INSERT INTO `act_config` VALUES ('prize_name10', '十等奖品');
INSERT INTO `act_config` VALUES ('prize_angle10', '');
INSERT INTO `act_config` VALUES ('prize_chance10', '1000');
INSERT INTO `act_config` VALUES ('prize_cnt10', '100');
INSERT INTO `act_config` VALUES ('prize_daycnt10', '10');
INSERT INTO `act_config` VALUES ('lottery_times_per_day', '30');

-- ----------------------------
-- Table structure for act_member
-- ----------------------------
DROP TABLE IF EXISTS `act_member`;
CREATE TABLE `act_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(60) NOT NULL,
  `nickname` varchar(30) NULL,
  `headimg` varchar(255) DEFAULT NULL,
  `name` varchar(20) DEFAULT NULL,
  `tel` varchar(11) DEFAULT NULL,
  `win_prize` tinyint(4) DEFAULT '0',
  `win_time` int(11) DEFAULT NULL,
  `win_date` date DEFAULT NULL,
  `lottery_times` tinyint(4) DEFAULT '0',
  `lottery_time` int(11) DEFAULT NULL,
  `click` int(11) DEFAULT '0',
  `sendtime` int(11) DEFAULT NULL,
  `fromip` varchar(16) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `openid_idx` (`openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of act_member
-- ----------------------------
INSERT INTO `act_member` VALUES ('1', 'test', 'huangxf', 'http://wechat.huangxf.com/activity2/public/addon/img/tx.jpg', 'a', '13333333333', '0', null, null, '0', null, '3', '1473508244', '0.0.0.0', '1');
INSERT INTO `act_member` VALUES ('2', 'test2', 'huangxf2', 'http://wechat.huangxf.com/activity2/public/addon/img/tx.jpg', 'b', '13333333333', '0', null, null, '0', null, '5', '1473508244', '0.0.0.0', '1');
INSERT INTO `act_member` VALUES ('3', 'test3', 'huangxf3', 'http://wechat.huangxf.com/activity2/public/addon/img/tx.jpg', 'c', '13333333333', '0', null, null, '0', null, '3', '1473508244', '0.0.0.0', '1');
INSERT INTO `act_member` VALUES ('4', 'test4', 'huangxf4', 'http://wechat.huangxf.com/activity2/public/addon/img/tx.jpg', 'd', '13333333333', '0', null, null, '0', null, '1', '1473508244', '0.0.0.0', '1');
INSERT INTO `act_member` VALUES ('5', 'test5', 'huangxf5', 'http://wechat.huangxf.com/activity2/public/addon/img/tx.jpg', 'e', '13333333333', '0', null, null, '0', null, '2', '1473508244', '0.0.0.0', '1');

-- ----------------------------
-- Table structure for act_click
-- ----------------------------
DROP TABLE IF EXISTS `act_click`;
CREATE TABLE `act_click` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `my_openid` varchar(60) NOT NULL,
  `by_openid` varchar(60) NOT NULL,
  `click_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `my_openid_idx` (`my_openid`),
  KEY `by_openid_idx` (`by_openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for act_coupon
-- ----------------------------
DROP TABLE IF EXISTS `act_coupon`;
CREATE TABLE `act_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status_idx` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for act_pic
-- ----------------------------
DROP TABLE IF EXISTS `act_pic`;
CREATE TABLE `act_pic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) DEFAULT '1',
  `openid` varchar(60) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `num` varchar(30) DEFAULT NULL,
  `coupon` varchar(30) DEFAULT NULL,
  `click` int(11) DEFAULT '0',
  `sendtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `openid_idx` (`openid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of act_pic
-- ----------------------------
INSERT INTO `act_pic` VALUES ('1', '1', 'test', 'pic.jpg', '123', null, '1', '1457427436');
INSERT INTO `act_pic` VALUES ('2', '1', 'test2', 'pic.jpg', '123', null, '5', '1457427436');
INSERT INTO `act_pic` VALUES ('3', '1', 'test3', 'pic.jpg', '123', null, '4', '1457427436');
INSERT INTO `act_pic` VALUES ('4', '1', 'test4', 'pic.jpg', '123', null, '2', '1457427436');
INSERT INTO `act_pic` VALUES ('5', '1', 'test5', 'pic.jpg', '123', null, '3', '1457427436');