<?php

/**
 * Class Epam_Brands_IndexController
 */
class Epam_Brands_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     *
     */
    const XML_PATH_ENABLED = 'ebrands/general/is_enabled';

    /**
     *
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (! Mage::getStoreConfigFlag(self::XML_PATH_ENABLED)) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }
    }

    /**
     *
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     *
     */
    public function viewAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
