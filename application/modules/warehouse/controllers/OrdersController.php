<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Warehouse_OrdersController extends Application_Controller_Abstract {

    protected $_orders;

    public function init() {
        /* Initialize action controller here */
        $this->_warehouses = new Application_Model_Warehouses_Table();
        $this->_products = new Application_Model_Products_Table();
        $this->_orders = new Application_Model_Orders_Table();
        $this->_orders->setItemCountPerPage(Application_Db_Table::ITEMS_PER_PAGE);
        $this->_orderlines = new Application_Model_Orders_Lines_Table();
        $this->_orderlines->setItemCountPerPage($this->_hasParam('count') ? $this->_getParam('count') : Application_Db_Table::ITEMS_PER_PAGE);
        $this->_orderlines->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : array('dateadd DESC','releasedate DESC'));
        $this->_users = new Application_Model_Users_Table();
        parent::init();

        $context = $this->_helper->getHelper('xlsContext');
        $context->addActionContext('index', 'html')
                ->addActionContext('products-list', array('html', 'json', 'xls'))
                ->addActionContext('basket', 'html')
                ->addActionContext('products-check', 'json')
                ->addActionContext('product-add', 'html')
                ->addActionContext('product-delete', 'html')
                ->addActionContext('product-return', 'html')
                ->addActionContext('product-add-release', 'html')
                ->addActionContext('release', 'html')
                ->setSuffix('html', '')
                ->initContext();

        if ($context->getCurrentContext() == 'xls') {
            $this->_orderlines->setItemCountPerPage(null);
        }
    }

    public function listAction() {
        $this->_forward('index');
    }

    public function indexAction() {
        // action body

        $request = $this->getRequest();
        $pageNumber = $request->getParam('page');
        if ($pageNumber) {
            $this->_orders->setPageNumber($pageNumber);
        }
        $this->view->orders = $this->_orders->getAll();
        $this->view->paginator = $this->_orders->getPaginator();
    }

    public function basketAction() {
        $request = $this->getRequest();
        $status = $this->_dictionaries->getStatusList('orders')->find('new', 'acronym');
        $order = $this->_orders->getAll(array('userid' => $this->_auth->getIdentity()->id))->current();
        if (!$order) {
            return;
        }
        $this->_orderlines->setLazyLoading(false);
        $this->view->products = $this->_orderlines->getAll(array('orderid' => $order->id, 'statusid' => $status->id));
    }

    public function productsCheckAction() {
        $request = $this->getRequest();
        $status = $this->_dictionaries->getStatusList('orders')->find('new', 'acronym');
        $order = $this->_orders->getAll(array('userid' => $this->_auth->getIdentity()->id))->current();
        if (!$order) {
            return;
        }
        $this->_orderlines->setLazyLoading(false);
        $this->view->products = $this->_orderlines->getAll(array('orderid' => $order->id, 'statusid' => $status->id));
        $this->view->itemCount = $this->_orderlines->getPaginator()->getTotalItemCount();
    }

    public function productsListAction() {
        $request = $this->getRequest();
        $status = $this->_dictionaries->getStatusList('orders')->find('new', 'acronym');
        $order = $this->_orders->getAll(array('userid' => $this->_auth->getIdentity()->id))->current();
        if (!$order) {
            $order = $this->_orders->createRow();
            $order->setFromArray(array('userid' => $this->_auth->getIdentity()->id,
                'statusid' => $status->id));
            $order->save();
        }
        $pageNumber = $request->getParam('page');
        if ($pageNumber) {
            $this->_orderlines->setPageNumber($pageNumber);
        }
        $columns = array('warehouse', 'dateadd', 'technician', 'product', 'serial', 'quantity', 'clientnumber', 'client', 'status');
        $orderBy = $request->getParam('orderBy');
        if ($orderBy) {
            list($orderBy, $orderDirection) = explode(" ", $orderBy);
            $this->_orderlines->setOrderBy("{$columns[$orderBy - 1]} $orderDirection");
        }
        if (is_object($this->_orderlines->getOrderBy())) {
            $orderBy = 'dateadd';
            $orderDirection = strpos((string)$this->_orderlines->getOrderBy(), 'desc') !== false ? 'desc' : 'asc';
        } else {
            list($orderBy, $orderDirection) = @explode(" ", $this->_orderlines->getOrderBy());//var_dump($this->_orderlines->getOrderBy());exit;
        }
        if ($orderBy) {
            foreach ($columns as $ix => $columnName) {
                if ($columnName != $orderBy) {
                    continue;
                }
                $ix++;
                $orderBy = "$ix {$orderDirection}";
            }
            $request->setParam('orderBy', $orderBy);
        }
        $request->setParam('orderBy', $orderBy);
        $request->setParam('count', $this->_orderlines->getItemCountPerPage());
        $this->_orderlines->setLazyLoading(false);
        if ($request->getParam('released') || 1) {
            $statusInvoiced = $this->_dictionaries->getStatusList('orders')->find('invoiced', 'acronym');
            $statusReleased = $this->_dictionaries->getStatusList('orders')->find('released', 'acronym');
            $statusReturned = $this->_dictionaries->getStatusList('orders')->find('returned', 'acronym');
            $this->_orderlines->setWhere($this->_orderlines->getAdapter()->quoteInto('statusid IN (?)', array($statusInvoiced->id, $statusReleased->id, $statusReturned->id)));
        }
        if ($this->_auth->getIdentity()->role == 'technician') {
            $this->_orderlines->setWhere($this->_orderlines->getAdapter()->quoteInto("technicianid = {$this->_auth->getIdentity()->id}", null));
        }
        $this->view->filepath = '/../data/temp/';
        $this->view->filename = 'Zestawienie_wydan-' . date('YmdHis') . '.xlsx';
        $this->view->products = $this->_orderlines->getAll($request->getParams());
        $this->view->paginator = $this->_orderlines->getPaginator();
        $this->view->warehouses = $this->_warehouses->getAll();
        $status = $this->_dictionaries->getStatusList('users')->find('active', 'acronym');
        $params['statusid'] = $status->id;
        $params['role'] = 'technician';
        $this->_users->setOrderBy(array('lastname','firstname'));
        $technicians = $this->_users->getAll($params);
        $this->view->technicians = $technicians;
        $this->view->request = $request->getParams();
        $this->view->statuses = $this->_dictionaries->getStatusList('orders');
    }

    public function productAddReleaseAction() {
        // action body

        $request = $this->getRequest();
        $id = (array) $request->getParam('id');
        $product = $this->_products->get($id);
        if (!$product) {
            throw new Exception('Nie znaleziono produktu');
        }
        $this->view->productsCount = $product->count();
        $form = new Application_Form_Orders_ProductAddRelease(array('productsCount' => $product->count()));
        $units = $this->_dictionaries->getDictionaryList('warehouse', 'unit');
        $status = $this->_dictionaries->getStatusList('users')->find('active', 'acronym');
        $params['statusid'] = $status->id;
        $params['role'] = 'technician';
        $this->_users->setOrderBy(array('lastname', 'firstname'));
        $technicians = $this->_users->getAll($params);
        $form->setOptions(array('units' => $units, 'technicians' => $technicians));
        $form->setProducts($product);
        //$form->setDefaults(array('unitid' => $product->unitid));
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                $counter = 0;
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                foreach ($product as $i => $item) {
                    $status = $this->_dictionaries->getStatusList('orders')->find('released', 'acronym');
                    if (!$order = $this->_orders->getAll(array('userid' => $this->_auth->getIdentity()->id))->current()) {
                        $order = $this->_orders->createRow();
                        $order->setFromArray(array('userid' => $this->_auth->getIdentity()->id));
                        $order->save();
                    }
                    if (!$values['unitid-' . $i]) {
                        $form->getElement('unitid-' . $i)->setErrors(array('unitid-' . $i => 'Brak wartości w polu jednostka'));
                        return;
                    }
                    if ($values['quantity-' . $i] <= 0) {
                        $form->getElement('quantity-' . $i)->setErrors(array('unitid-' . $i => 'Nieprawidłowa wartość w polu ilość'));
                        return;
                    }
                    if ($values['quantity-' . $i] > $item->qtyavailable) {
                        $form->getElement('quantity-' . $i)->setErrors(array('quantity-' . $i => 'Zbyt duża wartość w polu ilość'));
                        return;
                    }
                    $line = $this->_orderlines->createRow();
                    $line->setFromArray(array('orderid' => $order->id,
                        'productid' => $item->id,
                        'quantity' => $values['quantity-' . $i],
                        'qtyavailable' => $values['quantity-' . $i],
                        'statusid' => $status->id,
                        'dateadd' => date('Y-m-d H:i:s')));
                    $line->releasedate = date('Y-m-d H:i:s');
                    $line->save();
                    $item->qtyavailable -= $values['quantity-' . $i];
                    if ($item->qtyavailable < 0) {
                        $form->getElement('quantity-' . $i)->setErrors(array('quantity-' . $i => 'Zbyt duża wartość w polu ilość'));
                        return;
                    }
                    $item->qtyreleased += $values['quantity-' . $i];
                    if ($item->qtyavailable < 0) {
                        $form->getElement('quantity-' . $i)->setErrors(array('quantity-' . $i => 'Zbyt duża wartość w polu ilość'));
                        return;
                    }
                    if ($item->qtyavailable == 0) {
                        $status = $this->_dictionaries->getStatusList('products')->find('reserved', 'acronym');
                        $item->statusid = $status->id;
                    }
                    $item->save();
                    $status = $this->_dictionaries->getStatusList('orders')->find('released', 'acronym');
                    $line->statusid = $status->id;
                    $line->technicianid = $values['technicianid'];
                    $line->save();
                    $item = $line->getProduct();
                    if ($item->qtyavailable == 0) {
                        $status = $this->_dictionaries->getStatusList('products')->find('released', 'acronym');
                        $item->statusid = $status->id;
                    }//var_dump($line->toArray(),$item->toArray());exit;
                    $item->save();
                    $counter++;
                }
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Towar wydany technikowi (' . $counter . ' pozycji)';
            }
        }
    }

    public function productAddAction() {
        // action body

        $request = $this->getRequest();
        $id = (array) $request->getParam('id');
        $product = $this->_products->get($id);
        if (!$product) {
            throw new Exception('Nie znaleziono produktu');
        }
        $this->view->productsCount = $product->count();
        $form = new Application_Form_Orders_ProductAdd(array('productsCount' => $product->count()));
        $units = $this->_dictionaries->getDictionaryList('warehouse', 'unit');
        $technicians = $this->_users->getAll();
        $form->setOptions(array('units' => $units));
        $form->setProducts($product);
        //$form->setDefaults(array('unitid' => $product->unitid));
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues(true);
                $counter = 0;
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                foreach ($product as $i => $item) {
                    if (!$values['quantity-' . $i]) {
                        continue;
                    }
                    if ($values['quantity-' . $i] <= 0) {
                        $form->getElement('quantity-' . $i)->setErrors(array('unitid-' . $i => 'Nieprawidłowa wartość w polu ilość'));
                        return;
                    }
                    $status = $this->_dictionaries->getStatusList('orders')->find('new', 'acronym');
                    if (!$order = $this->_orders->getAll(array('userid' => $this->_auth->getIdentity()->id))->current()) {
                        $order = $this->_orders->createRow();
                        $order->setFromArray(array('userid' => $this->_auth->getIdentity()->id));
                        $order->save();
                    }
                    if (!$values['unitid-' . $i]) {
                        $form->getElement('unitid-' . $i)->setErrors(array('unitid-' . $i => 'Brak wartości w polu jednostka'));
                        return;
                    }
                    if ($values['quantity-' . $i] > $item->qtyavailable) {
                        $form->getElement('quantity-' . $i)->setErrors(array('quantity-' . $i => 'Zbyt duża wartość w polu ilość'));
                        return;
                    }
                    $line = $this->_orderlines->createRow();
                    $line->setFromArray(array('orderid' => $order->id,
                        'productid' => $item->id,
                        'quantity' => $values['quantity-' . $i],
                        'statusid' => $status->id,
                        'dateadd' => date('Y-m-d H:i:s')));
                    $line->save();
                    $item->qtyavailable -= $values['quantity-' . $i];
                    if ($item->qtyavailable < 0) {
                        $form->getElement('quantity-' . $i)->setErrors(array('quantity-' . $i => 'Zbyt duża wartość w polu ilość'));
                        return;
                    }
                    $item->qtyreserved += $values['quantity-' . $i];
                    if ($item->qtyreserved > $item->quantity) {
                        $form->getElement('quantity-' . $i)->setErrors(array('quantity-' . $i => 'Zbyt duża wartość w polu ilość'));
                        return;
                    }
                    if ($item->qtyavailable == 0) {
                        $status = $this->_dictionaries->getStatusList('products')->find('reserved', 'acronym');
                        $item->statusid = $status->id;
                    }
                    $item->save();
                    $counter++;
                }
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Towar dodany do koszyka (' . $counter . ' pozycji)';
            }
        }
    }

    public function releaseAction() {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $order = $this->_orders->getAll(array('userid' => $this->_auth->getIdentity()->id))->current();
        if (!$order) {
            throw new Exception('Nie znaleziono koszyka');
        }
        $status = $this->_dictionaries->getStatusList('orders')->find('new', 'acronym');
        $this->_orderlines->setLazyLoading(false);
        $products = $this->_orderlines->getAll(array('orderid' => $order->id, 'statusid' => $status->id));
        $this->view->productsCount = $products->count();
        $form = new Application_Form_Orders_Release(array('productsCount' => $products->count()));
        $status = $this->_dictionaries->getStatusList('users')->find('active', 'acronym');
        $params['statusid'] = $status->id;
        $params['role'] = 'technician';
        $technicians = $this->_users->getAll($params);
        $form->setOptions(array('technicians' => $technicians));
        $form->setDefaults($order->toArray());
        $form->setProducts($products);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                if (!$products) {
                    $form->setDescription('Nie zaznaczono produktów do wydania');
                    return;
                }
                $values = $form->getValues();
                $counter = 0;
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                $this->_orderlines->setLazyLoading(true);
                foreach ($products as $i => $product) {
                    if (!$values['id-' . $i]) {
                        continue;
                    }
                    $ids[] = $product->id;
                    $line = $this->_orderlines->get($product->id);
                    if (!$line->isNew()) {
                        $form->setDescription('Nieprawidłowy status towaru');
                        return;
                    }
                    $status = $this->_dictionaries->getStatusList('orders')->find('released', 'acronym');
                    $line->statusid = $status->id;
                    $line->technicianid = $values['technicianid'];
                    $line->releasedate = date('Y-m-d H:i:s');
                    $line->qtyavailable += $line->quantity;
                    $line->save();
                    $product = $line->getProduct();
                    $product->qtyreserved -= $line->quantity;
                    if ($product->qtyreserved < 0) {
                        throw new Exception('Nieprawidłowa ilość do wydania');
                    }
                    $product->qtyreleased += $line->quantity;
                    if ($product->qtyreleased > $product->quantity || $product->qtyreturned > $product->quantity) {
                        $form->setDescription('Nieprawidłowa ilość do wydania');
                        return;
                    }
                    if ($product->qtyavailable == 0) {
                        $status = $this->_dictionaries->getStatusList('products')->find('released', 'acronym');
                        $product->statusid = $status->id;
                    }//var_dump($line->toArray(),$product->toArray());exit;
                    $product->save();
                    $counter++;
                }
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->ids = $ids;
                $this->view->success = 'Produkty wydane technikowi (' . $counter . ' pozycji)';
            }
        }
    }

    public function productReturnAction() {
        $request = $this->getRequest();
        $id = (array) $request->getParam('id');
        $this->_orderlines->setLazyLoading(false);
        $lines = $this->_orderlines->get($id);
        if (!$lines) {
            throw new Exception('Nie znaleziono produktu');
        }
        $form = new Application_Form_Orders_ProductReturn(array('productsCount' => $lines->count()));
        $form->setProducts($lines);
        $codeTypes = array('modeminterchange', 'decoderinterchange');
        $dictionary = $this->_dictionaries->getDictionaryList('service');
        $statusDeleted = $this->_dictionaries->getStatusList('dictionaries')->find('deleted', 'acronym');
        foreach ($codeTypes as $type) {
            if ($code = $dictionary->find($type . 'code', 'acronym')) {
                $codes = array();
                foreach ($code->getChildren()->toArray() as $row) {
                    if ($row['statusid'] == $statusDeleted->id) {
                        continue;
                    }
                    if ($row['datefrom'] && strtotime($row['datefrom']) < time()) {
                        continue;
                    }
                    if ($row['datetill'] && strtotime($row['datetill']) < time()) {
                        continue;
                    }
                    $codes[] = $row;
                }
                $options[$type . 'codes'] = $codes;
            }
        }
        $options['demagecodes'] = array_merge($options['modeminterchangecodes'], $options['decoderinterchangecodes']);
        $form->setDemagecodes($options['demagecodes']);
        $this->view->form = $form;
        $this->view->product = $lines;
        
        $units = $this->_dictionaries->getDictionaryList('warehouse', 'unit');

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                if (!$lines) {
                    $form->setDescription('Nie zaznaczono produktów do wydania');
                    return;
                }
                $data = $request->getPost();
                if (!empty($data['demaged']))
                    foreach ($data['demaged'] as $key => $value) {
                        $data[$key] = $value;
                    }
                if (!empty($data['demagecodeid']))
                    foreach ($data['demagecodeid'] as $key => $value) {
                        $data[$key] = $value;
                    }
                $values = $form->getValues();
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                $this->_orderlines->setLazyLoading(true);
                foreach ($lines as $i => $line) {
                    if (!empty($line->serviceid)) {
                        $form->getElement('id-' . $i)->setErrors(array('id-' . $i => 'Produkt został wykorzystany w zleceniu'));
                        return;
                    }
                    $product = $line->getProduct();
                    if ($data['demaged']['demaged-' . $i] || $data['demagecodeid']['demagecodeid-' . $i]) {
                        $table = new Application_Model_Services_Returns_Table();
                        if (!$data['demagecodeid']['demagecodeid-' . $i]) {
                            $form->getElement('demagecodeid-' . $i)->setErrors(array('demagecodeid-' . $i => 'Wymagane podanie kodu uszkodzenia'));
                            return;
                        }
                        if (!$data['demaged']['demaged-' . $i]) {
                            $data['demagecodeid']['demagecodeid-' . $i] = 1;
                        }
                        $params = array('name' => $product->serial ? $product->serial : $product->name, 
                            'quantity' => $line->qtyavailable, 
                            'productid' => $line->id,
                            'unitid' => $units->find('szt', 'acronym') -> id,
                            'demaged' => (int)$data['demaged']['demaged-' . $i],
                            'demagecodeid' => (int)$data['demagecodeid']['demagecodeid-' . $i],
                            'statusid' => $this->_dictionaries->getStatusList('returns')->find('new', 'acronym')->id
                            );
                        $serviceProduct = $table->createRow($params);//var_dump($serviceProduct->toArray());exit;
                        try {
                            $serviceProduct->save();
                        } catch (Exception $e) {
                            $form->getElement('demaged-' . $i)->setErrors(array('demaged-' . $i => $e->getMessage()));
                            return;
                        }
                        $product->qtyreturned += $line->qtyavailable;
                        if ($product->qtyreturned > $product->quantity) {
                            $form->getElement('id-' . $i)->setErrors(array('id-' . $i => 'Wystąpił problem ze zwrotem'));
                            return;
                        }
                        if ($product->qtyreturned == $product->quantity) {
                            $status = $this->_dictionaries->getStatusList('products')->find('returned', 'acronym');
                            $product->statusid = $status->id;
                        }
                    } else {
                        $product->qtyreleased -= $line->qtyavailable;
                        if ($product->qtyreleased < 0) {
                            $form->getElement('id-' . $i)->setErrors(array('id-' . $i => 'Wystąpił problem ze zwrotem'));
                            return;
                        }
                        $product->qtyavailable += $line->qtyavailable;
                        if ($product->qtyavailable > $product->quantity) {
                            $form->getElement('id-' . $i)->setErrors(array('id-' . $i => 'Wystąpił problem ze zwrotem'));
                            return;
                        }
                        if ($product->qtyavailable > 0) {
                            $status = $this->_dictionaries->getStatusList('products')->find('instock', 'acronym');
                            $product->statusid = $status->id;
                        }
                    }//var_dump($product->toArray());exit;
                    $product->save();
                    $line = $this->_orderlines->get($line->id);
                    $line->qtyreturned += $line->qtyavailable;
                    if ($line->qtyreturned > $line->quantity) {
                        $form->getElement('id-' . $i)->setErrors(array('id-' . $i => 'Wystąpił problem ze zwrotem'));
                        return;
                    }
                    $line->qtyavailable -= $line->qtyavailable;
                    if ($line->qtyavailable < 0) {
                        $form->getElement('id-' . $i)->setErrors(array('id-' . $i => 'Wystąpił problem ze zwrotem'));
                        return;
                    }
                    if ($line->qtyavailable == 0 && $line->qtyreturned == 0) {
                        $status = $this->_dictionaries->getStatusList('orders')->find('deleted', 'acronym');
                        $line->statusid = $status->id;
                    }
                    if ($line->qtyreturned == $line->quantity) {
                        $status = $this->_dictionaries->getStatusList('orders')->find('returned', 'acronym');
                        $line->statusid = $status->id;
                    }
                    //var_dump($line->toArray(),$product->toArray(),$serviceProduct->toArray());exit;
                    $line->save();
                }
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Produkty zostały zwrócone';
            }
        }
    }

    public function productDeleteAction() {
        $request = $this->getRequest();
        $id = (array)$request->getParam('id');
        $lines = $this->_orderlines->get($id);
        if (!$lines) {
            throw new Exception('Nie znaleziono produktu');
        }
        $form = new Application_Form_Orders_ProductDelete(array('productsCount' => $lines->count()));
        $form->setProducts($lines);
        $this->view->form = $form;
        $this->view->product = $lines;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                if (!$lines) {
                    $form->setDescription('Nie zaznaczono produktów do usunięcia z koszyka');
                    return;
                }
                $values = $form->getValues();
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                $this->_orderlines->setLazyLoading(true);
                foreach ($lines as $i => $line) {
                    $line = $this->_orderlines->get($line->id);
                    $status = $this->_dictionaries->getStatusList('orders')->find('deleted', 'acronym');
                    $line->statusid = $status->id;
                    $line->technicianid = null;
                    $line->save();
                    $product = $line->getProduct();
                    $product->qtyreserved -= $line->quantity;
                    if ($product->qtyreserved < 0) {
                        $form->getElement('id-' . $i)->setErrors(array('id-' . $i => 'Wystąpił problem z usunięciem z koszyka'));
                        return;
                    }
                    $product->qtyavailable += $line->quantity;
                    if ($product->qtyavailable > $product->quantity) {
                        $form->getElement('id-' . $i)->setErrors(array('id-' . $i => 'Wystąpił problem z usunięciem z koszyka'));
                        return;
                    }
                    if ($product->qtyavailable == $product->quantity) {
                        $status = $this->_dictionaries->getStatusList('products')->find('instock', 'acronym');
                        $product->statusid = $status->id;
                    }
                    $product->save();
                }
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Produkty zostały usunięte z koszyka';
            }
        }
    }

}
