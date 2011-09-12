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
 * Zkilleman_Navigator_Model_Node_Content_Type_Cartlink
 *
 * @category   Zkilleman
 * @package    Zkilleman_Navigator
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
class Zkilleman_Navigator_Model_Node_Content_Type_Cartlink
        extends Zkilleman_Navigator_Model_Node_Content_Type_Link
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