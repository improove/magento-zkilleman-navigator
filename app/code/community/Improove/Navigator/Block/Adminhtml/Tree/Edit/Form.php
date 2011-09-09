<?php

class Improove_Navigator_Block_Adminhtml_Tree_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('navigator/tree/edit/form.phtml');
    }

    public function getSaveUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/save', $params);
    }

    public function getDeleteUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/delete', $params);
    }

    public function getCurrentNode()
    {
        return Mage::registry('current_navigator_node');
    }

    public function getNodeId()
    {
        return $this->getCurrentNode() ? 
                $this->getCurrentNode()->getId() : false;
    }

    public function getCurrentParentId()
    {
        return Mage::registry('current_navigator_node_parent_id');
    }

    protected function _prepareLayout()
    {
        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('navigator')->__('Save Node'),
                    'onclick'   => "nodeSubmit('" . $this->getSaveUrl() . "', true)",
                    'class' => 'save'
                ))
        );

        $confirm = $this->__('This will delete children aswell');
        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('navigator')->__('Delete Node'),
                    'onclick'   => "if(confirm('{$confirm}')) nodeSubmit('" . $this->getDeleteUrl() . "', true)",
                    'class' => 'delete'
                ))
        );
        
        $this->setChild('visibility_fieldset',
            $this->getLayout()->createBlock('adminhtml/template')
                ->setTemplate('navigator/form/visibility.phtml')
                ->setNode($this->getCurrentNode())
                ->setVisibilityModel($this->getCurrentNode()->getVisibility())
        );

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
    }

    protected function _prepareForm()
    {
        parent::_prepareForm();
        $node = $this->getCurrentNode();
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('navigator_node_');
        $form->setDataObject($node);

        $fieldset = $form->addFieldset(
                'base_fieldset',
                array(
                    'legend' =>
                        Mage::helper('catalog')->__('General Information'),
                    'class' => 'fieldset-wide'
                    )
                );

        if($node->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name'  => 'id',
                'value' => $node->getId()
            ));
        }
        else if($parentId = $this->getCurrentParentId()) {
            $fieldset->addField('parent', 'hidden', array(
                'name'  => 'parent',
                'value' => $parentId
            ));
        }

        $fieldset->addField('code', 'text', array(
            'label' => $this->__('Code'),
            'name'  => 'code',
            'value' => $node->getCode(),
            'required' => true
        ));

        $attributes = $node->getResource()
                ->loadAllAttributes($node)
                ->getSortedAttributes();

        $this->_setFieldset(
                $attributes,
                $fieldset,
                array('content_data', 'visibility') // exclude fields
                );

        $contentContainerId = $this->getContentContainerId();
        $contentFieldset = $form->addFieldset(
                'content_fieldset',
                array(
                    'legend' =>
                        Mage::helper('navigator')->__('Node Content'),
                    'class' => 'fieldset-wide',
                    'fieldset_container_id' => $contentContainerId
                    )
                );

        $node->getContentTypeInstance()->populateFieldset($contentFieldset);

        if($contentFieldset->getElements()->count() == 0) {
            $form->removeField($contentFieldset->getId()); // only removes from index ?!
            $form->getElements()->remove($contentFieldset->getId());
            $fieldset->setAfterElementHtml(
                    $fieldset->getAfterElementHtml() . 
                    "<div id=\"{$contentContainerId}\"></div>"
            );
        }

        $contentScript = "<script type=\"text/javascript\">
            function setContentVisibility()
            {
                if($('navigator_node_content_type_default').checked) {
                    $('{$contentContainerId}').hide();
                }
                else {
                    $('{$contentContainerId}').show();
                }
            }
            if($('navigator_node_content_type_default')) {
                setContentVisibility();
                $('navigator_node_content_type_default')
                    .observe('click', function() {
                        setContentVisibility()
                    });
            }
        </script>";
        $fieldset->setAfterElementHtml(
                    $fieldset->getAfterElementHtml() . $contentScript
            );

        $form->addValues($node->getData());
        $this->setForm($form);
    }

    public function getContentContainerId()
    {
        return 'navigator_node_content_fieldset_container';
    }
}
