<?php

class Improove_Navigator_Model_Resource_Eav_Mysql4_Node_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{

    protected $_storeId = null;

    protected function _construct()
    {
        $this->_init('navigator/node');
    }

    public function setStore($store)
    {
        $this->setStoreId(Mage::app()->getStore($store)->getId());
        return $this;
    }

    public function setStoreId($storeId)
    {
        if ($storeId instanceof Mage_Core_Model_Store) {
            $storeId = $storeId->getId();
        }
        $this->_storeId = $storeId;
        return $this;
    }

    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }
        return $this->_storeId;
    }

    public function getDefaultStoreId()
    {
        return 0;
    }

    /**
     * Retrieve attributes load select
     *
     * @param   string $table
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getLoadAttributesSelect($table, $attributeIds = array())
    {
        if (empty($attributeIds)) {
            $attributeIds = $this->_selectAttributes;
        }
        if ((int) $this->getStoreId()) {
            $entityIdField = $this->getEntity()->getEntityIdField();
            $joinCondition = 'store.attribute_id=default.attribute_id
                AND store.entity_id=default.entity_id
                AND store.store_id='.(int) $this->getStoreId();

            $select = $this->getConnection()->select()
                ->from(array('default'=>$table), array($entityIdField, 'attribute_id', 'default_value'=>'value'))
                ->joinLeft(
                    array('store'=>$table),
                    $joinCondition,
                    array(
                        'store_value' => 'value',
                        'value' => new Zend_Db_Expr('IF(store.value_id>0, store.value, default.value)')
                    )
                )
                ->where('default.entity_type_id=?', $this->getEntity()->getTypeId())
                ->where("default.$entityIdField in (?)", array_keys($this->_itemsById))
                ->where('default.attribute_id in (?)', $attributeIds)
                ->where('default.store_id = 0');
        }
        else {
            $select = parent::_getLoadAttributesSelect($table)
                ->where('store_id=?', $this->getDefaultStoreId());
        }
        return $select;
    }

    /**
     * Adding join statement to collection select instance
     *
     * @param   string $method
     * @param   object $attribute
     * @param   string $tableAlias
     * @param   array $condition
     * @param   string $fieldCode
     * @param   string $fieldAlias
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias)
    {
        if (isset($this->_joinAttributes[$fieldCode]['store_id'])) {
            $store_id = $this->_joinAttributes[$fieldCode]['store_id'];
        }
        else {
            $store_id = $this->getStoreId();
        }

        if ($store_id != $this->getDefaultStoreId() && !$attribute->isScopeGlobal()) {
            /**
             * Add joining default value for not default store
             * if value for store is null - we use default value
             */
            $defCondition = '('.join(') AND (', $condition).')';
            $defAlias     = $tableAlias.'_default';
            $defFieldCode = $fieldCode.'_default';
            $defFieldAlias= str_replace($tableAlias, $defAlias, $fieldAlias);

            $defCondition = str_replace($tableAlias, $defAlias, $defCondition);
            $defCondition.= $this->getConnection()->quoteInto(" AND $defAlias.store_id=?", $this->getDefaultStoreId());

            $this->getSelect()->$method(
                array($defAlias => $attribute->getBackend()->getTable()),
                $defCondition,
                array()
            );

            $method = 'joinLeft';
            $fieldAlias = new Zend_Db_Expr("IF($tableAlias.value_id>0, $fieldAlias, $defFieldAlias)");
            $this->_joinAttributes[$fieldCode]['condition_alias'] = $fieldAlias;
            $this->_joinAttributes[$fieldCode]['attribute']       = $attribute;
        }
        else {
            $store_id = $this->getDefaultStoreId();
        }
        $condition[] = $this->getConnection()->quoteInto("$tableAlias.store_id=?", $store_id);
        return parent::_joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias);
    }
}