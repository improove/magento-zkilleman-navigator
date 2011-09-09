<?php

class Improove_Navigator_Model_Node_Content_Type_Cartlink
        extends Improove_Navigator_Model_Node_Content_Type_Link
{
    public function getAnchorText()
    {
        $count = Mage::helper('checkout/cart')->getSummaryCount();
        $text = '';
        if( $count == 1 ) {
            $text = Mage::helper('navigator')->__('My Cart (%s item)', $count);
        } elseif( $count > 0 ) {
            $text = Mage::helper('navigator')->__('My Cart (%s items)', $count);
        } else {
            $text = Mage::helper('navigator')->__('My Cart');
        }
        return $text;
    }

    public function getHref()
    {
        return Mage::getModel('core/url')->getUrl('checkout/cart');
    }

    public function populateFieldset(Varien_Data_Form_Element_Fieldset &$fieldset)
    {
        parent::populateFieldset($fieldset);
        $fieldset->removeField('href');
    }

    public static function getCacheKeyInfo()
    {
        return 'cart_count_'.Mage::helper('checkout/cart')->getSummaryCount();
    }
}