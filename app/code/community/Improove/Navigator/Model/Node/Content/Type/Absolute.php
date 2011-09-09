<?php

class Improove_Navigator_Model_Node_Content_Type_Absolute 
        extends Improove_Navigator_Model_Node_Content_Type_Link
{
    public function isInCurrentPath()
    {
        //TODO: check for trailing slashes and stuff
        return $this->getHref() == Mage::helper('core/url')->getCurrentUrl();
    }
}