<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Warehouse_ProductsController extends Application_Controller_Abstract {

    protected $_products;

    public function init() {
        /* Initialize action controller here */
        $this->_warehouses = new Application_Model_Warehouses_Table();
        $this->_products = new Application_Model_Products_Table();
        $this->_products->setItemCountPerPage($this->_hasParam('count') ? $this->_getParam('count') : Application_Db_Table::ITEMS_PER_PAGE);
        $this->_products->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : 'name DESC');
        $this->_users = new Application_Model_Users_Table();
        parent::init();

        $context = $this->_helper->getHelper('xlsContext');
        $context->addActionContext('list', array('html', 'json', 'xls'))
                ->addActionContext('index', array('html', 'json', 'xls'))
                ->addActionContext('details', array('html'))
                ->addActionContext('migration', array('html', 'json', 'xls'))
                ->addActionContext('edit', 'html')
                ->addActionContext('delete', 'html')
                ->addActionContext('accept', 'html')
                ->addActionContext('import', 'html')
                ->setSuffix('html', '')
                ->initContext();

        if ($context->getCurrentContext() == 'xls') {
            $this->_products->setItemCountPerPage(null);
        }
    }

    public function indexAction() {
        $this->_forward('list');
    }

    public function listAction() {
        // action body

        $request = $this->getRequest();
        $warehouseid = $request->getParam('warehouseid');
        $pageNumber = $request->getParam('page');
        if ($pageNumber) {
            $this->_products->setPageNumber($pageNumber);
        }
        $orderBy = $request->getParam('orderBy');
        $columns = array('dateadd', 'warehouse', 'name', 'quantity', 'qtyavailable', 'unit', 'serial', 'pairedcard', 'status');
        if ($orderBy) {
            $orderBy = explode(" ", $orderBy);
            $this->_products->setOrderBy("{$columns[$orderBy[0] - 1]} {$orderBy[1]}");
        }
        $orderBy = explode(" ", $this->_products->getOrderBy());
        foreach ($columns as $ix => $columnName) {
            if ($columnName != $orderBy[0]) {
                continue;
            }
            $ix++;
            $orderBy = "$ix {$orderBy[1]}";
        }
        $request->setParam('orderBy', $orderBy);
        $request->setParam('count', $this->_products->getItemCountPerPage());
        if ($warehouseid) {
            $warehouse = $this->_warehouses->get($warehouseid);
            $this->view->warehouse = $warehouse;
        }
        $status = $this->_dictionaries->getStatusList('products')->find('deleted', 'acronym');
        if (!$request->getParam('statusid')) {
            $this->_products->setWhere($this->_products->getAdapter()->quoteInto("statusid != {$status->id}", null));
        }
        $this->view->filepath = '/../data/temp/';
        $this->view->filename = 'Zestawienie_produktow-' . date('YmdHis') . '.xlsx';
        $this->_products->setLazyLoading(false);
        $this->view->products = $this->_products->getAll($request->getParams());
        $this->view->paginator = $this->_products->getPaginator();
        $this->view->warehouses = $this->_warehouses->getAll();
        $this->view->statuses = $this->_dictionaries->getStatusList('products');
        $this->view->request = $request->getParams();
    }

    public function editAction() {
        // action body

        $request = $this->getRequest();
        $id = $request->getParam('id');
        $warehouseid = $request->getParam('warehouseid');
        $form = new Application_Form_Products();
        $parents = $this->_warehouses->getAll();
        $units = $this->_dictionaries->getDictionaryList('warehouse', 'unit');
        $form->setOptions(array('warehouses' => $parents, 'units' => $units));
        if ($id) {
            $product = $this->_products->get($id);
            $form->setDefaults($product->toArray());
        } else {
            $form->setDefaults(array('warehouseid' => $warehouseid));
        }
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                $values['price'] = str_replace(',', '.', $values['price']);
                if ($id) {
                    $product->setFromArray($values);
                } else {
                    $product = $this->_products->createRow($values);
                    $status = $this->_dictionaries->getStatusList('products')->find('instock', 'acronym');
                    $product->statusid = $status->id;
                    $product->qtyavailable = $product->quantity;
                    $product->id = null;
                }
                $product->save();
                $this->view->success = 'Product successfully saved';
            }
        }
    }

    public function importAction() {
        // action body

        $request = $this->getRequest();
        $form = new Application_Form_Products_Import();
        $parents = $this->_warehouses->getAll();
        $units = $this->_dictionaries->getDictionaryList('warehouse', 'unit');
        $status = $this->_dictionaries->getStatusList('products')->find('new', 'acronym');
        $form->setOptions(array('warehouses' => $parents, 'units' => $units));

        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                $upload = new Zend_File_Transfer_Adapter_Http();
                $upload->setDestination(APPLICATION_PATH . "/../data/temp");

                try {
                    $upload->receive();

                    //require_once APPLICATION_PATH . '/../library/PHPExcel/Classes/PHPExcel.php';
                    //$file = new SplFileObject($upload->getFileName('import', true));
                    //$file->setFlags(SplFileObject::READ_CSV);

                    //$reader = new Utils_File_Reader_CSV();
                    //$data = $reader->read($upload->getFileName('import', true), 1);
                    //$reader = new Utils_File_Reader_PHPExcel(array('readerType' => 'CSV', 'readOnly' => true, 'charset' => 'CP1250'));
                    $reader = new Utils_File_Reader_PHPExcel(array('readerType' => 'Excel5', 'readOnly' => true));
                    $sheet = $reader->read($upload->getFileName('import', true), 1);
                    $rows = $sheet->getRowIterator(2);

                    foreach ($rows as $i => $row) {
                        $line = array();
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(true);
                        
                        $params = array('warehouseid' => $values['warehouseid'],
                            'unitid' => $values['unitid'],
                            'statusid' => $status->id);
                        
                        try {
                            foreach ($cellIterator as $key => $cell) {
                                //$fields = preg_split("/;/", $cell);
                                //if (sizeof(preg_split("/,/", $cell)) > 1) {
                                //    throw new Exception('Nieprawidłowy format pliku, dozwolony format pliku to pola oddzielane przecinkiem');
                                //}
                                switch ($values['format']) {
                                    case 'arvato':
                                        switch ($key) {
                                            case 'B':
                                                $params['name'] = $cell->getValue();
                                                break;
                                            case 'C':
                                                $params['serial'] = $cell->getValue();
                                                break;
                                            case 'D': 
                                                $params['pairedcard'] = $cell->getValue();
                                                break;
                                            case 'E': 
                                                $params['quantity'] = $cell->getValue();
                                                $params['qtyavailable'] = $cell->getValue();
                                                break;
                                            case 'F': 
                                                $params['price'] = $cell->getValue();
                                                break;
                                        }
                                        break;
                                    default:
                                        switch ($key) {
                                            case 'A':
                                                $params['serial'] = $cell->getValue();
                                                break;
                                            case 'B':
                                                $params['name'] = $cell->getValue();
                                                break;
                                            case 'C': 
                                                $params['quantity'] = $cell->getValue();
                                                $params['qtyavailable'] = $cell->getValue();
                                                break;
                                            case 'D': 
                                                $params['pairedcard'] = $cell->getValue();
                                                break;
                                            case 'E': 
                                                $params['price'] = $cell->getValue();
                                                break;
                                        }
                                }
                            }

                            $product = $this->_products->createRow($params);
                            if (!$product->name || !$product->quantity) {
                                continue;
                            }
                            
                            $product->save();
                            array_unshift($line, 'OK');
                        } catch (Exception $e) {
                            array_unshift($line, $e->getMessage());
                        }
                        foreach ($row->getCellIterator() as $cell) {
                            $value = $cell->getValue();
                            $line[] = $value;
                        }

                        $lines[] = $line;
                    }

                    $this->view->data = $lines;
                } catch (Zend_File_Transfer_Exception $e) {
                    echo $e->message();
                }
                //if ($form->isValid($request->getPost())) {
                //    $data = $form->getData();var_dump($data);exit;
                //    $this->view->success = 'Order successfully saved';
                //} 
            }
        }
    }
    
    public function migrationAction() {
        // action body
        //var_dump(PHPExcel_Shared_Date::ExcelToPHP('42121'));
        $request = $this->getRequest();
        $form = new Application_Form_Products_Migration();
        $this->_products = new Application_Model_Products_Table();
        $this->_orderlines = new Application_Model_Orders_Lines_Table();
        $this->_orders = new Application_Model_Orders_Table();
        $units = $this->_dictionaries->getDictionaryList('warehouse', 'unit');
        $form->setOptions(array('units' => $units));
        $status = $this->_dictionaries->getStatusList('orders')->find('released', 'acronym');
        if (!$order = $this->_orders->getAll(array('userid' => $this->_auth->getIdentity()->id))->current()) {
            $order = $this->_orders->createRow();
            $order->setFromArray(array('userid' => $this->_auth->getIdentity()->id));
            $order->save();
        }

        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();

                $upload = new Zend_File_Transfer_Adapter_Http();
                $upload->setDestination(APPLICATION_PATH . "/../data/pliki/migration/");
                
                try {
                    $productStatusInstock = $this->_dictionaries->getStatusList('products')->find('instock', 'acronym');
                    $productStatusReleased = $this->_dictionaries->getStatusList('products')->find('released', 'acronym');
                    $orderStatusReleased = $this->_dictionaries->getStatusList('orders')->find('released', 'acronym');
                    $params = array('unitid' => $values['unitid'], 'statusid' => $productStatusInstock->id);
                    $upload->receive();

                    $rowNumber = 2;
                    $this->_products->setRowClass('Application_Model_Products_Migration');
                    $reader = new Utils_File_Reader_PHPExcel(array('readerType' => 'Excel2007', 'readOnly' => true));
                    $data = $reader->read($upload->getFileName('import', true), 1);
                    $rows = $data->getRowIterator($rowNumber);
                    
                    if (!empty($values['report'])) {
                        $objPHPExcel = new PHPExcel();
                        $objPHPExcel->setActiveSheetIndex(0);
                    }
                    
                    foreach ($rows as $i => $row) {
                        $product = $this->_products->createRow();
                        $product->setFromArray($params);
                        $i++;
                        $line = array();
                        $columnNo = 0;

                        $cellIterator = $row->getCellIterator();

                        // This loops all cells, even if it is not set.
                        // By default, only cells that are set will be iterated.
                        $cellIterator->setIterateOnlyExistingCells(false);

                        $product->setFromCellIterator($cellIterator);
                        if (!$product->name || !$product->quantity) {
                            continue;
                        }
                        try {
                            Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                            $product->save();
                            if ($data = $product->getTechnician()) {
                                if (!empty($data['firstname']) && !empty($data['lastname'])) {
                                    $user = $this->_users->getAll(array('lastname' => $data['firstname'], 'firstname' => $data['lastname']))->current();
                                    if (!$user) {
                                        throw new Exception('Nie znaleziono technika');
                                    }
                                    $product->qtyreleased = $product->quantity;
                                    $product->qtyavailable = 0;
                                    $product->statusid = $productStatusReleased->id;
                                    $product->save();
                                    $orderline = $this->_orderlines->createRow(array('orderid' => $order->id,
                                        'productid' => $product->id,
                                        'technicianid' => $user->id,
                                        'quantity' => $product->qtyreleased,
                                        'qtyavailable' => $product->qtyreleased,
                                        'releasedate' => $product->getReleasedate(),
                                        'statusid' => $orderStatusReleased->id));
                                    $orderline->save();
                                }
                            }//var_dump($product->toArray(),$orderline->toArray());exit;
                            $line[] = 'OK';
                            Zend_Db_Table::getDefaultAdapter()->commit();
                        } catch (Exception $e) {
                            Zend_Db_Table::getDefaultAdapter()->rollBack();
                            //var_dump($e->getMessage(), $e->getTraceAsString());exit;
                            //exit;
                            //array_unshift($line, $e->getMessage());
                            $line[] = $e->getMessage();
                        }
                        if (!empty($values['report'])) {
                            $value = current($line);
                            $key = PHPExcel_Cell::stringFromColumnIndex($columnNo);
                            $objPHPExcel->getActiveSheet()->SetCellValue($key . $i, $value);
                            $columnNo++;
                        }
                        
                        foreach ($row->getCellIterator() as $cell) {
                            $value = $cell->getValue();
                            $line[] = $value;
                            if (!empty($values['report'])) {
                                $key = PHPExcel_Cell::stringFromColumnIndex($columnNo);
                                $objPHPExcel->getActiveSheet()->SetCellValue($key . $i, $value);
                            }
                            $columnNo++;
                        }

                        $lines[] = $line;
                    }
                    
                    $this->view->data = $lines;
                    
                    if (!empty($values['report'])) {
                        $filename = $upload->getFileName('import');
                        //$ext = pathinfo($upload->getFileName('import', false), PATHINFO_EXTENSION);
                        $ext = 'csv';
                        $filename = pathinfo($upload->getFileName('import', false), PATHINFO_FILENAME);
                        $filename = '/../data/pliki/migration/' . $filename . '-raport-' . date('YmdHis') . '.' . $ext;
                        //$this->_writeToXLS($lines, $filename);
                        $this->view->filename = $filename;
                        
                        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
                        $objWriter->save($_SERVER['DOCUMENT_ROOT'] . $filename);
                    }
                } catch (Exception $ex) {
                    echo $e->message();
                }
            }
        }
    }

    public function deleteAction() {
        $request = $this->getRequest();
        $id = (array) $request->getParam('id');
        $typeid = $request->getParam('typeid');
        //$id = array_unique((array)$id);
        $product = $this->_products->get($id);
        if (!$product) {
            throw new Exception('Nie znaleziono produktu');
        }
        $form = new  Application_Form_Products_Delete(array('productsCount' => $product->count()));
        $form->setProducts($product);
        $status = $this->_dictionaries->getStatusList('products')->find('deleted', 'acronym');
        $this->view->form = $form;
        $this->view->product = $product;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                if (!$product) {
                    $form->setDescription('Nie zaznaczono produktów do usunięcia');
                    return;
                }
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                foreach ($product as $i => $item) {
                    if ($item->quantity != $item->qtyavailable) {
                        $form->getElement('id-' . $i)->setErrors(array('id-' . $i => 'Nie można usunąć towaru wydanego technikowi'));
                        return;
                    }
                    //$item->statusid = $status->id;
                    $item->delete();
                }
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Produkt usunięty';
            }
        }
    }

    public function acceptAction() {
        $request = $this->getRequest();
        $id = (array) $request->getParam('id');
        $typeid = $request->getParam('typeid');
        //$id = array_unique((array)$id);
        $product = $this->_products->get($id);
        if (!$product) {
            throw new Exception('Nie znaleziono produktu');
        }
        $form = new  Application_Form_Products_Accept(array('productsCount' => $product->count()));
        $form->setProducts($product);
        $status = $this->_dictionaries->getStatusList('products')->find('instock', 'acronym');
        $this->view->form = $form;
        $this->view->product = $product;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                if (!$product) {
                    $form->setDescription('Nie zaznaczono produktów do usunięcia');
                    return;
                }
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                foreach ($product as $i => $item) {
                    if (!$item->isNew()) {
                        $form->getElement('id-' . $i)->setErrors(array('id-' . $i => 'Nie można zaakceptować towaru'));
                        return;
                    }
                    $item->statusid = $status->id;
                    $item->save();
                }
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Produkt zaakceptowany';
            }
        }
    }
    
    public function detailsAction() {
        $request = $this->getRequest();
        $this->view->product = $this->_products->find($request->getParam('id'))->current();
    }

}
