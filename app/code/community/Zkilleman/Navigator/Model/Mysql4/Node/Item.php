<?php

class Zkilleman_Navigator_Model_Mysql4_Node_Item extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('navigator/node_item', 'node_item_id');
    }
}