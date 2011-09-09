<?php

class Improove_Navigator_Model_Node_Content_Type_Product
        extends Improove_Navigator_Model_Node_Content_Type_Link
{
    protected $_product;

    public function setNode(Improove_Navigator_Model_Node &$node)
    {
        parent::setNode($node);
        $this->_product = null;
        $this->setAnchorText($this->getProduct()->getName());
        $this->setAnchorTitle($this->getProduct()->getName());
    }

    public function isInCurrentPath()
    {
        if ($product = Mage::registry('current_product')
                && Mage::registry('current_product')->getId() == $this->getProductId()) {
            return true;
        }
        return false;
    }

    public function getProductId()
    {
        return Mage::getModel('catalog/product')
            ->getIdBySku(trim($this->_getContentDataItem('sku')));
    }

    public function getProduct()
    {
        if ($this->_product === null) {
            $this->_product =
                    Mage::getModel('catalog/product')
                        ->load($this->getProductId(), array('name'))
                        ->setDoNotUseCategoryId(true);
        }
        return $this->_product;
    }

    public function getHref()
    {
        return $this->getProduct()->getProductUrl();
    }

    public function populateFieldset(Varien_Data_Form_Element_Fieldset &$fieldset)
    {
        $sku = $fieldset->addField('sku', 'text', array(
            'label' => Mage::helper('navigator')->__('Sku'),
            'name'  => 'content_fields[sku]',
            'value' => (string) $this->_getContentDataItem('sku'),
            'note'  => Mage::helper('navigator')
        ));

        parent::populateFieldset($fieldset);
        $fieldset->removeField('href');
    }

    public static function getCacheKeyInfo()
    {
        if ($lastId = Mage::getSingleton('catalog/session')
                ->getLastVisitedCategoryId()) {
           return 'last_category_id_'.$lastId;
        }
        return null;
    }
}