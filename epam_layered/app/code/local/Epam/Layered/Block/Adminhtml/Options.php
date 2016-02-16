<?php
class Epam_Layered_Block_Adminhtml_Options extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('layered')->__('Custom Appearance');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('layered')->__('Custom Appearance');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        //$currentAttribute = Mage::registry("entity_attribute");
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    public function getOptions()
    {
        $filterableAttributes = Mage::getModel('layered/source_elements')->getAllOptions();
        asort($filterableAttributes);

        return $filterableAttributes;
    }

    public function canShowOptions()
    {
        $currentAttribute = Mage::registry("entity_attribute");
        return $currentAttribute->getData('is_filterable');
    }

}
