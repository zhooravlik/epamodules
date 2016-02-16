<?php

/**
 * Class Epam_Brands_Model_Source_Status
 */
class Epam_Brands_Model_Source_Status extends Varien_Object
{
    /**
     *
     */
    const STATUS_ENABLED    = 1;
    /**
     *
     */
    const STATUS_DISABLED   = 0;

    /**
     * @return array
     */
    static public function getAllOptions()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('ebrands')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('ebrands')->__('Disabled')
        );
    }
}
