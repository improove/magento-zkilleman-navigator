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
 * Zkilleman_Navigator_Model_Node_Visibility_Abstract
 *
 * @category   Zkilleman
 * @package    Zkilleman_Navigator
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
abstract class Zkilleman_Navigator_Model_Node_Visibility_Abstract extends Varien_Object
{
    const VISIBILITY_CONDITION_XML_PATH = 'global/navigator/visibility/condition';

    protected static $_types;
    protected $_editBlock;
    protected $_renderer;

    protected function _construct()
    {
        $this->_editBlock = null;
        $this->_renderer = 'navigator/form/visibility/default.phtml';
    }

    public abstract function assert();

    public static function getModelByCode($code, $data = array())
    {
        $types = self::getTypes();
        if(!isset($types[$code]) && !isset($types[$code]['model'])) {
            return false;
        }
        return Mage::getModel($types[$code]['model'], $data)->setCode($code);
    }

    public static function getTypes()
    {
        if(self::$_types !== null)
            return self::$_types;

        self::$_types = array();
        $types = Mage::getConfig()->getNode(self::VISIBILITY_CONDITION_XML_PATH)->asArray();
        foreach($types as $code => $type) {
            $module = 'navigator';
            if(isset($type['@']['module'])) {
                $module = $type['@']['module'];
            }
            $label = Mage::helper($module)->__($type['label']);
            self::$_types[$code] = array(
                'module' => $module,
                'model' => $type['model'],
                'label' => $label);
        }
        return self::$_types;
    }

    public function getEditBlock()
    {
        if($this->_editBlock == null) {
            $this->_editBlock =
                    Mage::app()
                        ->getLayout()
                        ->createBlock('adminhtml/template')
                        ->setTemplate($this->_renderer)
                        ->setVisibilityModel($this);
        }
        return $this->_editBlock;
    }
    
    public static function getCacheKeyInfo()
    {
        return null;
    }
}