<?php

class Improove_Navigator_Adminhtml_IndexController extends Improove_Navigator_Controller_Adminhtml_Action
{
    public function indexAction()
    {
        $this->_initNode();
        $this->loadLayout();
        $this->renderLayout();
    }
}