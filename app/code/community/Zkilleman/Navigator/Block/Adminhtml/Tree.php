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
 * Zkilleman_Navigator_Block_Adminhtml_Tree
 *
 * @category   Zkilleman
 * @package    Zkilleman_Navigator
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
class Zkilleman_Navigator_Block_Adminhtml_Tree
    extends Mage_Adminhtml_Block_Abstract
{
    /**
     * Prepares block for rendering
     *
     * @return Zkilleman_Navigator_Block_Adminhtml_Tree
     */
    protected function _prepareLayout()
    {
        $addUrl = $this->getUrl(
            "*/adminhtml_node/add",
            array(
                '_current'=>true,
                'id'=>null,
                '_query' => false
            )
        );

        $this->setChild(
            'add_item_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData(
                array(
                    'label'     =>
                        Mage::helper('navigator')->__('Add Node'),
                    'onclick'   => "addNew('".$addUrl."', false)",
                    'class'     => 'add',
                    'id'        => 'add_menu_item_button',
                    'style'     => 'margin: 5px 0 0 0;'
                )
            )
        );

        $storeSwitcherUrl = $this->getUrl(
            '*/*/*',
            array('_current' => true, '_query' => false, 'store' => null)
        );
        $storeSwitcherBlock = $this->getLayout()
            ->createBlock('adminhtml/store_switcher')
            ->setTemplate('store/switcher/enhanced.phtml')
            ->setSwitchUrl($storeSwitcherUrl);

        $this->setChild('store_switcher', $storeSwitcherBlock);

        return parent::_prepareLayout();
    }

    /**
     * Renders store switcher html
     *
     * @return string Store Switcher html
     */
    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    /**
     * Renders add item button
     *
     * @return string Add item button html
     */
    public function getAddItemButtonHtml()
    {
        return $this->getChildHtml('add_item_button');
    }

    /**
     * Gives url for move action
     *
     * @return string returns move action url
     */
    public function getMoveUrl()
    {
        return $this->getUrl('*/adminhtml_tree/move');
    }

    /**
     * Gives url for edit action
     *
     * @return string returns edit action url
     */
    public function getEditUrl()
    {
        return $this->getUrl('*/adminhtml_node/edit');
    }

    /**
     * Returns the current node
     *
     * @return Zkilleman_Navigator_Model_Node The current node
     */
    public function getNode()
    {
        return Mage::registry('current_navigator_node');
    }

    /**
     * Returns the id of the current id
     *
     * @return int The id of the current Node
     */
    public function getNodeId()
    {
        $node = $this->getNode();
        return $node ? $node->getId() : 1;
                                     // ^-- root id
    }

    public function getStoreId()
    {
        return (int) $this->getRequest()->getParam('store');
    }

    /**
     * Gives url for switch tree action
     *
     * @return string returns switch tree action url
     */
    public function getSwitchTreeUrl()
    {
        return $this->getUrl(
            '*/adminhtml_tree/switch',
            array(
                '_current'=>true,
                'store'=>null,
                '_query'=>false,
                'id'=>null,
                'parent'=>null
            )
        );
    }

    /**
     * Returns tree structure
     *
     * @return array Tree structure
     */
    public function getTree()
    {
        $tree = Mage::getModel('navigator/tree');
        $tree->load();
        $rootArray = $this->_getNodeJson($tree->getRootNode());
        return isset($rootArray['children']) ? $rootArray['children'] : array();
    }

    /**
     * Encodes the tree structure as json
     *
     * @return string A json encode string of the tree structure
     */
    public function getTreeJson()
    {
        $tree = Mage::getModel('navigator/tree');
        $tree->load();
        $rootArray = $this->_getNodeJson($tree->getRootNode());
        $json = Mage::helper('core')->jsonEncode(
            isset($rootArray['children']) ? $rootArray['children'] : array()
        );
        return $json;
    }

    /**
     * Prepares tree structure for json
     *
     * @param Zkilleman_Navigator_Model_Node &$node The node
     * 
     * @return array Tree structure
     */
    protected function _getNodeJson(Zkilleman_Navigator_Model_Node &$node)
    {
        $item = array();

        $item['text'] = $node->getCode();
        $item['id']  = $node->getId();
        $item['cls'] = 'folder '.($node->getIsEnabled() || true //TODO: fix greyness
                ? 'active-category' : 'no-active-category');
        $item['allowDrop'] = true;
        $item['allowDrag'] = true;

        if ($node->hasChildren()) {
            $item['children'] = array();
        }

        if ($node->hasChildren()) {
            $item['children'] = array();
            foreach ($node->getChildren() as $child) {
                $item['children'][] = $this->_getNodeJson($child);
            }
        }

        $item['expanded'] = true;

        return $item;
    }
}