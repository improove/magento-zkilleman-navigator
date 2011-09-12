<?php

abstract class Zkilleman_Navigator_Model_Node_Visibility_Select
    extends Zkilleman_Navigator_Model_Node_Visibility_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_renderer = 'navigator/form/visibility/select.phtml';
    }
    
    public abstract function getConditionText();
    public abstract function getSelectOptions();
    
    public function getSelectedValueLabel()
    {
        $opts = $this->getSelectOptions();
        return isset($opts[$this->getValue()]) ? 
                $opts[$this->getValue()] : '...';
    }
}