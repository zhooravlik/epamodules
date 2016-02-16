<?php

/**
 * Class Epam_Brands_Block_Adminhtml_Brand_Edit
 */
class Epam_Brands_Block_Adminhtml_Brand_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     *
     */
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_brand';
        $this->_blockGroup = 'ebrands';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('ebrands')->__('Save Brand'));
        $this->_updateButton('delete', 'label', Mage::helper('ebrands')->__('Delete Brand'));

        $this->_addButton(
            'save_and_edit_button', array(
            'label'   => Mage::helper('ebrands')->__('Save and Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class'   => 'save'
            ), 100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
            editForm.submit($('edit_form').action + 'back/edit/');}";
    }

    /**
     * @return mixed
     */
    public function getBrandId()
    {
        return Mage::registry('current_brand')->getId();
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_brand')->getId()) {
            return $this->htmlEscape(Mage::registry('current_brand')->getTitle());
        } else {
            return Mage::helper('ebrands')->__('New Brand');
        }
    }

    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }
}
