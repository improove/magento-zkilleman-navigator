<?php

$installer = $this;
$installer->startSetup();
$installer->addEntityType(
        'navigator_node',
        array(
            'entity_model' =>'navigator/node',
            'attribute_model' => 'navigator/resource_eav_attribute',
            'table' => 'navigator/node',
            //blank for now, but can also be eav/entity_increment_numeric
            'increment_model' => '',
            //appears that this needs to be/can be above "1" if we're using eav/entity_increment_numeric
            'increment_per_store' => '0'
));
$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('navigator_node')}`;
CREATE TABLE `{$this->getTable('navigator_node')}` (
`entity_id` int(10) unsigned NOT NULL auto_increment,
`entity_type_id` smallint(8) unsigned NOT NULL default '0',
`attribute_set_id` smallint(5) unsigned NOT NULL default '0',
`increment_id` varchar(50) NOT NULL default '',
`parent_id` int(10) unsigned NULL default '0',
`created_at` datetime NOT NULL default '0000-00-00 00:00:00',
`updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
`is_active` tinyint(1) unsigned NOT NULL default '1',
`code` varchar(255) NOT NULL default '',
`lft` int(10) unsigned NOT NULL default '0',
`rgt` int(10) unsigned NOT NULL default '0',
PRIMARY KEY  (`entity_id`),
KEY `navigator_node_code` (`code`),
CONSTRAINT `FK_{$this->getTable('navigator_node')}_type` FOREIGN KEY (`entity_type_id`) REFERENCES `eav_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);
$installer->createEntityTables(
    $this->getTable('navigator/node'),
    array('no-main' => true)
);
$installer->installEntities();

Mage::getModel('navigator/node')
    ->setId(1)
    ->setParentId(0)
    ->setCode('root')
    ->setLft(0)
    ->setRgt(1)
    ->save();

$installer->endSetup();