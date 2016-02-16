<?php

class Epam_Layered_Model_Source_Elements extends Varien_Object
{
    const EL_MULTISELECT            = 1;//Uses in LN
    const EL_DROPDOWN               = 2;//Uses in LN
    const EL_PRICE                  = 3;//Uses in LN//The same as `Decimal` attr
    //const EL_TEXT_INPUT             = 4;
    //const EL_TEXT_AREA              = 5;
    //const EL_DATE                   = 6;
    //const EL_BOOLEAN                = 7;

    public function getAllOptions()
    {
        return array(
            //self::EL_TEXT_INPUT     => Mage::helper('layered')->__('Text Input'),
            //self::EL_TEXT_AREA      => Mage::helper('layered')->__('Text Area'),
            //self::EL_DATE           => Mage::helper('layered')->__('Date'),
            //self::EL_BOOLEAN        => Mage::helper('layered')->__('Boolean'),
            self::EL_MULTISELECT    => Mage::helper('layered')->__('Multiselect'),
            self::EL_DROPDOWN       => Mage::helper('layered')->__('Dropdown'),
            self::EL_PRICE          => Mage::helper('layered')->__('Price')
        );
    }
}
