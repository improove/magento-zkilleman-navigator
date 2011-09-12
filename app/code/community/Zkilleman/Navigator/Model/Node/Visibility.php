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
 * Zkilleman_Navigator_Model_Node_Visibility
 *
 * @category   Zkilleman
 * @package    Zkilleman_Navigator
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
class Zkilleman_Navigator_Model_Node_Visibility
    extends Zkilleman_Navigator_Model_Node_Visibility_Abstract
{
    protected $_node;
    protected $_visibilityInstance;

    protected function _construct()
    {
        parent::_construct();
        $this->_node = null;
        $this->_visibilityInstance = null;
    }

    public function setNode(Zkilleman_Navigator_Model_Node &$node)
    {
        $this->_node = $node;
        $data = array();
        try {
            $nodeData = unserialize($node->getData('visibility'));
            if(is_array($nodeData) && isset($nodeData['conditions'])
                    && is_array($nodeData['conditions'])) {
                $data = $nodeData['conditions'];
            }
        }
        catch (Exception $e) {
            $data = array();
        }

        $data['cond_id'] = '1';

        $this->_visibilityInstance = self::getModelByCode(
            'group',
            $data
        );
        return $this;
    }
    
    public function assert()
    {
        return $this->_visibilityInstance->assert();
    }

    public function getEditBlock()
    {
        return $this->_visibilityInstance->getEditBlock();
    }
}