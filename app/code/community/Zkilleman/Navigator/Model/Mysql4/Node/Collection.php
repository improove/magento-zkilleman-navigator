<?php

class Zkilleman_Navigator_Model_Mysql4_Node_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('navigator/node');
    }
}