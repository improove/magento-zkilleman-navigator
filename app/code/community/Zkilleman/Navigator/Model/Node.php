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
 * Zkilleman_Navigator_Model_Node
 *
 * @category   Zkilleman
 * @package    Zkilleman_Navigator
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
class Zkilleman_Navigator_Model_Node extends Mage_Core_Model_Abstract
{
    protected $_parent;
    protected $_children;
    protected $_visibility;

    /**
     * This array contains codes of attributes which have value in current store
     *
     * @var array
     */
    protected $_storeValuesFlags = array();

    protected function _construct()
    {
        $this->_init('navigator/node');
        $this->_parent = null;
        $this->_children = array();
        $this->_visibility = null;
    }

    /**
     *
     * @return bool
     */
    public function hasParent()
    {
        return $this->_parent !== null;
    }

    public function setParent(Zkilleman_Navigator_Model_Node &$node)
    {
        $this->_parent = $node;
    }

    public function unsetParent()
    {
        $this->_parent = null;
    }

    /**
     *
     * @return bool
     */
    public function isRoot()
    {
        return !$this->hasParent();
    }

    /**
     *
     * @return Zkilleman_Navigator_Model_Node
     */
    public function getParent()
    {
        return $this->_parent;
    }

    public function appendChild(Zkilleman_Navigator_Model_Node &$node)
    {
        $node->setParent($this);
        $this->_children[] = $node;
    }

    public function insertChild(Zkilleman_Navigator_Model_Node &$node,
            Zkilleman_Navigator_Model_Node &$prevSibling = null)
    {
        $node->setParent($this);
        $pos = 0;
        if($prevSibling) {
            for($i = 0; $i < count($this->_children); ++$i) {
                if($this->_children[$i]->getId() == $prevSibling->getId()) {
                    $pos = $i + 1;
                    break;
                }
            }
        }

        array_splice($this->_children, $pos, 0, array($node));
    }

    public function unsetChild(Zkilleman_Navigator_Model_Node &$node)
    {
        if($node->getParent() && $node->getParent()->getId() == $this->getId()) {
            $node->unsetParent();
        }
        for($i = 0; $i < count($this->_children); ++$i) {
            if($this->_children[$i]->getId() == $node->getId()) {
                array_splice($this->_children, $i, 1);
                break;
            }
        }
    }

    public function unsetAllChildren($decend = true)
    {
        foreach($this->getChildren() as $child) {
            if($decend) {
                $child->unsetAllChildren();
            }
            $this->unsetChild($child);
        }
        return $this;
    }

    public function deleteWithChildren()
    {
        if($this->hasParent()) {
            $this->getParent()->unsetChild($this);
        }
        foreach($this->getChildren() as $child) {
            $child->deleteWithChildren();
        }
        $this->delete();
    }

    public function &getChildren()
    {
        return $this->_children;
    }

    public function getVisibleChildren($noCache = false)
    {
        if($noCache || !$this->hasData('visible_children')) {
            $children = array();
            /*$contentChildren = $this->getContentTypeInstance()->getChildNodes();
            if($this->getContentTypeInstance()->childNodesBefore()) {
                foreach($contentChildren as $child) {
                    $children[] = $child;
                }
            }*/
            foreach($this->getChildren() as $child) {
                if($child->isVisible())
                    $children[] = $child;
            }
            /*if(!$this->getContentTypeInstance()->childNodesBefore()) {
                foreach($contentChildren as $child) {
                    $children[] = $child;
                }
            }*/
            $this->setData('visible_children', $children);
        }
        return $this->getData('visible_children');
    }

    public function getChildCount()
    {
        return count($this->_children);
    }

    public function hasChildren()
    {
        return !empty($this->_children);
    }

    public function hasVisibleChildren()
    {
        return count($this->getVisibleChildren()) > 0;
    }

    public function getChildrenIds($decend = true, $includeSelf = false)
    {
        $ids = array();
        if($includeSelf) {
            $ids[] = $this->getId();
        }
        foreach($this->getChildren() as $child) {
            $ids[] = $child->getId();
            if($decend) {
                $ids = array_merge($ids, $child->getChildrenIds());
            }
        }
        return $ids;
    }

    public function setContentType($type)
    {
        $this->unsetData('content_type_instance');
        return parent::setContentType($type);
    }

    /**
     *
     * @return Zkilleman_Navigator_Model_Node_Content_Type
     */
    public function getContentTypeInstance()
    {
        if(!$this->hasData('content_type_instance')) {
            $this->setData(
                    'content_type_instance',
                    Mage::getModel('navigator/node_content_type')
                        ->factory($this)
            );
        }

        return $this->getData('content_type_instance');
    }

    public function isVisible()
    {
        return $this->getIsEnabled() && 
                $this->getVisibility()->assert();
    }

    public function getVisibility()
    {
        if($this->_visibility === null) {
            $this->_visibility = Mage::getModel('navigator/node_visibility');
            $this->_visibility->setNode($this);
        }
        return $this->_visibility;
    }

    public function isInCurrentPath()
    {
        $result = false;
        $result = $this->getContentTypeInstance()->isInCurrentPath();
        if ($result == false && $this->hasChildren()) {
            foreach ($this->getChildren() as $child) {
                if ($child->isInCurrentPath()) {
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }

    public function setStoreId($storeId)
    {
        if (!is_numeric($storeId)) {
            $storeId = Mage::app($storeId)->getStore()->getId();
        }
        $this->setData('store_id', $storeId);
        $this->getResource()->setStoreId($storeId);
        return $this;
    }

    public function getStoreId()
    {
        if ($this->hasData('store_id')) {
            return $this->_getData('store_id');
        }
        return Mage::app()->getStore()->getId();
    }

    /**
     * Set attribute code flag if attribute has value in current store and does not use
     * value of default store as value
     *
     * @param   string $attributeCode
     * @return  Mage_Catalog_Model_Abstract
     */
    public function setExistsStoreValueFlag($attributeCode)
    {
        $this->_storeValuesFlags[$attributeCode] = true;
        return $this;
    }

    /**
     * Check if object attribute has value in current store
     *
     * @param   string $attributeCode
     * @return  bool
     */
    public function getExistsStoreValueFlag($attributeCode)
    {
        return array_key_exists($attributeCode, $this->_storeValuesFlags);
    }
}