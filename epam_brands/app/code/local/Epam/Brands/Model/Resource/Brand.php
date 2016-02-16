<?php

/**
 * Class Epam_Brands_Model_Resource_Brand
 */
class Epam_Brands_Model_Resource_Brand extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        $this->_init('ebrands/brand', 'brand_id');
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$this->isUniqueBrandToStores($object)) {
            Mage::throwException(
                Mage::helper('ebrands')->__('A brand URL key for specified store already exists.')
            );
        }

        if ($this->isNumericBrandIdentifier($object)) {
            Mage::throwException(
                Mage::helper('ebrands')->__('The Brand URL key cannot consist only of numbers.')
            );
        }
        // modify create / update dates
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @return int
     */
    protected function isNumericBrandIdentifier(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function isUniqueBrandToStores(Mage_Core_Model_Abstract $object)
    {
        if (Mage::app()->isSingleStoreMode() || !$object->hasData('store_ids')) {
            $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
        } else {
            $stores = (array)$object->getData('store_ids');
        }

        $selectId = $this->_getSimilarBrand($object->getData('brand'), $stores);
        $fetchedSelectId = $this->_getWriteAdapter()->fetchRow($selectId);

        if (!$object['brand_id']) {
            if ($fetchedSelectId['brand_id']) {
                return false;
            }
        } elseif (
            $object['brand'] == $fetchedSelectId['brand']
            && $object['brand_id'] != $fetchedSelectId['brand_id']
        ) {
            return false;
        }

        $select = $this->_getLoadByIdentifierSelect($object->getData('identifier'), $stores);

        if ($object->getId()) {
            $select->where('mps.brand_id <> ?', $object->getId());
        }

        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }
        return true;
    }

    /**
     * @param Epam_Brands_Model_Brand $object
     */
    public function loadStoreIds(Epam_Brands_Model_Brand $object)
    {
        $pollId   = $object->getId();
        $storeIds = array();
        if ($pollId) {
            $storeIds = $this->lookupStoreIds($pollId);
        }
        $object->setStoreIds($storeIds);
    }

    /**
     * @param $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        return $this->_getReadAdapter()->fetchCol(
            $this->_getReadAdapter()->select()
                ->from(
                    $this->getTable(
                        'ebrands/store'
                    ),
                    'store_id'
                )
                ->where("{$this->getIdFieldName()} = :id_field"),
            array(':id_field' => $id)
        );
    }

    /**
     * @param $id
     * @param $storeId
     * @return string
     */
    public function getBrandName($id, $storeId)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_getReadAdapter()->select()
            ->from(array('eaov' => $this->getTable('eav/attribute_option_value')))
            ->join(
                array('eao' => $this->getTable('eav/attribute_option')),
                'eaov.option_id = eao.option_id',
                array()
            )
            ->where('eao.option_id = ?', $id)
            ->where('eaov.store_id IN (?)', $stores);

        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('eaov.value');
        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * String if OK and array if $param isn't exists or empty
     * @param string Param
     * @return string/array
     */
    public function getBrandData($brandId, $param)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('brand_id=?', $brandId);
        $brandData = $this->_getReadAdapter()->fetchRow($select);
        if (strlen($param)) {
            $paramData = @$brandData[@$param];
            return (strlen($paramData)) ? $paramData : $brandData;
        } else {
            return $this->_getReadAdapter()->fetchRow($select);
        }
    }

    /**
     * @param $id
     * @param $store
     * @param null $isActive
     * @return Varien_Db_Select
     */
    protected function _getSimilarBrand($id, $store, $isActive = null)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('mp' => $this->getMainTable()))
            ->join(
                array('mps' => $this->getTable('ebrands/store')),
                'mp.brand_id = mps.brand_id',
                array()
            )
            ->where('mp.brand = ?', $id)
            ->where('mps.store_id IN (?)', $store);

        if (!is_null($isActive)) {
            $select->where('mp.status = ?', $isActive);
        }
        return $select;
    }

    /**
     * @param $identifier
     * @param $store
     * @param null $isActive
     * @return Varien_Db_Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('mp' => $this->getMainTable()))
            ->join(
                array('mps' => $this->getTable('ebrands/store')),
                'mp.brand_id = mps.brand_id',
                array()
            )
            ->where('mp.identifier = ?', $identifier)
            ->where('mps.store_id IN (?)', $store);

        if (!is_null($isActive)) {
            $select->where('mp.status = ?', $isActive);
        }
        return $select;
    }

    /**
     * @param $identifier
     * @param $storeId
     * @return string
     */
    public function checkIdentifier($identifier, $storeId)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('mp.brand_id')
            ->order('mps.store_id DESC')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     */
    public function saveBrandStore(Mage_Core_Model_Abstract $object)
    {
        /** stores */
        $deleteWhere = $this->_getReadAdapter()->quoteInto('brand_id = ?', $object->getId());
        $this->_getReadAdapter()->delete($this->getTable('ebrands/store'), $deleteWhere);

        foreach ($object->getStoreIds() as $storeId) {
            $brandStoreData = array(
                'brand_id'   => $object->getId(),
                'store_id'  => $storeId
            );
            $this->_getWriteAdapter()->insert($this->getTable('ebrands/store'), $brandStoreData);
        }
    }
}
