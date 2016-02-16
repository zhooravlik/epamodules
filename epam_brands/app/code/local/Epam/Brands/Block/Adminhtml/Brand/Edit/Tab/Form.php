<?php

/**
 * Class Epam_Brands_Block_Adminhtml_Brand_Edit_Tab_Form
 */
class Epam_Brands_Block_Adminhtml_Brand_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    /**
     * @param $form
     */
    protected function _prepareImageFieldset($form)
    {
        $fieldset = $form->addFieldset(
            'image_fieldset',
            array(
                'legend'=>Mage::helper('ebrands')->__('Brand images'),
                'class'    => 'fieldset-wide',
            )
        );

        $fieldset->addField(
            'brand_logo',
            'Thumbnailbrand',
            array(
                'label'     => Mage::helper('ebrands')->__('Brand Logo'),
                'required' => 'true',
                'name'      => 'brand_logo',
            )
        );

        $fieldset->addField(
            'brand_banner',
            'Thumbnailbrand',
            array(
                'label'     => Mage::helper('ebrands')->__('Brand Banner'),
                'required'  => false,
                'name'      => 'brand_banner',
            )
        );
    }

    /**
     * @param $fieldset
     * @param $model
     */
    protected function _addStoreFields($fieldset, $model)
    {
        /**
         * Check is single store mode
         */
        if (! Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_ids',
                'multiselect',
                array(
                    'name'      => 'store_ids[]',
                    'label'     => Mage::helper('ebrands')->__('Visible In'),
                    'required'  => true,
                    'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                    'value'        => $model->getStoreIds(),
                )
            );
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                array(
                    'name'    => 'store_ids[]',
                    'value'    => Mage::app()->getStore(true)->getId()
                )
            );
            $model->setStoreIds(Mage::app()->getStore(true)->getId());
        }
    }

    /**
     * @param $form
     * @param $model
     */
    protected function _prepareBaseFieldset($form, $model)
    {
        $fieldset = $form->addFieldset(
            'base_fieldset',
            array(
                'legend'=>Mage::helper('ebrands')->__('Brand info'),
                'class'    => 'fieldset-wide',
            )
        );

        if ($model->getBrandId()) {
            $fieldset->addField(
                'brand_id',
                'hidden',
                array(
                    'name'    => 'brand_id',
                )
            );
        }

        $fieldset->addField(
            'brand',
            'select',
            array(
                'label'    => Mage::helper('ebrands')->__('Brand'),
                'name'     => 'brand',
                'required' => true,
                'options'  => Mage::getModel('ebrands/options')->getBrands(),
            )
        );

        $fieldset->addField(
            'identifier',
            'text',
            array(
                'label'    => Mage::helper('ebrands')->__('Identifier'),
                'name'     => 'identifier',
                'required' => false,
            )
        );

        $fieldset->addField(
            'status',
            'select',
            array(
                'label'    => Mage::helper('ebrands')->__('Status'),
                'name'     => 'status',
                'required' => 'true',
                'disabled' => (bool)$model->getIsReadonly(),
                'options'  => Mage::getModel('ebrands/source_status')->getAllOptions(),
            )
        );

        if (!$model->getId()) {
            $model->setData('status', Epam_Brands_Model_Source_Status::STATUS_ENABLED);
        }

        $this->_addStoreFields($fieldset, $model);

        $fieldset->addField(
            'is_display_home',
            'select',
            array(
                'label'    => Mage::helper('ebrands')->__('Display on Frontend'),
                'name'     => 'is_display_home',
                'required' => 'true',
                'disabled' => (bool)$model->getIsReadonly(),
                'options'  => Mage::getModel('ebrands/source_status')->getAllOptions(),
            )
        );

        $fieldset->addField(
            'description',
            'editor',
            array(
                'name'      => 'description',
                'label'     => Mage::helper('ebrands')->__('Description'),
                'title'     => Mage::helper('ebrands')->__('Description'),
                'style'     => 'height:36em',
                'required'  => true,
                'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('tab_id'=>$this->getTabId())),
            )
        );

        $fieldset->addField(
            'sort_order',
            'text',
            array(
                'label'        => Mage::helper('ebrands')->__('Sort Order'),
                'name'         => 'sort_order',
            )
        );
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $htmlIdPrefix = 'brand_information_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        /* @var $model Epam_Brands_Model_Brand */
        $model = Mage::registry('current_brand');

        $this->_prepareBaseFieldset($form, $model);
        $this->_prepareImageFieldset($form);

        $form->setValues($model->getData());
        $this->setForm($form);
        return $this;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('ebrands')->__('Brand Info');
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
}
