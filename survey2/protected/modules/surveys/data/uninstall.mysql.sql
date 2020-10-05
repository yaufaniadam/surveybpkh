
DELETE FROM `<DB_PREFIX>modules` WHERE `code` = 'surveys';
DELETE FROM `<DB_PREFIX>module_settings` WHERE `module_code` = 'surveys';

DELETE FROM `<DB_PREFIX>role_privileges` WHERE `privilege_id` IN (SELECT id FROM `<DB_PREFIX>privileges` WHERE `category` = 'surveys');
DELETE FROM `<DB_PREFIX>privileges` WHERE `category` = 'surveys';
DELETE FROM `<DB_PREFIX>role_privileges` WHERE `privilege_id` IN (SELECT id FROM `<DB_PREFIX>privileges` WHERE `category` = 'participants');
DELETE FROM `<DB_PREFIX>privileges` WHERE `category` = 'participants';
DELETE FROM `<DB_PREFIX>role_privileges` WHERE `privilege_id` IN (SELECT id FROM `<DB_PREFIX>privileges` WHERE `category` = 'survey_questionnaires');
DELETE FROM `<DB_PREFIX>privileges` WHERE `category` = 'survey_questionnaires';
DELETE FROM `<DB_PREFIX>role_privileges` WHERE `privilege_id` IN (SELECT id FROM `<DB_PREFIX>privileges` WHERE `category` = 'survey_participants');
DELETE FROM `<DB_PREFIX>privileges` WHERE `category` = 'survey_participants';
DELETE FROM `<DB_PREFIX>role_privileges` WHERE `privilege_id` IN (SELECT id FROM `<DB_PREFIX>privileges` WHERE `category` = 'survey_questions');
DELETE FROM `<DB_PREFIX>privileges` WHERE `category` = 'survey_questions';

DELETE FROM `<DB_PREFIX>email_template_translations` WHERE `template_code` IN (SELECT code FROM `<DB_PREFIX>email_templates` WHERE `module_code` = 'surveys');
DELETE FROM `<DB_PREFIX>email_templates` WHERE `module_code` = 'surveys';

DROP TABLE IF EXISTS `<DB_PREFIX>surveys_entities`;
DROP TABLE IF EXISTS `<DB_PREFIX>surveys_participants`;
DROP TABLE IF EXISTS `<DB_PREFIX>surveys_entity_participants`;
DROP TABLE IF EXISTS `<DB_PREFIX>surveys_entity_questionnaires`;
DROP TABLE IF EXISTS `<DB_PREFIX>surveys_entity_questionnaire_items`;
DROP TABLE IF EXISTS `<DB_PREFIX>surveys_entity_questionnaire_item_variants`;
DROP TABLE IF EXISTS `<DB_PREFIX>surveys_entity_answers`;
DROP TABLE IF EXISTS `<DB_PREFIX>surveys_question_types`;

DELETE FROM `<DB_PREFIX>backend_menu_translations` WHERE `menu_id` IN (SELECT id FROM `<DB_PREFIX>backend_menus` WHERE `module_code` = 'surveys');
DELETE FROM `<DB_PREFIX>backend_menus` WHERE `module_code` = 'surveys';
