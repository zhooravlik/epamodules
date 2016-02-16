<?php

/**
 * Class Epam_Brands_Block_Adminhtml_Brand_Grid
 */
class Epam_Brands_Block_Adminhtml_Brand_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('brandGrid');
        $this->setDefaultSort('brand_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('ebrands/brand_collection');
        $tableNameEAOV = Mage::getModel('core/resource')->getTableName('eav_attribute_option_value');
        $tableNameEAO = Mage::getModel('core/resource')->getTableName('eav_attribute_option');

        $collection->getSelect()->distinct()->join(
            array('eaov' => $tableNameEAOV),
            'main_table.brand = eaov.option_id',
            array('brand_name'=>'value')
        )
            ->join(array('eao' => $tableNameEAO), 'eao.option_id = eaov.option_id', array());

        if (!Mage::app()->isSingleStoreMode()) {
            $collection->addStoresVisibility();
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'brand_id',
            array(
                'header'=> Mage::helper('ebrands')->__('ID'),
                'type'  => 'number',
                'width' => '1',
                'index' => 'brand_id',
            )
        );

        $this->addColumn(
            'brand_name',
            array(
                'header' => Mage::helper('ebrands')->__('Brand Title'),
                'type'   => 'text',
                'index'  => 'brand_name',
            )
        );

        $this->addColumn(
            'update_time',
            array(
                'header'=> Mage::helper('ebrands')->__('Update Time'),
                'type' => 'datetime',
                'index'=> 'update_time',
            )
        );

        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('ebrands')->__('Status'),
                'align'   => 'center',
                'width'   => 1,
                'index'   => 'status',
                'type'    => 'options',
                'options' => Mage::getModel('ebrands/source_status')->getAllOptions(),
            )
        );

        if (! Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'visible_in',
                array(
                    'header'     => Mage::helper('ebrands')->__('Visible In'),
                    'type'       => 'store',
                    'index'      => 'stores',
                    'sortable'   => false,
                    'store_view' => true,
                    'width'      => 200
                )
            );
        }

        $this->addColumn(
            'action',
            array(
                'header'  => Mage::helper('ebrands')->__('Action'),
                'width'   => '50',
                'type'    => 'action',
                'align'   => 'center',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('ebrands')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            )
        );
        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('brand_id');
        $this->getMassactionBlock()->setFormFieldName('brand');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'    => Mage::helper('ebrands')->__('Delete'),
                'url'      => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('ebrands')->__(
                    'Are you sure you want to delete these brands?'
                ),
            )
        );
        return $this;
    }

    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * @param $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getIndex() == 'stores') {
            $this->getCollection()->addStoreFilter($column->getFilter()->getCondition(), false);
        } elseif ($column->getIndex() == 'brand_name') {
            $this->getCollection()->addBrandNameFilter($column->getFilter()->getCondition());
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
