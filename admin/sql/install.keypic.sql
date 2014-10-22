ALTER TABLE `#__users` ADD `spam_per` INT(10);
ALTER TABLE `#__users` ADD `spam_token` VARCHAR(100);
DROP TABLE IF EXISTS `#__keypic_tokens`;
CREATE TABLE `#__keypic_tokens`(`token` VARCHAR(100), `gen_on` TIMESTAMP);