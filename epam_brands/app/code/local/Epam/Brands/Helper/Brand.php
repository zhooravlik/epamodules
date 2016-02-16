<?php

/**
 * Class Epam_Brands_Helper_Collection
 */
class Epam_Brands_Helper_Brand extends Mage_Core_Helper_Abstract
{
    /**
     * @return mixed
     */
    public function getBrandsCollection()
    {
        return Mage::getResourceModel('ebrands/brand_collection')
            ->distinct(true)
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addFieldToFilter('status', Epam_Brands_Model_Source_Status::STATUS_ENABLED)
            ->addOrder('sort_order', 'ASC');
    }

    /**
     * @param $brandId
     * @return Object
     */
    public function getBrandsCollectionView($brandId)
    {
        $brandName  = Mage::helper('ebrands')->getBrandsAttributeName();

        $brandCollection = Mage::getResourceModel('catalog/product_collection');
        $brandCollection->setVisibility(
            Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds()//TODO
        );

        $brandCollection
            ->addStoreFilter()
            ->addAttributeToFilter($brandName, $brandId);

        return $brandCollection;
    }

    /**
     * @param $brandId
     * @return Mage_Core_Model_Abstract
     */
    public function getBrand($brandId)
    {
        if ($brandId) {
            $brand = Mage::getModel('ebrands/brand')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($brandId);
        } else {
            $brand = Mage::getSingleton('ebrands/brand');
        }

        return $brand;
    }
}