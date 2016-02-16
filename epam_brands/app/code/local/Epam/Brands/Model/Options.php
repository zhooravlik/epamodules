<?php

/**
 * Class Epam_Brands_Model_Options
 */
class Epam_Brands_Model_Options extends Mage_Core_Model_Abstract
{
    /**
     * @return mixed
     */
    public function getBrands()
    {
        $attrName   = Mage::helper('ebrands')->getBrandsAttributeName();
        $attr       = Mage::getModel('eav/config')->getAttribute('catalog_product', $attrName);

        foreach ($attr->getSource()->getAllOptions(true, true) as $opt) {
            $attrArray[$opt['value']] = $opt['label'];
        }
        return($attrArray);
    }
}
