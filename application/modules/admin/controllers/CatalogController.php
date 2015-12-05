<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// application/controllers/IndexController.php

class Admin_CatalogController extends Application_Controller_Abstract {

    protected $_catalog;

    public function init() {
        /* Initialize action controller here */
        parent::init();

        $this->_catalog = new Application_Model_Catalog_Table();
        $this->_catalog->setItemCountPerPage($this->_hasParam('count') ? $this->_getParam('count') : Application_Db_Table::ITEMS_PER_PAGE);
        $this->_catalog->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : 'name');

        $ajaxContext = $this->_helper->getHelper('AjaxContext');

        $ajaxContext->addActionContext('index', 'html')
                ->addActionContext('edit', 'html')
                ->addActionContext('delete', 'html')
                ->setSuffix('html', '')
                ->initContext();
    }

    public function listAction() {
        $this->_forward('index');
    }

    public function indexAction() {
        // action body
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $request = $this->getRequest();
        $pageNumber = $request->getParam('page');
        if ($pageNumber) {
            $this->_catalog->setPageNumber($pageNumber);
        }
        $orderBy = $request->getParam('orderBy');
        $columns = array('name');
        if ($orderBy) {
            $orderBy = explode(" ", $orderBy);
            $this->_catalog->setOrderBy("{$columns[$orderBy[0]]} {$orderBy[1]}");
        }
        $orderBy = explode(" ", $this->_catalog->getOrderBy());
        foreach ($columns as $ix => $columnName) {
            if ($columnName != $orderBy[0]) {
                continue;
            }
            $orderBy = "$ix {$orderBy[1]}";
        }
        $request->setParam('orderBy', $orderBy);
        $request->setParam('count', $this->_catalog->getItemCountPerPage());
        $status = $this->_dictionaries->getStatusList('catalog')->find('deleted', 'acronym');
        $this->_catalog->setWhere("statusid != {$status->id}");
        $this->_catalog->setCacheInClass(false);
        $this->_catalog->setLazyLoading(false);
        $this->view->catalog = $this->_catalog->getAll();
        $this->view->paginator = $this->_catalog->getPaginator();
        $this->view->request = $request->getParams();
    }

    public function deleteAction() {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $this->_catalog->setLazyLoading(true);
        $catalog = $this->_catalog->find($id)->current();
        if (!$catalog) {
            throw new Exception('Nie znaleziono pozycji katalogu');
        }
        $form = new Application_Form_Catalog_Delete();
        $form->setDefaults($catalog->toArray());
        $this->view->form = $form;
        $this->view->catalog = $catalog;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                $status = $this->_dictionaries->getStatusList('catalog')->find('deleted', 'acronym');
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                $returns = new Application_Model_Services_Returns_Table();
                $returns->setWhere("catalogid = {$this->id}");
                if ($returns) {
                    $form->setDescription('Nie można usunąć pozycji przypisanej do zwrotu');
                    return;
                }
                $catalog->statusid = $status->id;
                $catalog->save();
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Wpis usunięty';
            }
        }
    }

    public function editAction() {
        // action body

        $request = $this->getRequest();
        $id = $request->getParam('id');
        $form = new Application_Form_Catalog();
        $this->_catalog->setCacheInClass(false);
        $this->_catalog->setLazyLoading(false);
        $this->_catalog->clearCache();
        if ($id) {
            $catalog = $this->_catalog->get($id);
            $form->setDefaults($catalog->toArray());
        } 

        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                if ($id && !$this->_hasParam('copy')) {
                    $catalog->setFromArray($values);
                } else {
                    $catalog = $this->_catalog->createRow($values);
                    $catalog->id = null;
                }
                $this->_catalog->setLazyLoading(true);
                $catalog->save();
                $this->view->success = 'Katalog zapisany';
                Zend_Db_Table::getDefaultAdapter()->commit();
            }
        }
    }

}
