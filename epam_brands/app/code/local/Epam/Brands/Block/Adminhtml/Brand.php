<?php

/**
 * Class Epam_Brands_Block_Adminhtml_Brand
 */
class Epam_Brands_Block_Adminhtml_Brand extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     *
     */
    public function __construct()
    {
        $this->_controller      = 'adminhtml_brand';
        $this->_blockGroup      = 'ebrands';
        $this->_headerText      = Mage::helper('ebrands')->__('Manage Brand');
        $this->_addButtonLabel  = Mage::helper('ebrands')->__('Add Brand');

        parent::__construct();
    }
}
