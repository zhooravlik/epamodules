<?php
class Epam_Layered_Block_Ajax extends Mage_Core_Block_Template
{
    public function __construct()
    {
        $this->config = Mage::getStoreConfig('layered');
        $this->url = Mage::getStoreConfig('web/unsecure/base_url');

        $this->ajaxSlider = $this->config['price_slider']['use_ajax'];
        $this->loadingText = Mage::helper('layered')->__('Loading... Please wait');
        $this->loadingImage = $this->url.'media/epam/layered/ajax-loader.gif';
    }

    public function getCallbackJs()
    {
        return Mage::getStoreConfig('layered/price_slider/after_ajax');
    }
}
