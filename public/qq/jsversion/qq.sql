/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : qq

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-04-02 23:56:34
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for info
-- ----------------------------
DROP TABLE IF EXISTS `info`;
CREATE TABLE `info` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `avatar` varchar(50) NOT NULL COMMENT '头像',
  `new_message` varchar(200) NOT NULL COMMENT '消息内容',
  `message_number` tinyint(8) unsigned NOT NULL DEFAULT '0' COMMENT '新消息数量',
  `date_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of info
-- ----------------------------
INSERT INTO `info` VALUES ('1', '张萨安', '/qq/images/avatar/avatar0.jpg', '在干神马啊！！！', '10', '1522679944');
INSERT INTO `info` VALUES ('2', '李安', '/qq/images/avatar/avatar1.jpg', '你好，请查看消息啊', '0', '1422679988');
INSERT INTO `info` VALUES ('3', '王安石', '/qq/images/avatar/avatar2.jpg', '迟来一起喝酒吧', '13', '1440987223');
INSERT INTO `info` VALUES ('4', '张媛', '/qq/images/avatar/avatar3.jpg', '快起来啊，迟到了··', '22', '1368876352');
INSERT INTO `info` VALUES ('5', '司马望', '/qq/images/avatar/avatar4.jpg', '你要叛变吗··', '12', '1338766542');
INSERT INTO `info` VALUES ('6', '诸葛瑾', '/qq/images/avatar/avatar5.jpg', '你想做神马啊', '33', '1228976554');
INSERT INTO `info` VALUES ('7', '王石', '/qq/images/avatar/avatar6.jpg', '借点钱给我把·', '14', '1120988765');
INSERT INTO `info` VALUES ('8', '成安', '/qq/images/avatar/avatar7.jpg', '怎么回事，快偶啊', '0', '1339876554');
INSERT INTO `info` VALUES ('9', '张萨萨', '/qq/images/avatar/avatar0.jpg', '在干神马啊！！！', '10', '1522679944');
INSERT INTO `info` VALUES ('10', '李安安', '/qq/images/avatar/avatar1.jpg', '你好，请查看消息啊', '0', '1422679988');
INSERT INTO `info` VALUES ('11', '王安', '/qq/images/avatar/avatar2.jpg', '迟来一起喝酒吧', '13', '1440987223');
INSERT INTO `info` VALUES ('12', '张媛媛', '/qq/images/avatar/avatar3.jpg', '快起来啊，迟到了··', '22', '1368876352');
INSERT INTO `info` VALUES ('13', '马望', '/qq/images/avatar/avatar4.jpg', '你要叛变吗··', '12', '1338766542');
INSERT INTO `info` VALUES ('14', '葛瑾', '/qq/images/avatar/avatar5.jpg', '你想做神马啊', '33', '1228976554');
INSERT INTO `info` VALUES ('15', '王大石', '/qq/images/avatar/avatar6.jpg', '借点钱给我把·', '14', '1120988765');
INSERT INTO `info` VALUES ('16', '成安化', '/qq/images/avatar/avatar7.jpg', '怎么回事，快偶啊', '0', '1339876554');
INSERT INTO `info` VALUES ('17', '张萨萨1', '/qq/images/avatar/avatar0.jpg', '在干神马啊！！！', '10', '1522679944');
INSERT INTO `info` VALUES ('18', '李安安1', '/qq/images/avatar/avatar1.jpg', '你好，请查看消息啊', '0', '1422679988');
INSERT INTO `info` VALUES ('19', '王安1', '/qq/images/avatar/avatar2.jpg', '迟来一起喝酒吧', '13', '1440987223');
INSERT INTO `info` VALUES ('20', '张媛媛1', '/qq/images/avatar/avatar3.jpg', '快起来啊，迟到了··', '22', '1368876352');
INSERT INTO `info` VALUES ('21', '马望1', '/qq/images/avatar/avatar4.jpg', '你要叛变吗··', '12', '1338766542');
INSERT INTO `info` VALUES ('22', '葛瑾1', '/qq/images/avatar/avatar5.jpg', '你想做神马啊', '33', '1228976554');
INSERT INTO `info` VALUES ('23', '王大石1', '/qq/images/avatar/avatar6.jpg', '借点钱给我把·', '14', '1120988765');
INSERT INTO `info` VALUES ('24', '成安化1', '/qq/images/avatar/avatar7.jpg', '怎么回事，快偶啊', '0', '1339876554');
INSERT INTO `info` VALUES ('25', '张萨萨2', '/qq/images/avatar/avatar0.jpg', '在干神马啊！！！', '10', '1522679944');
INSERT INTO `info` VALUES ('26', '李安2', '/qq/images/avatar/avatar1.jpg', '你好，请查看消息啊', '0', '1422679988');
INSERT INTO `info` VALUES ('27', '王安2', '/qq/images/avatar/avatar2.jpg', '迟来一起喝酒吧', '13', '1440987223');
INSERT INTO `info` VALUES ('28', '张媛', '/qq/images/avatar/avatar3.jpg', '快起来啊，迟到了··', '22', '1368876352');
INSERT INTO `info` VALUES ('29', '马望1', '/qq/images/avatar/avatar4.jpg', '你要叛变吗··', '12', '1338766542');
INSERT INTO `info` VALUES ('30', '葛1', '/qq/images/avatar/avatar5.jpg', '你想做神马啊', '33', '1228976554');
INSERT INTO `info` VALUES ('31', '王大1', '/qq/images/avatar/avatar6.jpg', '借点钱给我把·', '14', '1120988765');
INSERT INTO `info` VALUES ('32', '成化1', '/qq/images/avatar/avatar7.jpg', '怎么回事，快偶啊', '0', '1339876554');
INSERT INTO `info` VALUES ('33', '张萨萨3', '/qq/images/avatar/avatar0.jpg', '在干神马啊！！！', '10', '1522679944');
INSERT INTO `info` VALUES ('34', '李安3', '/qq/images/avatar/avatar1.jpg', '你好，请查看消息啊', '0', '1422679988');
INSERT INTO `info` VALUES ('35', '王安3', '/qq/images/avatar/avatar2.jpg', '迟来一起喝酒吧', '13', '1440987223');
INSERT INTO `info` VALUES ('36', '张媛3', '/qq/images/avatar/avatar3.jpg', '快起来啊，迟到了··', '22', '1368876352');
INSERT INTO `info` VALUES ('37', '马望2', '/qq/images/avatar/avatar4.jpg', '你要叛变吗··', '12', '1338766542');
INSERT INTO `info` VALUES ('38', '葛地', '/qq/images/avatar/avatar5.jpg', '你想做神马啊', '33', '1228976554');
INSERT INTO `info` VALUES ('39', '王大2', '/qq/images/avatar/avatar6.jpg', '借点钱给我把·', '14', '1120988765');
INSERT INTO `info` VALUES ('40', '成化2', '/qq/images/avatar/avatar7.jpg', '怎么回事，快偶啊', '0', '1339876554');
INSERT INTO `info` VALUES ('41', '萨萨3', '/qq/images/avatar/avatar0.jpg', '在干神马啊！！！', '10', '1522679944');
INSERT INTO `info` VALUES ('42', '安3', '/qq/images/avatar/avatar1.jpg', '你好，请查看消息啊', '0', '1422679988');
INSERT INTO `info` VALUES ('43', '王3', '/qq/images/avatar/avatar2.jpg', '迟来一起喝酒吧', '13', '1440987223');
INSERT INTO `info` VALUES ('44', '张媛23', '/qq/images/avatar/avatar3.jpg', '快起来啊，迟到了··', '22', '1368876352');
INSERT INTO `info` VALUES ('45', '马2', '/qq/images/avatar/avatar4.jpg', '你要叛变吗··', '12', '1338766542');
INSERT INTO `info` VALUES ('46', '葛1地', '/qq/images/avatar/avatar5.jpg', '你想做神马啊', '33', '1228976554');
INSERT INTO `info` VALUES ('47', '王2', '/qq/images/avatar/avatar6.jpg', '借点钱给我把·', '14', '1120988765');
INSERT INTO `info` VALUES ('48', '成2', '/qq/images/avatar/avatar7.jpg', '怎么回事，快偶啊', '0', '1339876554');
