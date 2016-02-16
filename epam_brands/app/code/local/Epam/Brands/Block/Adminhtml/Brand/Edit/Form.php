<?php

/**
 * Class Epam_Brands_Block_Adminhtml_Brand_Edit_Form
 */
class Epam_Brands_Block_Adminhtml_Brand_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $brand = Mage::registry('current_brand');
        if ($brand->getId()) {
            $form->addField('brand_id', 'hidden', array('name' => 'brand_id'));
            $form->setValues($brand->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
