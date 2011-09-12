<?php

abstract class Zkilleman_Navigator_Model_Node_Visibility_Abstract extends Varien_Object
{
    const VISIBILITY_CONDITION_XML_PATH = 'global/navigator/visibility/condition';

    protected static $_types;
    protected $_editBlock;
    protected $_renderer;

    protected function _construct()
    {
        $this->_editBlock = null;
        $this->_renderer = 'navigator/form/visibility/default.phtml';
    }

    public abstract function assert();

    public static function getModelByCode($code, $data = array())
    {
        $types = self::getTypes();
        if(!isset($types[$code]) && !isset($types[$code]['model'])) {
            return false;
        }
        return Mage::getModel($types[$code]['model'], $data)->setCode($code);
    }

    public static function getTypes()
    {
        if(self::$_types !== null)
            return self::$_types;

        self::$_types = array();
        $types = Mage::getConfig()->getNode(self::VISIBILITY_CONDITION_XML_PATH)->asArray();
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

    public function getEditBlock()
    {
        if($this->_editBlock == null) {
            $this->_editBlock =
                    Mage::app()
                        ->getLayout()
                        ->createBlock('adminhtml/template')
                        ->setTemplate($this->_renderer)
                        ->setVisibilityModel($this);
        }
        return $this->_editBlock;
    }
    
    public static function getCacheKeyInfo()
    {
        return null;
    }
}