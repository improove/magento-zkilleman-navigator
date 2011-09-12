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
 * Zkilleman_Navigator_Adminhtml_TreeController
 *
 * @category   Zkilleman
 * @package    Zkilleman_Navigator
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
class Zkilleman_Navigator_Adminhtml_TreeController extends Zkilleman_Navigator_Controller_Adminhtml_Action
{
    public function moveAction()
    {
        $tree = $this->_initTree();
        $node           = $this->getRequest()->getPost('id', false);
        $parent         = $this->getRequest()->getPost('pid', false);
        $prevSibling    = $this->getRequest()->getPost('aid', false);
        if($tree->moveNode($node, $parent, $prevSibling) && $tree->save()) {
            $this->getResponse()->setBody('SUCCESS');
        }
        else {
            $this->getResponse()->setBody(
                    Mage::helper('navigator')->__('Could not move node.')
                    );
        }
    }

    public function switchAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        $node = $this->_initNode();

        $block = $this->getLayout()->createBlock('navigator/adminhtml_tree');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'data' => $block->getTree(),
            'parameters' => array(
                'text'        => 'Navigation Tree',
                'draggable'   => false,
                'allowDrop'   => true,
                'id'          => (int) 1,
                'expanded'    => (int) false,
                'store_id'    => (int) $storeId,
                'category_id' => (int) $node->getId(),
                'root_visible'=> (int) false
        ))));
    }
}