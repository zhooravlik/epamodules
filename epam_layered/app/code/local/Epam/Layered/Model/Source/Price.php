<?php
class Epam_Layered_Model_Source_Price extends Varien_Object
{
    const PT_DEFAULT    = 0;
    const PT_SLIDER     = 1;

    public function toOptionArray()
    {
        $hlp = Mage::helper('layered');
        return array(
            array('value' => self::PT_DEFAULT,  'label' => $hlp->__('Default Price Ranges')),
            array('value' => self::PT_SLIDER,   'label' => $hlp->__('jQuery UI Slider')),
        );
    }
}
