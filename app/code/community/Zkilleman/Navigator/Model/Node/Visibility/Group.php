<?php

class Zkilleman_Navigator_Model_Node_Visibility_Group
    extends Zkilleman_Navigator_Model_Node_Visibility_Abstract
{
    const CONDITION_AGGREGATOR_ALL = 'all';
    const CONDITION_AGGREGATOR_ANY = 'any';
    const CONDITION_VALUE_TRUE     = 'true';
    const CONDITION_VALUE_FALSE    = 'false';

    protected $_children;
    protected $_conditionAggregator;
    protected $_conditionValue;

    protected function _construct()
    {
        parent::_construct();
        $this->_children = array();
        $this->_conditionAggregator = self::CONDITION_AGGREGATOR_ALL;
        $this->_conditionValue = self::CONDITION_VALUE_TRUE;
        $this->_renderer = 'navigator/form/visibility/group.phtml';
        $this->_initConditions();
    }

    protected function _initConditions()
    {
        $id = $this->getCondId();
        $thisData = $this->getData($id);

        if (!is_array($thisData))
            return;

        if (isset($thisData['type']))
            $this->setType($thisData['type']);

        if (isset($thisData['aggregator']))
            $this->_conditionAggregator = $thisData['aggregator'];

        if (isset($thisData['value']))
            $this->_conditionValue = $thisData['value'];

        // this could become quite expensive for large conditions
        // maybe put tree data in a separate container?
        //TODO: contains duplicate content, please refactor
        $childPattern = "/^{$id}--\d+$/";
        foreach ($this->getData() as $cId => $cCond) {
            if (preg_match($childPattern, $cId)) {
                $args = $cCond;
                $args['cond_id'] = $cId;
                $args[$cId] = $cCond;
                
                $grandChildPattern = "/^{$cId}(--\d+)+$/";
                foreach ($this->getData() as $gcId => $gcCond) {
                    if (preg_match($grandChildPattern, $gcId)) {
                        $args[$gcId] = $gcCond;
                    }
                }
                $model = self::getModelByCode($cCond['type'], $args);
                $this->_children[] = $model;
            }
        }
    }

    public function getChildren()
    {
        return $this->_children;
    }

    public function getAggregator()
    {
        return $this->_conditionAggregator;
    }

    public function getValue()
    {
        return $this->_conditionValue;
    }

    public function getAggregatorOptions()
    {
        return array(
            self::CONDITION_AGGREGATOR_ALL => Mage::helper('navigator')->__('ALL'),
            self::CONDITION_AGGREGATOR_ANY => Mage::helper('navigator')->__('ANY')
        );
    }

    public function getSelectedAggregatorLabel()
    {
        $opts = $this->getAggregatorOptions();
        return $opts[$this->getAggregator()];
    }

    public function getValueOptions()
    {
        return array(
            self::CONDITION_VALUE_TRUE => Mage::helper('navigator')->__('TRUE'),
            self::CONDITION_VALUE_FALSE => Mage::helper('navigator')->__('FALSE')
        );
    }

    public function getSelectedValueLabel()
    {
        $opts = $this->getValueOptions();
        return $opts[$this->getValue()];
    }

    public function getAvailableConditions()
    {
        $types = self::getTypes();
        $result = array();
        foreach($types as $code => $type) {
            $result[$code] = $type['label'];
        }
        return $result;
    }

    public function assert()
    {
        foreach($this->_children as $child) {
            $cResult = (bool)$child->assert();
            if($this->_conditionValue == self::CONDITION_VALUE_FALSE) {
                $cResult = !$cResult;
            }
            // if everything is supposed to be true but something is false: fail
            if(($this->_conditionAggregator == self::CONDITION_AGGREGATOR_ALL) &&
                    ($cResult == false)) {
                return false;
            }
            // if one true is enough and one found: win
            if(($this->_conditionAggregator == self::CONDITION_AGGREGATOR_ANY) &&
                    ($cResult == true)) {
                return true;
            }
        }
        // if there are no children we should return success
        return (count($this->_children) == 0) ||
                // if there was an ALL-fail we hav already left the building
                ($this->_conditionAggregator == self::CONDITION_AGGREGATOR_ALL);
    }
}