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
 * Zkilleman_Navigator_Model_Node_Content_Type_Cmspage
 *
 * @category   Zkilleman
 * @package    Zkilleman_Navigator
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
class Zkilleman_Navigator_Model_Node_Content_Type_Cmspage
        extends Zkilleman_Navigator_Model_Node_Content_Type_Link
{
    public function getPageOptions()
    {
        $pages = Mage::getModel('cms/page')->getCollection();
        $options = array();
        foreach($pages as $page) {
            $options[$page->getId()] = $page->getTitle();
        }
        return $options;
    }

    public function getHref()
    {
        $id = (int) $this->_getContentDataItem('page_id');
        $page = Mage::getModel('cms/page')->load($id);
        return Mage::getBaseUrl().$page->getIdentifier();
    }

    public function populateFieldset(Varien_Data_Form_Element_Fieldset &$fieldset)
    {
        $page = $fieldset->addField('page_id', 'select', array(
            'label' => Mage::helper('navigator')->__('Page'),
            'name'  => 'content_fields[page_id]',
            'value' => (string) $this->_getContentDataItem('page_id'),
            'options' => $this->getPageOptions()
        ));

        parent::populateFieldset($fieldset);
        $fieldset->removeField('href');
    }

    public function isInCurrentPath()
    {
        $request = Mage::app()->getRequest();
        $pageId = (int) $this->_getContentDataItem('page_id');
        if($request->getModuleName() == 'cms') {
            if ($request->getControllerName() == 'page' &&
                $request->getActionName() == 'view' &&
                $request->getParam('page_id') == $pageId) {
                return true;
            }
            else if($request->getControllerName() == 'index' &&
                $request->getActionName() == 'index') {
                $options = $this->getPageOptions();
                // maybe to expensive with a model load?
                $page = Mage::getModel('cms/page')->load($pageId);
                if ($page->getIdentifier() == Mage::getStoreConfig(
                    Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE)) {
                    return true;
                }
            }
        }
        return false;
    }
}