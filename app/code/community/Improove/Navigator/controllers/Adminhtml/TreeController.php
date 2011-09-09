<?php

class Improove_Navigator_Adminhtml_TreeController extends Improove_Navigator_Controller_Adminhtml_Action
{
    public function moveAction()
    {
        $tree = $this->_initTree();
        $node           = $this->getRequest()->getPost('id', false);
        $parent         = $this->getRequest()->getPost('pid', false);
        $prevSibling    = $this->getRequest()->getPost('aid', false);
        if($tree->moveNode($node, $parent, $prevSibling) && $tree->save()) {
            $this->getResponse()->setBody('SUCCESS');
        }
        else {
            $this->getResponse()->setBody(
                    Mage::helper('navigator')->__('Could not move node.')
                    );
        }
    }

    public function switchAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        $node = $this->_initNode();

        $block = $this->getLayout()->createBlock('navigator/adminhtml_tree');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'data' => $block->getTree(),
            'parameters' => array(
                'text'        => 'Navigation Tree',
                'draggable'   => false,
                'allowDrop'   => true,
                'id'          => (int) 1,
                'expanded'    => (int) false,
                'store_id'    => (int) $storeId,
                'category_id' => (int) $node->getId(),
                'root_visible'=> (int) false
        ))));
    }
}