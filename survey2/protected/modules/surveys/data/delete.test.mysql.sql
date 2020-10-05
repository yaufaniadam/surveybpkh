UPDATE `<DB_PREFIX>site_info` SET `header` = 'PHP Directy CMF', `slogan` = 'Welcome to PHP Directy CMF!', `footer` = 'PHP Directy CMF © <a class="footer_link" target="_new" href="http://www.apphp.com/php-directy-cmf/index.php">ApPHP</a>', `meta_title` = 'PHP Directy CMF', `meta_description` = 'Directy CMF', `meta_keywords` = 'php cmf, php framework, php content management framework, php cms';

DELETE FROM `<DB_PREFIX>surveys_entities` WHERE `id` = 1;
DELETE FROM `<DB_PREFIX>surveys_participants` WHERE `id` < 5;
DELETE FROM `<DB_PREFIX>surveys_entity_participants` WHERE `id` < 4;
DELETE FROM `<DB_PREFIX>surveys_entity_questionnaires` WHERE `id` = 1;
DELETE FROM `<DB_PREFIX>surveys_entity_questionnaire_items` WHERE `id` < 20;
DELETE FROM `<DB_PREFIX>surveys_entity_questionnaire_item_variants` WHERE `id` < 76;
DELETE FROM `<DB_PREFIX>surveys_entity_answers` WHERE `id` < 28;

UPDATE `<DB_PREFIX>modules` SET `has_test_data` = 0 WHERE `code` = 'surveys';