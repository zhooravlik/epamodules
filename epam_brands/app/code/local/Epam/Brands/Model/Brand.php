<?php

/**
 * Class Epam_Brands_Model_Brand
 */
class Epam_Brands_Model_Brand extends Mage_Core_Model_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('ebrands/brand');
    }

    /**
     * @param $identifier
     * @param $storeId
     * @return mixed
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        if ($this->hasStoreIds()) {
            $this->_getResource()->saveBrandStore($this);
        }
        return parent::_afterSave();
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function addStoreId($storeId)
    {
        $ids = $this->getStoreIds();
        if (!in_array($storeId, $ids)) {
            $ids[] = $storeId;
        }
        $this->setStoreIds($ids);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStoreIds()
    {
        $ids = $this->_getData('store_ids');
        if (is_null($ids)) {
            $this->loadStoreIds();
            $ids = $this->getData('store_ids');
        }
        return $ids;
    }

    /**
     *
     */
    public function loadStoreIds()
    {
        $this->_getResource()->loadStoreIds($this);
    }

    /**
     * @param $error
     */
    public function addError($error)
    {
        $this->_errors[] = $error;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     *
     */
    public function resetErrors()
    {
        $this->_errors = array();
    }

    /**
     * @param $error
     * @param null $line
     * @return bool
     */
    public function printError($error, $line = null)
    {
        if ($error == null) {
            return false;
        }
        $img = 'error_msg_icon.gif';
        $liStyle = 'background-color:#FDD; ';
        echo '<li style="'.$liStyle.'">';
        echo '<img src="'.Mage::getDesign()->getSkinUrl('images/'.$img).'" class="v-middle"/>';
        echo $error;
        if ($line) {
            echo '<small>, Line: <b>'.$line.'</b></small>';
        }
        echo "</li>";
    }

    /**
     * @param $id
     * @param $storeId
     * @return mixed
     */
    public function getBrandName($id, $storeId)
    {
        return $this->_getResource()->getBrandName($id, $storeId);
    }

    /**
     * @param $brandId
     * @param $param
     * @return mixed
     */
    public function getBrandData($brandId, $param)
    {
        return $this->_getResource()->getBrandData($brandId, $param);
    }
}
