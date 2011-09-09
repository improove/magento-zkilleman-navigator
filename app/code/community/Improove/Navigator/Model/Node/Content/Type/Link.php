<?php

abstract class Improove_Navigator_Model_Node_Content_Type_Link
                    extends Improove_Navigator_Model_Node_Content_Type
{
    protected function _construct()
    {
        parent::_construct();
        $this->_renderer = 'navigator/node/link.phtml';
    }

    public function setNode(Improove_Navigator_Model_Node &$node)
    {
        parent::setNode($node);
        $this->setAnchorText($node->getTitle());
        $this->setAnchorTitle($node->getTitle());
    }

    public function getHref()
    {
        return (string) $this->_getContentDataItem('href');
    }

    public function setHref($href)
    {
        $this->_setContentDataItem('href', $href);
        return $this;
    }

    public function getTarget()
    {
        $target = (string) $this->_getContentDataItem('target');
        return $target == '_frame' ?
                (string) $this->_getContentDataItem('frame_name') : $target;
    }

    public function getClassList()
    {
        return (string) $this->_getContentDataItem('class_list');
    }

    public function getTargetOptions()
    {
        return array(
            '_self' => Mage::helper('navigator')->__('Current window or tab'),
            '_blank' => Mage::helper('navigator')->__('New window or tab'),
            '_parent' => Mage::helper('navigator')->__('Parent frameset'),
            '_top' => Mage::helper('navigator')->__('Full body of the window'),
            '_frame' => Mage::helper('navigator')->__('A named frame')
        );
    }

    public function populateFieldset(Varien_Data_Form_Element_Fieldset &$fieldset)
    {
        $target = $fieldset->addField('href', 'text', array(
            'label' => Mage::helper('navigator')->__('Link URL'),
            'name'  => 'content_fields[href]',
            'value' => (string) $this->_getContentDataItem('href'),
            'note'  => Mage::helper('navigator')->__('Example: http://improove.se')
        ));

        $target = $fieldset->addField('class_list', 'text', array(
            'label' => Mage::helper('navigator')->__('Class list'),
            'name'  => 'content_fields[class_list]',
            'value' => (string) $this->_getContentDataItem('class_list'),
            'note'  => Mage::helper('navigator')->__('Space separated list of css classes')
        ));

        $target = $fieldset->addField('target', 'select', array(
            'label' => Mage::helper('navigator')->__('Open link in..'),
            'name'  => 'content_fields[target]',
            'value' => (string) $this->_getContentDataItem('target'),
            'options' => $this->getTargetOptions()
        ));

        $target = $fieldset->addField('frame_name', 'text', array(
            'label' => Mage::helper('navigator')->__('Target frame name'),
            'name'  => 'content_fields[frame_name]',
            'value' => (string) $this->_getContentDataItem('frame_name'),
            'note'  => Mage::helper('navigator')
                        ->__('Only applicable if the option &apos;A named frame&apos; is selected above')
        ));

        $template = $fieldset->addField('template', 'text', array(
            'label' => Mage::helper('navigator')->__('Template'),
            'name'  => 'content_fields[template]',
            'value' => (string) $this->_getContentDataItem('template'),
            'note'  => sprintf(Mage::helper('navigator')
                        ->__('Leave blank to use default template: %s'),
                        $this->_renderer)
        ));
    }
}