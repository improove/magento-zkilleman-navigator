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
 * Zkilleman_Navigator_Block_Tree
 *
 * @category   Zkilleman
 * @package    Zkilleman_Navigator
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
class Zkilleman_Navigator_Block_Tree extends Mage_Core_Block_Template
{
    protected $_treeModel;
    protected $_rootCode;

    public function __construct()
    {
        parent::__construct();
        $this->_treeModel = Mage::getModel('navigator/tree');
        $this->_rootCode = null;
        $this->addData(array(
            'cache_lifetime'    => false,
            'cache_tags'        => array(
                Mage_Catalog_Model_Category::CACHE_TAG,
                Mage_Core_Model_Store_Group::CACHE_TAG
            ),
        ));
    }

    public function getCacheKeyInfo()
    {
        return array_merge(
                Mage::helper('navigator')->getCacheKeyInfo(),
                parent::getCacheKeyInfo());
    }

    public function setRootCode($code)
    {
        $this->_rootCode = $code;
    }

    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        if($this->_rootCode !== null) {
            $this->_treeModel->loadByCode($this->_rootCode);
        }
        else {
            $this->_treeModel->load();
        }
    }

    public function getTreeHtml()
    {
        $root = $this->_treeModel->getRootNode();
        $root->getContentTypeInstance(); // just touch to init type

        $html = '';
        $children = $root->getVisibleChildren();
        for($i = 0; $i < count($children); ++$i) {
            $child = $children[$i];
            if ($i == 0) {
                $child->setIsFirst(true);
            }
            else {
                $child->setIsFirst(false);
            }
            if ($i == count($children) - 1) {
                $child->setIsLast(true);
            }
            else {
                $child->setIsLast(false);
            }
            $html .= $this->helper('navigator')->getNodeHtml($child);
        }
        return $html;
    }
}