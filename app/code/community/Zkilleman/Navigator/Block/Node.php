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
 * Zkilleman_Navigator_Block_Node
 *
 * @category   Zkilleman
 * @package    Zkilleman_Navigator
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
class Zkilleman_Navigator_Block_Node extends Mage_Core_Block_Template
{
    protected static $_renderer = 'navigator/node.phtml';

    protected $_node;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate(self::$_renderer);
        $this->_node = null;
    }

    public function setNode(Zkilleman_Navigator_Model_Node &$node)
    {
        $this->_node = $node;
        return $this;
    }

    public function getNode()
    {
        return $this->_node;
    }

    public function getClassNames()
    {
        $classNames = array();
        $node = $this->getNode();
        if(!$node)
            return $classNames;

        $classNames[] = preg_replace(
            '/[^a-z]+/',
            '-',
            strtolower($node->getContentType())
        );

        $classNames[] = 'level'.$node->getLevel();

        if($node->hasChildren())
                $classNames[] = 'parent';

        if($node->getIsFirst())
                $classNames[] = 'first';

        if($node->getIsLast())
                $classNames[] = 'last';

        if($node->isInCurrentPath())
                $classNames[] = 'active';

        return $classNames;
    }

    public function getNodeContentHtml()
    {
        $block = $this->getNode()->getContentTypeInstance()->getContentBlock();
        return $block->toHtml();
        //return $this->_node->getTitle();
    }

    public function getShowTemplateHints()
    {
        return false;
    }

    public function getChildNodesHtml()
    {
        if($this->_node == null || !$this->_node->hasVisibleChildren())
            return '';

        $html = '';
        $children = $this->_node->getVisibleChildren();
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