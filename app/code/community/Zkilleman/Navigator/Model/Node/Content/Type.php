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
 * Zkilleman_Navigator_Model_Node_Content_Type
 *
 * @category   Zkilleman
 * @package    Zkilleman_Navigator
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
class Zkilleman_Navigator_Model_Node_Content_Type extends Varien_Object
{
    const DEFAULT_TYPE = 'plain_node';
    const DEFAULT_TYPE_MODEL = 'navigator/node_content_type_plain';
    const CONTENT_TYPES_XML_PATH = 'global/navigator/content/type';

    const TYPE_PLAIN = 'plain';

    protected static $_types = null;

    protected $_node;
    protected $_renderer;
    protected $_contentData;

    protected function _construct()
    {
        parent::_construct();
        $this->_renderer = 'navigator/node/plain.phtml';
        $this->_contentData = null;
    }

    public static function factory(Zkilleman_Navigator_Model_Node &$node)
    {
        $types = self::getTypes();
        $contentType = $node->getContentType();
        $typeModelName = self::DEFAULT_TYPE_MODEL;

        if(!empty($types[$contentType]['model'])) {
            $typeModelName = $types[$contentType]['model'];
        } else {
            $contentType = self::DEFAULT_TYPE;
        }

        $model = Mage::getModel($typeModelName);
        $model->setNode($node);

        return $model;
    }

    public static function getTypes()
    {
        if(self::$_types !== null)
            return self::$_types;

        self::$_types = array();
        $types = Mage::getConfig()->getNode(self::CONTENT_TYPES_XML_PATH)->asArray();
        foreach($types as $code => $type) {
            $module = 'navigator';
            if(isset($type['@']['module'])) {
                $module = $type['@']['module'];
            }
            $label = Mage::helper($module)->__($type['label']);
            self::$_types[$code] = array(
                'module' => $module,
                'model' => $type['model'],
                'label' => $label);
        }
        return self::$_types;
    }

    public function setNode(Zkilleman_Navigator_Model_Node &$node)
    {
        $this->_node = $node;
        try {
            $this->_contentData = $this->_unserialize($node->getContentData());
        }
        catch(Exception $e) {
            $this->_contentData = null;
        }
    }

    public function &getNode()
    {
        return $this->_node;
    }
    
    public static function getCacheKeyInfo()
    {
        return null;
    }

    public function getContentBlock()
    {
        if(!$this->hasData('content_block')) {
            $this->setData('content_block',
                    Mage::app()
                        ->getLayout()
                        ->createBlock('core/template')
                        ->setTemplate($this->_renderer)
                        ->setTypeModel($this)
                        ->setNode($this->getNode()));
        }
        return $this->getData('content_block');
    }

    public function isInCurrentPath()
    {
        return false;
    }

    public function populateFieldset(Varien_Data_Form_Element_Fieldset &$fieldset)
    {
        $contentData = $fieldset->addField('text', 'text', array(
            'label' => Mage::helper('navigator')->__('Content Data'),
            'name'  => 'content_fields[text]',
            'value' => (string) $this->_getContentDataItem('text'),
            'required' => false
        ));
        //$contentData->setAfterElementHtml('marklar');
    }

    protected function _getContentDataItem($key)
    {
        return is_array($this->_contentData) && isset($this->_contentData[$key])
                    ? $this->_contentData[$key] : null;
    }

    protected function _setContentDataItem($key, $value)
    {
        if (!is_array($this->_contentData))
            $this->_contentData = array();

        $this->_contentData[$key] = $value;
        return $this;
    }

    public function prepareForSave($data)
    {
        $this->_contentData = $data;
        $this->_storeNodeContentData();
    }

    protected function _storeNodeContentData()
    {
        try {
            $this->getNode()->setContentData(
                    $this->_serialize($this->_contentData)
            );
        }
        catch(Exception $e) {
            $this->getNode()->setContentData('');
        }
    }

    protected function _serialize($data)
    {
        return serialize($data);
    }

    protected function _unserialize($data)
    {
        return unserialize($data);
    }
}