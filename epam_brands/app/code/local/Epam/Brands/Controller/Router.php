<?php

/**
 * Class Epam_Brands_Controller_Router
 */
class Epam_Brands_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard
{
    /**
     *
     */
    const NAME_MODULE = 'brands';
    /**
     *
     */
    const NAME_CONTROLLER = 'index';
    /**
     *
     */
    const NAME_ACTION = 'view';
    /**
     *
     */
    const NAME_PARAM = 'brand_id';

    /**
     * Initialize Controller Router
     *
     * @param Varien_Event_Observer $observer
     */
    public function initControllerRouters($observer)
    {
        /* @var $front Mage_Core_Controller_Varien_Front */
        $front = $observer->getEvent()->getFront();

        $front->addRouter('brands', $this);
    }

    /**
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        $router = 'brands';
        $identifier = trim(str_replace('/' . $router . '/', '', $request->getPathInfo()), '/');

        $condition = new Varien_Object(
            array(
                'identifier' => $identifier,
                'continue'   => true
            )
        );
        $identifier = $condition->getIdentifier();

        $brand = Mage::getModel('ebrands/brand');
        $brandId = $brand->checkIdentifier($identifier, Mage::app()->getStore()->getId());
        if (trim($identifier) && $brandId) {
            $request->setModuleName(self::NAME_MODULE)
                ->setControllerName(self::NAME_CONTROLLER)
                ->setActionName(self::NAME_ACTION)
                ->setParam(self::NAME_PARAM, $brandId);
            $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                $router . '/' . $identifier
            );
            return true;
        }
        return false;
    }
}
