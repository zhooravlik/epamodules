<?php

$installer = $this;
$installer->startSetup();
$installer->run(
    "/* Table structure for table `ebrands` */

DROP TABLE IF EXISTS {$this->getTable('ebrands/brand')};
CREATE TABLE {$this->getTable('ebrands/brand')} (
    `brand_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Brand Id',
    `brand` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Brand Option Id',
    `status` smallint(6) NOT NULL COMMENT 'Status',
    `is_display_home` smallint(6) NOT NULL COMMENT 'Is Display Home',
    `identifier` varchar(255) DEFAULT NULL COMMENT 'Identifier',
    `brand_logo` varchar(255) DEFAULT NULL COMMENT 'Brand Logo',
    `brand_banner` varchar(255) DEFAULT NULL COMMENT 'Brand Banner',
    `description` text NOT NULL COMMENT 'Description',
    `sort_order` smallint(6) DEFAULT NULL COMMENT 'Sort Order',
    `meta_keywords` text COMMENT 'Meta Keywords',
    `meta_description` text COMMENT 'Meta Description',
    `creation_time` datetime DEFAULT NULL COMMENT 'Creation Time',
    `update_time` datetime DEFAULT NULL COMMENT 'Update Time',
    PRIMARY KEY (`brand_id`),
    KEY `IDX_BRANDS_OPTION_ID` (`brand`),
    CONSTRAINT `FK_BRANDS_OPT_ID_EAV_ATTR_OPT_OPT_ID` FOREIGN KEY (`brand`)
    REFERENCES {$this->getTable('eav_attribute_option')} (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='epam Brands Table';

/*Table structure for table `ebrands_store` */

DROP TABLE IF EXISTS {$this->getTable('ebrands/store')};
CREATE TABLE {$this->getTable('ebrands/store')} (
    `brand_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Brand Id',
    `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store Id',
    PRIMARY KEY (`brand_id`,`store_id`),
    KEY `IDX_BRANDS_STORE_BRAND_ID` (`brand_id`),
    KEY `IDX_BRANDS_STORE_STORE_ID` (`store_id`),
    CONSTRAINT `FK_BRANDS_STORE_BRAND_ID_BRANDS_BRAND_ID` FOREIGN KEY (`brand_id`)
    REFERENCES {$this->getTable('ebrands/brand')} (`brand_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_BRANDS_STORE_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`)
    REFERENCES {$this->getTable('core_store')} (`store_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='epam Brands Store Table';"
);

$installer->endSetup();
