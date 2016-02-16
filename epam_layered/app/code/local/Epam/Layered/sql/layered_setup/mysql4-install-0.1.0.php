<?php
$this->startSetup();

$this->run("
CREATE TABLE `{$this->getTable('layered/options')}` (
  `appear_id` mediumint(8) unsigned NOT NULL auto_increment,
  `attr_id` mediumint(8) unsigned NOT NULL,
  `option_id` TINYINT(1) unsigned NOT NULL,
  PRIMARY KEY  (`appear_id`),
  KEY `attr_id` (`attr_id`),
  KEY `option_id` (`option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Epam Layered attr_id|option_id Table';

");

$this->endSetup();
