<?php

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