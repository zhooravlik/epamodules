<?php
class Epam_Layered_Model_Resource_Options extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_appear = null;

    public function _construct()
    {
        $this->_init('layered/options', 'appear_id');
    }

    public function getAttrAppearance($curAttrId = 0)
    {
        if ($this->_appear){
            return $this->_appear;
        }

        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('mt' => $this->getMainTable()), 'option_id')
            ->where('attr_id=?', $curAttrId);
        return $read->fetchOne($select);
    }

    public function saveAttributeAppearance($attrId, $optionId)
    {
        if (! $attrId){
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('layered')->__('Some of identifiers has been missed. Please try again later.')
            );
            Mage::app()->getFrontController()->getResponse()->setRedirect('*/*/');
            return;
        }

        $read   = $this->_getReadAdapter();
        $write  = $this->_getWriteAdapter();
        $table  = $this->getMainTable();

        $condition = 'attr_id= ' . $attrId;

        $select = $read->select()->from(
            array('mt' => $this->getMainTable()),
            'option_id'
        )->where($condition);

        $isExists = $read->fetchOne($select);

        if ($isExists){
            $write->update($table, array('option_id' => $optionId), $condition);
        } else {
            $write->insert(
                $table,
                array(
                    'attr_id'   => $attrId,
                    'option_id' => $optionId
                )
            );
        }

        return true;
    }

    public function getCustomAttributeAppearance($attributeId)
    {
        $read = $this->_getReadAdapter();
        $table = $this->getMainTable();

        $select = $read->select()->from(
            array('mt' => $table),
            'option_id'
        )
            ->where('attr_id = ?', $attributeId);

        $result = $read->fetchOne($select);

        if ($result){
            return $result;
        } else {
            return false;
        }
    }
}
