<?php

/**
 * Class Epam_Brands_Model_Template_Filter
 */
class Epam_Brands_Model_Template_Filter extends Mage_Widget_Model_Template_Filter
{
    /**
     * @var null
     */
    protected $_useSessionInUrl = null;

    /**
     * @param bool $flag
     * @return $this
     */
    public function setUseSessionInUrl($flag)
    {
        $this->_useSessionInUrl = (bool)$flag;
        return $this;
    }
}
