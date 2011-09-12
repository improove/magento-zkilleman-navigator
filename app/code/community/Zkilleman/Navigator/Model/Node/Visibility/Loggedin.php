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
 * Zkilleman_Navigator_Model_Node_Visibility_Loggedin
 *
 * @category   Zkilleman
 * @package    Zkilleman_Navigator
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
class Zkilleman_Navigator_Model_Node_Visibility_Loggedin
    extends Zkilleman_Navigator_Model_Node_Visibility_Select
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