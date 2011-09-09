<?php

class Improove_Navigator_Model_Node_Visibility_Customergroup
    extends Improove_Navigator_Model_Node_Visibility_Select
{

    public function assert()
    {
        return ((int) $this->getValue()) ==
            ((int) Mage::getSingleton('customer/session')
                ->getCustomerGroupId());
    }

    /**
     * Use %s where the dropdown should be inserted
     *
     * @return string
     */
    public function getConditionText()
    {
        return Mage::helper('navigator')->__(
            'If customer belongs to %s group'
        );
    }

    public function getSelectOptions()
    {
        $groups = array();
        foreach(Mage::getModel('customer/customer_attribute_source_group')
                ->getAllOptions() as $group) {
            $groups[$group['value']] = $group['label'];
        }
        return $groups;
    }

    public static function getCacheKeyInfo()
    {
        $id = Mage::getSingleton('customer/session')->getCustomerGroupId();
        if ($id == Mage_Customer_Model_Group::NOT_LOGGED_IN_ID) {
            return null;
        }
        return 'customer_id_' . $id;
    }
}