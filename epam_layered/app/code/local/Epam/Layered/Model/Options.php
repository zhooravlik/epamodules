<?php
class Epam_Layered_Model_Options extends Mage_Core_Model_Abstract
{
    protected $_requestVar = null;

    protected function _construct()
    {
        $this->_init('layered/options');
        parent::_construct();
    }

    public function getAttrAppearance($curAttrId = 0)
    {
        return $this->getResource()->getAttrAppearance($curAttrId);
    }

    public function setAttrAppearance($curAttrId)
    {
        return $this->getResource()->setAttrAppearance($curAttrId);
    }

    public function saveAttributeAppearance($attrId, $optionId)
    {
        return $this->getResource()->saveAttributeAppearance($attrId, $optionId);
    }
}
