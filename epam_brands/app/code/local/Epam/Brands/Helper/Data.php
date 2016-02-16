<?php

/**
 * Class Epam_Brands_Helper_Data
 */
class Epam_Brands_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     *
     */
    const XML_PATH_ENABLED = 'ebrands/general/is_enabled';
    /**
     *
     */
    const XML_PATH_DEFAULT_ATTRIBUTE_NAME = 'ebrands/general/attr_name';
    /**
     *
     */
    const XML_PATH_DEFAULT_META_TITLE = 'ebrands/meta_info/meta_title';
    /**
     *
     */
    const XML_PATH_DEFAULT_META_KEYWORDS = 'ebrands/meta_info/meta_keywords';
    /**
     *
     */
    const XML_PATH_DEFAULT_META_DESCRIPTION = 'ebrands/meta_info/meta_description';

    /**
     * @param $value
     */
    public function setIsModuleEnabled($value)
    {
        Mage::getModel('core/config')->saveConfig(self::XML_PATH_ENABLED, $value);
    }

    /**
     * @return mixed
     */
    public function getBrandsAttributeName()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_ATTRIBUTE_NAME);
    }

    /**
     * @return mixed
     */
    public function getDefaultTitle()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_META_TITLE);
    }

    /**
     * @return mixed
     */
    public function getDefaultMetaKeywords()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_META_KEYWORDS);
    }

    /**
     * @return mixed
     */
    public function getDefaultMetaDescription()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_META_DESCRIPTION);
    }

    /**
     * @return false|Mage_Core_Model_Abstract
     */
    public function getBlockTemplateProcessor()
    {
        return Mage::getModel('ebrands/template_filter');
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED);
    }

    /**
     * @param int $brandId
     * @param string $param
     * @return bool|mixed
     */
    public function getBrandData($brandId = 0, $param = '')
    {
        if ($brandId > 0) {
            $model = Mage::getModel('ebrands/brand');
            $paramData = $model->getBrandData($brandId, $param);
            if ( !is_array($paramData) && strlen($paramData) ) {//not array and has length
                return $paramData;
            }
            return $this->_getBrandDataByParam($param);
        }
        return false;
    }

    protected function _getBrandDataByParam($param)
    {
        switch ($param) {
            case 'meta_description':
            case 'description':
                return $this->getDefaultMetaDescription();
                break;
            case 'meta_keywords':
            case 'keywords':
                return $this->getDefaultMetaKeywords();
                break;
            case 'meta_title':
            case 'title':
                return $this->getDefaultTitle();
                break;
            default:
                return false;
        }
    }
}
