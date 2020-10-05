
UPDATE `<DB_PREFIX>site_info` SET `header` = 'ApPHP Survey', `slogan` = 'Welcome to ApPHP Survey!', `footer` = 'ApPHP Survey &copy; <a class="footer_link" target="_new" href="http://www.apphp.com/php-survey/index.php">ApPHP</a>', `meta_title` = 'ApPHP Survey', `meta_description` = 'ApPHP Survey', `meta_keywords` = 'php survey, php votes, php questionnaire';


INSERT INTO `<DB_PREFIX>modules` (`id`, `code`, `class_code`, `name`, `description`, `version`, `icon`, `show_on_dashboard`, `show_in_menu`, `is_installed`, `is_system`, `is_active`, `installed_at`, `updated_at`, `has_test_data`, `sort_order`) VALUES
(NULL, 'surveys', 'Surveys', 'Surveys', 'Survey module allows creating and managing online surveys', '0.0.2', 'icon.png', 1, 1, 1, 1, 1, '<CURRENT_DATETIME>', NULL, 1, (SELECT COUNT(m.id) + 1 FROM `<DB_PREFIX>modules` m WHERE m.is_system = 1));


INSERT INTO `<DB_PREFIX>module_settings` (`id`, `module_code`, `property_group`, `property_key`, `property_value`, `name`, `description`, `property_type`, `property_source`, `trigger_condition`, `is_required`) VALUES
(NULL, 'surveys', '', 'survey_code_type', 'random', 'Survey Code Type', 'The type of survey code', 'enum', 'random,sequential', '', 0),
(NULL, 'surveys', '', 'show_page_complete_alert', '0', 'Show Page Complete Alert', 'Defines whether to show missing questions alert before going to next page', 'bool', '', '', 0),
(NULL, 'surveys', '', 'show_terms_and_conditions', '1', 'Show Terms and Conditions', 'Defines whether to show on login page "I agree with Terms and Conditions"', 'bool', '', '', 0),
(NULL, 'surveys', '', 'enable_login_by_url', '0', 'Enable Login by URL', 'Defines whether to enable direct login by URL', 'bool', '', '', 0),
(NULL, 'surveys', '', 'participant_identification_type', 'ip_address', 'Participant Identification Type', 'Specifies a type of participant identification for surveys with public access', 'enum', 'ip_address,cookies', '', 0),
(NULL, 'surveys', 'Email notifications', 'send_participant_email_notification', '0', 'Enable Participant Notifications', 'Defines whether to send email notification to participant after finishing the survey', 'bool', '', '', 0),
(NULL, 'surveys', 'Email notifications', 'send_admin_email_notification', '0', 'Enable Admin Notifications', 'Defines whether to send email notification to admin after finishing the survey', 'bool', '', '', 0),
(NULL, 'surveys', 'Email notifications', 'admin_notification_email', '', 'Admin Email for Notifications', 'The admin email address used to receive notification after participant finishes the survey', 'email', '', 'a:2:{s:7:"trigger";a:3:{s:3:"key";s:29:"send_admin_email_notification";s:9:"operation";s:2:"!=";s:5:"value";s:1:"0";}s:6:"action";a:2:{s:5:"field";s:11:"is_required";s:5:"value";s:1:"1";}}', 0),
(NULL, 'surveys', 'Participant Fields', 'field_identity_code', 'allow', 'Identity Code', 'Defines whether to allow Identity Code field on participant profile', 'enum', 'allow', '', 0),
(NULL, 'surveys', 'Participant Fields', 'field_password', 'allow', 'Password Field', 'Defines whether to allow Password field on participant profile', 'enum', 'allow,no', '', 0),
(NULL, 'surveys', 'Participant Fields', 'field_first_name', 'allow-optional', 'First Name Field', 'Defines whether to allow First Name field on participant profile', 'enum', 'allow-required,allow-optional,no', '', 0),
(NULL, 'surveys', 'Participant Fields', 'field_last_name', 'allow-optional', 'Last Name Field', 'Defines whether to allow Last Name field on participant profile', 'enum', 'allow-required,allow-optional,no', '', 0),
(NULL, 'surveys', 'Participant Fields', 'field_email', 'allow-optional', 'Email Field', 'Defines whether to allow Email field on participant profile', 'enum', 'allow-required,allow-optional,no', '', 0),
(NULL, 'surveys', 'Participant Fields', 'field_gender', 'allow-required', 'Gender Field', 'Defines whether to allow Gender field on participant profile', 'enum', 'allow-required,allow-optional,no', '', 0);


INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, 0, '', 'surveys', 'surveys.png', 0, 1, 6);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Surveys' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'surveys' AND bm.parent_id = 0), 'modules/settings/code/surveys', 'surveys', '', 0, 1, 0);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Settings' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'surveys' AND bm.parent_id = 0), 'surveys/manage', 'surveys', '', 0, 1, 1);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Surveys' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'surveys' AND bm.parent_id = 0), 'surveysParticipants/manage', 'surveys', '', 0, 1, 2);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Participants' FROM `<DB_PREFIX>languages`;
INSERT INTO `<DB_PREFIX>backend_menus` (`id`, `parent_id`, `url`, `module_code`, `icon`, `is_system`, `is_visible`, `sort_order`) VALUES (NULL, (SELECT bm.id FROM `<DB_PREFIX>backend_menus` bm WHERE bm.module_code = 'surveys' AND bm.parent_id = 0), 'surveysQuestionTypes/manage', 'surveys', '', 0, 1, 3);
INSERT INTO `<DB_PREFIX>backend_menu_translations` (`id`, `menu_id`, `language_code`, `name`) SELECT NULL, (SELECT MAX(id) FROM `<DB_PREFIX>backend_menus`), code, 'Questions Types' FROM `<DB_PREFIX>languages`;


INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'surveys', 'surveys', 'add', 'Add Surveys', 'Add surveys on the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'surveys', 'surveys', 'edit', 'Edit Surveys', 'Edit surveys on the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'surveys', 'surveys', 'delete', 'Delete Surveys', 'Delete surveys from the site'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'surveys', 'participants', 'add', 'Add Participants', 'Add participants'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'surveys', 'participants', 'edit', 'Edit Participants', 'Edit participants'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'surveys', 'participants', 'delete', 'Delete Participants', 'Delete participants'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'surveys', 'survey_questionnaires', 'add', 'Add Questionnaires', 'Add questionnaires to surveys'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'surveys', 'survey_questionnaires', 'edit', 'Edit Questionnaires', 'Edit questionnaires in surveys'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'surveys', 'survey_questionnaires', 'delete', 'Delete Questionnaires', 'Delete questionnaires from surveys'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'surveys', 'survey_participants', 'add', 'Add Survey Participants', 'Add participants to surveys'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'surveys', 'survey_participants', 'edit', 'Edit Survey Participants', 'Edit participants in surveys'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'surveys', 'survey_participants', 'delete', 'Delete Survey Participants', 'Delete participants from surveys'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'surveys', 'survey_questions', 'add', 'Add Questions', 'Add questions to surveys'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'surveys', 'survey_questions', 'edit', 'Edit Questions', 'Edit questions in surveys'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);
INSERT INTO `<DB_PREFIX>privileges` (`id`, `module_code`, `category`, `code`, `name`, `description`) VALUES (NULL, 'surveys', 'survey_questions', 'delete', 'Delete Questions', 'Delete questions from surveys'); 
INSERT INTO `<DB_PREFIX>role_privileges` (`id`, `role_id`, `privilege_id`, `is_active`) VALUES (NULL, 1, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 2, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 1), (NULL, 3, (SELECT MAX(id) FROM `<DB_PREFIX>privileges`), 0);


INSERT INTO `<DB_PREFIX>email_templates` (`id`, `code`, `module_code`, `is_system`) VALUES (NULL, 'surveys_survey_completed', 'surveys', 1);
INSERT INTO `<DB_PREFIX>email_template_translations` (`id`, `template_code`, `language_code`, `template_name`, `template_subject`, `template_content`) SELECT NULL, 'surveys_survey_completed', code, 'Survey has been completed', ' Thank you for participating in the survey!', 'Dear <b>{FIRST_NAME} {LAST_NAME}</b>!\r\n\r\nYou have now completed the survey and we thank you for your participation and input.\r\n\r\n-\r\nSincerely,\r\nAdministration' FROM `<DB_PREFIX>languages`;


DROP TABLE IF EXISTS `<DB_PREFIX>surveys_entities`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>surveys_entities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `login_message` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `welcome_message` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `welcome_message_f` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `complete_message` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `complete_message_f` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` date NULL DEFAULT NULL,
  `expires_at` date NULL DEFAULT NULL,
  `access_mode` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'p' COMMENT 'p - public, r - registered',
  `votes_mode` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'o' COMMENT 'o - one time, m - multiple times',
  `gender_formulation` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `items_per_page` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2;

INSERT INTO `<DB_PREFIX>surveys_entities` (`id`, `code`, `name`, `description`, `login_message`, `welcome_message`, `welcome_message_f`, `complete_message`, `complete_message_f`,`created_at`, `expires_at`, `access_mode`, `votes_mode`, `gender_formulation`, `items_per_page`, `sort_order`, `is_active`) VALUES
(1, 'AMB6S0P8JA', 'Test Survey', 'This is test survey.', '', '<h1>Welcome to the Test Survey!</h1>We value your candid feedback and appreciate you taking the time to complete our survey.', '<h1>Welcome to the Test Survey!</h1>We value your candid feedback and appreciate you taking the time to complete our survey.', '', '', '2017-01-01', NULL, 'r', 'o', '1', 10, 1, 1);


DROP TABLE IF EXISTS `<DB_PREFIX>surveys_entity_participants`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>surveys_entity_participants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) unsigned NOT NULL DEFAULT '0',
  `participant_id` int(11) unsigned NOT NULL DEFAULT '0',
  `start_date` datetime NULL DEFAULT NULL,
  `finish_date` datetime NULL DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `data_score` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `data_total_score` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `survey_participant` (`survey_id`,`participant_id`),
  KEY `survey_id` (`survey_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4;

INSERT INTO `<DB_PREFIX>surveys_entity_participants` (`id`, `survey_id`, `participant_id`, `start_date`, `finish_date`, `status`, `data_score`, `data_total_score`, `is_active`) VALUES
(1, 1, 1, '2017-05-23 16:15:24', '2017-05-23 16:23:26', 2, '', '', 1),
(2, 1, 2, NULL, NULL, 0, '', '', 1),
(3, 1, 3, NULL, NULL, 0, '', '', 1);


DROP TABLE IF EXISTS `<DB_PREFIX>surveys_entity_questionnaires`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>surveys_entity_questionnaires` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) unsigned NOT NULL DEFAULT '0',
  `category_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `questionnaire_key` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `start_message` text COLLATE utf8_unicode_ci NOT NULL,
  `start_message_f` text COLLATE utf8_unicode_ci NOT NULL,
  `finish_message` text COLLATE utf8_unicode_ci NOT NULL,
  `finish_message_f` text COLLATE utf8_unicode_ci NOT NULL,
  `items_per_page` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2;

INSERT INTO `<DB_PREFIX>surveys_entity_questionnaires` (`id`, `survey_id`, `category_title`, `name`, `description`, `questionnaire_key`, `start_message`, `start_message_f`, `finish_message`, `finish_message_f`, `items_per_page`, `sort_order`, `is_active`) VALUES
(1, 1, '', 'My Test', 'This is a test questionnaire', 'TST', '', '', '', '', 5, 1, 1);


DROP TABLE IF EXISTS `<DB_PREFIX>surveys_participants`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>surveys_participants`(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identity_code` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(64) CHARACTER SET latin1 NOT NULL,
  `first_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `last_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `gender` enum('','f','m') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'm',
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ip_address` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `cookie_code` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5;

INSERT INTO `<DB_PREFIX>surveys_participants` (`id`, `identity_code`, `password`, `first_name`, `last_name`, `gender`, `email`, `ip_address`, `cookie_code`, `is_active`) VALUES
(1, 'participant1', 'test', 'John', 'Smith', 'm', '', '', '', 1),
(2, 'participant2', 'test', 'Rebeka', 'Smith', 'f', '', '', '', 1),
(3, 'participant3', 'test', 'Rob', 'Smith', 'm', '', '', '', 1),
(4, 'participant4', 'test', 'Luisa', 'Smith', 'f', '', '', '', 1);


DROP TABLE IF EXISTS `<DB_PREFIX>surveys_question_types`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>surveys_question_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT '',
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `html_example` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code_example` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=19;

INSERT INTO `<DB_PREFIX>surveys_question_types` VALUES
(1, 'Multiple Choice (only one answer)', 'Ask your respondent to choose one answer from your list of answer choices.', '<div class="question-type-example"><strong>Which flavor of ice cream is your favorite?</strong><div><input type="radio" value="1" id="_1" name="a" /><label for="_1">Chocolate</label><input type="radio" value="2" id="_2" name="a" /><label for="_2">Vanilla</label><input type="radio" value="3" id="_3" name="a" /><label for="_3">Strawberry</label></div></div>', '<div class="code-example">Chocolate<br>Vanilla<br>Strawberry</div>', 1),
(2, 'Multiple Choice (only one answer with other option)', 'Ask your respondent to choose one answer from your list of answer choices or enter another option as a free text.', '<div class="question-type-example"><strong>Which flavor of ice cream is your favorite?</strong><div><input type="radio" value="1" id="_1" name="a" /><label for="_1">Chocolate</label><input type="radio" value="2" id="_2" name="a" /><label for="_2">Vanilla</label><input type="radio" value="3" id="_3" name="a" /><label for="_3">Strawberry</label><label for="_4">Other, please specify</label><input type="text" value="" id="_4" name="a" /></div></div>', '<div class="code-example">Chocolate<br>Vanilla<br>Strawberry<br>#Other, please specify</div>', 1),
(3, 'Multiple Choice (multiple answers)', 'Ask your respondent to choose multiple answers from your list of answer choices.', '<div class="question-type-example"><strong>Which flavor of ice cream is your favorite?</strong><div><input type="checkbox" value="1" id="_1" name="a" /><label for="_1">Chocolate</label><input type="checkbox" value="2" id="_2" name="a" /><label for="_2">Vanilla</label><input type="checkbox" value="3" id="_3" name="a" /><label for="_3">Strawberry</label></div></div>', '<div class="code-example">Chocolate<br>Vanilla<br>Strawberry</div>', 1),
(4, 'Multiple Choice (multiple answers with other option)', 'Ask your respondent to choose multiple answers from your list of answer choices or enter another option as a free text.', '<div class="question-type-example"><strong>Which flavor of ice cream is your favorite?</strong><div><input type="checkbox" value="1" id="_1" name="a" /><label for="_1">Chocolate</label><input type="checkbox" value="2" id="_2" name="a" /><label for="_2">Vanilla</label><input type="checkbox" value="3" id="_3" name="a" /><label for="_3">Strawberry</label><label for="_4">Other, please specify</label><input type="text" value="" id="_4" name="a" /></div></div>', '<div class="code-example">Chocolate<br>Vanilla<br>Strawberry<br>#Other, please specify</div>', 1),
(5, 'Dropdown (only one answer)', 'Provide a dropdown list of answer choices for respondents to choose from. Use the dropdown question when you need to ask a multiple choice, single answer question but want to save space.', '<div class="question-type-example"><strong>Which flavor of ice cream is your favorite?</strong><div><select id="sample_dd" name="sample_dd"><option value="" selected>-- Please Choose --</option><option value="1">Chocolate</option><option value="2">Vanilla</option><option value="3">Strawberry</option></select></div></div>', '<div class="code-example">Chocolate<br>Vanilla<br>Strawberry</div>', 1),
(6, 'Single Textbox', 'Add a single textbox to your survey when you want respondents to write in a short text or numerical answer to your question.', '<div class="question-type-example"><strong>What is your favorite ice cream brand?</strong><div><input type="text" value="" maxlength="255" size="20" /></div></div>', '<div class="code-example"></div>', 1),
(7, 'Multiple Textboxes', 'Add multiple textboxes to your survey when you want respondents to write in more than one short text or numerical answer to your question. You can also specify answer length, and require and validate a number value, date format, or email address.', '<div class="question-type-example"><strong>What are your three favorite ice cream brands?</strong><div><table cellspacing="0"><tr><td class="first">Your favorite:</td><td><input type="text" name="a"></td></tr><tr><td class="first">Second favorite:</td><td><input type="text" name="b"></td></tr><tr><td class="first">Third favorite:</td><td><input type="text" name="c"></td></tr></table></div></div>', '<div class="code-example">Your favorite<br>Second favorite<br>Third favorite</div>', 1),
(8, 'Comment/Essay Box', 'Use the comment or essay box to collect open-ended, written feedback from respondents. You can also specify answer length.', '<div class="question-type-example"><strong>What do you like about your favorite brand of ice cream?</strong><div><textarea cols="20" rows="4"></textarea></div></div>', '<div class="code-example"></div>', 1),
(9, 'Date/Time', 'Ask respondents to enter a specific date and/or time.', '<div class="question-type-example"><strong>When was the last time you ate the following ice cream flavors?</strong><div><table cellspacing="0"><thead><tr><th colspan=5>The Time (DD/MM/YYY)</th></tr></thead><tr><td><input id="_1_DD" name="_1_DD" type="text" class="xsmall" value="" maxlength="2" /></td><td>/</td><td><input id="_1_MM" name="_1_MM" type="text" class="xsmall" value="" maxlength="2" /></td><td>/</td><td><input id="_1_YY" name="_1_YY" type="text" class="small" value="" maxlength="4" /></td></tr></table></div></div>', '<div class="code-example"></div>', 1),
(10, 'Matrix Choice (only one answer per row)', 'Use a matrix (grid) question if you want respondents to apply the same measurement when answering several related questions. You can set the matrix question to collect only one answer per row.', '<div class="question-type-example"><strong>Which flavor of ice cream is each member od your family''s favorite</strong><div><table cellspacing="0"><thead><tr><th>&nbsp;</th><td>Chocolate</td><td>Vanilla</td><td>Strawberry</td></tr></thead><tr><td class="first">Mother</td><td class="center"><input type="radio" value="1" name="m" /></td><td class="center"><input type="radio" value="2" name="m" /></td><td class="center"><input type="radio" value="3" name="m" /></td></tr><tr><td class="first">Father</td><td class="center"><input type="radio" value="1" name="f" /></td><td class="center"><input type="radio" value="2" name="f" /></td><td class="center"><input type="radio" value="3" name="f" /></td></tr><tr><td class="first">Brother</td><td class="center"><input type="radio" value="1" name="b" /></td><td class="center"><input type="radio" value="2" name="b" /></td><td class="center"><input type="radio" value="3" name="b" /></td></tr></table></div></div>', '<div class="code-example">[Mother]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry<br>===<br>[Father]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry<br>===<br>[Brother]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry</div>', 1),
(11, 'Matrix Choice (only one answer per row with other option)', 'Use a matrix (grid) question if you want respondents to apply the same measurement when answering several related questions. You can set the matrix question to collect only one answer per row or enter another option as a free text.', '<div class="question-type-example"><strong>Which flavor of ice cream is each member od your family''s favorite</strong><div><table cellspacing="0"><thead><tr><th>&nbsp;</th><td>Chocolate</td><td>Vanilla</td><td>Strawberry</td><td>Other</td></tr></thead><tr><td class="first">Mother</td><td class="center"><input type="radio" value="1" name="m" /></td><td class="center"><input type="radio" value="2" name="m" /></td><td class="center"><input type="radio" value="3" name="m" /></td><td><input type="text" class="small"></td></tr><tr><td class="first">Father</td><td class="center"><input type="radio" value="1" name="f" /></td><td class="center"><input type="radio" value="2" name="f" /></td><td class="center"><input type="radio" value="3" name="f" /></td><td><input type="text" class="small"></td></tr></table></div></div>', '<div class="code-example">[Mother]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry<br>#Other<br>===<br>[Father]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry<br>#Other<br></div>', 1),
(12, 'Matrix Choice (multiple answers per row)', 'Use a matrix (grid) question if you want respondents to apply the same measurement when answering several related questions. You can set the matrix question to collect multiple answers per row.', '<div class="question-type-example"><strong>Which flavor of ice cream is each member od your family''s favorite</strong><div><table cellspacing="0"><thead><tr><th>&nbsp;</th><td>Chocolate</td><td>Vanilla</td><td>Strawberry</td></tr></thead><tr><td class="first">Mother</td><td class="center"><input type="checkbox" value="1" name="m" /></td><td class="center"><input type="checkbox" value="2" name="m" /></td><td class="center"><input type="checkbox" value="3" name="m" /></td></tr><tr><td class="first">Father</td><td class="center"><input type="checkbox" value="1" name="f" /></td><td class="center"><input type="checkbox" value="2" name="f" /></td><td class="center"><input type="checkbox" value="3" name="f" /></td></tr><tr><td class="first">Brother</td><td class="center"><input type="checkbox" value="1" name="b" /></td><td class="center"><input type="checkbox" value="2" name="b" /></td><td class="center"><input type="checkbox" value="3" name="b" /></td></tr></table></div></div>', '<div class="code-example">[Mother]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry<br>===<br>[Father]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry<br>===<br>[Brother]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry</div>', 1),
(13, 'Matrix Choice (multiple answers per row with other option)', 'Use a matrix (grid) question if you want respondents to apply the same measurement when answering several related questions. You can set the matrix question to collect multiple answers per row or enter another option as a free text.', '<div class="question-type-example"><strong>Which flavor of ice cream is each member od your family''s favorite</strong><div><table cellspacing="0"><thead><tr><th>&nbsp;</th><td>Chocolate</td><td>Vanilla</td><td>Strawberry</td><td>Other</td></tr></thead><tr><td class="first">Mother</td><td class="center"><input type="checkbox" value="1" name="m" /></td><td class="center"><input type="checkbox" value="2" name="m" /></td><td class="center"><input type="checkbox" value="3" name="m" /></td><td><input type="text" class="small"></td></tr><tr><td class="first">Father</td><td class="center"><input type="checkbox" value="1" name="f" /></td><td class="center"><input type="checkbox" value="2" name="f" /></td><td class="center"><input type="checkbox" value="3" name="f" /></td><td><input type="text" class="small"></td></tr></table></div></div>', '<div class="code-example">[Mother]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry<br>#Other<br>===<br>[Father]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry<br>#Other<br></div>', 1),
(14, 'Matrix Choice (Date/Time)', 'Ask respondents to enter a specific date and/or time.', '<div class="question-type-example"><strong>When was the last time you ate the following ice cream flavors?</strong><div><table cellspacing="0"><thead><tr><th>&nbsp;</th><th colspan=5>The Time (DD/MM/YYY)</th></tr></thead><tr><td class="first">Chocolate</td><td><input id="_1_DD" name="_1_DD" type="text" class="xsmall" value="" maxlength="2" /></td><td>/</td><td><input id="_1_MM" name="_1_MM" type="text" class="xsmall" value="" maxlength="2" /></td><td>/</td><td><input id="_1_YY" name="_1_YY" type="text" class="small" value="" maxlength="4" /></td></tr><tr><td class="first">Vanilla</td><td><input id="_2_DD" name="_2_DD" type="text" class="xsmall" value="" maxlength="2" /></td><td>/</td><td><input id="_2_MM" name="_2_MM" type="text" class="xsmall" value="" maxlength="2" /></td><td>/</td><td><input id="_2_YY" name="_2_YY" type="text" class="small" value="" maxlength="4" /></td></tr></table></div></div>', '<div class="code-example">[Chocolate]<br>The Time (DD/MM/YYYY)<br>===<br>[Vanilla]<br>The Time (DD/MM/YYYY)</div>', 1),
(15, 'Rating Scale (Multiple/Matrix Choice)', 'Use a rating scale when you want to assign weights to respondents'' answers.', '<div class="question-type-example"><strong>How important is price when you buy ice cream?</strong><div><table cellspacing="0"><thead><tr><td width="20%">1. Important</td><td width="20%"></td><td width="20%">3. Moderately Important</td><td width="20%"></td><td width="20%">5. Not Important</td></tr></thead><tr><td class="center"><input type="radio" value="1" name="m" /></td><td class="center"><input type="radio" value="2" name="m" /></td><td class="center"><input type="radio" value="3" name="m" /></td><td class="center"><input type="radio" value="4" name="m" /></td><td class="center"><input type="radio" value="5" name="m" /></td></tr></table></div></div>', '<div class="code-example">1|Important<br>2|<br>3|Moderately Important<br>4|<br>5|Not Important</div>', 1),
(16, 'Ranking', 'Ask respondents to rank a list of options in the order they prefer using numeric dropdown menus.', '<div class="question-type-example"><strong>Rank the following ice cream flavors:</strong><div><div class="question-ranking-rank"><select id="_1"><option></option><option value="1">1</option><option value="2">2</option><option value="3">3</option></select><label for="_1">Chocolate</label></div><div class="question-ranking-rank"><select id="_2"><option></option><option value="1">1</option><option value="2">2</option><option value="3">3</option></select><label for="_2">Vanilla</label></div><div class="question-ranking-rank"><select id="_3"><option></option><option value="1">1</option><option value="2">2</option><option value="3">3</option></select><label for="_3">Strawberry</label></div></div></div>', '<div class="code-example">[Chocolate]<br>1<br>2<br>3<br>[Vanilla]<br>1<br>2<br>3<br>[Strawberry]<br>1<br>2<br>3</div>', 1),
(17, 'Text/HTML Code', 'Include written text or HTML code as a separate page.', '<div class="question-type-example"><img src="assets/modules/surveys/images/icon.png" style="float:left;"><b>Lorem</b> <u>ipsum</u> dolor sit amet, consectetur adipiscing elit. Nam ligula justo, tristique a tellus ac, sollicitudin accumsan diam. Quisque tempor ultricies lacus nec ultrices. Mauris commodo scelerisque nibh ac eleifend. Nullam scelerisque tellus eget nisi blandit, vitae lacinia risus adipiscing.</div>', '<div class="code-example">&lt;img src="assets/modules/surveys/images/icon.png"&gt; &lt;b&gt;Lorem&lt;/b&gt; &lt;u&gt;ipsum&lt;/u&gt; dolor sit amet, consectetur adipiscing elit. Nam ligula justo, tristique a tellus ac, sollicitudin accumsan diam. Quisque tempor ultricies lacus nec ultrices. Mauris commodo scelerisque nibh ac eleifend. Nullam scelerisque tellus eget nisi blandit, vitae lacinia risus adipiscing.</div>', 1),
(18, 'Action Script', 'Specify action script to playing on separate page by specifying the URL of the file.', '', '', 0);


DROP TABLE IF EXISTS `<DB_PREFIX>surveys_entity_answers`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>surveys_entity_answers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `participant_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `survey_id` int(11) NOT NULL DEFAULT '0',
  `questionnaire_id` int(11) NOT NULL DEFAULT '0',
  `questionnaire_item_id` int(11) NOT NULL DEFAULT '0',
  `questionnaire_item_variant_id` int(11) NOT NULL DEFAULT '0',
  `questionnaire_item_other` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `answer_text` longtext COLLATE utf8_unicode_ci,
  `actions_data` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_record` (`participant_id`,`survey_id`,`questionnaire_id`,`questionnaire_item_id`,`questionnaire_item_variant_id`),
  KEY `participant_id` (`participant_id`),
  KEY `survey_id` (`survey_id`),
  KEY `questionnaire_item_id` (`questionnaire_item_id`),
  KEY `questionnaire_item_variant_id` (`questionnaire_item_variant_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=28 ;

INSERT INTO `<DB_PREFIX>surveys_entity_answers` (`id`, `participant_id`, `survey_id`, `questionnaire_id`, `questionnaire_item_id`, `questionnaire_item_variant_id`, `questionnaire_item_other`, `answer_text`, `actions_data`) VALUES
(1, '1', 1, 1, 1, 2, '', '', ''),
(2, '1', 1, 1, 2, 4, '', '', ''),
(3, '1', 1, 1, 3, 8, '', '', NULL),
(4, '1', 1, 1, 4, 13, '', '', NULL),
(5, '1', 1, 1, 5, 14, '', '', NULL),
(6, '1', 1, 1, 6, 18, '', '', ''),
(7, '1', 1, 1, 7, 0, '', 'test', ''),
(8, '1', 1, 1, 9, 0, '', 'test', ''),
(9, '1', 1, 1, 10, 0, '', '12-12-2017', ''),
(10, '1', 1, 1, 11, 26, '', '', NULL),
(11, '1', 1, 1, 11, 29, '', '', NULL),
(12, '1', 1, 1, 12, 33, '', '', NULL),
(13, '1', 1, 1, 12, 37, '', '', NULL),
(14, '1', 1, 1, 13, 41, '', '', NULL),
(15, '1', 1, 1, 13, 44, '', '', NULL),
(16, '1', 1, 1, 14, 46, '', '', NULL),
(17, '1', 1, 1, 14, 49, '', '', NULL),
(18, '1', 1, 1, 15, 52, '', '', NULL),
(19, '1', 1, 1, 15, 56, '', '', NULL),
(20, '1', 1, 1, 16, -1, '', '12-12-1977', NULL),
(21, '1', 1, 1, 16, -2, '', '12-12-1978', NULL),
(23, '1', 1, 1, 17, 62, '', '', NULL),
(24, '1', 1, 1, 18, 67, '', '', NULL),
(25, '1', 1, 1, 18, 70, '', '', NULL),
(26, '1', 1, 1, 18, 75, '', '', NULL),
(27, '1', 1, 1, 19, 0, '', '', '');


DROP TABLE IF EXISTS `<DB_PREFIX>surveys_entity_questionnaire_items`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>surveys_entity_questionnaire_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_questionnaire_id` int(11) NOT NULL DEFAULT '0',
  `question_text` text COLLATE utf8_unicode_ci,
  `question_text_f` text COLLATE utf8_unicode_ci,
  `help_text` mediumtext COLLATE utf8_unicode_ci,
  `question_type_id` tinyint(1) NOT NULL DEFAULT '0',
  `date_format` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `file_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `validation_type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `alignment_type` char(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'v - vertical, h - horozontal',
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `entity_questionnaire_id` (`entity_questionnaire_id`),
  KEY `question_type_id` (`question_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20;

INSERT INTO `<DB_PREFIX>surveys_entity_questionnaire_items` (`id`, `entity_questionnaire_id`, `question_text`, `question_text_f`, `help_text`, `question_type_id`, `date_format`, `file_path`, `validation_type`, `content`, `alignment_type`, `is_required`, `sort_order`, `is_active`) VALUES
(1, 1, 'Multiple Choice (only one answer)', 'Multiple Choice (only one answer)', 'Multiple Choice (only one answer)', 1, '', '', '', '', '', 1, 1, 1),
(2, 1, 'Multiple Choice (only one answer with other option)', 'Multiple Choice (only one answer with other option)', 'Multiple Choice (only one answer with other option)', 2, '', '', '', '', '', 1, 2, 1),
(3, 1, 'Multiple Choice (multiple answers)', 'Multiple Choice (multiple answers)', '', 3, '', '', '', '', '', 1, 3, 1),
(4, 1, 'Multiple Choice (multiple answers) with other option', 'Multiple Choice (multiple answers) with other option', '', 4, '', '', '', '', '', 1, 4, 1),
(5, 1, 'Multiple Choice (multiple answers) with other option', 'Multiple Choice (multiple answers) with other option', '', 4, '', '', '', '', '', 1, 5, 1),
(6, 1, 'Dropdown (only one answer)', '', '', 5, '', '', '', '', '', 1, 6, 1),
(7, 1, 'Single Textbox', '', '', 6, '', '', 'text', '', '', 1, 7, 1),
(8, 1, 'Multiple Textboxes', '', '', 7, '', '', 'text', '', '', 0, 8, 1),
(9, 1, 'Comment/Essay Box', '', '', 8, '', '', '', '', '', 1, 9, 1),
(10, 1, 'Date/Time', '', '', 9, 'DD/MM/YYYY', '', '', '', '', 1, 10, 1),
(11, 1, 'Matrix Choice (only one answer per row)', '', '', 10, '', '', '', '', '', 1, 11, 1),
(12, 1, 'Matrix Choice (only one answer per row with other option) with other', '', '', 11, '', '', '', '', '', 1, 12, 1),
(13, 1, 'Matrix Choice (only one answer per row with other option) w/o other', '', '', 11, '', '', '', '', '', 1, 13, 1),
(14, 1, 'Matrix Choice (multiple answers per row)', '', '', 12, '', '', '', '', '', 1, 14, 1),
(15, 1, 'Matrix Choice (multiple answers per row with other option)', '', '', 13, '', '', '', '', '', 1, 15, 1),
(16, 1, 'Matrix Choice (Date/Time)', '', '', 14, 'DD/MM/YYYY', '', '', '', '', 1, 16, 1),
(17, 1, 'Rating Scale (Multiple/Matrix Choice)', '', '', 15, '', '', '', '', '', 1, 17, 1),
(18, 1, 'Ranking', '', '', 16, '', '', '', '', '', 1, 18, 1),
(19, 1, 'Text/HTML Code', '', '', 17, '', '', '', 'This is a simple Text/HTML Code: Lorem <b>ipsum</b> dolor <i>sit amet</i>, consectetur adipiscing elit. Morbi aliquet metus at mi bibendum sagittis. Mauris pulvinar vitae elit varius ultrices.', '', 0, 19, 1);


DROP TABLE IF EXISTS `<DB_PREFIX>surveys_entity_questionnaire_item_variants`;
CREATE TABLE IF NOT EXISTS `<DB_PREFIX>surveys_entity_questionnaire_item_variants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_questionnaire_item_id` int(11) unsigned NOT NULL DEFAULT '0',
  `question_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `row_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `content_value` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `votes` smallint(6) unsigned NOT NULL DEFAULT '0',
  `sort_order` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `entity_questionnaire_item_id` (`entity_questionnaire_item_id`),
  KEY `question_type_id` (`question_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=76 ;

INSERT INTO `<DB_PREFIX>surveys_entity_questionnaire_item_variants` (`id`, `entity_questionnaire_item_id`, `question_type_id`, `row_title`, `content`, `content_value`, `votes`, `sort_order`) VALUES
(1, 1, 1, '', 'Chocolate', '', 0, 1),
(2, 1, 1, '', 'Vanilla', '', 1, 2),
(3, 1, 1, '', 'Strawberry', '', 0, 3),
(4, 2, 2, '', 'Chocolate', '', 1, 1),
(5, 2, 2, '', 'Vanilla', '', 0, 2),
(6, 2, 2, '', 'Strawberry', '', 0, 3),
(7, 2, 2, '', '#Other, please specify', '', 0, 4),
(8, 3, 3, '', 'Chocolate', '', 1, 1),
(9, 3, 3, '', 'Vanilla', '', 0, 2),
(10, 3, 3, '', 'Strawberry', '', 0, 3),
(11, 4, 4, '', 'Chocolate', '', 0, 1),
(12, 4, 4, '', 'Vanilla', '', 0, 2),
(13, 4, 4, '', 'Strawberry', '', 1, 3),
(14, 5, 4, '', 'Chocolate', '', 1, 1),
(15, 5, 4, '', 'Vanilla', '', 0, 2),
(16, 5, 4, '', 'Strawberry', '', 0, 3),
(17, 5, 4, '', '#Other', '', 0, 4),
(18, 6, 5, '', 'Chocolate', '', 1, 1),
(19, 6, 5, '', 'Vanilla', '', 0, 2),
(20, 6, 5, '', 'Strawberry', '', 0, 3),
(21, 6, 5, '', 'Chocolate', '', 0, 3),
(22, 6, 5, '', 'Ice-Cream', '', 0, 3),
(23, 8, 7, '', 'Your favorite', '', 0, 1),
(24, 8, 7, '', 'Second favorite', '', 0, 2),
(25, 8, 7, '', 'Third favorite', '', 0, 3),
(26, 11, 10, 'Father', 'Chocolate', '1', 1, 1),
(27, 11, 10, 'Father', 'Vanilla', '2', 0, 2),
(28, 11, 10, 'Father', 'Strawberry', '3', 0, 3),
(29, 11, 10, 'Brother', 'Chocolate', '1', 1, 1),
(30, 11, 10, 'Brother', 'Vanilla', '2', 0, 2),
(31, 11, 10, 'Brother', 'Strawberry', '3', 0, 3),
(32, 12, 11, 'Mother', 'Chocolate', '1', 0, 1),
(33, 12, 11, 'Mother', 'Vanilla', '2', 1, 2),
(34, 12, 11, 'Mother', 'Strawberry', '3', 0, 3),
(35, 12, 11, 'Mother', '#Other', '', 0, 4),
(36, 12, 11, 'Father', 'Chocolate', '1', 0, 1),
(37, 12, 11, 'Father', 'Vanilla', '2', 1, 2),
(38, 12, 11, 'Father', 'Strawberry', '3', 0, 3),
(39, 12, 11, 'Father', '#Other', '', 0, 4),
(40, 13, 11, 'Mother', 'Chocolate', '1', 0, 1),
(41, 13, 11, 'Mother', 'Vanilla', '2', 1, 2),
(42, 13, 11, 'Mother', 'Strawberry', '3', 0, 3),
(43, 13, 11, 'Father', 'Chocolate', '1', 0, 1),
(44, 13, 11, 'Father', 'Vanilla', '2', 1, 2),
(45, 13, 11, 'Father', 'Strawberry', '3', 0, 3),
(46, 14, 12, 'Father', 'Chocolate', '1', 1, 1),
(47, 14, 12, 'Father', 'Vanilla', '2', 0, 2),
(48, 14, 12, 'Father', 'Strawberry', '3', 0, 3),
(49, 14, 12, 'Mother', 'Chocolate', '1', 1, 1),
(50, 14, 12, 'Mother', 'Vanilla', '2', 0, 2),
(51, 14, 12, 'Mother', 'Strawberry', '3', 0, 3),
(52, 15, 13, 'Mother', 'Chocolate', '1', 1, 1),
(53, 15, 13, 'Mother', 'Vanilla', '2', 0, 2),
(54, 15, 13, 'Mother', 'Strawberry', '3', 0, 3),
(55, 15, 13, 'Mother', '#Other', '', 0, 4),
(56, 15, 13, 'Father', 'Chocolate', '1', 1, 1),
(57, 15, 13, 'Father', 'Vanilla', '2', 0, 2),
(58, 15, 13, 'Father', 'Strawberry', '3', 0, 3),
(59, 15, 13, 'Father', '#Other', '', 0, 4),
(60, 16, 14, 'Chocolate', 'The Time (DD/MM/YYYY)', '', 0, 1),
(61, 16, 14, 'Vanilla', 'The Time (DD/MM/YYYY)', '', 0, 1),
(62, 17, 15, '', 'Important', '1', 1, 1),
(63, 17, 15, '', '', '2', 0, 2),
(64, 17, 15, '', 'Moderately Important', '3', 0, 3),
(65, 17, 15, '', '', '4', 0, 4),
(66, 17, 15, '', 'Not Important', '5', 0, 5),
(67, 18, 16, 'Chocolate', '1', '', 1, 1),
(68, 18, 16, 'Chocolate', '2', '', 0, 2),
(69, 18, 16, 'Chocolate', '3', '', 0, 3),
(70, 18, 16, 'Vanilla', '1', '', 1, 4),
(71, 18, 16, 'Vanilla', '2', '', 0, 5),
(72, 18, 16, 'Vanilla', '3', '', 0, 6),
(73, 18, 16, 'Strawberry', '1', '', 0, 7),
(74, 18, 16, 'Strawberry', '2', '', 0, 8),
(75, 18, 16, 'Strawberry', '3', '', 1, 9);
