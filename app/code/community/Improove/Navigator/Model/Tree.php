<?php

class Improove_Navigator_Model_Tree extends Varien_Object
{
    /* @var Improove_Navigator_Model_Node */
    protected $_root;

    /* @var Improove_Navigator_Model_Mysql4_Node_Collection */
    protected $_nodeCollection;

    public function __construct()
    {
        parent::__construct();
        $this->_nodeCollection = null;
        $this->_root = null;
    }

    public function loadByCode($code)
    {
        $node = Mage::getModel('navigator/node')->getCollection()
                ->addAttributeToFilter('code', $code)->getFirstItem();
        if($node) {
            $this->load($node->getId());
        }
        return $this;
    }

    public function load($rootId = false)
    {
        $this->_nodeCollection =
                Mage::getModel('navigator/node')->getCollection()
                ->addAttributeToSelect('*')
                ->setOrder('lft', 'asc');

        if($rootId !== false) {
            $root = Mage::getModel('navigator/node')->load($rootId);
            if($root->getId()) {
                $this->_nodeCollection
                        ->addFieldToFilter(
                            'lft', array('gteq' => $root->getLft()))
                        ->addFieldToFilter(
                            'rgt', array('lteq' => $root->getRgt()));
            }
        }

        $stack = array();
        $popped = array();
        $popCount = 0;

        foreach($this->_nodeCollection as $node) {
            if(count($stack) > 0) {
                $popped = array();
                while(end($stack)->getRgt() < $node->getRgt()) {
                    $popped[] = array_pop($stack);
                }
                $popCount = count($popped);
                // if popCount == 0 the previos node was your parent
                // if popCount == 1 the previos node was your left sibling
                // if popCount > 1 the node at the top of the stack was your
                // leftmost sibling and the rest of the popped nodes where the
                // rightmost children of the subtree
                if($popCount > 1) {
                    for($i = 0; $i < $popCount - 1; ++$i) {
                        $popped[$i]->setIsLast(true);
                    }
                }
                $node->setIsFirst($popCount == 0)
                        ->setIsLast(false) // might be changed in later
                                           // iterations when this node is
                                           // popped from the stack
                        ->setLevel(count($stack));
                end($stack)->appendChild($node);
            }
            else {
                $this->_root = $node
                        ->setIsFirst(true)
                        ->setIsLast(true)
                        ->setIsRoot(true);
            }
            $stack[] = $node;
        }
        // The remaining nodes of the stack are all on the
        // right 'wall' of the tree
        foreach($stack as $node) {
            $node->setIsLast(true);
        }
        return $this;
    }

    public function save()
    {
        try {
            $this->normalize();
            $this->getNodeCollection()->save();
            return true;
        }
        catch(Exception $e) {
            return false;
        }
    }

    public function deleteNode(Improove_Navigator_Model_Node $node, $rebuildTreeStructure = true)
    {
        $ids = $node->getChildrenIds(true, true);
        foreach($ids as $id) {
            $this->getNodeCollection()->removeItemByKey($id);
        }
        $node->deleteWithChildren();
        if($rebuildTreeStructure) {
            $this->normalize();
        }
    }

    /**
     *
     * @return Improove_Navigator_Model_Node
     */
    public function &getRootNode()
    {
        return $this->_root;
    }

    /**
     *
     * @return Improove_Navigator_Model_Mysql4_Node_Collection
     */
    public function &getNodeCollection()
    {
        return $this->_nodeCollection;
    }

    /**
     *
     * @return Improove_Navigator_Model_Node
     */
    public function &getNodeById($id)
    {
        return $this->getNodeCollection()->getItemById($id);
    }

    public function appendNode(Improove_Navigator_Model_Node $node, Improove_Navigator_Model_Node &$parent = null)
    {
        if($parent === null) {
            $this->_root->appendChild($node);
        }
        else {
            $parent->appendChild($node);
        }
        $this->getNodeCollection()->addItem($node);
        return $this;
    }

    public function moveNode($id, $parentId, $prevSiblingId)
    {
        $node = $this->getNodeById($id);
        $parent = $this->getNodeById($parentId);
        if($node && $node->hasParent() && $parent) {
            $node->getParent()->unsetChild($node);
            $prevSibling = $this->getNodeById($prevSiblingId);
            $parent->insertChild($node, $prevSibling);
            return true;
        }
        else {
            return false;
        }
    }

    public function normalize()
    {
        if(!$this->_root)
                return;

        $this->_root->setLft(0);
        $this->_leftRightTraverse($this->_root);
        //$this->_nodeCollection->save();
    }

    /**
     *
     * @param Improove_Navigator_Model_Node $node
     * @param int Value of previous node (left of parent or right of sibling)
     * @return int Right value set for given node
     */
    protected function _leftRightTraverse(Improove_Navigator_Model_Node &$node)
    {
        $left = $node->getLft();

        foreach($node->getChildren() as $child) {
            $child->setLft($left + 1);
                        // ^--- Parent left or right value
                        //      of previous sibling
            $left = $this->_leftRightTraverse($child);
        //  ^--- Actually right value of the child
        }                                  
                                           
        $node->setRgt($left + 1);
        //            ^--- Right value of the last child or self::left
        return $node->getRgt();
    }


}