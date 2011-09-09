<?php

class Improove_Navigator_Model_Node_Visibility_Loggedin
    extends Improove_Navigator_Model_Node_Visibility_Select
{
    const SELECT_OPTION_IS = 'is';
    const SELECT_OPTION_ISNOT = 'isnot';

    protected function _construct()
    {
        parent::_construct();
        if(!$this->hasData('value'))
            $this->setValue(self::SELECT_OPTION_IS);
    }

    public function assert()
    {
        $isLoggedIn = (bool)Mage::getSingleton('customer/session')->isLoggedIn();
        if($this->getValue() == self::SELECT_OPTION_ISNOT)
            $isLoggedIn = !$isLoggedIn;
        return $isLoggedIn;
    }

    /**
     * Use %s where the dropdown should be inserted
     *
     * @return string
     */
    public function getConditionText()
    {
        return Mage::helper('navigator')->__(
            'If customer %s logged in'
        );
    }

    public function getSelectOptions()
    {
        return array(
            self::SELECT_OPTION_IS => Mage::helper('navigator')->__('is'),
            self::SELECT_OPTION_ISNOT => Mage::helper('navigator')->__('is not')
        );
    }

    public static function getCacheKeyInfo()
    {
        return 'is_logged_in_'.(int)Mage::getSingleton('customer/session')->isLoggedIn();
    }
}