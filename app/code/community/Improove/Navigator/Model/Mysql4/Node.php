<?php

class Improove_Navigator_Model_Mysql4_Node extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('navigator/node', 'node_id');
    }
}