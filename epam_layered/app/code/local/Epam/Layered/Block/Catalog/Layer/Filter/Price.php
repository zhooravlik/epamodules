<?php
class Epam_Layered_Block_Catalog_Layer_Filter_Price extends Epam_Layered_Block_Catalog_Layer_Filter_Price_Adapter
{
    public $_currentCategory;
    public $_searchSession;
    public $_productCollection;
    public $_maxPrice;
    public $_minPrice;
    public $_currMinPrice;
    public $_currMaxPrice;
    public $_imagePath;

    public function __construct()
    {
        $this->_currentCategory = Mage::registry('current_category');
        $this->_searchSession = Mage::getSingleton('catalog/session');
        $this->setProductCollection();
        $this->setMinPrice();
        $this->setMaxPrice();
        $this->setCurrentPrices();
        $this->_imagePath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'layered/slider/';//TODO:move from skin/.../css/images

        parent::__construct();
    }

    public function getSliderStatus()
    {
        return Mage::getStoreConfig('layered/price_slider/price_type');
    }

    public function showFromTo()
    {
        return $this->getConfig('layered/price_slider/show_fromto');
    }

    public function setNewPrices()
    {
        $this->setInSession('_newCurrMinPrice', $this->_currMinPrice);
        $this->setInSession('_newCurrMaxPrice', $this->_currMaxPrice);

        if (! is_numeric($this->_currMinPrice)) {
            $this->_currMinPrice = 0;
            $this->setInSession('_currMinPrice', 0);
        }

        if (! is_numeric($this->_currMaxPrice)) {
            $this->_currMaxPrice = 0;
            $this->setInSession('_currMaxPrice', 0);
        }

        $sMin = $this->getFromSession('_minPrice');
        $sMax = $this->getFromSession('_maxPrice');
        $csMin = $this->getFromSession('_currMinPrice');
        $csMax = $this->getFromSession('_currMaxPrice');
        $ncsMin = $this->getFromSession('_newCurrMinPrice');
        $ncsMax = $this->getFromSession('_newCurrMaxPrice');

        // if Filters are called
        $a[0][] = 'price_index.min_price';
        $a[0][] = 'ASC';
        $loadedCollection = $this->getLayout()
            ->getBlockSingleton('catalog/product_list')
            ->getLoadedProductCollection()
            ->setOrder('min_price','DESC')
            ->getSelect()
            ->setPart('order',$a)
            ->query()
            ->fetchAll();

        $tot = count($loadedCollection);

        if (count($loadedCollection) > 0) {
            $loadedMin = $loadedCollection[0]['min_price'];
            $loadedMax = $loadedCollection[$tot - 1]['min_price'];
        }

        if ($this->_currMinPrice == $csMin && $this->_currMaxPrice == $csMax) {
            if ($this->_minPrice != $ncsMin) {
                $this->setInSession('_minPrice', $loadedMin);
                $this->_minPrice = $loadedMin;
            }
            if ($loadedMin >= $csMin) {
                $this->_currMinPrice = $loadedMin;
                $this->setInSession('_currMinPrice', $loadedMin);
            }
            if ($this->_maxPrice != $ncsMax) {
                $this->setInSession('_maxPrice', $loadedMin);
                $this->_maxPrice = $loadedMax;
            }
            if ($loadedMax <= $csMax) {
                $this->_currMaxPrice = $loadedMax;
                $this->setInSession('_currMaxPrice', $loadedMax);
            }
        } else {
            if ($ncsMin == $loadedMin) {
                $this->setInSession('_minPrice', $loadedMin);
                $this->_minPrice = $loadedMin;
            }
            if ($ncsMax == $loadedMax) {
                $this->setInSession('_maxPrice', $loadedMin);
                $this->_maxPrice = $loadedMax;
            }
        }
    }

    public function getPriceDisplayType()
    {
        if ($this->showFromTo()) {
            $html = '
                <div class="text-box">
                        <span class="priceCurrency">'.$this->getCurrencySymbol().' </span>
                        <input type="text" name="min" id="minPrice" class="priceTextBox" value="'.$this->getCurrMinPrice().'" />
                        <span class="priceCurrency"> - '.$this->getCurrencySymbol().' </span>
                        <input type="text" name="max" id="maxPrice" class="priceTextBox" value="'.$this->getCurrMaxPrice().'" />
                    <input type="button" value="' . Mage::helper('layered')->__('Find') . '" name="go" class="goBtn button" />
                    <input type="hidden" name="price" id="amount" readonly="readonly" style="background:none; border:none;" value="'.$this->getCurrencySymbol().$this->getCurrMinPrice()." - ".$this->getCurrencySymbol().$this->getCurrMaxPrice().'" />
                </div>';
        } else {
            $html = '<p>
                <input
                    type="text" id="amount" class="layered-amount-label" readonly="readonly"
                    style="background: none; border: none;"
                    value="'.$this->getCurrencySymbol().$this->getCurrMinPrice()." - ".$this->getCurrencySymbol().$this->getCurrMaxPrice().'" />
                </p>';//TODO:change as in ILN
        }
        return $html;
    }

    public function getHtml(){
        if ($this->getSliderStatus()) {
            $this->setNewPrices();
            $text='
                <div class="price">

                    <div id="slider-range"></div>
                    '.$this->getPriceDisplayType().'
                </div>'.$this->getSliderJs();//TODO:add the `getSliderJs()` function
            //TODO:change as in ILN

            return $text;
        }
    }

    public function prepareParams(){
        $url="";
        $params = $this->getRequest()->getParams();

        foreach ($params as $key => $val) {
            if ($key == 'id') {
                continue;
            }
            if ($key == 'price') {
                continue;
            }
            if (is_array($val)) {
                $attrModel = Mage::getSingleton('layered/catalog_layer_filter_attribute');
                $valStr = implode($attrModel::QS_DELIMITER, $val);

                $url .= '&' . $key . '=' . $valStr;
            } else {
                $url .= '&' . $key . '=' . $val;
            }
        }

        return $url;
    }

    public function getCurrencySymbol()
    {
        return Mage::app()
            ->getLocale()
            ->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
            ->getSymbol();
    }

    public function getCurrMinPrice()
    {
        if ($this->_currMinPrice > 0) {
            $min = $this->_currMinPrice;
        } else {
            $min = $this->_minPrice;
        }

        return $this->getRoundPriceVal($min, 'down');
    }

    public function getCurrMaxPrice()
    {
        if ($this->_currMaxPrice > 0) {
            $max = $this->_currMaxPrice;
        } else {
            $max = $this->_maxPrice;
        }

        return $this->getRoundPriceVal($max, 'up');
    }

    protected function getRoundPriceVal($float, $mode = '')
    {
        switch ($mode) {
            case 'down':
                return round($float, 0, PHP_ROUND_HALF_DOWN);
            case 'up':
                return round($float, 0, PHP_ROUND_HALF_UP);
            default:
                return round($float, 0, PHP_ROUND_HALF_EVEN);
        }
    }

    /*
    * Gives you the current url without parameters
    *
    * @return url
    */
    public function getCurrentUrlWithoutParams()
    {
        $baseUrl = explode('?', Mage::helper('core/url')->getCurrentUrl());
        $baseUrl = $baseUrl[0];
        return $baseUrl;
    }

    /*
    * Get the Slider config
    *
    * @return object
    */
    public function getConfig($key)
    {
        return Mage::getStoreConfig($key);
    }

    /*
    * Set the Actual Min Price of the search and catalog collection
    *
    * @use category | search collection
    */
    public function setMinPrice()
    {
        if (
            (isset($_GET['q']) && !isset($_GET['min'])) ||
            ! isset($_GET['q'])
        ) {
            if (Mage::getVersion() < '1.7.0.2') {
                $this->_productCollection->getSelect()->reset('order');
                $this->_productCollection->getSelect()->order('final_price','asc');
                $this->_minPrice = round($this->_productCollection->getFirstItem()->getFinalPrice());
            } else {
                $this->_minPrice = floor($this->_productCollection->getMinPrice());
            }

            $this->_searchSession->setMinPrice($this->_minPrice);
        } else {
            $this->_minPrice = $this->_searchSession->getMinPrice();
        }
    }

    /*
    * Set the Actual Max Price of the search and catalog collection
    *
    * @use category | search collection
    */
    public function setMaxPrice()
    {
        if (
            (isset($_GET['q']) && !isset($_GET['max'])) ||
            ! isset($_GET['q'])
        ) {
            if (Mage::getVersion() < '1.7.0.2') {
                $this->_productCollection->getSelect()->reset('order');
                $this->_productCollection->getSelect()->order('final_price','asc');
                $this->_maxPrice = round($this->_productCollection->getLastItem()->getFinalPrice());
            } else {
                $this->_maxPrice = ceil($this->_productCollection->getMaxPrice());
            }
            $this->_searchSession->setMaxPrice($this->_maxPrice);
        } else {
            $this->_maxPrice = $this->_searchSession->getMaxPrice();
        }

    }

    /*
    * Set the Product collection based on the page server to user
    * Might be a category or search page
    *
    * @set /*
    * Set the Product collection based on the page server to user
    * Might be a category or search page
    *
    * @set Mage_Catalogsearch_Model_Layer
    * @set Mage_Catalog_Model_Layer
    */
    public function setProductCollection()
    {
        if ($this->_currentCategory) {
            $this->_productCollection = $this->_currentCategory
                ->getProductCollection()
                ->addAttributeToSelect('*')
                ->setOrder('price', 'ASC');
        } else {
            $this->_productCollection = Mage::getSingleton('enterprise_search/layer')
                ->getProductCollection()
                ->addAttributeToSelect('*')
                ->setOrder('price', 'ASC');
        }
    }

    /*
    * Set Current Max and Min Prices choosed by the user
    *
    * @set price
    */
    public function setCurrentPrices()
    {
        $priceRange = $this->getRequest()->getParam('price');
        if($priceRange) {
            $minMax = explode('-', $priceRange);

            if ($minMax[0] == '') {
                $minMax[0] = $this->getCurrMinPrice();
                $this->_currMinPrice = $this->getCurrMinPrice();
            } else {
                $this->_currMinPrice = $minMax[0];
            }

            if ($minMax[1] == '') {
                $minMax[1] = $this->getCurrMaxPrice();
                $this->_currMaxPrice = $this->getCurrMaxPrice();
            } else {
                $this->_currMaxPrice = $minMax[1];
            }
        } else {
            $this->_currMinPrice = $this->getCurrMinPrice();
            $this->_currMaxPrice = $this->getCurrMaxPrice();
        }

    }

    /*
    * Set Current Max and Min Prices choosed by the user
    *
    * @set price
    */
    public function baseToCurrent($srcPrice)
    {
        $store = $this->getStore();
        return $store->convertPrice($srcPrice, false, false);
    }

    public function setInSession($variable, $value)
    {
        $set = "set" . $variable;
        Mage::getSingleton('catalog/session')->$set($value);
    }

    public function getFromSession($variable)
    {
        $get = "get" . $variable;
        return Mage::getSingleton('catalog/session')->$get();
    }

    public function getStore()
    {
        return Mage::app()->getStore();
    }

    protected function isAjaxSliderEnabled()
    {
        return $this->getConfig('layered/price_slider/use_ajax');
    }

    protected function getConfigTimeOut()
    {
        return $this->getConfig('layered/price_slider/ajax_timeout');
    }

    /*
    * Get JS that brings the slider in Action
    *
    * @return JavaScript
    */
    public function getSliderJs()
    {
        $baseUrl = $this->getCurrentUrlWithoutParams();
        $timeout = $this->getConfigTimeOut();

        if($this->isAjaxSliderEnabled()){
            $ajaxCall = 'sliderAjax(url, curUrl);';
        } else {
            $ajaxCall = 'if (url != curUrl) {window.location=url;}';
        }

        if ($this->showFromTo()) {
            $updateTextBoxPriceJs = '
                            // Update TextBox Price
                            $("#minPrice").val(newMinPrice);
                            $("#maxPrice").val(newMaxPrice);';
        } else {
            $updateTextBoxPriceJs = '';
        }

        $html = '
            <script type="text/javascript">
                jQuery(function($) {
                /*$j(function($) {*/
                    var newMinPrice, newMaxPrice, url, temp;
                    var curUrl = document.URL;
                    /*adding ranges for available prices*/
                    var categoryMinPrice = ' . $this->getRoundPriceVal((0.9 * $this->_minPrice), 'down') . ';//added ranges
                    var categoryMaxPrice = ' . $this->getRoundPriceVal((1.1 * $this->_maxPrice), 'up') . ';//added ranges
                    function isNumber(n) {
                      return !isNaN(parseFloat(n)) && isFinite(n);
                    }

                    $(".priceTextBox").focus(function(){
                        temp = $(this).val();
                    });

                    $(".priceTextBox").keyup(function(){
                        var value = $(this).val();
                        if(!isNumber(value)){
                            $(this).val(temp);
                        }
                    });

                    $(".priceTextBox").keypress(function(e){
                        if(e.keyCode == 13){
                            var value = $(this).val();
                            if(value < categoryMinPrice || value > categoryMaxPrice){
                                $(this).val(temp);
                            }
                            url = getUrl($("#minPrice").val(), $("#maxPrice").val());
                            '.$ajaxCall.'
                        }
                    });

                    $(".priceTextBox").blur(function(){
                        var value = $(this).val();
                        if(value < categoryMinPrice || value > categoryMaxPrice){
                            $(this).val(temp);
                        }

                    });

                    $(".goBtn").click(function(){
                        url = getUrl($("#minPrice").val(), $("#maxPrice").val());
                        '.$ajaxCall.'
                    });

                    $( "#slider-range" ).slider({
                        range: true,
                        min: categoryMinPrice,
                        max: categoryMaxPrice,
                        values: [ '.$this->getCurrMinPrice().', '.$this->getCurrMaxPrice().' ],
                        slide: function( event, ui ) {
                            newMinPrice = ui.values[0];
                            newMaxPrice = ui.values[1];

                            $( "#amount" ).val( "'.$this->getCurrencySymbol().'" + newMinPrice + " - '.$this->getCurrencySymbol().'" + newMaxPrice );

                            '.$updateTextBoxPriceJs.'

                        },
                        stop: function( event, ui ) {

                            // Current Min and Max Price
                            var newMinPrice = ui.values[0];
                            var newMaxPrice = ui.values[1];

                            // Update Text Price
                            $( "#amount" ).val( "'.$this->getCurrencySymbol().'"+newMinPrice+" - '.$this->getCurrencySymbol().'"+newMaxPrice );

                            '.$updateTextBoxPriceJs.'

                            url = getUrl(newMinPrice,newMaxPrice);
                            if(newMinPrice != '.$this->getCurrMinPrice().' && newMaxPrice != '.$this->getCurrMaxPrice().'){
                                clearTimeout(timer);
                                //window.location= url;

                            }else{
                                    timer = setTimeout(function(){
                                        '.$ajaxCall.'
                                    }, '.$timeout.');
                                }
                        }
                    });

                    function getUrl(newMinPrice, newMaxPrice){
                        return "'.$baseUrl.'"+"?price="+newMinPrice+"-"+newMaxPrice+"'.$this->prepareParams().'";
                    }
                });
            </script>
        ';

        return $html;
    }

    public function getItemsCount()
    {
        if ($this->getSliderStatus()) {
            return true;
        } else {
            $result = parent::getItemsCount();
            if (! $result) {
                return true;
            }
        }
    }
}
