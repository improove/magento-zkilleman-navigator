<?php

class Improove_Navigator_Model_Node_Content_Type_Internal 
        extends Improove_Navigator_Model_Node_Content_Type_Link
{
    public function getModelOptions()
    {
        $options = array();
        foreach (Mage::getConfig()->getNode('frontend/routers')->children()
                as $code => $content) {
            $options[$code] = $content->args->module;
        }
        return $options;
    }

    public function getHref()
    {
        $route = trim((string) $this->_getContentDataItem('router'));
        $controller = trim((string) $this->_getContentDataItem('controller'));
        if (strlen($controller) > 0) {
            $route .= '/'.$controller;
            $action = trim((string) $this->_getContentDataItem('action'));
            if (strlen($action)) {
                $route .= '/'.$action;
            }
        }

        return Mage::getModel('core/url')->getUrl($route);
    }

    public function populateFieldset(Varien_Data_Form_Element_Fieldset &$fieldset)
    {
        $router = $fieldset->addField('router', 'select', array(
            'label' => Mage::helper('navigator')->__('Module'),
            'name'  => 'content_fields[router]',
            'value' => (string) $this->_getContentDataItem('router'),
            'options' => $this->getModelOptions()
        ));

        $controller = $fieldset->addField('controller', 'text', array(
            'label' => Mage::helper('navigator')->__('Controller'),
            'name'  => 'content_fields[controller]',
            'value' => (string) $this->_getContentDataItem('controller'),
            'note'  => Mage::helper('navigator')
                ->__('Example: &quot;account&quot; for the &quot;Mage_Customer&quot; module. Leave empty for default.')
        ));

        $controller = $fieldset->addField('action', 'text', array(
            'label' => Mage::helper('navigator')->__('Action'),
            'name'  => 'content_fields[action]',
            'value' => (string) $this->_getContentDataItem('action'),
            'note'  => Mage::helper('navigator')
                ->__('Example: &quot;login&quot; for the &quot;Mage_Customer&quot; module with &quot;account&quot; controller. Leave empty for default.')
        ));

        parent::populateFieldset($fieldset);
        $fieldset->removeField('href');
    }

    public function isInCurrentPath()
    {
        $route = trim((string) $this->_getContentDataItem('router'));
        $controller = trim((string) $this->_getContentDataItem('controller'));
        $action = trim((string) $this->_getContentDataItem('action'));
        if(strlen($controller) == 0) $controller = 'index';
        if(strlen($action) == 0) $action = 'index';

        $request = Mage::app()->getRequest();
        if($request->getRouteName() == $route &&
                $request->getControllerName() == $controller &&
                $request->getActionName() == $action
                ) {
            return true;
        }
        return false;
    }
}