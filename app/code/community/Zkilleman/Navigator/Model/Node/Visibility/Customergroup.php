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
 * Zkilleman_Navigator_Model_Node_Visibility_Customergroup
 *
 * @category   Zkilleman
 * @package    Zkilleman_Navigator
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
class Zkilleman_Navigator_Model_Node_Visibility_Customergroup
    extends Zkilleman_Navigator_Model_Node_Visibility_Select
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