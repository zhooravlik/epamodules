<?php
class Epam_Layered_Model_Layer extends Mage_Catalog_Model_Layer
{
    public function getFilterableAttributes()
    {
        /** @var $collection Mage_Catalog_Model_Resource_Product_Attribute_Collection */
        $collection = Mage::getResourceModel('catalog/product_attribute_collection');
        $collection
            ->setItemObjectClass('catalog/resource_eav_attribute')
            ->addStoreLabel(Mage::app()->getStore()->getId())
            ->setOrder('position', 'ASC');
        $collection = parent::_prepareAttributeCollection($collection);
        $collection->load();

        $filterableAttributes = $collection->getData('items');

        return $filterableAttributes;
    }
}
