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
 * Zkilleman_Navigator_Controller_Adminhtml_Action
 *
 * @category   Zkilleman
 * @package    Zkilleman_Navigator
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
class Zkilleman_Navigator_Controller_Adminhtml_Action extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initializes and returns the full tree structure
     *
     * @return Zkilleman_Navigator_Model_Tree
     */
    protected function _initTree()
    {
        $tree = Mage::getModel('navigator/tree')->load();
        return $tree;
    }

    /**
     * Registers and returns a Node object based on current parameters
     *
     * @return Zkilleman_Navigator_Model_Node
     */
    protected function _initNode()
    {
        $nodeId = (int) $this->getRequest()->getParam('id', false);
        $parentId = (int) $this->getRequest()->getParam('parent', false);
        $storeId = (int) $this->getRequest()->getParam('store');
        $node = Mage::getModel('navigator/node');
        $node->setStoreId($storeId);
        $node->load($nodeId);
        Mage::register('current_navigator_node', $node);
        Mage::register('current_navigator_node_parent_id', $parentId);
        return $node;
    }
}