<?php

class Improove_Navigator_Adminhtml_NodeController extends Improove_Navigator_Controller_Adminhtml_Action
{
    public function editAction()
    {
        $node = $this->_initNode();
        if($this->getRequest()->getQuery('isAjax')) {
            $this->loadLayout();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
                'messages' => $this->getLayout()->getMessagesBlock()->getGroupedHtml(),
                'content' => $this->getLayout()->getBlock('navigator.edit')->getFormHtml()
            )));
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveAction()
    {
        //TODO: massive error handling & dispatch content management to type model
        $node = $this->_initNode();
        $data = $this->getRequest()->getPost();
        $node->addData($data);
        $node->getContentTypeInstance()->prepareForSave($data['content_fields']);
        $node->setVisibility(serialize($data['rule']));
        
        if($useDefaults = $this->getRequest()->getPost('use_default')) {
            foreach ($useDefaults as $attributeCode) {
                $node->setData($attributeCode, false);
                if($attributeCode == 'content_type') {
                    $node->setData('content_data', false);
                }
            }
        }
        
        if(isset($data['parent'])) {
            $tree = Mage::getModel('navigator/tree')->load();
            $parent = $tree->getNodeById($data['parent']);
            if($parent) {
                $tree->appendNode($node, $parent);
                $tree->save();
            }
            else {
                $node->save();
            }
        }
        else {
            $node->save();
        }
        $this->_redirect(
                '*/*/edit',
                array(
                    'id' => $node->getId(),
                    'store' => $node->getStoreId()
                )
        );
//        $url = $this->getUrl('*/*/edit', array('_current' => true, 'id' => $node->getId()));
//        $this->getResponse()->setBody(
//            '<script type="text/javascript">parent.updateContent("' . $url . '", {}, true);</script>'
//        );
    }

    public function addAction()
    {
        /*$node = $this->_initNode();
        if($this->getRequest()->getQuery('isAjax')) {
            $this->loadLayout();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
                'messages' => $this->getLayout()->getMessagesBlock()->getGroupedHtml(),
                'content' => $this->getLayout()->getBlock('navigator.edit')->getFormHtml()
            )));
            return;
        }
        $this->loadLayout();
        $this->renderLayout();*/
        $this->editAction();
    }

    public function contentFieldsetAction()
    {
        $node = $this->_initNode();

        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_element')
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
                ->setTemplate('navigator/form/renderer/fieldset.phtml')
        );
        //TODO: don't use catalog block, create your own
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('adminhtml/catalog_form_renderer_fieldset_element')
        );

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('navigator_node_');
        $form->setDataObject($node);

        $fieldset = $form->addFieldset(
                'content_fieldset',
                array(
                    'legend' =>
                        Mage::helper('navigator')->__('Node Content'),
                    'class' => 'fieldset-wide'
                    )
                );

        $node->setContentType($this->getRequest()->getParam('content_type'));
        $node->getContentTypeInstance()->populateFieldset($fieldset);
        if($fieldset->getElements()->count() > 0) {
            $this->getResponse()->setBody($fieldset->toHtml());
        }
        else {
            $this->getResponse()->setBody('');
        }
        //var_dump($node->getContentTypeInstance());
    }

    public function deleteAction()
    {
        $id =
        $tree = $this->_initTree();
        $node = $tree->getNodeById($this->getRequest()->getParam('id'));
        $tree->deleteNode($node, false);
        $tree->save();
        $this->_redirect('*/*/edit');
    }

    public function newVisibilityAction()
    {
        $id = $this->getRequest()->getParam('id');
        $type = $this->getRequest()->getParam('type');
        // find cond. type by param
        $vCond =
            Improove_Navigator_Model_Node_Visibility::getModelByCode($type);
        $vCond->setCondId($id);
        $this->getResponse()->setBody(
            $vCond->getEditBlock()->toHtml()
        );
    }
}