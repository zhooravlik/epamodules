<?php

/**
 * Class Epam_Brands_Block_Adminhtml_Brand_Edit_Tab_Meta
 */
class Epam_Brands_Block_Adminhtml_Brand_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('ebrands')->__('Meta Info');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $htmlIdPrefix = 'brand_meta_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $fieldsetHtmlClass = 'fieldset-wide';

        $model = Mage::registry('current_brand');

        Mage::dispatchEvent(
            'adminhtml_brand_edit_tab_meta_before_prepare_form',
            array('model' => $model, 'form' => $form)
        );

        // add meta information fieldset
        $fieldset = $form->addFieldset(
            'default_fieldset',
            array(
                'legend' => Mage::helper('ebrands')->__('Meta Information'),
                'class'  => $fieldsetHtmlClass,
            )
        );

        $fieldset->addField(
            'meta_keywords',
            'textarea',
            array(
                'name'     => 'meta_keywords',
                'label'    => Mage::helper('ebrands')->__('Meta Keywords'),
                'disabled' => (bool)$model->getIsReadonly(),
            )
        );

        $fieldset->addField(
            'meta_description', 'textarea',
            array(
                'name'     => 'meta_description',
                'label'    => Mage::helper('ebrands')->__('Meta Description'),
                'disabled' => (bool)$model->getIsReadonly(),
            )
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
