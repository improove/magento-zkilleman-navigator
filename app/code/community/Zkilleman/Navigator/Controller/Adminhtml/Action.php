<?php
/**
 * Zkilleman
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 *
 * DISCLAIMER
 *
 * This piece of software is made available strictly on an "as is" basis
 * and comes with absolutely NO warranty
 *
 * @category    Zkilleman
 * @package     Zkilleman_Navigator
 * @copyright   Copyright (c) 2011 Improove (http://www.improove.se)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Main Controller class for the Zkilleman_Navigator module
 *
 * @author      Henrik Hedelund <henrik@improove.se>
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