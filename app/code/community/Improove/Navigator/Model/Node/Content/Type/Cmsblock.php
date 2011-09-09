<?php

class Improove_Navigator_Model_Node_Content_Type_Cmsblock extends
        Improove_Navigator_Model_Node_Content_Type
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