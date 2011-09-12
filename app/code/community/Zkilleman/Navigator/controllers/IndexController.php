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
 * Zkilleman_Navigator_IndexController
 *
 * @category   Zkilleman
 * @package    Zkilleman_Navigator
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
class Zkilleman_Navigator_IndexController extends Mage_Core_Controller_Front_Action
{
    const VISIBILITY_TYPES_XML_PATH = 'global/navigator/visibility/type';

    public function indexAction()
    {
        /*$result = array();
        $visibilities = Mage::getConfig()->getNode(self::VISIBILITY_TYPES_XML_PATH)->asArray();
        foreach($visibilities as $code => $visibility) {
            $module = 'navigator';
            if(isset($visibility['@']['module'])) {
                $module = $visibility['@']['module'];
            }
            $label = Mage::helper($module)->__($visibility['label']);
            $result[$code] = array('module' => $module, 'label' => $label);
        }
        var_dump($result);*/
        //$node = Mage::getModel('navigator/node')->load(3);
        //$node->setContentData('checkout/cart/index')->save();
        //echo $node->getLink();
//        $item = Mage::getModel('navigator/node_item')->load(1);
//        $item->setData('data', 'http://www.google.se');
//        $item->save();
        //$node->save();
        /*$tree = Mage::getModel('navigator/tree')->load();
        $node = Mage::getModel('navigator/node');
        $node->setCode('reptiles');
        $node->setTitle('Reptiles');
        $node->save();*/
    }
}
