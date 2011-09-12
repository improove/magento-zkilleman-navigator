<?php

class Zkilleman_Navigator_Model_Node_Visibility
    extends Zkilleman_Navigator_Model_Node_Visibility_Abstract
{
    protected $_node;
    protected $_visibilityInstance;

    protected function _construct()
    {
        parent::_construct();
        $this->_node = null;
        $this->_visibilityInstance = null;
    }

    public function setNode(Zkilleman_Navigator_Model_Node &$node)
    {
        $this->_node = $node;
        $data = array();
        try {
            $nodeData = unserialize($node->getData('visibility'));
            if(is_array($nodeData) && isset($nodeData['conditions'])
                    && is_array($nodeData['conditions'])) {
                $data = $nodeData['conditions'];
            }
        }
        catch (Exception $e) {
            $data = array();
        }

        $data['cond_id'] = '1';

        $this->_visibilityInstance = self::getModelByCode(
            'group',
            $data
        );
        return $this;
    }
    
    public function assert()
    {
        return $this->_visibilityInstance->assert();
    }

    public function getEditBlock()
    {
        return $this->_visibilityInstance->getEditBlock();
    }
}