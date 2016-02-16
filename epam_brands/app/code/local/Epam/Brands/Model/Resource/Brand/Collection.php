<?php

/**
 * Class Epam_Brands_Model_Resource_Brand_Collection
 */
class Epam_Brands_Model_Resource_Brand_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        $this->_init('ebrands/brand');
        $this->_map['fields']['brand_id'] = 'main_table.brand_id';
        $this->_map['fields']['update_time'] = 'main_table.update_time';
        $this->_map['fields']['status'] = 'main_table.status';
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->getFlag('add_stores_column')) {
            $this->_addStoresVisibility();
        }
        $this->_addBrandName();
        return $this;
    }

    /**
     * @return $this
     */
    public function addStoresVisibility()
    {
        $this->setFlag('add_stores_column', true);
        return $this;
    }

    /**
     * @return $this
     */
    protected function _addStoresVisibility()
    {
        $brandIds = $this->getColumnValues('brand_id');
        $brandStores = array();
        if (sizeof($brandIds)>0) {
            $select = $this->getConnection()->select()
                ->from($this->getTable('ebrands/store'), array('store_id', 'brand_id'))
                ->where('brand_id IN(?)', $brandIds);
            $brandRaw = $this->getConnection()->fetchAll($select);
            foreach ($brandRaw as $brand) {
                if (!isset($brandStores[$brand['brand_id']])) {
                    $brandStores[$brand['brand_id']] = array();
                }

                $brandStores[$brand['brand_id']][] = $brand['store_id'];
            }
        }

        foreach ($this as $item) {
            if (isset($brandStores[$item->getId()])) {
                $item->setStores($brandStores[$item->getId()]);
            } else {
                $item->setStores(array());
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _addBrandName()
    {
        $brandIds = $this->getColumnValues('brand');
        $brands = array();
        if (sizeof($brandIds)>0) {
            $select = $this->getConnection()->select()
                ->from($this->getTable('eav/attribute_option_value'), array('option_id','value'))
                ->where('option_id IN(?)', $brandIds);
            $brandRaw = $this->getConnection()->fetchAll($select);

            foreach ($brandRaw as $brand) {
                $brands[$brand['option_id']] = array();
                $brands[$brand['option_id']] = $brand['value'];
            }
        }

        foreach ($this as $item) {
            if (isset($brands[$item->getBrand()])) {
                $item->setBrand($brands[$item->getBrand()]);
            } else {
                $item->setBrand('');
            }
        }
        return $this;
    }

    /**
     * @param $storeIds
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($storeIds, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter')) {
            if ($withAdmin) {
                $storeIds = array(0, $storeIds);
            }

            $this->getSelect()->join(
                array('store_table' => $this->getTable('ebrands/store')),
                'main_table.brand_id = store_table.brand_id',
                array()
            )
                ->where('store_table.store_id in (?)', $storeIds);
            $this->setFlag('store_filter', true);
        }
        return $this;
    }

    /**
     * @param $brandName
     * @return $this
     */
    public function addBrandNameFilter($brandName)
    {
        if (!$this->getFlag('brand_name_filter')) {
            $this->getSelect()->join(
                array('brand_name_table' => $this->getTable('eav_attribute_option_value')),
                'main_table.brand = brand_name_table.option_id',
                array()
            )
                ->where('brand_name_table.value like (?)', $brandName);

            $this->setFlag('brand_name_filter', true);
        }
        return $this;
    }
}
