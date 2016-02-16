<?php

/**
 * Class Epam_Brands_Adminhtml_Brand_ListController
 */
class Epam_Brands_Adminhtml_Brand_ListController extends Mage_Adminhtml_Controller_Action
{
    /**
     *
     */
    public function indexAction()
    {
        $this->_title($this->__('epam Modules'))->_title($this->__('Brand'));
        $this->loadLayout();
        $this->_setActiveMenu('epamodules/ebrands');
        $this->renderLayout();
    }

    /**
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     *
     */
    public function editAction()
    {
        $id     = $this->getRequest()->getParam('id');
        $model  = $this->_initBrand('id');
        $model  = Mage::getModel('ebrands/brand')->load($id);

        if (!$model->getId() && $id) {
            Mage::getSingleton('adminhtml/session')->addError(
                $this->__('Unknown brand.')
            );
            $this->redirect('*/*/');
            return;
        }
        $this->_title($model->getId() ? $model->getTitle() : $this->__('Add brand'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->addData($data);
        }

        $this->loadLayout();
        $this->_setActiveMenu('epamodules/ebrands');

        $bcLabel = $id ? Mage::helper('ebrands')->__('Edit Brand') : Mage::helper('ebrands')->__('Add Brand');
        $this->_addBreadcrumb($bcLabel, $bcLabel)->renderLayout();
    }

    /**
     * @throws Exception
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model  = $this->_initBrand();

            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('Unknown brand.')
                );
                $this->_redirect('*/*/');
                return;
            }
            $brandName  = $model->getBrandName($data['brand'], Mage::app()->getStore()->getId());
            $identifier = $data['identifier'] ? $data['identifier'] : $brandName;
            $data['identifier'] = Mage::getModel('ebrands/url')->formatUrlKey($identifier);//TODO

            $data = $this->_processFiles($model, $data);
            $redirectBack = $this->_save($model, $data);

            if ($redirectBack) {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * @param $model
     * @param $data
     * @return bool
     */
    protected function _save($model, $data)
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        try {
            if (!empty($data)) {
                $model->addData($data);
                Mage::getSingleton('adminhtml/session')->setFormData($data);
            }
            $model->save();
            Mage::getSingleton('adminhtml/session')->setFormData(false);
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('ebrands')->__('The brand has been saved.')
            );
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $redirectBack = true;
        } catch (Exception $e) {
            $this->_getSession()->addError(
                Mage::helper('ebrands')->__('Something went wrong while saving the brand. Please try again later.')
            );
            $redirectBack = true;
            Mage::logException($e);
        }
        return $redirectBack;
    }

    /**
     * @param $data
     * @param $model
     * @return mixed
     * @throws Exception
     */
    protected function _processFiles($model, $data)
    {
        $fieldName = array();
        $fieldName[0] =  'brand_logo';
        $fieldName[1] =  'brand_banner';

        if ($count = count($_FILES)) {
            for ($i = 0; $i < $count; $i++) {
                if (isset($_FILES[$fieldName[$i]]['name']) && $_FILES[$fieldName[$i]]['tmp_name'] != '') {
                    $uploader = new Varien_File_Uploader($fieldName[$i]);
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    $path = Mage::getBaseDir('media') . DS . 'brand' . DS;
                    $_FILES[$fieldName[$i]]['name'] = str_replace(' ', '-', $_FILES[$fieldName[$i]]['name']);
                    $uploader->save($path, $_FILES[$fieldName[$i]]['name']);
                    $data[$fieldName[$i]] = $_FILES[$fieldName[$i]]['name'];
                    if ($_FILES['brand_logo']['name'] && $_FILES['brand_logo']['tmp_name'] != '') {
                        $model->setBrandLogo($uploader->getUploadedFileName());
                    } else {
                        $model->setBrandBanner($uploader->getUploadedFileName());
                    }
                } else if (isset($data[$fieldName[$i]]['delete']) && $data[$fieldName[$i]]['delete'] == 1) {
                    $data[$fieldName[$i]] = '';
                } else {
                    unset($data[$fieldName[$i]]);

                }
            }
        }

        return $data;
    }

    /**
     *
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                // init model and delete
                $model = Mage::getModel('ebrands/brand');
                $model->load($id);
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('ebrands')->__('The brand has been deleted.')
                );
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('ebrands')->__(
                        'An error occurred while deleting brand data. Please review log and try again.'
                    )
                );
                Mage::logException($e);
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData(null);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('ebrands')->__('Unable to find a brand to delete.')
        );
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     *
     */
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('brand');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select brand(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getSingleton('ebrands/brand')->load($id);
                    $model->delete();
                }

                $this->_getSession()->addSuccess($this->__('Total of %d record(s) have been deleted.', count($ids)));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('ebrands')->__(
                        'An error occurred while mass deleting brand. Please review log and try again.'
                    )
                );
                Mage::logException($e);
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     *
     */
    public function massStatusAction()
    {
        $ids = $this->getRequest()->getParam('brand');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select brand(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getSingleton('ebrands/brand')->load($id);
                    $model->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) have been updated', count($ids)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('ebrands')->__(
                        'An error occurred while mass updating brand. Please review log and try again.'
                    )
                );
                Mage::logException($e);
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * @param string $idFieldName
     * @return false|Mage_Core_Model_Abstract
     */
    protected function _initBrand($idFieldName = 'brand_id')
    {
        $id = (int)$this->getRequest()->getParam($idFieldName);
        $model = Mage::getModel('ebrands/brand');
        if ($id) {
            $model->load($id);
        }
        if (!Mage::registry('current_brand')) {
            Mage::register('current_brand', $model);
        }
        return $model;
    }

    /**
     *
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
