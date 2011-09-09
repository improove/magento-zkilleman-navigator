<?php

class Improove_Navigator_Block_Tree extends Mage_Core_Block_Template
{
    protected $_treeModel;
    protected $_rootCode;

    public function __construct()
    {
        parent::__construct();
        $this->_treeModel = Mage::getModel('navigator/tree');
        $this->_rootCode = null;
        $this->addData(array(
            'cache_lifetime'    => false,
            'cache_tags'        => array(
                Mage_Catalog_Model_Category::CACHE_TAG,
                Mage_Core_Model_Store_Group::CACHE_TAG
            ),
        ));
    }

    public function getCacheKeyInfo()
    {
        return array_merge(
                Mage::helper('navigator')->getCacheKeyInfo(),
                parent::getCacheKeyInfo());
    }

    public function setRootCode($code)
    {
        $this->_rootCode = $code;
    }

    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        if($this->_rootCode !== null) {
            $this->_treeModel->loadByCode($this->_rootCode);
        }
        else {
            $this->_treeModel->load();
        }
    }

    public function getTreeHtml()
    {
        $root = $this->_treeModel->getRootNode();
        $root->getContentTypeInstance(); // just touch to init type

        $html = '';
        $children = $root->getVisibleChildren();
        for($i = 0; $i < count($children); ++$i) {
            $child = $children[$i];
            if ($i == 0) {
                $child->setIsFirst(true);
            }
            else {
                $child->setIsFirst(false);
            }
            if ($i == count($children) - 1) {
                $child->setIsLast(true);
            }
            else {
                $child->setIsLast(false);
            }
            $html .= $this->helper('navigator')->getNodeHtml($child);
        }
        return $html;
    }
}