<?php

/**
 * Class Epam_Brands_Block_Home
 */
class Epam_Brands_Block_Home extends Epam_Brands_Block_Base
{

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_brandsCollection->addFieldToFilter('is_display_home', Epam_Brands_Model_Source_Status::STATUS_ENABLED);
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $head = $this->getLayout()->getBlock('head');
        $head->addItem('skin_css', 'css/epam/brands.css');

        return parent::_prepareLayout();
    }
}
