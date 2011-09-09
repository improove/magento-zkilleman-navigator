<?php

class Improove_Navigator_Model_Node_Content_Type extends Varien_Object
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

    public static function factory(Improove_Navigator_Model_Node &$node)
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

    public function setNode(Improove_Navigator_Model_Node &$node)
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