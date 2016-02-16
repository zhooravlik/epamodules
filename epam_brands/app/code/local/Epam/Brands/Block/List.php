<?php

/**
 * Class Epam_Brands_Block_List
 */
class Epam_Brands_Block_List extends Epam_Brands_Block_Base
{
    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $hlp    = Mage::helper('ebrands');

        if ($bCrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $bCrumbs->addCrumb(
                'home',
                array(
                    'label' => $hlp->__('Home'),
                    'title' => $hlp->__('Go to Homepage'),
                    'link'  => Mage::getBaseUrl(),
                )
            );
            $bCrumbs->addCrumb(
                'brands_list',
                array(
                    'label' => $hlp->__('Brands'),
                    'title' => $hlp->__('Brands'),
                )
            );
        }
        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $head->addItem('skin_css', 'css/epam/brands.css');

            $head->setTitle($hlp->getDefaultTitle());
            $head->setDescription($hlp->getDefaultMetaDescription());
            $head->setKeywords($hlp->getDefaultMetaKeywords());
        }
        return parent::_prepareLayout();
    }
}
