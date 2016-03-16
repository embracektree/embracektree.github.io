
-- --------------------------------------------------------

--
-- Table structure for table `faq_admin_settings_cfg`
--

CREATE TABLE IF NOT EXISTS `faq_admin_settings_cfg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `label` varchar(255) CHARACTER SET utf8 NOT NULL,
  `group` varchar(255) CHARACTER SET utf8 NOT NULL,
  `type` enum('text','password','dropdown','textarea','multiselect','image','checkbox') CHARACTER SET utf8 NOT NULL,
  `validations` text CHARACTER SET utf8 NOT NULL,
  `options` text CHARACTER SET utf8 NOT NULL,
  `values` text CHARACTER SET utf8 NOT NULL,
  `default_values` text CHARACTER SET utf8 NOT NULL,
  `status` enum('Active','Inactive') CHARACTER SET utf8 NOT NULL DEFAULT 'Active',
  `created_by` int(11) NOT NULL,
  `created_time` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  `updated_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `faq_admin_settings_cfg`
--

INSERT INTO `faq_admin_settings_cfg` (`id`, `name`, `label`, `group`, `type`, `validations`, `options`, `values`, `default_values`, `status`, `created_by`, `created_time`, `updated_by`, `updated_time`) VALUES
(1, 'site_name', 'Site Name', 'General', 'text', 'required', '', '', '', 'Active', 1, '2016-01-21 04:43:00', 1, '2016-01-21 04:44:00'),
(2, 'site_description', 'Site Description', 'General', 'textarea', 'required', '', '<p>Site Description</p>\r\n', '', 'Active', 1, '2016-01-21 04:46:00', 1, '2016-01-21 04:46:00'),
(3, 'meta_title', 'Meta Title', 'SEO', 'text', 'NULL', '', 'Meta Title', '', 'Active', 1, '2016-01-21 04:47:00', 1, '2016-01-21 04:42:00'),
(4, 'meta_description', 'Meta Description', 'SEO', 'textarea', 'NULL', '', '<p>Meta Description</p>\r\n', '', 'Active', 1, '2016-01-21 04:47:00', 1, '2016-01-21 04:47:00'),
(5, 'searchengine_hide', 'Hide From Search Engines', 'SEO', 'checkbox', 'NULL', '', '1', '', 'Active', 1, '2016-01-21 04:50:00', 1, '2016-01-21 04:52:00'),
(6, 'posts', 'Posts', 'General', 'text', 'NULL', '', 'posts', '', 'Active', 1, '2016-01-21 04:52:00', 1, '2016-01-21 04:50:00'),
(7, 'logo', 'Site Logo', 'General', 'image', 'NULL', '', 'admin_settings_images/ktree logo new.png', '', 'Active', 1, '2016-01-21 04:53:00', 1, '2016-01-21 04:51:00'),
(8, 'meta_keywords', 'Meta Keywords', 'SEO', 'text', '', '', 'Keyword', '', 'Active', 1, '2016-01-22 04:48:00', 1, '2016-01-22 04:47:00'),
(9, 'enable_sphnix_search', 'Enable Sphnix Search', 'General', 'checkbox', '', '', '0', '', 'Active', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00'),
(10, 'footer_content', 'Footer Content', 'General', 'textarea', '', '', '<p>Copyright <strong>&copy;</strong> 2015-2016 All rights reserved<strong>.</strong></p>\r\n', '', 'Active', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00'),
(11, 'theme', 'Theme', 'General', 'text', 'required', '', 'ktreefaq', '', 'Active', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00'),
(12, 'default_language', 'Default Language', 'General', 'dropdown', 'required', '{"EN":"English","FR":"French","IT":"Italian","FI":"Finnish","SP":"Spanish"}', 'EN', 'EN', 'Active', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `faq_auth_assignment`
--

CREATE TABLE IF NOT EXISTS `faq_auth_assignment` (
  `item_name` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `faq_auth_assignment`
--

INSERT INTO `faq_auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('admin', '1', 1448340539),
('admin', '10', 1448347077),
('author', '11', 1448347119);

-- --------------------------------------------------------

--
-- Table structure for table `faq_auth_item`
--

CREATE TABLE IF NOT EXISTS `faq_auth_item` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `faq_auth_item`
--

INSERT INTO `faq_auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
('admin', 1, NULL, NULL, NULL, 1448340539, 1448340539),
('author', 1, NULL, NULL, NULL, 1448340539, 1448340539),
('create', 2, 'Create a post', NULL, NULL, 1448341485, 1448341485),
('createPost', 2, 'Create a post', NULL, NULL, 1448340539, 1448340539),
('update', 2, 'Update post', NULL, NULL, 1448341485, 1448341485),
('updatePost', 2, 'Update post', NULL, NULL, 1448340539, 1448340539);

-- --------------------------------------------------------

--
-- Table structure for table `faq_auth_item_child`
--

CREATE TABLE IF NOT EXISTS `faq_auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `faq_auth_item_child`
--

INSERT INTO `faq_auth_item_child` (`parent`, `child`) VALUES
('admin', 'author'),
('author', 'createPost'),
('admin', 'updatePost');

-- --------------------------------------------------------

--
-- Table structure for table `faq_auth_rule`
--

CREATE TABLE IF NOT EXISTS `faq_auth_rule` (
  `name` varchar(64) NOT NULL,
  `data` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `faq_filemanager_mediafile`
--

CREATE TABLE IF NOT EXISTS `faq_filemanager_mediafile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `alt` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `thumbs` varchar(255) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=58 ;

--
-- Dumping data for table `faq_filemanager_mediafile`
--

INSERT INTO `faq_filemanager_mediafile` (`id`, `filename`, `type`, `url`, `alt`, `size`, `description`, `thumbs`, `created_at`, `updated_at`) VALUES
(57, '1457962906ktree-logo-new.png', 'image/png', '/upload/2016/03/1457962906ktree-logo-new.png', '', 4511, '', 'a:3:{s:5:"small";s:52:"/upload/2016/03/1457962906ktree-logo-new-100x100.png";s:6:"medium";s:52:"/upload/2016/03/1457962906ktree-logo-new-300x200.png";s:5:"large";s:52:"/upload/2016/03/1457962906ktree-logo-new-500x400.png";}', 1457962906, 0);

-- --------------------------------------------------------

--
-- Table structure for table `faq_filemanager_owners`
--

CREATE TABLE IF NOT EXISTS `faq_filemanager_owners` (
  `mediafile_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` varchar(255) NOT NULL,
  `owner_attribute` varchar(255) NOT NULL,
  PRIMARY KEY (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `faq_media`
--

CREATE TABLE IF NOT EXISTS `faq_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_name` varchar(255) NOT NULL,
  `media_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `status` int(4) NOT NULL,
  `created_by` int(4) NOT NULL,
  `created_date` datetime NOT NULL,
  `modified_by` int(4) NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_media_created_by` (`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Media Information' AUTO_INCREMENT=43 ;

--
-- Dumping data for table `faq_media`
--

INSERT INTO `faq_media` (`id`, `media_name`, `media_id`, `image_path`, `status`, `created_by`, `created_date`, `modified_by`, `modified_date`) VALUES
(41, 'Topic Images', 10, '/upload/2016/03/1457962906ktree-logo-new.png', 1, 1, '2016-03-14 19:11:57', 1, '2016-03-14 19:11:57'),
(42, 'Topic Images', 1, '/upload/2016/03/1457962906ktree-logo-new.png', 1, 1, '2016-03-14 19:16:18', 1, '2016-03-14 19:16:18');

-- --------------------------------------------------------

--
-- Table structure for table `faq_questions`
--

CREATE TABLE IF NOT EXISTS `faq_questions` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `parent_question_id` int(11) DEFAULT '0',
  `question_image` varchar(255) DEFAULT NULL,
  `topic_id` int(11) NOT NULL,
  `sort_order` int(11) DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`question_id`),
  KEY `faq_topics_id` (`topic_id`),
  KEY `fk_ques_created_by` (`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Question Main Information storage' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `faq_questions`
--

INSERT INTO `faq_questions` (`question_id`, `question_name`, `slug`, `parent_question_id`, `question_image`, `topic_id`, `sort_order`, `created_by`, `created_date`, `modified_by`, `modified_date`) VALUES
(1, 'Sample Topic With Question', 'sample-topic-with-question', 0, NULL, 1, 1, 1, '2016-03-14 19:16:18', 1, '2016-03-14 19:16:18');

-- --------------------------------------------------------

--
-- Table structure for table `faq_questions_info`
--

CREATE TABLE IF NOT EXISTS `faq_questions_info` (
  `question_id` int(11) NOT NULL,
  `question_name` varchar(255) NOT NULL,
  `sort_order` int(4) NOT NULL DEFAULT '0',
  `question_description` longtext NOT NULL,
  `language` varchar(55) NOT NULL DEFAULT 'EN',
  `question_status` int(4) NOT NULL COMMENT '0-Draft 1-Publish 2-Delete',
  `created_by` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date` datetime NOT NULL,
  KEY `fk_question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Questions Information storage';

--
-- Dumping data for table `faq_questions_info`
--

INSERT INTO `faq_questions_info` (`question_id`, `question_name`, `sort_order`, `question_description`, `language`, `question_status`, `created_by`, `created_date`, `modified_by`, `modified_date`) VALUES
(1, 'Sample Topic With Question', 0, '', 'EN', 0, 1, '2016-03-14 19:16:18', 1, '2016-03-14 19:16:18');

-- --------------------------------------------------------

--
-- Table structure for table `faq_sys_lang_cfg`
--

CREATE TABLE IF NOT EXISTS `faq_sys_lang_cfg` (
  `language_id` int(10) NOT NULL AUTO_INCREMENT,
  `language_name` varchar(100) NOT NULL,
  `short_name` varchar(5) NOT NULL,
  `status` int(4) NOT NULL,
  `created_by` int(4) NOT NULL,
  `created_date` datetime NOT NULL,
  `modified_by` int(4) NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `faq_sys_lang_cfg`
--

INSERT INTO `faq_sys_lang_cfg` (`language_id`, `language_name`, `short_name`, `status`, `created_by`, `created_date`, `modified_by`, `modified_date`) VALUES
(1, 'English', 'EN', 1, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00'),
(2, 'French', 'FR', 1, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00'),
(3, 'Italian', 'IT', 1, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00'),
(4, 'Finnish', 'FI', 1, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00'),
(5, 'Spanish', 'SP', 1, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `faq_topics`
--

CREATE TABLE IF NOT EXISTS `faq_topics` (
  `topic_id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `topic_image` varchar(255) DEFAULT NULL,
  `created_by` int(4) NOT NULL,
  `created_date` datetime NOT NULL,
  `modified_by` int(4) NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`topic_id`),
  KEY `fk_created_by` (`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Topics information storage' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `faq_topics`
--

INSERT INTO `faq_topics` (`topic_id`, `topic_name`, `slug`, `topic_image`, `created_by`, `created_date`, `modified_by`, `modified_date`) VALUES
(1, 'Sample Topic With Question', 'sample-topic-with-question', '/upload/2016/03/1457962906ktree-logo-new.png', 1, '2016-03-14 19:16:18', 1, '2016-03-14 19:16:18');

-- --------------------------------------------------------

--
-- Table structure for table `faq_topics_info`
--

CREATE TABLE IF NOT EXISTS `faq_topics_info` (
  `topic_id` int(11) NOT NULL,
  `topic_name` varchar(255) NOT NULL,
  `language` varchar(55) NOT NULL DEFAULT 'EN',
  `topic_description` text NOT NULL,
  `topic_short_desc` text NOT NULL,
  `topic_status` int(4) NOT NULL COMMENT '0-Draft 1-Publish 2-Delete',
  `created_by` int(4) NOT NULL,
  `created_date` datetime NOT NULL,
  `modified_by` int(4) NOT NULL,
  `modified_date` datetime NOT NULL,
  KEY `fk_topic_id` (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Topics information storage';

--
-- Dumping data for table `faq_topics_info`
--

INSERT INTO `faq_topics_info` (`topic_id`, `topic_name`, `language`, `topic_description`, `topic_short_desc`, `topic_status`, `created_by`, `created_date`, `modified_by`, `modified_date`) VALUES
(1, 'Sample Topic With Question', 'EN', 'Sample Topic With Question', 'Sample Topic With Question', 0, 1, '2016-03-14 19:16:18', 1, '2016-03-14 19:16:18');

-- --------------------------------------------------------

--
-- Table structure for table `faq_users`
--

CREATE TABLE IF NOT EXISTS `faq_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_username` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `user_firstname` varchar(255) NOT NULL,
  `user_lastname` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `roles` text NOT NULL,
  `groups` text NOT NULL,
  `user_type` varchar(255) NOT NULL DEFAULT 'application',
  `user_status` int(4) NOT NULL DEFAULT '1' COMMENT '0 -Inactive  1 - Active  2 - Terminated',
  `password_hash` varchar(255) NOT NULL,
  `auth_key` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) NOT NULL,
  `user_created_by` int(4) NOT NULL,
  `user_created_date` datetime NOT NULL,
  `user_modified_by` int(4) NOT NULL,
  `user_modified_date` datetime NOT NULL,
  `created_at` varchar(255) NOT NULL,
  `updated_at` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='LMS USER INFORMATION' AUTO_INCREMENT=14 ;

--
-- Dumping data for table `faq_users`
--

INSERT INTO `faq_users` (`user_id`, `user_username`, `slug`, `user_firstname`, `user_lastname`, `user_password`, `user_email`, `roles`, `groups`, `user_type`, `user_status`, `password_hash`, `auth_key`, `password_reset_token`, `user_created_by`, `user_created_date`, `user_modified_by`, `user_modified_date`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', 'admin', 'admin', '4acb4bc224acbbe3c2bfdcaa39a4324e', 'adminuser@ktree.com', 'admin', 'Groups', 'application', 1, '$2y$13$c5VKuqQ/fbV5GXPxlxhTh.J56Hw9xtRHDqjUn7.LhcguBrCsxO9qW', 'r6A8dYnkLHJ5N_bsn85DCc7Il1l1eisa', '-b45qPZvfyC0B7JRv1ubU-DnN8PL1a0A_1457938990', 1, '0000-00-00 00:00:00', 1, '2016-03-14 12:33:10', '', '1448458407');

-- --------------------------------------------------------

--
-- Table structure for table `faq_user_login_history`
--

CREATE TABLE IF NOT EXISTS `faq_user_login_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `logged_in` datetime NOT NULL,
  `logged_out` datetime NOT NULL,
  `time_spent` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User Login History' AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `faq_auth_assignment`
--
ALTER TABLE `faq_auth_assignment`
  ADD CONSTRAINT `faq_auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `faq_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `faq_auth_assignment_ibfk_2` FOREIGN KEY (`item_name`) REFERENCES `faq_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `faq_auth_item`
--
ALTER TABLE `faq_auth_item`
  ADD CONSTRAINT `faq_auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `faq_auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `faq_auth_item_ibfk_2` FOREIGN KEY (`rule_name`) REFERENCES `faq_auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `faq_auth_item_child`
--
ALTER TABLE `faq_auth_item_child`
  ADD CONSTRAINT `faq_auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `faq_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `faq_auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `faq_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `faq_auth_item_child_ibfk_3` FOREIGN KEY (`parent`) REFERENCES `faq_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `faq_auth_item_child_ibfk_4` FOREIGN KEY (`child`) REFERENCES `faq_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `faq_media`
--
ALTER TABLE `faq_media`
  ADD CONSTRAINT `fk_media_created_by` FOREIGN KEY (`created_by`) REFERENCES `faq_users` (`user_id`);

--
-- Constraints for table `faq_questions`
--
ALTER TABLE `faq_questions`
  ADD CONSTRAINT `faq_topics_id` FOREIGN KEY (`topic_id`) REFERENCES `faq_topics` (`topic_id`),
  ADD CONSTRAINT `fk_ques_created_by` FOREIGN KEY (`created_by`) REFERENCES `faq_users` (`user_id`);

--
-- Constraints for table `faq_questions_info`
--
ALTER TABLE `faq_questions_info`
  ADD CONSTRAINT `fk_question_id` FOREIGN KEY (`question_id`) REFERENCES `faq_questions` (`question_id`);

--
-- Constraints for table `faq_topics`
--
ALTER TABLE `faq_topics`
  ADD CONSTRAINT `fk_created_by` FOREIGN KEY (`created_by`) REFERENCES `faq_users` (`user_id`);

--
-- Constraints for table `faq_topics_info`
--
ALTER TABLE `faq_topics_info`
  ADD CONSTRAINT `fk_topic_id` FOREIGN KEY (`topic_id`) REFERENCES `faq_topics` (`topic_id`);

--
-- Constraints for table `faq_user_login_history`
--
ALTER TABLE `faq_user_login_history`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `faq_users` (`user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;