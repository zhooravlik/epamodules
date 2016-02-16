<?php

/**
 * Class Epam_Layered_Model_Resource_Enterprise_Search_Collection
 */
class Epam_Layered_Model_Resource_Enterprise_Search_Collection extends Enterprise_Search_Model_Resource_Collection
{
    var $_field;

    public function setFacetDataIsLoaded($flag)
    {
        $this->_facetedDataIsLoaded = $flag;
        return $this;
    }

    public function getFacetedData($field = null)
    {
        if (!$this->_facetedDataIsLoaded) {
            $this->_field = $field;
            $this->loadFacetedData();
        }

        if (isset($this->_facetedData[$field])) {
            return $this->_facetedData[$field];
        }

        return array();
    }

    public function loadFacetedData()
    {
        $field = $this->_field;
        if (empty($this->_facetedConditions)) {
            $this->_facetedData = array();
            return $this;
        }

        list($query, $params) = $this->_prepareBaseParams();

        if ($field != 'category_ids' && $field != 'visibility') {
            unset($params['filters'][$field]);
        }
        $params['solr_params']['facet'] = 'on';
        $params['facet'] = $this->_facetedConditions;

        $result = $this->_engine->getResultForRequest($query, $params);
        $this->_facetedData = $result['faceted_data'];
        $this->_facetedDataIsLoaded = true;

        return $this;
    }
}
