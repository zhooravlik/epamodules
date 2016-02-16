<?php

/**
 * Class Epam_Brands_Block_Base
 */
class Epam_Brands_Block_Base extends Mage_Core_Block_Template
{
    /**
     * @var
     */
    protected $_brandsCollection;

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_prepareBrandsCollection();
    }

    /**
     *
     */
    protected function _prepareBrandsCollection()
    {
        $this->_brandsCollection = Mage::helper('ebrands/brand')->getBrandsCollection();
    }

    /**
     * @return mixed
     */
    public function getBrandsCollection()
    {
        return $this->_brandsCollection;
    }
}