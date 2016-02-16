<?php
class Epam_Layered_Model_Catalog_Layer_Filter_Attribute extends Epam_Layered_Model_Catalog_Layer_Filter_Attribute_Adapter
{
    const QS_DELIMITER = '_';

    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getResourceModel('layered/catalog_layer_filter_attribute');
        }
        return $this->_resource;
    }

    protected function _initItems()
    {
        $data = $this->_getItemsData();
        if(Mage::helper('layered')->isSolr()){
            $data = $this->_sortArray($data);
        }
        $items = array();

        if (! $data) {
            return $this;
        }

        foreach ($data as $itemData) {
            $items[] = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count'],
                $itemData['checked'],
                $itemData['link']
            );
        }
        $this->_items = $items;
        return $this;
    }

    protected function _createItem($label, $value, $count = 0, $checked = '', $link = '')
    {
        return Mage::getModel('catalog/layer_filter_item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count)
            ->setChecked($checked)
            ->setLink($link);
    }

    protected function _getLink($value)
    {
        $request = Mage::app()->getRequest()->getQuery();
        $query = $this->_buildQueryString(array($this->getRequestVar() => $value), $request);
        $query[Mage::getBlockSingleton('page/html_pager')->getPageVarName()] = null;
        $newUrl = Mage::getUrl('*/*/*', array('_current' => false, '_use_rewrite' => true, '_query' => $query));
        return $newUrl;
    }

    protected function _buildQueryString($currentFilter, $request)
    {
        //parse options as separate array values
        foreach ($request as $getKey => $getValue) {
            $aRequest[$getKey] = explode(self::QS_DELIMITER, $getValue);
        }

        //add new option or remove if already exists
        foreach ($currentFilter as $key => $value) {
            if (isset($aRequest[$key]) && in_array($value, $aRequest[$key])) {
                unset($aRequest[$key][array_search($value, $aRequest[$key])]);
            } else {
                $aRequest[$key][] = $value;
            }
        }

        //parse again array to string and remove attribute without selected options
        foreach ($aRequest as $attribute => $options) {
            $request[$attribute] = implode(self::QS_DELIMITER, $options);
            if (empty($request[$attribute])) {
                unset($request[$attribute]);
            }
        }

        return $request;
    }

    protected function _getItemsData()
    {//TODO:correct as far as I see on Mage_Catalog. Recheck on Solr
        $isSolr = Mage::helper('layered')->isSolr();

        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        if ($isSolr) {//Enterprise_Search_Model_Catalog_Layer_Filter_Attribute
            $engine = Mage::getResourceSingleton('enterprise_search/engine');
            $fieldName = $engine->getSearchEngineFieldName($attribute, 'nav');

            $productCollection = $this->getLayer()->getProductCollection();
            $optionsFacetedData = $productCollection
                ->setFacetDataIsLoaded(false)
                ->getFacetedData($fieldName);
            $options = $attribute->getSource()->getAllOptions(false);

            $data = array();
        } else {//Mage_Catalog_Model_Layer_Filter_Attribute
            $key = $this->getLayer()->getStateKey().'_'.$this->_requestVar;
            $data = $this->getLayer()->getAggregator()->getCacheData($key);

            if ($data !== null) {
                return $data;
            }
            $options = $attribute->getFrontend()->getSelectOptions();
            $optionsCount = $this->_getResource()->getCount($this);
            $data = array();
        }

        foreach ($options as $option) {
            if ($isSolr) {
                $optionId = $option['value'];
                // Check filter type
                if ($this->_getIsFilterableAttribute($attribute) != self::OPTIONS_ONLY_WITH_RESULTS
                    || !empty($optionsFacetedData[$optionId])
                ) {
                    $data[] = array(
                        'label' => $option['label'],
                        'value' => $option['label'],
                        'count' => isset($optionsFacetedData[$optionId]) ? $optionsFacetedData[$optionId] : 0,
                        'checked' => $this->_selected($attribute->getAttributeCode(), $option['label']),
                        'link' => $this->_getLink($option['label'])
                    );
                }
            } else {
                if (is_array($option['value'])) {
                    continue;
                }
                //TODO:fix code above for Multiple Attributes & ~Solr

                if (Mage::helper('core/string')->strlen($option['value'])) {
                    // Check filter type
                    if ($this->_getIsFilterableAttribute($attribute) == self::OPTIONS_ONLY_WITH_RESULTS) {
                        if (!empty($optionsCount[$option['value']])) {
                            $data[] = array(
                                'label' => $option['label'],
                                'value' => $option['value'],
                                'count' => $optionsCount[$option['value']],
                                'checked' => $this->_selected($attribute->getAttributeCode(), $option['value']),
                                'link' => $this->_getLink($option['value'])
                            );
                        }
                    } else {
                        $data[] = array(
                            'label' => $option['label'],
                            'value' => $option['value'],
                            'count' => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
                        );
                    }
                }
            }
        }

        if (! $isSolr) {
            $tags = array(
                Mage_Eav_Model_Entity_Attribute::CACHE_TAG.':'.$attribute->getId()
            );

            $data = $this->_sortArray($data);

            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }

    protected function _selected($attributeCode, $value)
    {
        $filters = Mage::app()->getRequest()->getParams();
        if (isset($filters[$attributeCode])) {
            $selectedValues = explode(self::QS_DELIMITER, $filters[$attributeCode]);
            if (in_array($value, $selectedValues)) {
                return 'checked';
            }
        }
        return '';
    }

    protected function _sortArray($data)
    {
        if (! sizeof($data)) {
            return false;
        }
        $sortedData = array();
        foreach ($data as $value) {
            if (! is_array(@$sortedData[$value['count']])) {
                $sortedData[$value['count']] = array();
            }
            $sortedData[$value['count']][] = $value;
        }

        krsort($sortedData);

        $newData = array();
        foreach ($sortedData as $byCount) {
            foreach ($byCount as $row) {
                $newData[] = $row;
            }
        }

        return $newData;
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $isSolr = Mage::helper('layered')->isSolr();
        $filter = $request->getParam($this->_requestVar);

        if (is_null($filter)) {
            return $this;
        }

        if (is_array($filter)) {
            foreach ($filter as $_filter) {
                if($isSolr){
                    $this->applyFilterToCollection($this, $_filter);
                    $this->getLayer()->getState()->addFilter($this->_createItem($_filter, $_filter));
                } else {
                    $this->_getResource()->applyFilterToCollection($this, $_filter);

                    $productModel = Mage::getModel('catalog/product');
                    $attrOption = $productModel->getResource()->getAttribute($this->_requestVar);
                    $attrLabel = $attrOption->getSource()->getOptionText($_filter);

                    $this->getLayer()->getState()->addFilter($this->_createItem($attrLabel, $_filter));
                }
            }
        } else {
            if ($filter) {
                if($isSolr){
                    $this->applyFilterToCollection($this, $filter);
                    $this->getLayer()->getState()->addFilter($this->_createItem($filter, $filter));
                } else {
                    $this->_getResource()->applyFilterToCollection($this, $filter);

                    $productModel = Mage::getModel('catalog/product');
                    $attrOption = $productModel->getResource()->getAttribute($this->_requestVar);
                    $attrLabel = $attrOption->getSource()->getOptionText($filter);

                    $this->getLayer()->getState()->addFilter($this->_createItem($attrLabel, $filter));
                }
            }
        }
        //$this->_items = array();

        return $this;
    }
}
