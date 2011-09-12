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
 * Zkilleman_Navigator_Model_Node_Content_Type_Cmsblock
 *
 * @category   Zkilleman
 * @package    Zkilleman_Navigator
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
class Zkilleman_Navigator_Model_Node_Content_Type_Cmsblock extends
        Zkilleman_Navigator_Model_Node_Content_Type
{
    protected function _construct()
    {
        $this->_renderer = 'navigator/node/cmsblock.phtml';
    }

    public function getContentBlock()
    {
        $block = parent::getContentBlock();
        $block->setBlockId((string) $this->_getContentDataItem('block_id'));
        return $block;
    }

    public function populateFieldset(Varien_Data_Form_Element_Fieldset &$fieldset)
    {
        $cmsBlocks = Mage::getModel('cms/block')->getCollection();
        $options = array();
        foreach($cmsBlocks as $block) {
            $options[$block->getIdentifier()] = $block->getTitle();
        }
        $contentData = $fieldset->addField('block_id', 'select', array(
            'label' => Mage::helper('navigator')->__('Block Id'),
            'name'  => 'content_fields[block_id]',
            'value' => (string) $this->_getContentDataItem('block_id'),
            'required' => false,
            'options' => $options
        ));
        
        return 1;
    }
}