<?php

/**
 * Class Epam_Layered_Helper_Data
 */
class Epam_Layered_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     *
     */
    const RT_MULTISELECT    = 'epam/layered/attribute/renderer/multiselect.phtml';    //1
    const RT_DROPDOWN       = 'epam/layered/attribute/renderer/dropdown.phtml';       //2
    //const RT_PRICE          = 'epam/layered/attribute/renderer/price.phtml';          //3
    const RT_PRICE          = 'epam/layered/ajax.phtml';          //3
    const RT_FILTER         = 'epam/layered/attribute/renderer/filter.phtml';         //def

    protected $_solr;

    /**
     * @return string
     */
    public function getRendererTemplate($frontendInput)
    {
        switch ($frontendInput) {
            case 1:
                return self::RT_MULTISELECT;
                break;
            case 2:
                return self::RT_DROPDOWN;
                break;
            case 3:
                return self::RT_PRICE;
                break;
            default:
                return self::RT_FILTER;
        }
    }

    public function optionNameShorter($optionName, $renderer)
    {
        switch ($renderer) {
            //price is not needed because of slider appearance&behavior needs
            case 'multiselect':
                $strLen = 30;
                break;
            case 'dropdown':
                $strLen = 26;
                break;
            case 'filter':
            default:
                $strLen = 32;
        }

        if (strlen($optionName) > $strLen) {
            return substr($optionName, 0, $strLen - 3) . '...';
        } else {
            return $optionName;
        }
    }

    public function isSolr()
    {
        if (isset($this->_solr)) {
            return $this->_solr;
        }

        if ($this->isModuleEnabled('Enterprise_Search')) {
            /** @var Enterprise_Search_Helper_Data $entSearchHelper */
            $entSearchHelper = Mage::helper('enterprise_search');

            $routeName = Mage::app()->getRequest()->getRequestedRouteName();
            if ($routeName == 'catalog' || $routeName == 'layered') {
                $result = $entSearchHelper->getIsEngineAvailableForNavigation(true);
            } elseif ($routeName == 'catalogsearch') {
                $result = $entSearchHelper->getIsEngineAvailableForNavigation(false);
            } elseif ($routeName == 'adminhtml' || is_null($routeName)) {
                $result = $entSearchHelper->isActiveEngine();
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }

        $this->_solr = $result;
        return $result;
    }
}
