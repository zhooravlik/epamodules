<?php

/**
 * Class Epam_Brands_Model_Url
 */
class Epam_Brands_Model_Url extends Varien_Object
{
    /**
     * @param $str
     * @return mixed|string
     */
    public function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('ebrands/url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
    }
}
