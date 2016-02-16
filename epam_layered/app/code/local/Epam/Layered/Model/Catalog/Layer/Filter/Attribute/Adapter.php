<?php
if (Mage::helper('layered')->isSolr()) {
    class Epam_Layered_Model_Catalog_Layer_Filter_Attribute_Adapter extends Enterprise_Search_Model_Catalog_Layer_Filter_Attribute {}
} else {
    class Epam_Layered_Model_Catalog_Layer_Filter_Attribute_Adapter extends Mage_Catalog_Model_Layer_Filter_Attribute {}
}
