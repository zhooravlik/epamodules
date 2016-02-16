<?php

class Epam_Layered_Model_Observer
{
    public function storeCustomAppearance(Varien_Event_Observer $observer)
    {
        $attrId     = intval(Mage::app()->getRequest()->getParam('attribute_id'));
        $optionId   = intval(Mage::app()->getRequest()->getParam('custom_appearance'));

        if (intval($optionId) == 0){//if nil, then default appearance and nothing storing into the DB
            return true;
        }

        if ($attrId && $optionId){
            $attrModel = Mage::getModel('layered/options');
            $attrModel->saveAttributeAppearance($attrId, $optionId);
            return true;
        } else {
            Mage::getSingleton("checkout/session")
                ->addError('Something going wrong while custom attribute appearance saving. Please try again later.');
            return false;
        }
    }
}
