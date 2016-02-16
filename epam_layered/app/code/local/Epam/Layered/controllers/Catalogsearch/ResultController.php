<?php
require_once('app/code/core/Mage/CatalogSearch/controllers/ResultController.php');
class Epam_Layered_Catalogsearch_ResultController extends Mage_CatalogSearch_ResultController
{
    public function indexAction()
    {
        if($this->getRequest()->isXmlHttpRequest()) {//Check for AJAX query
            $response = array();

            /* @var $query Mage_CatalogSearch_Model_Query */
            $query = Mage::helper('catalogsearch')->getQuery();

            $query->setStoreId(Mage::app()->getStore()->getId());

            if ($query->getQueryText()) {
                if (Mage::helper('catalogsearch')->isMinQueryLength()) {
                    $query->setId(0)
                        ->setIsActive(1)
                        ->setIsProcessed(1);
                } else {
                    if ($query->getId()) {
                        $query->setPopularity($query->getPopularity()+1);
                    } else {
                        $query->setPopularity(1);
                    }

                    if ($query->getRedirect()) {
                        $query->save();
                        $this->getResponse()->setRedirect($query->getRedirect());
                        return;
                    } else {
                        $query->prepare();
                    }
                }

                Mage::helper('catalogsearch')->checkNotes();

                $this->loadLayout();
                $this->_initLayoutMessages('catalog/session');
                $this->_initLayoutMessages('checkout/session');
                $this->renderLayout();

                if (! Mage::helper('catalogsearch')->isMinQueryLength()) {
                    $query->save();
                }
            } else {
                $this->_redirectReferer();
                $response['status'] = 'FAILURE'; //Add failure when filter can't be applied
            }

            if($this->getLayout()->getBlock('enterprisecatalog.leftnav', 1)) {
                $catNavleftBlock = $this->getLayout()->getBlock('enterprisecatalog.leftnav');
            } elseif ($this->getLayout()->getBlock('catalog.leftnav')) {
                $catNavleftBlock = $this->getLayout()->getBlock('catalog.leftnav');
            } else {
                $catNavleftBlock = $this->getLayout()->getBlock('catalogsearch.leftnav');
            }

            $viewpanel = $catNavleftBlock->toHtml(); //Get the new Layered Manu
            $productlist = $this->getLayout()->getBlock('search_result_list')->toHtml(); //New product List
            $response['status'] = 'SUCCESS'; //Send Success
            $response['viewpanel'] = $viewpanel;
            $response['productlist'] = $productlist;
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }

        /* @var $query Mage_CatalogSearch_Model_Query */
        $query = Mage::helper('catalogsearch')->getQuery();

        $query->setStoreId(Mage::app()->getStore()->getId());

        if ($query->getQueryText()) {
            if (Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->setId(0)
                    ->setIsActive(1)
                    ->setIsProcessed(1);
            } else {
                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity()+1);
                } else {
                    $query->setPopularity(1);
                }

                if ($query->getRedirect()){
                    $query->save();
                    $this->getResponse()->setRedirect($query->getRedirect());
                    return;
                } else {
                    $query->prepare();
                }
            }

            Mage::helper('catalogsearch')->checkNotes();

            $this->loadLayout();
            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');
            $this->renderLayout();

            if (! Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->save();
            }
        } else {
            $this->_redirectReferer();
        }
    }
}
