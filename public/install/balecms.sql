/*
 Navicat MySQL Data Transfer

 Source Server         : vagrant-lnmp-me
 Source Server Type    : MySQL
 Source Server Version : 80013
 Source Host           : 192.168.33.10:3306
 Source Schema         : balecms

 Target Server Type    : MySQL
 Target Server Version : 80013
 File Encoding         : 65001

 Date: 21/10/2019 15:39:29
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for bl_change_pass
-- ----------------------------
DROP TABLE IF EXISTS `bl_change_pass`;
CREATE TABLE `bl_change_pass` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '已经修改密码用户ID',
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='已修改密码用户表';

-- ----------------------------
-- Table structure for bl_courses
-- ----------------------------
DROP TABLE IF EXISTS `bl_courses`;
CREATE TABLE `bl_courses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `semester_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学期id',
  `teacher_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教师id',
  `class_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级id',
  `subject_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目id',
  `week_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '周天id',
  `interval_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '时段id',
  `room_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教室id',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态（2不可用，1可用）',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `semester_teacher_class_subject_week_interval` (`semester_id`,`teacher_id`,`class_id`,`subject_id`,`week_id`,`interval_id`) USING BTREE COMMENT 'CP安排课程唯一',
  KEY `teacher_id` (`teacher_id`),
  KEY `class_id` (`class_id`),
  KEY `subject_id` (`subject_id`),
  KEY `week_id` (`week_id`),
  KEY `interval_id` (`interval_id`),
  KEY `semester_id` (`semester_id`),
  KEY `room_id` (`room_id`),
  KEY `semester_week_interval_room` (`semester_id`,`week_id`,`interval_id`,`room_id`) USING BTREE COMMENT '安排科目在同一学期，星期，时间段，教室保证唯一'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='教师课程';

-- ----------------------------
-- Table structure for bl_credits
-- ----------------------------
DROP TABLE IF EXISTS `bl_credits`;
CREATE TABLE `bl_credits` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `name` char(30) NOT NULL COMMENT '名称',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='周天类别';

-- ----------------------------
-- Records of bl_credits
-- ----------------------------
BEGIN;
INSERT INTO `bl_credits` VALUES (2, '3', '2018-01-30 06:29:37', '2018-01-30 06:36:21');
INSERT INTO `bl_credits` VALUES (3, '1', '2018-01-30 06:30:48', '2018-01-30 06:30:48');
INSERT INTO `bl_credits` VALUES (4, '4', '2018-01-30 06:31:07', '2018-01-30 06:31:07');
INSERT INTO `bl_credits` VALUES (5, '2', '2018-01-30 06:33:43', '2018-01-30 06:33:43');
COMMIT;

-- ----------------------------
-- Table structure for bl_defenses
-- ----------------------------
DROP TABLE IF EXISTS `bl_defenses`;
CREATE TABLE `bl_defenses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `name` varchar(220) NOT NULL DEFAULT '' COMMENT '开题题目',
  `start_time` varchar(32) NOT NULL COMMENT '开题时间',
  `student_id` int(11) NOT NULL DEFAULT '0' COMMENT '学生ID',
  `semester_id` int(11) NOT NULL DEFAULT '0' COMMENT '学期ID',
  `member` text COMMENT '开题成员',
  `remark` text NOT NULL COMMENT '开题备注',
  `result` tinyint(1) NOT NULL DEFAULT '3' COMMENT '开题结果 1(通过) 2 未通过 3 未评估',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_name_student_id` (`name`,`student_id`) USING BTREE COMMENT '保证题目唯一'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='学期类别';

-- ----------------------------
-- Table structure for bl_electives
-- ----------------------------
DROP TABLE IF EXISTS `bl_electives`;
CREATE TABLE `bl_electives` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `course_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '安排课程id',
  `student_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生id',
  `semester_id` int(11) NOT NULL DEFAULT '0' COMMENT '学期ID',
  `teacher_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教师id',
  `subject_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目id',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态（2不可用，1可用）',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `is_fraction` tinyint(4) DEFAULT '0' COMMENT '是否录入成绩 1 已录入 0 未录入',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_student_course` (`student_id`,`course_id`,`semester_id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `class_id` (`course_id`),
  KEY `subject_id` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='教师课程';

-- ----------------------------
-- Table structure for bl_fractions
-- ----------------------------
DROP TABLE IF EXISTS `bl_fractions`;
CREATE TABLE `bl_fractions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '成绩id',
  `semester_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学期id',
  `student_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生id',
  `subject_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目id',
  `score` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分数',
  `comment` char(255) NOT NULL DEFAULT '' COMMENT '教师点评',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `elective_id` int(11) DEFAULT '0' COMMENT '课程id',
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `subject_id` (`subject_id`),
  KEY `score` (`score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='学生成绩';

-- ----------------------------
-- Table structure for bl_grades
-- ----------------------------
DROP TABLE IF EXISTS `bl_grades`;
CREATE TABLE `bl_grades` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `name` char(30) NOT NULL COMMENT '名称',
  `value` varchar(32) NOT NULL COMMENT '分数 50,60',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用（0否，1是）',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '顺序',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='周天类别';

-- ----------------------------
-- Records of bl_grades
-- ----------------------------
BEGIN;
INSERT INTO `bl_grades` VALUES (1, '优', '90-100', 1, 0, '2018-01-13 13:22:23', '2018-01-13 14:05:26');
INSERT INTO `bl_grades` VALUES (2, '良', '70-89', 1, 0, '2018-01-13 13:22:54', '2018-07-12 09:32:09');
INSERT INTO `bl_grades` VALUES (3, '中', '60-69', 1, 0, '2018-01-13 13:23:51', '2018-07-12 09:32:18');
INSERT INTO `bl_grades` VALUES (4, '差', '1-59', 1, 0, '2018-01-13 13:24:12', '2018-07-12 09:32:47');
COMMIT;

-- ----------------------------
-- Table structure for bl_intervals
-- ----------------------------
DROP TABLE IF EXISTS `bl_intervals`;
CREATE TABLE `bl_intervals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `name` char(30) NOT NULL COMMENT '名称',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用（0否，1是）',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '顺序',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='时段类别';

-- ----------------------------
-- Records of bl_intervals
-- ----------------------------
BEGIN;
INSERT INTO `bl_intervals` VALUES (23, '09:00-12:00', 1, 0, '2018-06-29 11:42:20', '2018-01-11 20:37:28');
INSERT INTO `bl_intervals` VALUES (24, '13:30-16:30', 1, 0, '2018-06-29 11:42:54', '2018-01-11 20:37:53');
INSERT INTO `bl_intervals` VALUES (29, '18:00-21:00', 1, 0, '2018-06-29 11:43:21', '2018-01-11 20:38:30');
INSERT INTO `bl_intervals` VALUES (30, '网站另行通知', 1, 0, '2019-10-15 18:53:14', '2019-04-04 02:05:00');
COMMIT;

-- ----------------------------
-- Table structure for bl_menus
-- ----------------------------
DROP TABLE IF EXISTS `bl_menus`;
CREATE TABLE `bl_menus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '路由名称',
  `icon` varchar(32) NOT NULL DEFAULT '' COMMENT '小图标',
  `permission_id` int(11) NOT NULL DEFAULT '0' COMMENT '权限位',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1 开启  2 不启用',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8 COMMENT='权限表';

-- ----------------------------
-- Records of bl_menus
-- ----------------------------
BEGIN;
INSERT INTO `bl_menus` VALUES (80, 0, '主页中心', 'fa fa-home', 1, 1, '2018-01-18 10:54:51', '2018-01-19 19:42:58');
INSERT INTO `bl_menus` VALUES (81, 0, '学生管理', 'fa fa-mortar-board', 2, 1, '2018-01-18 11:03:09', '2018-01-18 11:03:09');
INSERT INTO `bl_menus` VALUES (82, 81, '学生列表', '', 3, 1, '2018-01-18 11:10:04', '2018-01-18 11:10:04');
INSERT INTO `bl_menus` VALUES (83, 81, '新增学生', '', 5, 1, '2018-01-18 11:11:11', '2018-01-18 11:11:11');
INSERT INTO `bl_menus` VALUES (84, 0, '成绩管理', 'fa fa-line-chart', 16, 1, '2018-01-18 11:13:08', '2018-01-18 11:13:08');
INSERT INTO `bl_menus` VALUES (85, 84, '成绩管理', '', 17, 1, '2018-01-18 11:13:32', '2018-01-19 17:31:34');
INSERT INTO `bl_menus` VALUES (87, 0, '选课管理', 'fa fa-flask', 80, 1, '2018-01-18 14:39:39', '2018-01-18 14:39:39');
INSERT INTO `bl_menus` VALUES (88, 87, '选课列表', '', 140, 1, '2018-01-18 14:40:00', '2018-01-18 14:40:00');
INSERT INTO `bl_menus` VALUES (89, 87, '已选列表', '', 83, 1, '2018-01-18 14:40:26', '2018-01-18 14:40:26');
INSERT INTO `bl_menus` VALUES (90, 0, '教师管理', 'fa fa-gavel', 55, 1, '2018-01-18 14:41:02', '2018-01-18 14:41:02');
INSERT INTO `bl_menus` VALUES (91, 90, '教师列表', '', 56, 1, '2018-01-18 14:41:33', '2018-01-18 14:41:33');
INSERT INTO `bl_menus` VALUES (92, 90, '课程安排', '', 70, 1, '2018-01-18 14:42:08', '2018-01-18 14:42:08');
INSERT INTO `bl_menus` VALUES (93, 0, '课程管理', 'fa fa-desktop', 63, 1, '2018-01-18 14:42:45', '2018-01-18 14:42:45');
INSERT INTO `bl_menus` VALUES (94, 93, '课程列表', '', 64, 1, '2018-01-18 14:43:07', '2018-01-18 14:43:07');
INSERT INTO `bl_menus` VALUES (95, 0, '导师管理', 'fa  fa-institution', 40, 1, '2018-01-18 14:43:59', '2018-01-18 14:43:59');
INSERT INTO `bl_menus` VALUES (96, 95, '导师列表', '', 41, 1, '2018-01-18 14:44:20', '2018-01-18 14:44:20');
INSERT INTO `bl_menus` VALUES (97, 95, '导师类型', '', 48, 1, '2018-01-18 14:44:56', '2018-01-18 14:44:56');
INSERT INTO `bl_menus` VALUES (98, 0, '开题管理', 'fa fa-list', 88, 1, '2018-01-18 14:47:15', '2018-01-18 14:47:15');
INSERT INTO `bl_menus` VALUES (100, 98, '开题列表', '', 89, 1, '2018-01-18 14:47:55', '2018-01-18 14:47:55');
INSERT INTO `bl_menus` VALUES (101, 0, '答辩管理', 'fa fa-suitcase', 96, 1, '2018-01-18 14:48:57', '2018-01-18 14:48:57');
INSERT INTO `bl_menus` VALUES (102, 101, '答辩列表', '', 97, 1, '2018-01-18 14:49:36', '2018-01-18 14:49:36');
INSERT INTO `bl_menus` VALUES (103, 0, '系统管理', 'fa fa-tachometer', 144, 1, '2018-01-18 14:52:09', '2018-01-18 20:09:18');
INSERT INTO `bl_menus` VALUES (104, 103, '用户管理', '', 104, 1, '2018-01-18 14:52:36', '2018-01-18 14:52:36');
INSERT INTO `bl_menus` VALUES (105, 103, '角色管理', '', 111, 1, '2018-01-18 14:52:55', '2018-01-18 14:52:55');
INSERT INTO `bl_menus` VALUES (106, 103, '菜单管理', '', 121, 1, '2018-01-18 14:53:06', '2018-01-18 14:53:06');
INSERT INTO `bl_menus` VALUES (107, 103, '权限管理', '', 130, 1, '2018-01-18 14:53:17', '2018-01-18 14:53:17');
INSERT INTO `bl_menus` VALUES (108, 0, '网站管理', 'fa fa-rss', 138, 1, '2018-01-18 14:53:57', '2018-01-18 14:53:57');
INSERT INTO `bl_menus` VALUES (109, 108, '站点信息', '', 139, 1, '2018-01-18 14:54:21', '2018-01-18 14:54:21');
INSERT INTO `bl_menus` VALUES (110, 0, '类别管理', 'fa fa-balance-scale', 23, 1, '2018-01-18 14:55:16', '2018-01-18 20:36:45');
INSERT INTO `bl_menus` VALUES (111, 110, '学期管理', '', 23, 1, '2018-01-18 14:55:30', '2018-01-18 14:55:44');
INSERT INTO `bl_menus` VALUES (112, 110, '课程相关', '', 33, 1, '2018-01-18 14:56:15', '2018-01-18 18:54:58');
INSERT INTO `bl_menus` VALUES (117, 84, '学生成绩', '', 145, 1, '2018-01-19 17:56:32', '2018-01-19 17:56:32');
COMMIT;

-- ----------------------------
-- Table structure for bl_permissions
-- ----------------------------
DROP TABLE IF EXISTS `bl_permissions`;
CREATE TABLE `bl_permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '路由名称',
  `method` varchar(255) NOT NULL DEFAULT '' COMMENT '请求方法',
  `route` varchar(255) NOT NULL DEFAULT '' COMMENT '路由',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1 开启  2 不启用',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_method_route_name` (`method`,`route`,`name`) USING BTREE COMMENT 'method_route 唯一索引'
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8 COMMENT='权限表';

-- ----------------------------
-- Records of bl_permissions
-- ----------------------------
BEGIN;
INSERT INTO `bl_permissions` VALUES (1, 0, '主页管理', 'GET', '/home/admin.html', 1, '2018-01-16 20:06:00', '2018-01-17 19:21:00');
INSERT INTO `bl_permissions` VALUES (2, 0, '学生管理', 'GET', '/student/index.html', 1, '2018-01-16 20:08:13', '2018-01-16 20:08:13');
INSERT INTO `bl_permissions` VALUES (3, 2, '学生列表', 'GET', '/student/index.html', 1, '2018-01-16 20:08:45', '2018-01-16 20:08:45');
INSERT INTO `bl_permissions` VALUES (4, 2, '学生数据', 'POST', '/student/getdata.html', 1, '2018-01-16 20:09:22', '2018-01-16 20:09:22');
INSERT INTO `bl_permissions` VALUES (5, 2, '添加学生页面', 'GET', '/student/adddata.html', 1, '2018-01-16 20:10:30', '2018-01-16 20:10:30');
INSERT INTO `bl_permissions` VALUES (6, 2, '添加学生操作', 'POST', '/student/adddata.html', 1, '2018-01-16 20:10:38', '2018-01-16 20:10:38');
INSERT INTO `bl_permissions` VALUES (7, 2, '检查学生唯一性', 'POST', '/student/unique.html', 1, '2018-01-16 20:11:28', '2018-01-16 20:11:28');
INSERT INTO `bl_permissions` VALUES (8, 2, '导出学生数据', 'GET', '/student/export.html', 1, '2018-01-16 20:11:48', '2018-01-16 20:11:48');
INSERT INTO `bl_permissions` VALUES (9, 2, '导入学生数据', 'POST', '/student/import.html', 1, '2018-01-16 20:12:17', '2018-01-16 20:12:17');
INSERT INTO `bl_permissions` VALUES (10, 2, '上传学生表格', 'POST', '/student/upload.html', 1, '2018-01-16 20:12:51', '2018-01-16 20:12:51');
INSERT INTO `bl_permissions` VALUES (11, 2, '学生重复数据', 'GET', '/student/repeat.html', 1, '2018-01-16 20:13:19', '2018-01-16 20:13:19');
INSERT INTO `bl_permissions` VALUES (12, 3, '删除学生', 'GET', '/student/del/@.html', 1, '2018-01-16 20:28:25', '2018-04-16 19:34:17');
INSERT INTO `bl_permissions` VALUES (13, 3, '学生编辑页面', 'GET', '/student/update/@.html', 1, '2018-01-16 20:43:33', '2018-01-16 20:43:33');
INSERT INTO `bl_permissions` VALUES (14, 3, '执行编辑操作', 'POST', '/student/update.html', 1, '2018-01-16 20:43:46', '2018-01-16 20:43:46');
INSERT INTO `bl_permissions` VALUES (15, 2, '根据条件获取学生信息', 'POST', '/student/where.html', 1, '2018-01-16 21:04:45', '2018-01-16 21:04:45');
INSERT INTO `bl_permissions` VALUES (16, 0, '成绩管理', 'GET', '/fraction/index.html', 1, '2018-01-16 21:05:32', '2018-01-16 21:05:32');
INSERT INTO `bl_permissions` VALUES (17, 16, '成绩列表', 'GET', '/fraction/index.html', 1, '2018-01-16 21:05:50', '2018-01-16 21:06:04');
INSERT INTO `bl_permissions` VALUES (18, 17, '获取成绩数据', 'POST', '/fraction/getdata.html', 1, '2018-01-16 21:06:56', '2018-01-16 21:06:56');
INSERT INTO `bl_permissions` VALUES (19, 17, '添加成绩页面', 'GET', '/fraction/adddata/@.html', 1, '2018-01-16 21:07:37', '2018-01-16 21:07:56');
INSERT INTO `bl_permissions` VALUES (20, 17, '添加成绩', 'POST', '/fraction/adddata.html', 1, '2018-01-16 21:07:45', '2018-01-16 21:07:45');
INSERT INTO `bl_permissions` VALUES (21, 16, '删除成绩', 'GET', '/fraction/del/@.html', 1, '2018-01-16 21:08:30', '2018-01-16 21:08:30');
INSERT INTO `bl_permissions` VALUES (22, 16, '修改成绩', 'POST', '/fraction/update.html', 1, '2018-01-16 21:08:56', '2018-01-16 21:08:56');
INSERT INTO `bl_permissions` VALUES (23, 0, '学期管理', 'GET', '/semester/index.html', 1, '2018-01-17 11:38:02', '2018-01-17 11:38:02');
INSERT INTO `bl_permissions` VALUES (24, 23, '学期列表', 'GET', '/semester/index.html', 1, '2018-01-17 11:39:41', '2018-01-17 11:39:41');
INSERT INTO `bl_permissions` VALUES (25, 23, '获取学期数据', 'POST', '/semester/getdata.html', 1, '2018-01-17 11:42:39', '2018-01-17 11:56:31');
INSERT INTO `bl_permissions` VALUES (26, 23, '学期唯一性', 'POST', '/semester/unique.html', 1, '2018-01-17 11:44:18', '2018-01-17 11:44:18');
INSERT INTO `bl_permissions` VALUES (27, 23, '添加学期', 'POST', '/semester/adddata.html', 1, '2018-01-17 11:55:02', '2018-01-17 11:55:02');
INSERT INTO `bl_permissions` VALUES (28, 24, '删除学期', 'GET', '/semester/del/@.html', 1, '2018-01-17 14:06:43', '2018-01-17 14:06:43');
INSERT INTO `bl_permissions` VALUES (29, 24, '修改学期', 'POST', '/semester/update.html', 1, '2018-01-17 14:07:15', '2018-01-17 14:07:15');
INSERT INTO `bl_permissions` VALUES (30, 24, '启用和注销学期', 'GET', '/semester/cancelstart/@/@.html', 1, '2018-01-17 14:07:45', '2018-01-17 14:07:45');
INSERT INTO `bl_permissions` VALUES (31, 24, '降序学期', 'GET', '/semester/desc/@.html', 1, '2018-01-17 14:09:41', '2018-01-17 14:09:41');
INSERT INTO `bl_permissions` VALUES (32, 24, '升序学期', 'GET', '/semester/asc/@.html', 1, '2018-01-17 14:09:59', '2018-01-17 14:09:59');
INSERT INTO `bl_permissions` VALUES (33, 0, '课程相关', 'GET', '/category/index.html', 1, '2018-01-17 14:10:38', '2018-01-17 17:50:13');
INSERT INTO `bl_permissions` VALUES (34, 33, '星期，时间段，教室 列表', 'GET', '/category/index.html', 1, '2018-01-17 14:10:57', '2018-01-17 14:11:29');
INSERT INTO `bl_permissions` VALUES (35, 33, '唯一性检查', 'POST', '/category/unique.html', 1, '2018-01-17 14:12:15', '2018-05-24 23:29:20');
INSERT INTO `bl_permissions` VALUES (36, 33, '修改操作', 'POST', '/category/update.html', 1, '2018-01-17 14:12:36', '2018-01-17 14:12:36');
INSERT INTO `bl_permissions` VALUES (37, 33, '删除操作', 'GET', '/category/del/@.html', 1, '2018-01-17 14:13:03', '2018-01-17 14:13:03');
INSERT INTO `bl_permissions` VALUES (38, 33, '添加操作', 'POST', '/category/adddata.html', 1, '2018-01-17 14:13:25', '2018-01-30 06:18:44');
INSERT INTO `bl_permissions` VALUES (39, 33, '获取父类信息', 'POST', '/category/getparent.html', 1, '2018-01-17 14:13:45', '2018-01-17 14:13:45');
INSERT INTO `bl_permissions` VALUES (40, 0, '导师管理', 'GET', '/tutor/index.html', 1, '2018-01-17 14:14:22', '2018-01-17 14:14:50');
INSERT INTO `bl_permissions` VALUES (41, 40, '导师列表', 'GET', '/tutor/index.html', 1, '2018-01-17 14:15:41', '2018-01-17 14:15:41');
INSERT INTO `bl_permissions` VALUES (42, 40, '获取导师数据', 'POST', '/tutor/getdata.html', 1, '2018-01-17 14:16:03', '2018-01-17 14:16:03');
INSERT INTO `bl_permissions` VALUES (43, 40, '唯一性检查导师', 'POST', '/tutor/unique.html', 1, '2018-01-17 14:16:24', '2018-01-17 14:16:24');
INSERT INTO `bl_permissions` VALUES (44, 40, '添加导师', 'POST', '/tutor/adddata.html', 1, '2018-01-17 14:16:39', '2018-01-17 14:16:39');
INSERT INTO `bl_permissions` VALUES (45, 40, '删除导师', 'GET', '/tutor/del/@.html', 1, '2018-01-17 14:17:03', '2018-01-17 14:17:03');
INSERT INTO `bl_permissions` VALUES (46, 40, '修改操作', 'POST', '/tutor/update.html', 1, '2018-01-17 14:17:37', '2018-01-17 14:17:37');
INSERT INTO `bl_permissions` VALUES (47, 40, '启用或者注销', 'GET', '/tutor/cancelstart/@/@.html', 1, '2018-01-17 14:18:06', '2018-04-13 11:24:06');
INSERT INTO `bl_permissions` VALUES (48, 0, '导师类型', 'GET', '/tutor/class.html', 1, '2018-01-17 14:20:10', '2018-01-17 14:20:10');
INSERT INTO `bl_permissions` VALUES (49, 48, '导师类型列表', 'GET', '/tutor/class.html', 1, '2018-01-17 14:21:10', '2018-01-17 14:21:10');
INSERT INTO `bl_permissions` VALUES (50, 48, '获取导师类型数据', 'POST', '/tutor/getclass.html', 1, '2018-01-17 14:21:26', '2018-01-17 14:21:26');
INSERT INTO `bl_permissions` VALUES (51, 48, '唯一性检查', 'POST', '/tutor/classunique.html', 1, '2018-01-17 14:21:43', '2018-01-17 14:21:43');
INSERT INTO `bl_permissions` VALUES (52, 48, '添加导师类型', 'POST', '/tutor/addclassdata.html', 1, '2018-01-17 14:22:01', '2018-01-17 14:22:32');
INSERT INTO `bl_permissions` VALUES (53, 48, '修改导师类型', 'POST', '/tutor/updateclassdata.html', 1, '2018-01-17 14:22:22', '2018-01-17 14:22:22');
INSERT INTO `bl_permissions` VALUES (54, 48, '删除导师类型', 'GET', '/tutor/delclassdata/@.html', 1, '2018-01-17 14:23:10', '2018-01-17 14:23:22');
INSERT INTO `bl_permissions` VALUES (55, 0, '教师管理', 'GET', '/teacher/index.html', 1, '2018-01-17 14:30:35', '2018-01-17 14:30:35');
INSERT INTO `bl_permissions` VALUES (56, 55, '教师管理列表', 'GET', '/teacher/index.html', 1, '2018-01-17 14:30:48', '2018-01-17 14:30:48');
INSERT INTO `bl_permissions` VALUES (57, 55, '教师数据', 'POST', '/teacher/getdata.html', 1, '2018-01-17 14:31:01', '2018-01-17 14:31:01');
INSERT INTO `bl_permissions` VALUES (58, 55, '唯一性', 'POST', '/teacher/unique.html', 1, '2018-01-17 14:31:12', '2018-01-17 14:31:12');
INSERT INTO `bl_permissions` VALUES (59, 55, '教师添加', 'POST', '/teacher/adddata.html', 1, '2018-01-17 14:31:25', '2018-01-17 14:31:25');
INSERT INTO `bl_permissions` VALUES (60, 55, '教师删除', 'GET', '/teacher/del/@.html', 1, '2018-01-17 14:31:51', '2018-01-17 14:31:51');
INSERT INTO `bl_permissions` VALUES (61, 55, '修改删除', 'POST', '/teacher/update.html', 1, '2018-01-17 14:32:02', '2018-01-17 14:32:02');
INSERT INTO `bl_permissions` VALUES (62, 55, '启用或注销', 'GET', '/teacher/cancelstart/@/@.html', 1, '2018-01-17 14:32:40', '2018-01-17 14:32:40');
INSERT INTO `bl_permissions` VALUES (63, 0, '课程管理', 'GET', '/subject/index.html', 1, '2018-01-17 14:33:04', '2018-01-17 14:33:04');
INSERT INTO `bl_permissions` VALUES (64, 63, '课程列表', 'GET', '/subject/index.html', 1, '2018-01-17 14:33:21', '2018-01-18 18:46:47');
INSERT INTO `bl_permissions` VALUES (65, 63, '唯一性检查', 'POST', '/subject/unique.html', 1, '2018-01-17 14:33:34', '2018-01-17 14:33:34');
INSERT INTO `bl_permissions` VALUES (66, 63, '添加教程', 'POST', '/subject/adddata.html', 1, '2018-01-17 14:33:47', '2018-01-17 14:33:47');
INSERT INTO `bl_permissions` VALUES (67, 63, '删除教程', 'GET', '/subject/del/@.html', 1, '2018-01-17 14:34:05', '2018-01-17 14:34:05');
INSERT INTO `bl_permissions` VALUES (68, 63, '修改课程', 'POST', '/subject/update.html', 1, '2018-01-17 14:34:27', '2018-01-17 14:34:27');
INSERT INTO `bl_permissions` VALUES (69, 63, '启用和注销', 'GET', '/subject/cancelstart/@/@.html', 1, '2018-01-17 14:35:04', '2018-01-17 14:35:04');
INSERT INTO `bl_permissions` VALUES (70, 0, '课程安排', 'GET', '/course/index.html', 1, '2018-01-17 14:37:49', '2018-01-17 14:37:49');
INSERT INTO `bl_permissions` VALUES (71, 70, '课程安排列表', 'GET', '/course/index.html', 1, '2018-01-17 14:37:59', '2018-01-17 14:37:59');
INSERT INTO `bl_permissions` VALUES (72, 70, '添加课程界面', 'GET', '/course/adddata/@.html', 1, '2018-01-17 14:38:31', '2018-01-17 14:38:31');
INSERT INTO `bl_permissions` VALUES (73, 70, '添加课程', 'POST', '/course/adddata.html', 1, '2018-01-17 14:38:38', '2018-01-17 14:38:38');
INSERT INTO `bl_permissions` VALUES (74, 70, '获取课程数据', 'POST', '/course/getdata.html', 1, '2018-01-17 14:38:55', '2018-01-17 14:38:55');
INSERT INTO `bl_permissions` VALUES (75, 70, '编辑课程界面', 'GET', '/course/update/@.html', 1, '2018-01-17 14:39:17', '2018-01-17 14:39:17');
INSERT INTO `bl_permissions` VALUES (76, 70, '编辑课程', 'POST', '/course/update.html', 1, '2018-01-17 14:39:25', '2018-01-17 14:39:25');
INSERT INTO `bl_permissions` VALUES (77, 70, '启用或者注销', 'GET', '/course/cancelstart/@/@.html', 1, '2018-01-17 14:39:57', '2018-01-17 14:39:57');
INSERT INTO `bl_permissions` VALUES (78, 70, '删除课程安排', 'GET', '/course/del/@.html', 1, '2018-01-17 14:40:21', '2018-01-17 14:40:21');
INSERT INTO `bl_permissions` VALUES (80, 0, '选课管理', 'GET', '/elective/index.html', 1, '2018-01-18 12:15:29', '2018-01-18 12:15:29');
INSERT INTO `bl_permissions` VALUES (81, 80, '学生选课', 'GET', '/elective/index/@.html', 1, '2018-01-18 12:15:52', '2018-01-18 14:36:57');
INSERT INTO `bl_permissions` VALUES (82, 80, '获取选课列表数据', 'POST', '/elective/getdata.html', 1, '2018-01-18 12:16:06', '2018-01-19 09:58:41');
INSERT INTO `bl_permissions` VALUES (83, 80, '已选课程列表', 'GET', '/elective/select.html', 1, '2018-01-18 12:17:04', '2018-01-18 12:17:04');
INSERT INTO `bl_permissions` VALUES (84, 80, '获取已选课程', 'POST', '/elective/selectdata.html', 1, '2018-01-18 12:17:22', '2018-01-18 12:17:22');
INSERT INTO `bl_permissions` VALUES (85, 81, '确定选课', 'POST', '/elective/adddata.html', 1, '2018-01-18 12:18:01', '2018-01-18 12:18:01');
INSERT INTO `bl_permissions` VALUES (86, 81, '取消选课', 'GET', '/elective/del/@.html', 1, '2018-01-18 12:18:36', '2018-01-18 12:18:36');
INSERT INTO `bl_permissions` VALUES (87, 80, '获取未选课学生', 'POST', '/elective/notselected.html', 1, '2018-01-18 12:19:24', '2018-01-18 12:19:24');
INSERT INTO `bl_permissions` VALUES (88, 0, '开题管理', 'GET', '/proposal/index.html', 1, '2018-01-18 12:21:28', '2018-01-18 12:21:28');
INSERT INTO `bl_permissions` VALUES (89, 88, '开题列表', 'GET', '/proposal/index.html', 1, '2018-01-18 12:21:45', '2018-01-18 18:48:29');
INSERT INTO `bl_permissions` VALUES (90, 88, '获取开题数据', 'POST', '/proposal/getdata.html', 1, '2018-01-18 12:22:25', '2018-01-18 12:22:25');
INSERT INTO `bl_permissions` VALUES (91, 88, '唯一性检查', 'POST', '/proposal/unique.html', 1, '2018-01-18 14:11:49', '2018-01-18 14:11:49');
INSERT INTO `bl_permissions` VALUES (92, 88, '添加开题', 'POST', '/proposal/adddata.html', 1, '2018-01-18 14:12:01', '2018-01-18 14:12:01');
INSERT INTO `bl_permissions` VALUES (93, 88, '删除开题', 'GET', '/proposal/del/@.html', 1, '2018-01-18 14:12:21', '2018-01-18 14:12:21');
INSERT INTO `bl_permissions` VALUES (94, 88, '编辑开题', 'POST', '/proposal/update.html', 1, '2018-01-18 14:13:11', '2018-01-18 14:13:11');
INSERT INTO `bl_permissions` VALUES (95, 88, '启用或者注销开题', 'GET', '/proposal/cancelstart/@/@.html', 1, '2018-01-18 14:13:42', '2018-01-18 14:13:42');
INSERT INTO `bl_permissions` VALUES (96, 0, '答辩管理', 'GET', '/defense/index.html', 1, '2018-01-18 14:14:11', '2018-01-18 14:14:11');
INSERT INTO `bl_permissions` VALUES (97, 96, '答辩列表', 'GET', '/defense/index.html', 1, '2018-01-18 14:14:33', '2018-01-18 18:49:23');
INSERT INTO `bl_permissions` VALUES (98, 96, '获取答辩数据', 'POST', '/defense/getdata.html', 1, '2018-01-18 14:14:58', '2018-01-18 14:14:58');
INSERT INTO `bl_permissions` VALUES (99, 96, '唯一性检查', 'POST', '/defense/unique.html', 1, '2018-01-18 14:15:19', '2018-01-18 14:15:19');
INSERT INTO `bl_permissions` VALUES (100, 96, '添加答辩', 'POST', '/defense/adddata.html', 1, '2018-01-18 14:15:35', '2018-01-18 14:15:35');
INSERT INTO `bl_permissions` VALUES (101, 96, '删除答辩', 'GET', '/defense/del/@.html', 1, '2018-01-18 14:16:52', '2018-01-18 14:16:52');
INSERT INTO `bl_permissions` VALUES (102, 96, '修改答辩', 'POST', '/defense/update.html', 1, '2018-01-18 14:17:08', '2018-01-18 14:17:08');
INSERT INTO `bl_permissions` VALUES (103, 96, '注销和启用答辩', 'GET', '/defense/cancelstart/@/@.html', 1, '2018-01-18 14:17:33', '2018-01-18 14:17:33');
INSERT INTO `bl_permissions` VALUES (104, 144, '用户管理', 'GET', '/user/index.html', 1, '2018-01-18 14:18:27', '2018-01-18 20:08:47');
INSERT INTO `bl_permissions` VALUES (105, 104, '获取用户', 'POST', '/user/getdata.html', 1, '2018-01-18 14:18:45', '2018-01-18 14:18:45');
INSERT INTO `bl_permissions` VALUES (106, 104, '用户唯一性', 'POST', '/user/unique.html', 1, '2018-01-18 14:19:07', '2018-01-18 14:19:07');
INSERT INTO `bl_permissions` VALUES (107, 104, '添加用户', 'POST', '/user/adddata.html', 1, '2018-01-18 14:19:22', '2018-01-18 14:19:22');
INSERT INTO `bl_permissions` VALUES (108, 104, '删除用户', 'GET', '/user/del/@.html', 1, '2018-01-18 14:19:42', '2018-01-18 14:19:42');
INSERT INTO `bl_permissions` VALUES (109, 104, '更新用户', 'POST', '/user/update.html', 1, '2018-01-18 14:19:57', '2018-01-18 14:19:57');
INSERT INTO `bl_permissions` VALUES (110, 104, '注销和启用用户', 'GET', '/user/cancelstart/@/@.html', 1, '2018-01-18 14:21:32', '2018-01-18 14:21:32');
INSERT INTO `bl_permissions` VALUES (111, 144, '角色管理', 'GET', '/role/index.html', 1, '2018-01-18 14:22:02', '2018-01-18 20:08:44');
INSERT INTO `bl_permissions` VALUES (112, 111, '角色列表', 'GET', '/role/index.html', 1, '2018-01-18 14:22:45', '2018-01-18 14:22:45');
INSERT INTO `bl_permissions` VALUES (113, 111, '获取角色', 'POST', '/role/getdata.html', 1, '2018-01-18 14:23:02', '2018-01-18 14:23:02');
INSERT INTO `bl_permissions` VALUES (114, 111, '唯一性角色', 'POST', '/role/unique.html', 1, '2018-01-18 14:23:19', '2018-01-18 14:23:19');
INSERT INTO `bl_permissions` VALUES (115, 111, '添加角色', 'POST', '/role/adddata.html', 1, '2018-01-18 14:24:02', '2018-01-18 14:24:02');
INSERT INTO `bl_permissions` VALUES (116, 111, '删除角色', 'GET', '/role/del/@.html', 1, '2018-01-18 14:24:21', '2018-01-18 14:24:21');
INSERT INTO `bl_permissions` VALUES (117, 111, '修改角色', 'POST', '/role/update.html', 1, '2018-01-18 14:24:49', '2018-01-18 14:24:49');
INSERT INTO `bl_permissions` VALUES (118, 111, '注销和启用角色', 'GET', '/role/cancelstart/@/@.html', 1, '2018-01-18 14:25:36', '2018-01-18 14:25:36');
INSERT INTO `bl_permissions` VALUES (119, 111, '分配权限页面', 'GET', '/role/auth/@.html', 1, '2018-01-18 14:26:09', '2018-01-18 14:26:09');
INSERT INTO `bl_permissions` VALUES (120, 111, '分配权限', 'POST', '/role/auth.html', 1, '2018-01-18 14:26:17', '2018-01-18 14:26:17');
INSERT INTO `bl_permissions` VALUES (121, 144, '菜单管理', 'GET', '/menu/index.html', 1, '2018-01-18 14:26:53', '2018-01-18 20:09:07');
INSERT INTO `bl_permissions` VALUES (122, 121, '菜单列表', 'GET', '/menu/index.html', 1, '2018-01-18 14:27:08', '2018-01-18 14:27:08');
INSERT INTO `bl_permissions` VALUES (123, 121, '获取菜单', 'POST', '/menu/getdata.html', 1, '2018-01-18 14:27:40', '2018-01-18 14:27:40');
INSERT INTO `bl_permissions` VALUES (124, 121, '唯一性检查', 'POST', '/menu/unique.html', 1, '2018-01-18 14:27:53', '2018-01-18 14:27:53');
INSERT INTO `bl_permissions` VALUES (125, 121, '添加菜单', 'POST', '/menu/adddata.html', 1, '2018-01-18 14:28:06', '2018-01-18 14:28:06');
INSERT INTO `bl_permissions` VALUES (126, 121, '删除菜单', 'GET', '/menu/del/@.html', 1, '2018-01-18 14:28:22', '2018-01-18 14:28:22');
INSERT INTO `bl_permissions` VALUES (127, 121, '修改菜单', 'POST', '/menu/update.html', 1, '2018-01-18 14:28:36', '2018-01-18 14:28:36');
INSERT INTO `bl_permissions` VALUES (128, 121, '注销和启用菜单', 'GET', '/menu/cancelstart/@/@.html', 1, '2018-01-18 14:29:02', '2018-01-18 14:29:02');
INSERT INTO `bl_permissions` VALUES (129, 121, '菜单图标', 'GET', '/menu/fontawesome.html', 1, '2018-01-18 14:29:17', '2018-01-18 14:29:17');
INSERT INTO `bl_permissions` VALUES (130, 144, '权限管理', 'GET', '/permission/index.html', 1, '2018-01-18 14:30:26', '2018-01-18 20:09:03');
INSERT INTO `bl_permissions` VALUES (131, 130, '权限列表', 'GET', '/permission/index.html', 1, '2018-01-18 14:30:44', '2018-01-18 14:30:44');
INSERT INTO `bl_permissions` VALUES (132, 130, '获取权限', 'POST', '/permission/getdata.html', 1, '2018-01-18 14:31:01', '2018-01-18 14:31:01');
INSERT INTO `bl_permissions` VALUES (133, 130, '权限唯一性检查', 'POST', '/permission/unique.html', 1, '2018-01-18 14:31:14', '2018-01-18 14:31:14');
INSERT INTO `bl_permissions` VALUES (134, 130, '添加权限', 'POST', '/permission/adddata.html', 1, '2018-01-18 14:31:25', '2018-01-18 14:31:25');
INSERT INTO `bl_permissions` VALUES (135, 130, '删除权限', 'GET', '/permission/del/@.html', 1, '2018-01-18 14:31:47', '2018-01-18 14:31:47');
INSERT INTO `bl_permissions` VALUES (136, 130, '修改权限', 'POST', '/permission/update.html', 1, '2018-01-18 14:32:01', '2018-01-18 14:32:01');
INSERT INTO `bl_permissions` VALUES (137, 130, '注销和启用权限', 'GET', '/permission/cancelstart/@/@.html', 1, '2018-01-18 14:32:24', '2018-01-18 14:32:24');
INSERT INTO `bl_permissions` VALUES (138, 0, '网站管理', 'GET', '/website/index.html', 1, '2018-01-18 14:32:46', '2018-01-18 14:32:46');
INSERT INTO `bl_permissions` VALUES (139, 138, '站点信息', 'GET', '/website/index.html', 1, '2018-01-18 14:33:05', '2018-01-18 14:33:05');
INSERT INTO `bl_permissions` VALUES (140, 80, '选课列表', 'GET', '/elective/index.html', 1, '2018-01-18 14:37:18', '2018-01-18 14:37:18');
INSERT INTO `bl_permissions` VALUES (141, 64, '获取课程数据', 'POST', '/subject/getdata.html', 1, '2018-01-18 18:47:11', '2018-01-18 18:47:11');
INSERT INTO `bl_permissions` VALUES (142, 89, '学生开题', 'GET', '/proposal/index/@.html', 1, '2018-01-18 18:48:48', '2018-01-18 18:48:48');
INSERT INTO `bl_permissions` VALUES (143, 97, '学生答辩', 'GET', '/defense/index/@.html', 1, '2018-01-18 18:49:42', '2018-01-18 18:49:42');
INSERT INTO `bl_permissions` VALUES (144, 0, '系统管理', 'GET', '/user/index.html', 1, '2018-01-18 20:07:52', '2018-01-18 20:08:15');
INSERT INTO `bl_permissions` VALUES (145, 16, '学生成绩(单独提供查看)', 'GET', '/fraction/student.html', 1, '2018-01-19 17:31:05', '2018-01-20 07:27:58');
INSERT INTO `bl_permissions` VALUES (146, 138, '获取网站配置信息', 'POST', '/website/getdata.html', 1, '2018-01-25 11:26:51', '2018-01-25 11:26:51');
INSERT INTO `bl_permissions` VALUES (147, 139, '新增网站配置', 'POST', '/website/adddata.html', 1, '2018-01-25 11:27:26', '2018-01-25 11:27:26');
INSERT INTO `bl_permissions` VALUES (148, 139, '修改网站配置', 'POST', '/website/update.html', 1, '2018-01-25 11:27:41', '2018-01-25 11:27:41');
INSERT INTO `bl_permissions` VALUES (149, 139, '删除网站配置', 'GET', '/website/del/@.html', 1, '2018-01-25 11:28:00', '2018-01-25 11:28:00');
INSERT INTO `bl_permissions` VALUES (150, 139, '检测配置项唯一性', 'POST', '/website/unique.html', 1, '2018-01-25 11:45:28', '2018-01-25 11:45:28');
INSERT INTO `bl_permissions` VALUES (151, 104, '重置密码', 'GET', '/user/repass/@.html', 1, '2018-04-16 16:07:46', '2018-04-16 16:07:46');
COMMIT;

-- ----------------------------
-- Table structure for bl_proposals
-- ----------------------------
DROP TABLE IF EXISTS `bl_proposals`;
CREATE TABLE `bl_proposals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `name` varchar(220) NOT NULL DEFAULT '' COMMENT '开题题目',
  `start_time` varchar(32) NOT NULL COMMENT '开题时间',
  `student_id` int(11) NOT NULL DEFAULT '0' COMMENT '学生ID',
  `semester_id` int(11) NOT NULL DEFAULT '0' COMMENT '学期ID',
  `member` text COMMENT '开题成员',
  `remark` text NOT NULL COMMENT '开题备注',
  `result` tinyint(1) NOT NULL DEFAULT '3' COMMENT '开题结果 1(通过) 2 未通过 3 未评估',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_name_student_id` (`name`,`student_id`) USING BTREE COMMENT '保证题目唯一'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='学期类别';

-- ----------------------------
-- Table structure for bl_roles
-- ----------------------------
DROP TABLE IF EXISTS `bl_roles`;
CREATE TABLE `bl_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `show_name` varchar(64) NOT NULL COMMENT '展示名称',
  `name` varchar(32) NOT NULL COMMENT '角色名称（保证创建不可修改）',
  `permission_id` text COMMENT '权限ID值 1,2,3,4,5 格式要求',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 开启  2 注销',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_username` (`show_name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='角色表';

-- ----------------------------
-- Records of bl_roles
-- ----------------------------
BEGIN;
INSERT INTO `bl_roles` VALUES (1, '管理员', 'admin', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,140,141,142,143,145', 1, '2018-01-17 19:50:04', '2018-04-18 15:40:48');
INSERT INTO `bl_roles` VALUES (2, '教师', 'teacher', '1,16,17,18,19,20,21,22,55,70,71,74,80,83,84,141,145', 1, '2018-01-17 19:51:38', '2018-01-20 11:28:42');
INSERT INTO `bl_roles` VALUES (3, '在校学生', 'student', '1,16,18,80,81,82,83,84,85,86,90,98,145', 1, '2018-01-17 19:52:03', '2018-01-20 03:11:22');
INSERT INTO `bl_roles` VALUES (4, '学生导师', 'tutor', '1,2,3,4,8,16,17,18,28,29,30,31,32,88,89,90,96,97,98', 1, '2018-01-17 19:52:14', '2018-01-21 02:11:08');
INSERT INTO `bl_roles` VALUES (5, '超级管理员', 'super', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151', 1, '2018-01-17 20:03:11', '2018-04-16 16:08:18');
COMMIT;

-- ----------------------------
-- Table structure for bl_rooms
-- ----------------------------
DROP TABLE IF EXISTS `bl_rooms`;
CREATE TABLE `bl_rooms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `name` char(64) NOT NULL COMMENT '名称',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用（0否，1是）',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '顺序',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `is_enable` (`status`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='教室类别';

-- ----------------------------
-- Table structure for bl_semesters
-- ----------------------------
DROP TABLE IF EXISTS `bl_semesters`;
CREATE TABLE `bl_semesters` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `name` varchar(120) NOT NULL COMMENT '名称',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用（0否，1是）',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '顺序',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='学期类别';

-- ----------------------------
-- Records of bl_semesters
-- ----------------------------
BEGIN;
INSERT INTO `bl_semesters` VALUES (1, '2018-2019学年第1学期', 1, 1, '2019-10-21 15:00:04', '2019-10-21 15:00:04');
COMMIT;

-- ----------------------------
-- Table structure for bl_students
-- ----------------------------
DROP TABLE IF EXISTS `bl_students`;
CREATE TABLE `bl_students` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '学生id',
  `number` varchar(32) NOT NULL DEFAULT '' COMMENT '学生编号-唯一（年份+8位自增id，不足以0前置补齐）',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '学生状态 1 在读 2 开题 3 结业 4 毕业 5 肄业 6退学',
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `gender` tinyint(1) unsigned NOT NULL DEFAULT '3' COMMENT '性别（1女，2男 ,3保密）',
  `semester_id` int(11) NOT NULL DEFAULT '0' COMMENT '学期ID',
  `tutor_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '导师id',
  `enrolment` varchar(64) NOT NULL DEFAULT '000-00-00' COMMENT '入学时间',
  `my_mobile` varchar(64) NOT NULL DEFAULT '' COMMENT '手机号码-学生本人',
  `email` varchar(120) NOT NULL DEFAULT '' COMMENT '电子邮箱（最大长度60个字符）',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_number` (`number`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='学生';

-- ----------------------------
-- Records of bl_students
-- ----------------------------
BEGIN;
INSERT INTO `bl_students` VALUES (1, '20191021', 1, 'jackin.chen', 2, 1, 1, '2019-10-21', '18823765411', 'yuncopy@sina.com', '2019-10-21 15:00:44', '2019-10-21 15:00:44');
COMMIT;

-- ----------------------------
-- Table structure for bl_subjects
-- ----------------------------
DROP TABLE IF EXISTS `bl_subjects`;
CREATE TABLE `bl_subjects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT '' COMMENT '导师名称',
  `credit` varchar(4) DEFAULT '0' COMMENT '课程学分',
  `start_time` varchar(64) DEFAULT '' COMMENT '课程选课时间',
  `end_time` varchar(64) DEFAULT '' COMMENT '课程选课截止时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 启用  2 注销',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updte_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COMMENT='安排课程表';

-- ----------------------------
-- Records of bl_subjects
-- ----------------------------
BEGIN;
INSERT INTO `bl_subjects` VALUES (1, '社会主义建设理论与实践', '2', '2018-09-10', '2019-04-16', 1, '2018-04-17 15:08:59', '2019-04-16 12:39:40');
INSERT INTO `bl_subjects` VALUES (2, '外国语（英语）', '2', '2018-09-10', '2019-04-16', 1, '2018-04-17 15:09:17', '2019-04-16 12:39:51');
INSERT INTO `bl_subjects` VALUES (3, '公共管理学', '3', '2019-03-01', '2019-03-10', 1, '2018-04-17 15:09:32', '2019-02-25 11:27:02');
INSERT INTO `bl_subjects` VALUES (4, '公共政策分析', '2', '2018-09-10', '2019-04-16', 1, '2018-04-17 15:09:52', '2019-04-16 12:40:01');
INSERT INTO `bl_subjects` VALUES (5, '公共经济学', '3', '2018-05-01', '2018-09-21', 1, '2018-04-17 15:10:07', '2018-05-23 15:18:57');
INSERT INTO `bl_subjects` VALUES (6, '宪法与行政法', '2', '2018-09-10', '2018-09-14', 1, '2018-04-17 15:10:19', '2018-09-10 11:35:01');
INSERT INTO `bl_subjects` VALUES (7, '社会研究方法', '2', '2018-09-10', '2018-09-14', 1, '2018-04-17 15:10:31', '2018-09-10 11:35:25');
INSERT INTO `bl_subjects` VALUES (8, '电子政务', '2', '2018-09-10', '2018-09-14', 1, '2018-04-17 15:10:39', '2018-09-10 11:34:35');
INSERT INTO `bl_subjects` VALUES (9, '政治学', '2', '2019-03-01', '2019-03-10', 1, '2018-04-17 15:10:48', '2019-02-25 11:34:42');
INSERT INTO `bl_subjects` VALUES (10, '公共行政理论', '2', '2019-03-01', '2019-03-10', 1, '2018-04-17 15:10:57', '2019-02-25 11:50:23');
INSERT INTO `bl_subjects` VALUES (11, '领导艺术与方法', '2', '2019-03-01', '2019-03-10', 1, '2018-04-17 15:11:06', '2019-02-25 11:49:56');
INSERT INTO `bl_subjects` VALUES (13, '专题讲座', '1', '2019-03-01', '2019-03-10', 1, '2018-04-17 15:18:41', '2019-02-25 11:54:25');
INSERT INTO `bl_subjects` VALUES (14, '社会实践', '2', '2019-03-01', '2019-03-10', 1, '2018-04-17 15:18:48', '2019-02-25 11:53:50');
INSERT INTO `bl_subjects` VALUES (15, '公文写作', '2', '2018-09-10', '2019-04-17', 1, '2018-04-17 15:18:55', '2019-04-16 12:40:42');
INSERT INTO `bl_subjects` VALUES (16, '城市管理学', '2', '2018-05-01', '2018-09-21', 1, '2018-04-17 15:19:04', '2018-05-23 15:27:10');
INSERT INTO `bl_subjects` VALUES (17, '社会风险管理', '2', '2019-03-01', '2019-03-10', 1, '2018-04-17 15:21:33', '2019-02-25 11:49:29');
INSERT INTO `bl_subjects` VALUES (18, '区域规划理论与实践', '2', '2019-03-01', '2019-03-10', 1, '2018-04-17 15:22:23', '2019-02-25 11:48:43');
INSERT INTO `bl_subjects` VALUES (19, '经济学研究方法', '2', '2018-05-01', '2018-09-21', 2, '2018-04-17 15:22:32', '2018-09-10 11:04:34');
INSERT INTO `bl_subjects` VALUES (20, '社会阶层与利益集团分析', '2', '2018-05-04', '2018-09-21', 2, '2018-04-17 15:22:47', '2018-09-10 10:56:27');
INSERT INTO `bl_subjects` VALUES (21, '非营利组织管理', '2', '2018-05-01', '2018-09-21', 2, '2018-04-17 15:23:02', '2018-09-10 10:56:21');
INSERT INTO `bl_subjects` VALUES (22, '福利经济学', '2', '2018-05-01', '2018-09-21', 2, '2018-04-17 15:23:10', '2018-09-10 10:56:24');
INSERT INTO `bl_subjects` VALUES (23, '公共工程管理概论', '2', '2019-03-01', '2019-03-10', 1, '2018-04-17 15:23:18', '2019-02-25 11:48:09');
INSERT INTO `bl_subjects` VALUES (24, '社会保障理论与政策', '2', '2019-03-01', '2019-03-10', 1, '2018-04-17 15:23:31', '2019-02-25 11:47:43');
INSERT INTO `bl_subjects` VALUES (25, '组织行为学', '2', '2018-09-01', '2018-09-21', 2, '2018-04-17 15:24:00', '2018-05-30 09:37:33');
INSERT INTO `bl_subjects` VALUES (26, '公共危机管理', '2', '2018-05-01', '2018-09-21', 2, '2018-04-17 15:24:10', '2018-05-30 09:37:30');
INSERT INTO `bl_subjects` VALUES (27, '公共伦理', '2', '2018-09-10', '2018-09-14', 2, '2018-04-17 16:14:34', '2018-09-10 11:12:26');
INSERT INTO `bl_subjects` VALUES (28, '世界经济专题', '2', '2018-09-10', '2018-09-14', 2, '2018-04-17 16:14:51', '2018-09-10 11:12:10');
INSERT INTO `bl_subjects` VALUES (30, '公共部门人力资源管理', '2', '2018-09-10', '2019-04-16', 1, '2018-05-23 15:12:19', '2019-04-16 12:40:22');
INSERT INTO `bl_subjects` VALUES (31, '公共经济学B', '2', '2018-09-10', '2019-04-16', 1, '2018-09-10 11:02:11', '2019-04-16 12:40:12');
COMMIT;

-- ----------------------------
-- Table structure for bl_tasks
-- ----------------------------
DROP TABLE IF EXISTS `bl_tasks`;
CREATE TABLE `bl_tasks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL COMMENT '任务名称',
  `command` varchar(200) NOT NULL COMMENT '执行任务',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '执行状态',
  `result` varchar(64) NOT NULL DEFAULT '' COMMENT '执行结果',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='任务记录表';

-- ----------------------------
-- Records of bl_tasks
-- ----------------------------
BEGIN;
INSERT INTO `bl_tasks` VALUES (1, '测试执行函数', '/usr/bin/php /usr/share/nginx/www/mpa/app/Console/Test.php', 1, '1', '2018-04-14 12:51:37', '2018-04-15 15:29:10');
COMMIT;

-- ----------------------------
-- Table structure for bl_teachers
-- ----------------------------
DROP TABLE IF EXISTS `bl_teachers`;
CREATE TABLE `bl_teachers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '教师编号',
  `number` varchar(120) NOT NULL COMMENT '教师登录使用账号',
  `tutor_class_id` int(11) NOT NULL COMMENT '导师类别',
  `name` varchar(32) DEFAULT '' COMMENT '教师名称',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 启用  2 注销',
  `email` varchar(32) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updte_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_num` (`number`) USING BTREE COMMENT '邮箱唯一性'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='教师表';

-- ----------------------------
-- Table structure for bl_tutor_class
-- ----------------------------
DROP TABLE IF EXISTS `bl_tutor_class`;
CREATE TABLE `bl_tutor_class` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(64) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='导师分类表';

-- ----------------------------
-- Records of bl_tutor_class
-- ----------------------------
BEGIN;
INSERT INTO `bl_tutor_class` VALUES (1, '副教授', '2018-04-13 10:44:35', '2018-04-17 11:50:50');
INSERT INTO `bl_tutor_class` VALUES (2, '教授', '2018-04-17 11:48:55', '2018-04-17 11:50:33');
INSERT INTO `bl_tutor_class` VALUES (3, '讲师', '2018-04-17 11:50:59', '2018-04-17 11:50:59');
INSERT INTO `bl_tutor_class` VALUES (4, '其他', '2018-04-17 11:51:08', '2018-04-17 11:51:08');
INSERT INTO `bl_tutor_class` VALUES (5, '高级会计', '2018-04-17 11:51:23', '2018-04-17 11:57:52');
INSERT INTO `bl_tutor_class` VALUES (6, '研究员', '2018-04-17 11:53:44', '2018-04-17 11:57:40');
INSERT INTO `bl_tutor_class` VALUES (7, '高级工程师', '2018-04-17 11:57:30', '2018-04-17 11:57:30');
INSERT INTO `bl_tutor_class` VALUES (8, '副研究员', '2018-04-17 11:58:06', '2018-04-17 11:58:06');
INSERT INTO `bl_tutor_class` VALUES (9, '兼职教授', '2018-04-17 14:39:37', '2018-04-17 14:39:37');
INSERT INTO `bl_tutor_class` VALUES (10, '研究馆员', '2018-04-17 14:44:12', '2018-04-17 14:44:12');
INSERT INTO `bl_tutor_class` VALUES (11, '编审', '2018-04-17 14:46:05', '2018-04-17 14:46:05');
COMMIT;

-- ----------------------------
-- Table structure for bl_tutors
-- ----------------------------
DROP TABLE IF EXISTS `bl_tutors`;
CREATE TABLE `bl_tutors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(64) DEFAULT NULL COMMENT '导师编号',
  `tutor_class_id` int(11) DEFAULT NULL COMMENT '导师类别',
  `name` varchar(32) DEFAULT '' COMMENT '导师名称',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 启用  2 注销',
  `email` varchar(32) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updte_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_email` (`email`) USING BTREE COMMENT '邮箱唯一性',
  UNIQUE KEY `idx_number` (`number`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='导师表';

-- ----------------------------
-- Records of bl_tutors
-- ----------------------------
BEGIN;
INSERT INTO `bl_tutors` VALUES (1, '20191021864', 2, 'jackin.chen', 1, 'yuncopy@sina.com', '2019-10-21 14:58:57', '2019-10-21 14:58:57');
COMMIT;

-- ----------------------------
-- Table structure for bl_user_relations
-- ----------------------------
DROP TABLE IF EXISTS `bl_user_relations`;
CREATE TABLE `bl_user_relations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `relation_id` int(11) DEFAULT NULL COMMENT '关系ID，对应不同角色区分',
  `role_id` int(11) DEFAULT NULL COMMENT '角色ID',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='用户关联表';

-- ----------------------------
-- Records of bl_user_relations
-- ----------------------------
BEGIN;
INSERT INTO `bl_user_relations` VALUES (1, 2, 1, 4, '2019-10-21 14:58:57', '2019-10-21 14:58:57');
INSERT INTO `bl_user_relations` VALUES (2, 3, 1, 3, '2019-10-21 15:00:44', '2019-10-21 15:00:44');
COMMIT;

-- ----------------------------
-- Table structure for bl_users
-- ----------------------------
DROP TABLE IF EXISTS `bl_users`;
CREATE TABLE `bl_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(200) DEFAULT NULL,
  `passwd` varchar(255) DEFAULT NULL,
  `role_id` int(11) DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_username` (`username`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of bl_users
-- ----------------------------
BEGIN;
INSERT INTO `bl_users` VALUES (1, 'jackin', 'f9a15c1a9d9d536642fb7f2ed750099f', 5, 'yuncopy@sina.com', '2018-04-13 10:20:40', '2019-10-21 15:39:04');
INSERT INTO `bl_users` VALUES (2, '20191021864', 'a54399b8ce0c6d7251c851d55a03f7df', 4, 'yuncopy@sina.com', '2019-10-21 14:58:57', '2019-10-21 14:58:57');
INSERT INTO `bl_users` VALUES (3, '20191021', 'dc1e4d819a50ce5948b6f14f1c85994d', 3, 'yuncopy@sina.com', '2019-10-21 15:00:44', '2019-10-21 15:00:44');
COMMIT;

-- ----------------------------
-- Table structure for bl_website
-- ----------------------------
DROP TABLE IF EXISTS `bl_website`;
CREATE TABLE `bl_website` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(64) NOT NULL,
  `value` varchar(255) NOT NULL,
  `remark` varchar(255) DEFAULT NULL COMMENT '备注说明',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='站点信息';

-- ----------------------------
-- Records of bl_website
-- ----------------------------
BEGIN;
INSERT INTO `bl_website` VALUES (1, 'description', '轻量级教务系统', '网站信息描述', '2018-01-25 07:56:39', '2018-01-25 12:29:42');
INSERT INTO `bl_website` VALUES (2, 'keywords', '教务系统', '网站关键字，利于网站搜索 SEO', '2018-01-25 07:57:05', '2018-01-25 07:58:05');
INSERT INTO `bl_website` VALUES (3, 'pass', '111111', '用户初始化密码', '2018-01-25 07:57:27', '2018-02-02 04:37:11');
INSERT INTO `bl_website` VALUES (4, 'version', 'BaleCMS v2.3.1', '软件版本', '2018-01-25 08:10:09', '2019-10-15 19:31:03');
INSERT INTO `bl_website` VALUES (5, 'copyright', '所有版权 © 2017 星点 All rights reserved | Design by ', '版权所有信息。', '2018-01-25 08:11:28', '2018-01-26 08:09:23');
INSERT INTO `bl_website` VALUES (6, 'author', 'Ajay，Jackin.chen', '项目发起人', '2018-01-25 08:13:02', '2019-04-05 00:25:31');
INSERT INTO `bl_website` VALUES (7, 'team', '英女，无崖子', '团队', '2018-01-25 08:21:59', '2018-01-25 08:22:56');
INSERT INTO `bl_website` VALUES (8, 'title', '教务系统', '系统名称', '2018-01-25 12:25:23', '2018-01-25 12:25:23');
INSERT INTO `bl_website` VALUES (9, 'imglimt', '20M', '图片最大限制（单位B [上传图片还受到服务器空间PHP配置最大上传 20M 限制]）', '2018-01-25 12:32:47', '2018-01-25 12:32:47');
INSERT INTO `bl_website` VALUES (11, 'template', 'photo', '前台模板设置', '2018-02-02 02:23:52', '2018-02-02 02:28:09');
COMMIT;

-- ----------------------------
-- Table structure for bl_weeks
-- ----------------------------
DROP TABLE IF EXISTS `bl_weeks`;
CREATE TABLE `bl_weeks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `name` char(30) NOT NULL COMMENT '名称',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用（0否，1是）',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '顺序',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='周天类别';

SET FOREIGN_KEY_CHECKS = 1;
