<?php

class Improove_Navigator_Block_Adminhtml_Tree_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup  = 'navigator';
        $this->_controller  = 'adminhtml_tree';
        $this->_mode        = 'edit';

        parent::__construct();
        $this->setTemplate('navigator/tree/edit.phtml');
    }
}