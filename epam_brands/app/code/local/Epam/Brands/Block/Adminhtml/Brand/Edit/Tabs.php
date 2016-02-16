<?php

/**
 * Class Epam_Brands_Block_Adminhtml_Brand_Edit_Tabs
 */
class Epam_Brands_Block_Adminhtml_Brand_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('brand_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('ebrands')->__('Brand Info'));
    }
}
