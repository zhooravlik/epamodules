<?php

/**
 * Class Epam_Layered_Block_Catalog_Layer_Filter_Attribute
 */
class Epam_Layered_Block_Catalog_Layer_Filter_Attribute extends Epam_Layered_Block_Catalog_Layer_Filter_Attribute_Adapter
{
    /**
     * @return string
     */
    public function getHtml()
    {
        $currentAttributeId = $this->getAttributeModel()->getAttributeId();
        $frontendInput = Mage::getResourceModel('layered/options')->getCustomAttributeAppearance($currentAttributeId);
        if (! $frontendInput) {
            $frontendInput = 0;
        }
        $this->setTemplate(Mage::helper('layered')->getRendererTemplate($frontendInput));

        return parent::_toHtml();
    }
}
