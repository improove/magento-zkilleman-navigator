<?php

class Improove_Navigator_Block_Node extends Mage_Core_Block_Template
{
    protected static $_renderer = 'navigator/node.phtml';

    protected $_node;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate(self::$_renderer);
        $this->_node = null;
    }

    public function setNode(Improove_Navigator_Model_Node &$node)
    {
        $this->_node = $node;
        return $this;
    }

    public function getNode()
    {
        return $this->_node;
    }

    public function getClassNames()
    {
        $classNames = array();
        $node = $this->getNode();
        if(!$node)
            return $classNames;

        $classNames[] = preg_replace(
            '/[^a-z]+/',
            '-',
            strtolower($node->getContentType())
        );

        $classNames[] = 'level'.$node->getLevel();

        if($node->hasChildren())
                $classNames[] = 'parent';

        if($node->getIsFirst())
                $classNames[] = 'first';

        if($node->getIsLast())
                $classNames[] = 'last';

        if($node->isInCurrentPath())
                $classNames[] = 'active';

        return $classNames;
    }

    public function getNodeContentHtml()
    {
        $block = $this->getNode()->getContentTypeInstance()->getContentBlock();
        return $block->toHtml();
        //return $this->_node->getTitle();
    }

    public function getShowTemplateHints()
    {
        return false;
    }

    public function getChildNodesHtml()
    {
        if($this->_node == null || !$this->_node->hasVisibleChildren())
            return '';

        $html = '';
        $children = $this->_node->getVisibleChildren();
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