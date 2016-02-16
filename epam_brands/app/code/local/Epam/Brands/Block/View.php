<?php

/**
 * Class Epam_Brands_Block_View
 */
class Epam_Brands_Block_View extends Mage_Catalog_Block_Product_Abstract
{

    /**
     * @var
     */
    protected $_brandsCollection;

    /**
     * @var
     */
    protected $_brand;

    /**
     * @var string
     */
    protected $_defToolbarBlock = 'catalog/product_list_toolbar';


    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_prepareBrand();
        $this->_prepareBrandsCollection();
    }

    /**
     * @throws Exception
     */
    protected function _prepareBrand()
    {
        $brandId = $this->getRequest()->getParam('brand_id', false);
        $this->_brand = Mage::helper('ebrands/brand')->getBrand($brandId);
    }

    /**
     *
     */
    protected function _prepareBrandsCollection()
    {
        $this->_brandsCollection = Mage::helper('ebrands/brand')
            ->getBrandsCollectionView($this->getBrand()->getBrand());
        $this->_brandsCollection = $this->_addProductAttributesAndPrices($this->_brandsCollection);
    }

    /**
     * @return Mage_Core_Model_Abstract
     * @throws Exception
     */
    public function getBrand()
    {
        return $this->_brand;
    }

    /**
     * @return mixed
     */
    public function getBrandsCollection()
    {
        return $this->_brandsCollection;
    }

    /**
     * @return Mage_Catalog_Block_Product_Abstract
     */
    protected function _prepareLayout()
    {
        $brand  = $this->getBrand();
        $hlp    = Mage::helper('ebrands');

        if ($bCrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $bCrumbs->addCrumb(
                'home',
                array(
                    'label' => $hlp->__('Home'),
                    'title' => $hlp->__('Go to Homepage'),
                    'link'=>Mage::getBaseUrl()
                )
            );
            $bCrumbs->addCrumb(
                'brands_list',
                array(
                    'label' => $hlp->__('Brands'),
                    'title' => $hlp->__('Brands'),
                    'link'=>Mage::getUrl('brands')
                )
            );
            $bCrumbs->addCrumb(
                'brands_view',
                array(
                    'label' => Mage::getModel('ebrands/brand')
                        ->getBrandName($brand->getBrand(), Mage::app()->getStore()->getId()),
                    'title' => $brand->getIdentifier()
                )
            );
        }

        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $head->addItem('skin_css', 'css/epam/brands.css');

            $head->setTitle($hlp->getBrandData($brand->getId(), 'meta_title'));
            $head->setDescription($hlp->getBrandData($brand->getId(), 'meta_description'));
            $head->setKeywords($hlp->getBrandData($brand->getId(), 'meta_keywords'));
        }

        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        $collection = $this->getBrandsCollection();

        if ($orders = $this->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        if ($sort = $this->getSortBy()) {
            $toolbar->setDefaultOrder($sort);
        }
        if ($dir = $this->getDefaultDirection()) {
            $toolbar->setDefaultDirection($dir);
        }
        if ($modes = $this->getModes()) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        Mage::dispatchEvent(
            'catalog_block_product_list_collection',
            array(
                'collection' => $this->getBrandsCollection()
            )
        );

        $this->setProductCollection($collection);

        return parent::_beforeToHtml();
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    public function getToolbarBlock()
    {
        if ($blockName = $this->getToolbarBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defToolbarBlock, microtime());
        return $block;
    }

    /**
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * @param $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_brandCollection = $collection;
        return $this;
    }
}
