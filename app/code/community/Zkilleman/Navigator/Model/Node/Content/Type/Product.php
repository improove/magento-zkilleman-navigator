<?php
/**
 * Zkilleman_Navigator
 *
 * Copyright (C) 2011 Henrik Hedelund (henke.hedelund@gmail.com)
 *
 * This file is part of Zkilleman_Navigator.
 *
 * Zkilleman_Navigator is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Zkilleman_Navigator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Zkilleman_Navigator.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP Version 5.1
 *
 * @category  Zkilleman
 * @package   Zkilleman_Navigator
 * @author    Henrik Hedelund <henke.hedelund@gmail.com>
 * @copyright 2011 Henrik Hedelund (henke.hedelund@gmail.com)
 * @license   http://www.gnu.org/licenses/lgpl.html GNU LGPL
 * @link      https://github.com/henkelund/magento-zkilleman-navigator
 */

/**
 * Zkilleman_Navigator_Model_Node_Content_Type_Product
 *
 * @category   Zkilleman
 * @package    Zkilleman_Navigator
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
class Zkilleman_Navigator_Model_Node_Content_Type_Product
        extends Zkilleman_Navigator_Model_Node_Content_Type_Link
{
    protected $_product;

    public function setNode(Zkilleman_Navigator_Model_Node &$node)
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