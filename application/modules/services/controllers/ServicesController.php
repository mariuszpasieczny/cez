<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Services_ServicesController extends Application_Controller_Abstract {

    protected $_services;
    protected $_clients;
    protected $_orders;
    protected $_orderlines;

    public function init() {
        /* Initialize action controller here */
        $this->_warehouses = new Application_Model_Warehouses_Table();
        $this->_catalog = new Application_Model_Catalog_Table();
        $this->_catalog->setItemCountPerPage(null);
        $this->_catalog->setOrderBy(array('name'));
        $this->_clients = new Application_Model_Clients_Table();
        $this->_services = new Application_Model_Services_Table();
        $this->_services->setItemCountPerPage($this->_hasParam('count') ? $this->_getParam('count') : Application_Db_Table::ITEMS_PER_PAGE);
        $this->_services->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : array('planneddate', new Zend_Db_Expr('clientstreet COLLATE utf8_polish_ci'), 'clientstreetno', 'clientapartment'));
        $this->_users = new Application_Model_Users_Table();
        $this->_orders = new Application_Model_Orders_Table();
        $this->_orderlines = new Application_Model_Orders_Lines_Table();

        parent::init();

        $context = $this->_helper->getHelper('xlsContext');
        $context->addActionContext('index', 'html')
                ->addActionContext('list', array('html', 'json', 'xls'))
                ->addActionContext('index', array('html', 'json', 'xls'))
                ->addActionContext('search', array('html', 'json', 'xls'))
                //->addActionContext('list', array('html', 'json'))
                //->addActionContext('index', array('html', 'json'))
                ->addActionContext('edit', 'html')
                ->addActionContext('set', 'html')
                ->addActionContext('details', 'html')
                ->addActionContext('view', 'html')
                ->addActionContext('import', 'html')
                ->addActionContext('migration', 'html')
                ->addActionContext('assign', 'html')
                ->addActionContext('finish', 'html')
                ->addActionContext('close', 'html')
                ->addActionContext('close-multi', 'html')
                ->addActionContext('return', 'html')
                ->addActionContext('delete', 'html')
                ->addActionContext('withdraw', 'html')
                ->addActionContext('send-comments', 'html')
                ->addActionContext('check-solution-code', 'json')
                ->addActionContext('get-solution-code-list', 'json')
                ->setSuffix('html', '')
                ->initContext();

        if ($context->getCurrentContext() == 'xls') {
            $this->_services->setItemCountPerPage(null);
        }
    }

    public function listAction() {
        // action body

        $request = $this->getRequest();
        $warehouseid = $request->getParam('warehouseid');
        $this->view->typeid = $typeid = $request->getParam('typeid');
        if ($warehouseid) {
            $warehouse = $this->_warehouses->get($warehouseid);
            $this->view->warehouse = $warehouse;
        }
        $pageNumber = $request->getParam('page');
        if ($pageNumber) {
            $this->_services->setPageNumber($pageNumber);
        }

        $request->setParam('count', $this->_services->getItemCountPerPage());
        $this->_services->setLazyLoading(false);
        $statuses = $this->_dictionaries->getStatusList('service');

        $this->view->filepath = '/../data/temp/';
        $types = $this->_dictionaries->getTypeList('service');
        switch ($this->_getParam('typeid')) {
            case $types->find('installation', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_XLS_Installation');
                $this->view->filename = 'Zestawienie_instalacyjne-' . date('YmdHis') . '.xlsx';
                $this->view->template = $_SERVER['DOCUMENT_ROOT'] . '/../data/pliki/zlecenia instalacyjne.xls';
                $this->view->rowNo = 2;
                $columns = array('planneddate', 'timefrom', 'servicetype', 'calendar', 'number', 'clientid', 'client', 'technician', 'documentspassed', 'closedupc', 'status', 'performed');
                $this->view->codeTypes = array('installation', 'installationcancel');
                if ($this->_auth->getIdentity()->role == 'technician') {
                    $this->_services->setWhere($this->_services->getAdapter()->quoteInto("(technicianid = {$this->_auth->getIdentity()->id})", null));
                }
                break;
            case $types->find('service', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_XLS_Export');
                $this->view->filename = 'Zestawienie_serwisowe-' . date('YmdHis') . '.xlsx';
                $this->view->template = $_SERVER['DOCUMENT_ROOT'] . '/../data/pliki/zlecenia serwisowe.xls';
                $this->view->rowNo = 2;
                $columns = array('planneddate', 'timefrom', 'timetill', 'number', 'clientid', 'client', 'technician', 'status', 'performed');
                $this->view->codeTypes = array('error', 'solution', 'cancellation', 'modeminterchange', 'decoderinterchange');
                if ($this->_auth->getIdentity()->role == 'technician') {
                    $status = $statuses->find('new', 'acronym');
                    $this->_services->setWhere($this->_services->getAdapter()->quoteInto("(technicianid = {$this->_auth->getIdentity()->id} OR statusid = {$status->id})", null));
                }
                $this->_services->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : array('planneddate', 'timefrom', new Zend_Db_Expr('clientstreet COLLATE utf8_polish_ci'), 'clientstreetno', 'clientapartment'));
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
        
        $this->view->dictionary = $dictionary = $this->_dictionaries->getDictionaryList('service');

        $orderBy = $request->getParam('orderBy');
        if ($orderBy) {
            list($orderBy, $orderDirection) = explode(" ", $orderBy);
            switch($columns[$orderBy - 1]) {
                case 'client':
                    $this->_services->setOrderBy(array(new Zend_Db_Expr("clientstreet $orderDirection")));
                    break;
                default:
                    $this->_services->setOrderBy("{$columns[$orderBy - 1]} $orderDirection");
            }
        }
        if (is_object($this->_services->getOrderBy())) {
            $orderBy = 'client';
            $orderDirection = strpos((string)$this->_services->getOrderBy(), 'desc') !== false ? 'desc' : 'asc';
        } else {
            list($orderBy, $orderDirection) = @explode(" ", $this->_services->getOrderBy());//var_dump($this->_services->getOrderBy());
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

        $status = $statuses->find('deleted', 'acronym');
        if (!$request->getParam('statusid')) {
            $this->_services->setWhere($this->_services->getAdapter()->quoteInto("statusid != ?", $status->id));
        }
        $params = array_filter(array_intersect_key($request->getParams(), array_flip(array('statusid', 'technicianid', 'clientnumber', 'client', 'clientstreet', 'clientstreetno', 'clientapartment', 'number', 'planneddatefrom', 'planneddatetill'))));
        if ($request->getParam('street')&&0) {
            $params['clientaddress'] = $request->getParam('street');
            if ($request->getParam('streetno')) {
                $params['clientaddress'] .= ' ' . $request->getParam('streetno');
                if ($request->getParam('apartmentno')) {
                    $params['clientaddress'] .=  '/' . $request->getParam('apartmentno');
                }
            }
            $request->setParam('clientaddress', $params['clientaddress']);
        }
        if (empty($params)) {
            $statuses = array($statuses->find('new', 'acronym')->id,
                $statuses->find('assigned', 'acronym')->id,
                $statuses->find('reassigned', 'acronym')->id);
            //$this->_services->setWhere($this->_services->getAdapter()->quoteInto("statusid IN (?)", $statuses));
            //$request->setParam('statusid', $statuses);
            $planneddatefrom = date('Y-m-d');
            $this->_services->setWhere($this->_services->getAdapter()->quoteInto("DATE_FORMAT(planneddate, '%Y-%m-%d') >= ?", $planneddatefrom));
            $request->setParam('planneddatefrom', $planneddatefrom);
        }
        $this->view->request = $request->getParams();
        $this->view->services = $this->_services->getAll($request->getParams());
        $this->view->paginator = $this->_services->getPaginator();
        $status = $this->_dictionaries->getStatusList('users')->find('active', 'acronym');
        $params['statusid'] = $status->id;
        $params['role'] = 'technician';
        $this->_users->setOrderBy(array('lastname','firstname'));
        $technicians = $this->_users->getAll($params);
        $this->view->technicians = $technicians;
        $this->view->statuses = $this->_dictionaries->getStatusList('service');
        $this->view->types = $types;
        $this->view->clients = $this->_clients->getAllStreets();
    }

    public function searchAction() {
        // action body

        $request = $this->getRequest();
        $typeid = $request->getParam('typeid');

        $this->_services->setLazyLoading(false);
        $statuses = $this->_dictionaries->getStatusList('service');
        $status = $statuses->find('deleted', 'acronym');
        $this->_services->setWhere($this->_services->getAdapter()->quoteInto("statusid != {$status->id}", null));
        $params = array_filter(array_intersect_key($request->getParams(), array_flip(array('clientnumber', 'clientaddress', 'number'))));
        //$params['clientaddress'] = $request->getParam('street') . ' ' . $request->getParam('streetno') . '/' . $request->getParam('apartmentno') . ',';
        if ($request->getParam('street') && $request->getParam('streetno')) {
            //$params['clientaddress'] = $request->getParam('street') . ' ' . $request->getParam('streetno');
            $params['clientstreet'] = $request->getParam('street');
            $params['clientstreetno'] = $request->getParam('streetno');
            if ($request->getParam('apartmentno')) {
                //$params['clientaddress'] .=  '/' . $request->getParam('apartmentno');
                $params['clientapartment'] = $request->getParam('apartmentno');
            }
            //$request->setParam('clientaddress', $params['clientaddress']);
        }
        if (!empty($params)) {
            $this->view->services = $this->_services->getAll($params);
        }
        $this->view->clients = $this->_clients->getAllStreets();
    }

    public function indexAction() {
        $this->_forward('list');
    }
    
    public function detailsAction() {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $typeid = $request->getParam('typeid');
        //$id = array_unique((array)$id);
        $types = $this->_dictionaries->getTypeList('service');
        switch ($this->_getParam('typeid')) {
            case $types->find('installation', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Installation');
                break;
            case $types->find('service', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Service');
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
        $service = $this->_services->get($id);
        if (!$service) {
            throw new Exception('Nie znaleziono zgłoszenia');
        }

        $this->view->service = $service;
        $this->view->types = $types;
    }

    public function viewAction() {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $typeid = $request->getParam('typeid');
        //$id = array_unique((array)$id);
        $types = $this->_dictionaries->getTypeList('service');
        switch ($this->_getParam('typeid')) {
            case $types->find('installation', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Installation');
                break;
            case $types->find('service', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Service');
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
        $service = $this->_services->get($id);
        if (!$service) {
            throw new Exception('Nie znaleziono zgłoszenia');
        }
        if (!empty($service->datefinished)) {
            if ($types->find('installation', 'acronym')->id == $typeid /* && !in_array($this->_auth->getIdentity(), array('admin', 'coordinator')) */) {
                //$service->datefinished = $service->planneddate . ' ' . date('H:i'); //$service->timetill;
                $service->datefinished = date('Y-m-d', strtotime($service->planneddate));
            } else if ($types->find('service', 'acronym')->id == $typeid) {
                //$service->datefinished = date('H:i'); //$service->timetill;
            }
        }

        $this->view->service = $service;
        $this->view->types = $types;
    }

    public function addAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        // action body

        $request = $this->getRequest();
        $id = $request->getParam('id');
        //$id = array_unique((array)$id);
        $warehouseid = $request->getParam('warehouseid');
        $clientid = $request->getParam('clientid');
        $typeid = $request->getParam('typeid');
        $types = $this->_dictionaries->getTypeList('service');
        $warehouses = $this->_warehouses->getAll();
        $clients = $this->_clients->getAll();
        $this->_users->setOrderBy(array('lastname','firstname'));
        $technicians = $this->_users->getAll();
        $statuses = $this->_dictionaries->getStatusList('service');
        $servicetypes = $this->_dictionaries->getDictionaryList('service', 'type');
        $units = $this->_dictionaries->getDictionaryList('warehouse', 'unit');
        $status = $this->_dictionaries->getStatusList('catalog')->find('deleted', 'acronym');
        $this->_catalog->setWhere("statusid != {$status->id}");
        $catalog = $this->_catalog->getAll();
        $options = array('types' => $types->toArray(),
            'warehouses' => $warehouses->toArray(),
            'clients' => $clients->toArray(),
            'statuses' => $statuses->toArray(),
            'servicetypes' => $servicetypes->toArray(),
            'technicians' => $technicians->toArray(),
            'catalog' => $catalog->toArray());
        switch ($typeid) {
            // zlecenie instalacyjne
            case $types->find('installation', 'acronym')->id:
                //$form = new Application_Form_Services_Installation();
                $codeTypes = array('installation', 'installationcancel', 'modeminterchange', 'decoderinterchange');
                $this->_services->setRowClass('Application_Model_Services_Installation');
                break;
            // zlecenie serwisowe
            case $types->find('service', 'acronym')->id:
                $regions = $this->_dictionaries->getAll(array('acronym' => 'region'))->current()->getChildren();
                $laborcodes = $this->_dictionaries->getAll(array('acronym' => 'laborcode'))->current()->getChildren();
                $complaintcodes = $this->_dictionaries->getAll(array('acronym' => 'complaintcode'))->current()->getChildren();
                $calendars = $this->_dictionaries->getAll(array('acronym' => 'calendar'))->current()->getChildren();
                $areas = $this->_dictionaries->getAll(array('acronym' => 'area'))->current()->getChildren();
                //$form = new Application_Form_Services_Service();
                $options['regions'] = $regions->toArray();
                $options['laborcodes'] = $laborcodes->toArray();
                $options['complaintcodes'] = $complaintcodes->toArray();
                $options['calendars'] = $calendars->toArray();
                $options['areas'] = $areas->toArray();
                $codeTypes = array('error', 'solution', 'cancellation', 'modeminterchange', 'decoderinterchange');
                $this->_services->setRowClass('Application_Model_Services_Service');
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
        $parents = $this->_dictionaries->getAll(array('parentid' => '0'));
        $dictionary = $this->_dictionaries->getDictionaryList('service');
        if ($id) {
            $service = $this->_services->get($id);
            if (!empty($service->datefinished)) {
                if ($types->find('installation', 'acronym')->id == $typeid /* && !in_array($this->_auth->getIdentity(), array('admin', 'coordinator')) */) {
                    //$service->datefinished = $service->planneddate . ' ' . date('H:i'); //$service->timetill;
                    $service->datefinished = date('Y-m-d', strtotime($service->datefinished ? $service->datefinished : $service->planneddate));
                } else if ($types->find('service', 'acronym')->id == $typeid) {
                    $service->datefinished = $service->datefinished ? date('H:i', strtotime($service->datefinished)) : null; //$service->timetill;
                }
            }
            $statusInvoiced = $this->_dictionaries->getStatusList('orders')->find('invoiced', 'acronym');
            $statusReleased = $this->_dictionaries->getStatusList('orders')->find('released', 'acronym');
            $this->_orderlines->setLazyLoading(false);
            if ($service->technicianid) {
                $this->_orderlines->setWhere("technicianid = '{$service->technicianid}' AND (statusid = {$statusReleased->id} OR (statusid = {$statusInvoiced->id} AND serviceid = {$service->id}))");
                $products = $this->_orderlines->getAll(
                    //array(
                    //'statusid' => array(
                    //    $statusInvoiced->id, 
                    //    $statusReleased->id
                    //), 
                    //'technicianid' => $service->technicianid)
                );//var_dump($products->toArray());
            }
            $options['products'] = $products; //->toArray();
            $defaults['productid'] = $service->getProducts()->toArray();
            /*$products = explode(',', $service->serialnumbers);
            $products = array_combine($products, $products);
            $options['productsreturned'] = $products;
            $products = explode(',', $service->productsreturned);
            $products = array_combine($products, $products);
            $defaults['productreturnedid'] = $products;*/
            $productsReturned = explode(',', $service->productsreturned);
            $productsReturned = array_combine($productsReturned, $productsReturned);
            $defaults['productreturnedid'] = $productsReturned;
            $defaults['productreturnedid'] = $service->getReturns()->toArray();
            $serialsReturned = explode(',', $service->serialnumbers);
            $serialsReturned = array_combine($serialsReturned, $serialsReturned);
            $serialsReturned = array_merge($serialsReturned, $productsReturned);
            $options['productsreturned'] = $serialsReturned;
            $this->_dictionaries->setLazyLoading(false);
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
                    $attributeId = $code->id;
                    if ($codes = $service->getCodes()->filter(array('attributeid' => $attributeId))) {
                        $defaults[$type . 'codeid'] = $codes->toArray();
                    }
                }
            }
            $options['demagecodes'] = array_merge($options['modeminterchangecodes'], $options['decoderinterchangecodes']);
            if (!empty($defaults['installationcodeid']))
            foreach ($defaults['installationcodeid'] as $i => $item) {
                $defaults['installationcodeid-' . $i] = $item;
            }
            if (!empty($defaults['productreturnedid']))
            foreach ($defaults['productreturnedid'] as $i => $item) {
                $defaults['productreturnedid-' . $i] = $item;
            }
            if (!empty($defaults['productid']))
            foreach ($defaults['productid'] as $i => $item) {
                $defaults['productid-' . $i] = $item;
            }
            if (!empty($defaults['catalogid']))
            foreach ($defaults['catalogid'] as $i => $item) {
                $defaults['catalogid-' . $i] = $item;
            }
        } else {
            $defaults = array('warehouseid' => $warehouseid,
                'clientid' => $clientid,
                'typeid' => $typeid,
                'statusid' => $statuses->find('new', 'acronym')->id,
                    //'performed' => $request->getParam('performed') !== null ? $request->getParam('performed') : 1
            );
        }
        $productsReturnedCount = @sizeof($defaults['productreturnedid']);
        $installationCodesCount = @sizeof($defaults['installationcodeid']);
        $productsCount = @sizeof($defaults['productid']);
        if ($this->getRequest()->isPost()) {
            $data = $request->getPost();
            $productsReturnedCount = @sizeof($this->getRequest()->getParam('productreturnedid'));
            $installationCodesCount = @sizeof($this->getRequest()->getParam('installationcodeid'));
            $productsCount = @sizeof($this->getRequest()->getParam('productid'));
        }
        switch ($typeid) {
            // zlecenie instalacyjne
            case $types->find('installation', 'acronym')->id:
                $form = new Application_Form_Services_Installation(array('installationCodesCount' => $installationCodesCount,
                    'productsReturnedCount' => $productsReturnedCount,
                    'productsCount' => $productsCount));
                break;
            // zlecenie serwisowe
            case $types->find('service', 'acronym')->id:
                $form = new Application_Form_Services_Service(array('installationCodesCount' => $installationCodesCount,
                    'productsReturnedCount' => $productsReturnedCount,
                    'productsCount' => $productsCount));
                break;
        }
        if ($service) {
            $form->setDefaults($service->toArray());
        }
        $form->setOptions($options); 
        $form->setDefaults($defaults); 
        if (!in_array($this->_auth->getIdentity()->role, array('admin', 'coordinator'))) {
            $form->removeElement('coordinatorcomments');
        }
        $this->view->form = $form;
        $this->view->service = $service;
        $this->view->types = $types;

        if ($this->getRequest()->isPost()) {
            $data = $request->getPost();
            if (!empty($data['installationcodeid'])) 
                foreach ($data['installationcodeid'] as $key => $value) {
                    $data[$key] = $value;
                }
            if (!empty($data['productid'])) 
                foreach ($data['productid'] as $key => $value) {
                    $data[$key] = $value;
                }
            if (!empty($data['quantity'])) 
                foreach ($data['quantity'] as $key => $value) {
                    $data[$key] = $value;
                }
            if (!empty($data['productreturnedid'])) 
                foreach ($data['productreturnedid'] as $key => $value) {
                    $data[$key] = $value;
                }
            if (!empty($data['demaged']))
                foreach ($data['demaged'] as $key => $value) {
                    $data[$key] = $value;
                }
            if (!empty($data['demagecodeid']))
                foreach ($data['demagecodeid'] as $key => $value) {
                    $data[$key] = $value;
                }
            if (!empty($data['catalogid']))
                foreach ($data['catalogid'] as $key => $value) {
                    $data[$key] = $value;
                }
            $form->setDefaults($data);
            $values = $form->getValues();
            if (($values['performed'] === '1' || $values['performed'] === '0') && !$values['datefinished']) {
                $form->getElement('datefinished')->setRequired(true);
            }
            if ($form->isValid($data)) {
                $values = $form->getValues();
                /* if ($service && $service->isAssigned() && !$values['technicianid'] && $values['statusid'] != $statuses->find('new', 'acronym')->id) {
                  $form->getElement('statusid')->setErrors(array('statusid' => 'Nie można usunąć przypisanego technika dla przypisanego zlecenia'));
                  return;
                  }
                  if ($service && $service->isAssigned() && !in_array($values['statusid'], array($statuses->find('assigned', 'acronym')->id, $statuses->find('finished', 'acronym')->id))) {
                  $form->getElement('statusid')->setErrors(array('statusid' => 'Nieprawidłowy status zlecenia'));
                  return;
                  }
                  if ($service && $service->isFinished() && !in_array($values['statusid'], array($statuses->find('finished', 'acronym')->id, $statuses->find('closed', 'acronym')->id))) {
                  $form->getElement('statusid')->setErrors(array('statusid' => 'Nieprawidłowy status zlecenia'));
                  return;
                  }
                  if ($service && $service->isClosed() && !in_array($values['statusid'], array($statuses->find('closed', 'acronym')->id))) {
                  $form->getElement('statusid')->setErrors(array('statusid' => 'Nieprawidłowy status zlecenia'));
                  return;
                  } */
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                //var_dump($service->toArray(),$value);exit;
                if ($id) {
                    $service->setFromArray($values);
                    //if ($values['performed'] === '0' && !$values['technicalcomments']) {
                    //    $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie uzasadnienia'));
                    //    return;
                    //}
                    if ($types->find('service', 'acronym')->id == $typeid) {
                        if ($values['performed'] === '0' && !$values['cancellationcodeid']) {
                            $form->getElement('cancellationcodeid')->setErrors(array('cancellationcodeid' => 'Wymagane podanie kodu odwołania'));
                            return;
                        }
                        if ($values['performed'] === '1' && empty($data['solutioncodeid'])) {
                            $form->getElement('solutioncodeid')->setErrors(array('solutioncodeid' => 'Wymagane podanie kodu rozwiązania'));
                            return;
                        }
                    } else {
                        if ($values['performed'] === '0' && !$values['installationcancelcodeid']) {
                            $form->getElement('installationcancelcodeid')->setErrors(array('installationcancelcodeid' => 'Wymagane podanie kodu odwołania'));
                            return;
                        }
                        if ($values['performed'] === '0' && !$values['technicalcomments']) {
                            $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie uzasadnienia'));
                            return;
                        }
                    if ($values['performed'] === '1' && empty($data['installationcodeid'])) {
                        $form->getElement('installationcodeid-0')->setErrors(array('installationcodeid-0' => 'Wymagane podanie kodu instalacji'));
                        return;
                    }
                    }
                    if ($values['performed'] === '0' && !$values['datefinished']) {
                        $form->getElement('datefinished')->setErrors(array('datefinished' => 'Wymagane podanie daty zakończenia'));
                        return;
                    }
                    $status = $this->_dictionaries->getStatusList('service')->find('finished', 'acronym');
                    $service->technicalcomments = $values['technicalcomments'];
                    $service->coordinatorcomments = $values['coordinatorcomments'];
                    //$service->statusid = $status->id;
                    if (!strlen($values['performed'])) {
                        $service->performed = null;
                    } else {
                        $service->performed = $values['performed'];
                    }
                    if (!empty($values['datefinished'])) {
                        if ($types->find('service', 'acronym')->id == $typeid) {
                            $service->datefinished = date('Y-m-d H:i', strtotime($service->planneddate . ' ' . $values['datefinished']));
                        } else {
                            $service->datefinished = date('Y-m-d H:i', strtotime($values['datefinished']));
                        }
                    } else {
                        $service->datefinished = null;
                    }
                } else {
                    $service = $this->_services->createRow($values);
                    $service->id = null;
                }
                $productsReturned = (array)$request->getParam('productreturnedid');
                $demaged = (array)$request->getParam('demaged');
                $demagecode = (array)$request->getParam('demagecodeid');
                $catalog = (array)$request->getParam('catalogid');
                $table = new Application_Model_Services_Returns_Table();
                foreach ($service->getReturns() as $product) {
                    $return = null;
                    foreach ($productsReturned as $ix => $productId) {
                        $productId = current($productId);
                        preg_match("/\d+/", $ix, $found);
                        $ix = $found[0];
                        if ($productId == $product->name) {
                            $return = $table->find($product->id)->current();
                            unset($productsReturned['productreturnedid-' . $ix]);
                            break;
                        }
                    }
                    
                    if ($return) {
                        if (!$product->isNew()
                                && $return->demaged != (int)$demaged['demaged-' . $ix]
                                 && $return->demagecodeid != (int)$demagecode['demagecodeid-' . $ix]) {
                            $form->getElement('demaged-' . $ix)->setErrors(array('demaged-' . $ix => 'Nie można zmodyfikować potwierdzonego zwrotu'));
                            return;
                        }
                        $return->setFromArray(array('demaged' => (int)$demaged['demaged-' . $ix],
                            'demagecodeid' => (int)$demagecode['demagecodeid-' . $ix],
                            'catalogid' => (int)$catalog['catalogid-' . $ix]));
                        try {
                            if ($return->demaged && !$return->demagecodeid) {
                                throw new Exception("Brak kodu uszkodzenia");
                            }
                            if (!$return->catalogid) {
                                //throw new Exception("Brak nazwy katalogowej");
                            }
                            $return->save();
                        }  catch (Exception $e) {var_dump($return->toArray());
                            $form->getElement('demagecodeid-' . $ix)->setErrors(array('demaged-' . $ix => $e->getMessage()));
                            return;
                        }
                    } else {
                        if (!$product->isNew()) {
                            $form->setDescription('Nie można usunąć potwierdzonego zwrotu');
                            return;
                        }
                        $product->delete();
                        continue;
                    }
                }
                if ($productsReturned) {
                    $returns = array();
                    foreach($productsReturned as $ix => $productId) {
                        $productId = current($productId);//var_dump($productId);exit;
                        preg_match("/\d+/", $ix, $found);
                        $ix = $found[0];
                        //if (!$form->getElement('productreturnedid-' . $ix)) {
                        //    var_dump($ix,$productId,$data,array_keys($form->getElements()));exit;
                        //}
                        //$form->getElement('productreturnedid-' . $ix)->addMultiOption($productId, $productId);
                        $params = array('serviceid' => $service->id, 
                            'name' => $productId, 
                            'quantity' => 1, 
                            'unitid' => $units->find('szt', 'acronym') -> id,
                            'demaged' => (int)$demaged['demaged-' . $ix],
                            'catalogid' => (int)$catalog['catalogid-' . $ix],
                            'demagecodeid' => (int)$demagecode['demagecodeid-' . $ix],
                            'statusid' => $this->_dictionaries->getStatusList('returns')->find('new', 'acronym')->id
                            );
                        $serviceProduct = $table->createRow($params);
                        try {
                            if ($serviceProduct->demaged && !$serviceProduct->demagecodeid) {
                                throw new Exception("Brak kodu uszkodzenia");
                            }
                            if (!$serviceProduct->catalogid) {
                                //throw new Exception("Brak nazwy katalogowej");
                            }
                            $serviceProduct->save();
                        } catch (Exception $e) {
                            $form->getElement('demagecodeid-' . $ix)->setErrors(array('demaged-' . $ix => $e->getMessage()));
                            return;
                        }
                        $returns[] = $productId;
                    }
                    $service->productsreturned = join(', ', $returns);
                } else {
                    $service->productsreturned = '';
                }//var_dump($data,$values);return;
                $service->save();
                $status = $this->_dictionaries->getStatusList('orders')->find('invoiced', 'acronym');
                $statusReleased = $this->_dictionaries->getStatusList('orders')->find('released', 'acronym');
                $this->_orderlines->setLazyLoading(true);
                foreach ($service->getProducts() as $ix => $product) {
                    if ($orderLine = $this->_orderlines->find($product->productid)->current()) {
                        if ($orderLine->isDeleted()) {
                            continue;
                        }
                        //var_dump($orderLine->toArray(),$product->toArray());
                        $orderLine->statusid = $statusReleased->id;
                        //$orderLine->serviceid = null;
                        $orderLine->qtyavailable += $product->quantity;
                        if ($orderLine->qtyavailable > $orderLine->quantity) {
                            $form->getElement('quantity-' . $ix)->setErrors(array('quantity-' . $ix => 'Wystąpił ze zmianą produktu'));
                            return;
                        }
                        $orderLine->save();
                    }
                    $product->delete();
                }
                $productIds = array_filter((array)$request->getParam('productid'));
                if (!empty($productIds)) {
                    $quantities = array_filter((array)$request->getParam('quantity'));
                    $table = new Application_Model_Services_Products_Table();
                    foreach ($productIds as $ix => $orderLineId) {
                        $orderLineId = current($orderLineId);
                        preg_match("/\d+/", $ix, $found);
                        $ix = $found[0];
                        $params = array('serviceid' => $service->id);
                        if ($orderLine = $this->_orderlines->find($orderLineId)->current()) {
                            $params['productid'] = $orderLine->id;
                            $params['productname'] = $orderLine->getProduct()->name;
                            if ($products->find($orderLineId)->serial) {  
                                $quantity = $params['quantity'] = 1;
                            } else {
                                if (!($quantity = $quantities['quantity-' . $ix])) {
                                    $form->getElement('quantity-' . $ix)->setErrors(array('quantity-' . $ix => 'Brak ilości'));
                                    return;
                                }
                                $params['quantity'] = $quantity;
                            }
                            $orderLine->qtyavailable -= (int) $quantity;
                            if ($orderLine->qtyavailable < 0) {
                                $form->getElement('quantity-' . $ix)->setErrors(array('quantity-' . $ix => 'Wystąpił problem z dodaniem produktu'));
                                return;
                            }
                            if ($orderLine->qtyavailable == 0) {
                                $orderLine->statusid = $this->_dictionaries->getStatusList('orders')->find('invoiced', 'acronym')->id;
                            }
                            $orderLine->save();
                        } else {
                            $id = $ix + 1;
                            $params['productid'] = "-$id";
                            $params['productname'] = $orderLineId;
                            $params['quantity'] = 1;
                            $form->getElement('productid-' . $ix)->addMultiOption($orderLineId, $orderLineId);
                        }
                        if ($serviceProduct->quantity < 0) {
                            $form->getElement('quantity-' . $ix)->setErrors(array('quantity-' . $ix => 'Wystąpił problem z dodaniem produktu'));
                            return;
                        }
                        $serviceProduct = $table->createRow($params);
                        //var_dump($serviceProduct->toArray());//exit;
                        $serviceProduct->save();
                        //$this->_orderlines->update(array('statusid' => $status->id, 'serviceid' => $service->id), $this->_orderlines->getAdapter()->quoteInto('id = ?', $orderLineId));
                    }
                }//return;
                foreach ($service->getCodes() as $attribute) {//var_dump($attribute);continue;
                    $attribute->delete();
                }
                if (!empty($codeTypes)) {
                    $table = new Application_Model_Services_Codes_Table();
                    foreach ($codeTypes as $type) {
                        switch ($type) {
                            case 'cancellation':
                                if ($values['performed'] == 1) {
                                    continue 2;
                                }
                                break;
                            case 'error':
                            case 'solution':
                            case 'modeminterchange':
                            case 'decoderinterchange':
                                if ($values['performed'] != 1) {
                                    continue 2;
                                }
                                break;
                        }
                        if (!$code = $dictionary->find($type . 'code', 'acronym')) {
                            continue;
                        }
                        $attributeId = $code->id;
                        foreach ((array) $this->_getParam($type . 'codeid') as $codeId) {
                            if (empty($codeId)) {
                                continue;
                            }
                            switch ($type) {
                                case 'solution':
                                    $code = $dictionary->find($type . 'code', 'acronym')->getChildren()->find($codeId);
                                    if (empty($code)) {
                                        break;
                                    }
                                    list($error, $solution) = explode('-', $code->acronym);//var_dump($error,$solution,$values);exit;
                                    switch ($solution) {
                                        case 'WKW':
                                        case 'WKX':
                                            if (!$values['decoderinterchangecodeid'] && (in_array($error, array('SCI', 'ST1')) || (0 /*&& $service->complaintcode == 'STB'*/))) {
                                                $form->getElement('decoderinterchangecodeid')->setErrors(array('decoderinterchangecodeid' => 'Wymagany kod wymiany dekodera'));
                                                return;
                                            }
                                            if (!$values['modeminterchangecodeid'] && (in_array($error, array('WIF', 'UMD', 'SIP')) || (0 /*&& $service->complaintcode == 'WCH'*/))) {
                                                $form->getElement('modeminterchangecodeid')->setErrors(array('modeminterchangecodeid' => 'Wymagany kod wymiany modemu'));
                                                return;
                                            }
                                            if (in_array($error, array('HZ1','PAY'))) {
                                                if (!$values['decoderinterchangecodeid'] && !$values['modeminterchangecodeid']) {
                                                    $form->setDescription('Wymagany kod wymiany modemu lub dekodera');
                                                    return;
                                                }
                                                if ($values['decoderinterchangecodeid'] && $values['modeminterchangecodeid']) {
                                                    $form->setDescription('Dozwolone podanie tylko jednego kodu: wymiany modemu lub dekodera');
                                                    return;
                                                }
                                            }
                                            if (!array_filter((array)$request->getParam('productid')) && !$values['productid']) {
                                                $form->getElement('quantity-0')->setErrors(array('quantity-0' => 'Wymagane kody produktów wydanych'));
                                                return;
                                            }
                                            if (!($productsReturned) && !($values['productreturnedid']) && !((array)$request->getParam('productreturnedid'))) {
                                                $form->getElement('demaged-0')->setErrors(array('demaged-0' => 'Wymagane kody produktów odebranych'));
                                                return;
                                            }
                                            break;
                                        case 'PLI':
                                            if (!$values['technicalcomments']) {
                                                $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie komentarza'));
                                                return;
                                            }
                                            break;
                                    }
                                    switch ($error) {
                                        case 'PAY':
                                        case 'SOW':
                                        case 'WOK':
                                        case 'ZMT':
                                            if (!$values['technicalcomments']) {
                                                $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie komentarza'));
                                                return;
                                            }
                                            break;
                                    }
                                    //$attribute = $table->createRow();
                                    //$attribute->serviceid = $service->id;
                                    //$attribute->attributeid = $errorAttributeId;
                                    //$attribute->codeid = $code->errorcodeid;
                                    //$attribute->save();
                                    break;
                                case 'modeminterchange':
                                    switch ($code) {
                                        case 'MW0':
                                        case 'MW1':
                                            if (!$values['technicalcomments']) {
                                                $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie komentarza'));
                                                return;
                                            }
                                            break;
                                    }
                                    break;
                                case 'decoderinterchange':
                                    switch ($code) {
                                        case 'WK1':
                                        case 'WKK':
                                            if (!$values['technicalcomments']) {
                                                $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie komentarza'));
                                                return;
                                            }
                                            break;
                                    }
                                    break;
                                case 'cancellation':
                                    switch ($code) {
                                        case 'P01':
                                        case 'PRT':
                                            if (!$values['technicalcomments']) {
                                                $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie komentarza'));
                                                return;
                                            }
                                            break;
                                    }
                                    break;
                                case 'installation':
                                    $codeId = current($codeId);
                                    break;
                                default:
                                    break;
                            }
                            $attribute = $table->createRow();
                            $attribute->serviceid = $service->id;
                            $attribute->attributeid = $attributeId;
                            $attribute->codeid = $codeId;
                            $attribute->save();
                        }
                    }
                }

                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Zgłoszenie zapisane';
            }
        }
    }
    
    public function setAction() {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $typeid = $request->getParam('typeid');
        //$id = array_unique((array)$id);
        $types = $this->_dictionaries->getTypeList('service');
        switch ($this->_getParam('typeid')) {
            case $types->find('installation', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Installation');
                break;
            case $types->find('service', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Service');
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
        $service = $this->_services->get($id);
        if (!$service) {
            throw new Exception('Nie znaleziono zgłoszenia');
        }
        $form = new Application_Form();
        $this->view->form = $form;
        $this->view->service = $service;
        $this->view->types = $types;

        if ($this->getRequest()->isPost()||1) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                $params = $request->getParams();
                if (isset($params['documentspassed'])) {
                    $service->documentspassed = $params['documentspassed'];
                }
                if (isset($params['closedupc'])) {
                    $service->closedupc = $params['closedupc'];
                }
                $service->save();
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Zgłoszenie zostało zmodyfikowane';
            }
        }
    }

    public function importAction() {
        // action body
        //var_dump(PHPExcel_Shared_Date::ExcelToPHP('42121'));
        $request = $this->getRequest();
        $typeid = $request->getParam('typeid');
        $types = $this->_dictionaries->getTypeList('service');
        switch ($typeid) {
            // zlecenie instalacyjne
            case $types->find('installation', 'acronym')->id:
                $form = new Application_Form_Services_Installation_Import();
                break;
            // zlecenie serwisowe
            case $types->find('service', 'acronym')->id:
                $form = new Application_Form_Services_Service_Import();
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
        //$form = new Application_Form_Services_Import();
        $this->_dictionaries->setCacheInClass(false);
        $this->_dictionaries->clearCache();
        $types = $this->_dictionaries->getTypeList('service');
        $form->setOptions(array('types' => $types));
        $form->setDefaults(array('typeid' => $typeid));

        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();

                if ($typeid == $types->find('installation', 'acronym')->id) {
                    if (!$values['planneddate']&&0) {
                        $form->getElement('planneddate')->setErrors(array('planneddate' => 'Nieprawidłowa data zlecenia'));
                        return;
                    }
                    if ($values['planneddate']) {
                        $plannedDate = new Zend_Date($values['planneddate'], 'YYYY-MM-dd');
                    }
                }

                $upload = new Zend_File_Transfer_Adapter_Http();
                $upload->setDestination(APPLICATION_PATH . "/../data/pliki/import/");

                try {
                    $userStatus = $this->_dictionaries->getStatusList('users')->find('active', 'acronym');
                    $serviceStatusAssigned = $this->_dictionaries->getStatusList('service')->find('assigned', 'acronym');
                    $serviceStatusNew = $this->_dictionaries->getStatusList('service')->find('new', 'acronym');

                    switch ($typeid) {
                        // zlecenie instalacyjne
                        case $types->find('installation', 'acronym')->id:
                            $rowNumber = 2;
                            $this->_services->setRowClass('Application_Model_Services_XLS_Installation');
                            break;
                        // zlecenie serwisowe
                        case $types->find('service', 'acronym')->id:
                            $rowNumber = 3;
                            $this->_services->setRowClass('Application_Model_Services_XLS_Service');
                            break;
                        default:
                            throw new Exception('Nieprawidłowy typ zlecenia');
                    }
                    $params = array('statusid' => $serviceStatusNew->id, 'typeid' => $values['typeid']/* , 'warehouseid' => $values['warehouseid'] */);

                    $upload->receive();

                    /* $config = array('filename' => $upload->getFileName('import', true), 
                      'options' => array('readerType' => 'Excel2007', 'readOnly' => true));
                      $adapter = new Application_Db_Adapter_PHPExcel($config);
                      $services = new Application_Model_Services_PHPExcel_Table($adapter);
                      $rows = $services->getAll(); */

                    $reader = new Utils_File_Reader_PHPExcel(array('readerType' => 'Excel2007', 'readOnly' => true));
                    $sheet = $reader->read($upload->getFileName('import', true), 1);
                    $rows = $sheet->getRowIterator($rowNumber);
                    if ($typeid == $types->find('service', 'acronym')->id) {
                        $additionalData = $reader->read($upload->getFileName('import', true), 2);
                        $additionalData1 = $reader->read($upload->getFileName('import', true), 3);
                    }
                    
                    if (!empty($values['report'])) {
                        $objPHPExcel = new PHPExcel();
                        $objPHPExcel->setActiveSheetIndex(0);
                    }

                    $this->_dictionaries->setLazyLoading(true);
                    $success = 0;
                    $error = 0;
                    foreach ($rows as $i => $row) {
                        $params['technicalcommentsrequired'] = 0;
                        
                        $dictionary = $this->_dictionaries->getDictionaryList('service');

                        $service = $this->_services->createRow();
                        $service->setFromArray($params);
                        $i++;
                        $line = array();
                        $columnNo = 0;

                        $cellIterator = $row->getCellIterator();

                        // This loops all cells, even if it is not set.
                        // By default, only cells that are set will be iterated.
                        $cellIterator->setIterateOnlyExistingCells(true);

                        $service->setFromCellIterator($cellIterator);
                        if (!$service->number) {
                            continue;
                        }

                        try {
                            Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                            $data = $service->getServicetype();
                            $servicetype = $dictionary->find('type', 'acronym')->getChildren()->find(strtoupper($data), 'acronym');
                            if (!$servicetype) {
                                $servicetype = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('type', 'acronym')->id, 'acronym' => $data));
                                $servicetype->save();
                            }
                            $service->servicetypeid = $servicetype->id;
                            if ($data = $service->getTechnician()) {
                                //$user = $this->_users->getAll(array('symbol' => $data['symbol']))->current();
                                //if (!$user && $data['symbol'] != 'UNP') {
                                //if (!in_array(strtoupper($data['firstname']), array('UNP', 'ZWZ')) && !in_array(strtoupper($data['lastname']), array('UNP', 'ZWZ')) && !in_array(strtoupper($data['symbol']), array('UNP', 'ZWZ'))) {
                                    if (!empty($data['firstname']) && !empty($data['lastname'])) {
                                        $this->_users->clearWhere();
                                        $this->_users->setWhere($this->_users->getAdapter()->quoteInto("UPPER(firstname) LIKE UPPER(?)", "{$data['firstname']}%"));
                                        $this->_users->setWhere($this->_users->getAdapter()->quoteInto("UPPER(lastname) = UPPER(?)", "{$data['lastname']}"));
                                        $user = $this->_users->getAll()->current();
                                    } else if (!empty($data['symbol'])) {
                                        $this->_users->clearWhere();
                                        //$user = $this->_users->getAll(array('symbol' => $data['symbol']))->current();
                                        $this->_users->setWhere($this->_users->getAdapter()->quoteInto("UPPER(symbol) = UPPER(?)", "{$data['symbol']}"));
                                        $user = $this->_users->getAll()->current();
                                    }
                                    if (!$user) {
                                        $user = $this->_users->createRow($data);
                                        $user->role = 'technician';
                                        $user->statusid = $userStatus->id;
                                        //$user->save();
                                        //$service->technicalcomments = join(" ", array_values($data));
                                    }//var_dump($data,$user->toArray());//exit;
                                    if ($user && $user->id) {
                                        $service->statusid = $serviceStatusAssigned->id;
                                        $service->technicianid = $user->id;
                                    }
                                //}
                            }
                            $data = $service->getClient();
                            $client = null;
                            $addressId = 1;
                            foreach ($this->_clients->getAll(array('number' => $data['number'])) as $c) {
                                if ($c->city == $data['city'] && $c->street == $data['street'] && $c->apartmentno == $data['apartmentno'] && $c->streetno == $data['streetno']) {
                                    $client = $c;
                                    $client->setFromArray(array_intersect_key($data, array_flip(array('homephone','cellphone','workphone'))));
                                    if ($client->isModified()) {
                                        $client->save();
                                    }
                                    break;
                                }
                                $addressId++;
                            }
                            $data['addressid'] = $addressId;
                            if (!$client) {
                                $client = $this->_clients->createRow($data);
                                $client->save();
                            }
                            $service->clientid = $client->id;
                            $service->addressid = $client->addressid;//var_dump($client->toArray());
                            if ($typeid == $types->find('service', 'acronym')->id) {
                                if ($data = $service->getSystem()) {
                                    $system = $dictionary->find('system', 'acronym')->getChildren()->find($data, 'acronym');
                                    if (!$system) {
                                        $system = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('system', 'acronym')->id, 'acronym' => $data));
                                        $system->save();
                                    }
                                    $service->systemid = $system->id;
                                }
                                if ($data = $service->getRegion()) {
                                    $region = $dictionary->find('region', 'acronym')->getChildren()->find($data, 'acronym');
                                    if (!$region) {
                                        $region = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('region', 'acronym')->id, 'acronym' => $data));
                                        $region->save();
                                    }
                                    $service->regionid = $region->id;
                                }
                                if ($data = $service->getBlockadecode()) {
                                    $blockadecode = $dictionary->find('blockadecode', 'acronym')->getChildren()->find($data, 'acronym');
                                    if (!$blockadecode) {
                                        $blockadecode = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('blockadecode', 'acronym')->id, 'acronym' => $data));
                                        $blockadecode->save();
                                    }
                                    $service->blockadecode = $blockadecode->id;
                                }
                                if ($data = $service->getLaborcode()) {
                                    $laborcode = $dictionary->find('laborcode', 'acronym')->getChildren()->find($data, 'acronym');
                                    if (!$laborcode) {
                                        $laborcode = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('laborcode', 'acronym')->id, 'acronym' => $data));
                                        $laborcode->save();
                                    }
                                    $service->laborcodeid = $laborcode->id;
                                }
                                if ($data = $service->getComplaintcode()) {
                                    $complaintcode = $dictionary->find('complaintcode', 'acronym')->getChildren()->find($data, 'acronym');
                                    if (!$complaintcode) {
                                        $complaintcode = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('complaintcode', 'acronym')->id, 'acronym' => $data));
                                        $complaintcode->save();
                                    }
                                    $service->complaintcodeid = $complaintcode->id;
                                }
                                if ($data = $service->getCalendar()) {
                                    $calendar = $dictionary->find('calendar', 'acronym')->getChildren()->find($data, 'acronym');
                                    if (!$calendar) {
                                        $calendar = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('calendar', 'acronym')->id, 'acronym' => $data));
                                        $calendar->save();
                                    }
                                    $service->calendarid = $calendar->id;
                                }
                                if ($data = $service->getArea()) {
                                    $area = $dictionary->find('area', 'acronym')->getChildren()->find($data, 'acronym');
                                    if (!$area) {
                                        $area = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('area', 'acronym')->id, 'acronym' => $data));
                                        $area->save();
                                    }
                                    $service->areaid = $area->id;
                                }

                                $service->description = $additionalData->getCellByColumnAndRow(PHPExcel_Cell::columnIndexFromString('I') - 1, $i - 1)->getValue();
                                $service->parameters = $additionalData->getCellByColumnAndRow(PHPExcel_Cell::columnIndexFromString('J') - 1, $i - 1)->getValue();
                                $service->equipment = $additionalData->getCellByColumnAndRow(PHPExcel_Cell::columnIndexFromString('G') - 1, $i - 1)->getValue();
                                $date = $additionalData->getCellByColumnAndRow(PHPExcel_Cell::columnIndexFromString('F') - 1, $i - 1)->getValue();
                                if (!strtotime($date)) {
                                    $date = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date));
                                }
                                $service->modifieddate = date('Y-m-d', strtotime($date));
                                //$service->technicalcommentsrequired = $additionalData1->getCellByColumnAndRow(PHPExcel_Cell::columnIndexFromString('P') - 1, $i-2)->getValue();
                                foreach ($additionalData1->toArray() as $r) {
                                    if ($r[3] == $service->number) {
                                        $service->technicalcommentsrequired = 1;
                                        break;
                                    }
                                }
                            } else {
                                if ($data = $service->getCalendar()) {
                                    $calendar = $dictionary->find('calendar', 'acronym')->getChildren()->find($data, 'acronym');
                                    if (!$calendar) {
                                        $calendar = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('calendar', 'acronym')->id, 'acronym' => $data));
                                        $calendar->save();
                                    }
                                    $service->calendarid = $calendar->id;
                                }
                            }
                            //
                            if (!empty($plannedDate)) {
                                $service->planneddate = $plannedDate->toString('YYYY-MM-dd');
                            }
                            //var_dump($service->planneddate);exit;
                            $service->save();
                            //array_unshift($line, 'OK');
                            $line[] = 'OK';
                            $success++;
                            Zend_Db_Table::getDefaultAdapter()->commit();
                        } catch (Exception $e) {
                            $error++;
                            Zend_Db_Table::getDefaultAdapter()->rollBack();
                            //var_dump($e->getMessage(), $area->toArray(), $e->getTraceAsString());
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
                    }//return;
                    //$this->view->success = 'Zaimportowano ' . $rows->key() . ' pozycji';

                    $this->view->data = $lines;
                    
                    if (!empty($values['report'])) {
                        $filename = $upload->getFileName('import');
                        $ext = pathinfo($upload->getFileName('import', false), PATHINFO_EXTENSION);
                        //$ext = 'csv';
                        $filename = pathinfo($upload->getFileName('import', false), PATHINFO_FILENAME);
                        $filename = '/../data/pliki/import/' . $filename . '-raport-' . date('YmdHis') . '.' . $ext;
                        //$this->_writeToXLS($lines, $filename);
                        $this->view->filename = $filename;
                        
                        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                        //$objWriter->setPreCalculateFormulas(false);
                        $objWriter->save($_SERVER['DOCUMENT_ROOT'] . $filename);
                    }
                } catch (Zend_File_Transfer_Exception $e) {
                    echo $e->message();
                }
                //if ($form->isValid($request->getPost())) {
                //    $data = $form->getData();var_dump($data);exit;
                    $this->view->success = 'Zaimportowano ' . $success . ' zleceń z ' . $error . ' błędami';
                //} 
            }
        }
    }
    
    protected function _writeToXLS($data, $filename) {
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        //foreach ($data as $rowNo => $row) {
        for ($rowNo = 0; $i < count($data); $rowNo++) {
            $row = $data[$rowNo];
            //foreach ($row as $columnNo => $value) {
            for ($columnNo = 0; $columnNo < count($row); $columnNo++) {
                $key = PHPExcel_Cell::stringFromColumnIndex($columnNo);
                $objPHPExcel->getActiveSheet()->SetCellValue($key . ($rowNo + 1), $value);
            }
        }
        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        $objWriter->save($_SERVER['DOCUMENT_ROOT'] . $filename);
    }

    public function assignAction() {
        $request = $this->getRequest();
        $id = (array) $request->getParam('id');
        $typeid = $request->getParam('typeid');
        //$id = array_unique((array)$id);
        $types = $this->_dictionaries->getTypeList('service');
        switch ($this->_getParam('typeid')) {
            case $types->find('installation', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Installation');
                break;
            case $types->find('service', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Service');
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
        $service = $this->_services->get($id);
        if (!$service) {
            throw new Exception('Nie znaleziono zgłoszenia');
        }
        $form = new Application_Form_Services_Assign(array('servicesCount' => $service->count()));
        $form->setServices($service);
        $form->setDefaults($service->toArray());
        if ($this->_auth->getIdentity()->role == 'technician') {
            $form->setDefaults(array('technicianid' => $this->_auth->getIdentity()->id));
            $params = array('id' => $this->_auth->getIdentity()->id);
            $form->removeElement('technicianid');
        }
        $status = $this->_dictionaries->getStatusList('users')->find('active', 'acronym');
        $params['statusid'] = $status->id;
        $params['role'] = 'technician';
        $this->_users->setOrderBy(array('lastname','firstname'));
        $technicians = $this->_users->getAll($params);
        $form->setOptions(array('technicians' => $technicians));
        $status = $this->_dictionaries->getStatusList('service')->find('assigned', 'acronym');
        $this->view->form = $form;
        $this->view->service = $service;
        $this->view->types = $types;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                foreach ($service as $i => $item) {
                    if (!$item->isNew() && !$item->isAssigned()) {
                        $form->getElement('id-' . $i)->setErrors(array('id-' . $i => 'Nieprawidłowy status zlecenia'));
                        return;
                    }
                    $values = $form->getValues();
                    $item->technicianid = !empty($values['technicianid']) ? $values['technicianid'] : $this->_auth->getIdentity()->id;
                    $item->statusid = $status->id;
                    $item->save();
                }
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Zgłoszenie przypisane';
            }
        }
    }

    public function finishAction() {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $typeid = $request->getParam('typeid');
        //$id = array_unique((array)$id);
        $types = $this->_dictionaries->getTypeList('service');
        switch ($this->_getParam('typeid')) {
            case $types->find('installation', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Installation');
                break;
            case $types->find('service', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Service');
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
        $service = $this->_services->get($id);
        if (!$service) {
            throw new Exception('Nie znaleziono zgłoszenia');
        }
        $this->_users->setOrderBy(array('lastname','firstname'));
        $technicians = $this->_users->getAll();
        //$order = $this->_orders->getAll(array('technicianid' => $service->technicianid))->current();
        $statusInvoiced = $this->_dictionaries->getStatusList('orders')->find('invoiced', 'acronym');
        $statusReleased = $this->_dictionaries->getStatusList('orders')->find('released', 'acronym');
        $units = $this->_dictionaries->getDictionaryList('warehouse', 'unit');
        $this->_orderlines->setLazyLoading(false);
        if ($service->technicianid) {
            $this->_orderlines->setWhere("technicianid = '{$service->technicianid}' AND (statusid = {$statusReleased->id} OR (statusid = {$statusInvoiced->id} AND serviceid = {$service->id}))");
            $products = $this->_orderlines->getAll(
                //array(
                //'statusid' => array(
                //    $statusInvoiced->id, 
                //    $statusReleased->id
                //), 
                //'technicianid' => $service->technicianid)
            );//var_dump($products->toArray());
            $options = array('products' => $products);
        }
        $this->_dictionaries->setLazyLoading(false);
        $types = $this->_dictionaries->getTypeList('service');
        //$options = array('products' => $products->toArray());
        $productsReleased = $service->getProducts()->toArray();
        $defaults = array(//'performed' => 1,
            'productid' => $productsReleased);
        if ($service->performed !== null) {
            //$defaults['performed'] = $service->performed;
        }
        if ($request->getParam('performed') !== null) {
            //$defaults['performed'] = $request->getParam('performed');
        }
        $productsReturned = explode(',', $service->productsreturned);
        $productsReturned = array_combine($productsReturned, $productsReturned);
        $defaults['productreturnedid'] = $productsReturned;
        $defaults['productreturnedid'] = $service->getReturns()->toArray();
        $serialsReturned = explode(',', $service->serialnumbers);
        $serialsReturned = array_combine($serialsReturned, $serialsReturned);
        $serialsReturned = array_merge($serialsReturned, $productsReturned);
        $options['productsreturned'] = $serialsReturned;//var_dump($serialsReturned,$productsReturned);
        switch ($service->typeid) {
            // zlecenie instalacyjne
            case $types->find('installation', 'acronym')->id:
                $codeTypes = array('installation', 'installationcancel', 'modeminterchange', 'decoderinterchange');
                break;
            // zlecenie serwisowe
            case $types->find('service', 'acronym')->id:
                $codeTypes = array('error', 'solution', 'cancellation', 'modeminterchange', 'decoderinterchange');
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
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
                $attributeId = $code->id;
                if ($codes = $service->getCodes()->filter(array('attributeid' => $attributeId))) {
                    $defaults[$type . 'codeid'] = $codes->toArray();
                }
            }
        }
        $options['demagecodes'] = array_merge($options['modeminterchangecodes'], $options['decoderinterchangecodes']);
        $this->_catalog->setWhere("statusid != {$status->id}");
        $catalog = $this->_catalog->getAll();
        $options['catalog'] = $catalog->toArray();
        if (!empty($defaults['installationcodeid']))
        foreach ($defaults['installationcodeid'] as $i => $item) {
            $defaults['installationcodeid-' . $i] = $item;
        }
        if (!empty($defaults['productreturnedid']))
        foreach ($defaults['productreturnedid'] as $i => $item) {
            $defaults['productreturnedid-' . $i] = $item;
        }
        if (!empty($defaults['productid']))
        foreach ($defaults['productid'] as $i => $item) {
            $defaults['productid-' . $i] = $item;
        }
        if ($types->find('installation', 'acronym')->id == $typeid /* && !in_array($this->_auth->getIdentity(), array('admin', 'coordinator')) */) {
            $service->datefinished = date('Y-m-d', strtotime($service->datefinished ? $service->datefinished : $service->planneddate)); //$service->timetill;
        } else if ($types->find('service', 'acronym')->id == $typeid) {
            $service->datefinished = $service->datefinished ? date('H:i', strtotime($service->datefinished)) : null; //$service->timetill;
        }
        $productsReturnedCount = @sizeof($defaults['productreturnedid']);
        $installationCodesCount = @sizeof($defaults['installationcodeid']);
        $productsCount = @sizeof($defaults['productid']);
        if ($this->getRequest()->isPost()) {
            $data = $request->getPost();
            $productsReturnedCount = @sizeof($this->getRequest()->getParam('productreturnedid'));
            $installationCodesCount = @sizeof($this->getRequest()->getParam('installationcodeid'));
            $productsCount = @sizeof($this->getRequest()->getParam('productid'));
        }
        switch ($typeid) {
            // zlecenie instalacyjne
            case $types->find('installation', 'acronym')->id:
                $form = new Application_Form_Services_Installation(array('installationCodesCount' => $installationCodesCount,
                    'productsReturnedCount' => $productsReturnedCount,
                    'productsCount' => $productsCount));
                break;
            // zlecenie serwisowe
            case $types->find('service', 'acronym')->id:
                $form = new Application_Form_Services_Service(array('installationCodesCount' => $installationCodesCount,
                    'productsReturnedCount' => $productsReturnedCount,
                    'productsCount' => $productsCount));
                break;
        }
        if ($service) {
            $form->setDefaults($service->toArray());
        }
        $form->setOptions($options);
        $form->setDefaults($defaults);
        $this->view->form = $form;
        $this->view->service = $service;
        $this->view->types = $types;

        if ($this->getRequest()->isPost()) {
            $data = $request->getPost();
            foreach ($data['installationcodeid'] as $key => $value) {
                $data[$key] = $value;
            }
            foreach ($data['productid'] as $key => $value) {
                $data[$key] = $value;
            }
            foreach ($data['quantity'] as $key => $value) {
                $data[$key] = $value;
            }
            if (!empty($data['productreturnedid']))
                foreach ($data['productreturnedid'] as $key => $value) {
                    $data[$key] = $value;
                }
            if (!empty($data['demaged']))
                foreach ($data['demaged'] as $key => $value) {
                    $data[$key] = $value;
                }
            if (!empty($data['demagecodeid']))
                foreach ($data['demagecodeid'] as $key => $value) {
                    $data[$key] = $value;
                }
            $form->setDefaults($data);
            if ($form->isValid($data)) {
                if (!$service->isAssigned()) {
                    //$form->setDescription('Nieprawidłowy status zlecenia');
                    //return;
                }
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                $values = $form->getValues();
                //if ($values['performed'] === '0' && !$values['technicalcomments']) {
                //    $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie uzasadnienia'));
                //    return;
                //}
                if ($types->find('service', 'acronym')->id == $typeid) {
                    if ($values['performed'] === '0' && !$values['cancellationcodeid']) {
                        $form->getElement('cancellationcodeid')->setErrors(array('cancellationcodeid' => 'Wymagane podanie kodu odwołania'));
                        return;
                    }
                    if ($values['performed'] === '1' && empty($data['solutioncodeid'])) {
                        $form->getElement('solutioncodeid')->setErrors(array('solutioncodeid' => 'Wymagane podanie kodu rozwiązania'));
                        return;
                    }
                } else {
                    if ($values['performed'] === '0' && !$values['installationcancelcodeid']) {
                        $form->getElement('installationcancelcodeid')->setErrors(array('installationcancelcodeid' => 'Wymagane podanie kodu odwołania'));
                        return;
                    }
                    if ($values['performed'] === '0' && !$values['technicalcomments']) {
                        $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie uzasadnienia'));
                        return;
                    }
                    if ($values['performed'] === '1' && empty($data['installationcodeid'])) {
                        $form->getElement('installationcodeid-0')->setErrors(array('installationcodeid-0' => 'Wymagane podanie kodu instalacji'));
                        return;
                    }
                }
                if ($values['performed'] === '0' && !$values['datefinished']) {
                    $form->getElement('datefinished')->setErrors(array('datefinished' => 'Wymagane podanie daty zakończenia'));
                    return;
                }
                $status = $this->_dictionaries->getStatusList('service')->find('finished', 'acronym');
                $service->technicalcomments = $values['technicalcomments'];
                $service->coordinatorcomments = $values['coordinatorcomments'];
                if (!empty($values['datefinished'])) {
                    if ($types->find('service', 'acronym')->id == $typeid) {
                        $service->datefinished = date('Y-m-d H:i', strtotime($service->planneddate . ' ' . $values['datefinished']));
                    } else {
                        $service->datefinished = date('Y-m-d H:i', strtotime($values['datefinished']));
                    }
                }
                $service->statusid = $status->id;
                if (!strlen($values['performed'])) {
                    $service->performed = null;
                } else {
                    $service->performed = $values['performed'];
                }
                $productsReturned = (array)$request->getParam('productreturnedid');
                $demaged = (array)$request->getParam('demaged');
                $demagecode = (array)$request->getParam('demagecodeid');
                $table = new Application_Model_Services_Returns_Table();
                foreach ($service->getReturns() as $product) {
                    $return = null;
                    foreach ($productsReturned as $ix => $productId) {
                        $productId = current($productId);
                        preg_match("/\d+/", $ix, $found);
                        $ix = $found[0];
                        if ($productId == $product->name) {
                            $return = $table->find($product->id)->current();
                            unset($productsReturned['productreturnedid-' . $ix]);
                            break;
                        }
                    }
                    
                    if ($return) {
                        if (!$product->isNew()
                                && $return->demaged != (int)$demaged['demaged-' . $ix]
                                 && $return->demagecodeid != (int)$demagecode['demagecodeid-' . $ix]) {
                            $form->getElement('demaged-' . $ix)->setErrors(array('demaged-' . $ix => 'Nie można zmodyfikować potwierdzonego zwrotu'));
                            return;
                        }
                        $return->setFromArray(array('demaged' => (int)$demaged['demaged-' . $ix],
                            'demagecodeid' => (int)$demagecode['demagecodeid-' . $ix]))->save();
                    } else {
                        if (!$product->isNew()) {
                            $form->setDescription('Nie można usunąć potwierdzonego zwrotu');
                            return;
                        }
                        $product->delete();
                        continue;
                    }
                }
                if ($productsReturned) {
                    $returns = array();
                    foreach($productsReturned as $ix => $productId) {
                        $productId = current($productId);//var_dump($productId);exit;
                        preg_match("/\d+/", $ix, $found);
                        $ix = $found[0];
                        //if (!$form->getElement('productreturnedid-' . $ix)) {
                        //    var_dump($ix,$productId,$data,array_keys($form->getElements()));exit;
                        //}
                        //$form->getElement('productreturnedid-' . $ix)->addMultiOption($productId, $productId);
                        $params = array('serviceid' => $service->id, 
                            'name' => $productId, 
                            'quantity' => 1, 
                            'unitid' => $units->find('szt', 'acronym') -> id,
                            'demaged' => (int)$demaged['demaged-' . $ix],
                            'demagecodeid' => (int)$demagecode['demagecodeid-' . $ix],
                            'statusid' => $this->_dictionaries->getStatusList('returns')->find('new', 'acronym')->id
                            );
                        $serviceProduct = $table->createRow($params);
                        try {
                            if ($serviceProduct->demaged && !$serviceProduct->demagecodeid) {
                                throw new Exception("Brak kodu uszkodzenia");
                            }
                            $serviceProduct->save();
                        } catch (Exception $e) {
                            $form->getElement('demaged-' . $ix)->setErrors(array('demaged-' . $ix => $e->getMessage()));
                            return;
                        }
                        $returns[] = $productId;
                    }
                    $service->productsreturned = join(', ', $returns);
                } else {
                    $service->productsreturned = '';
                }//var_dump($data,$values);return;
                $service->save();
                $status = $this->_dictionaries->getStatusList('orders')->find('invoiced', 'acronym');
                $statusReleased = $this->_dictionaries->getStatusList('orders')->find('released', 'acronym');
                $this->_orderlines->setLazyLoading(true);
                foreach ($service->getProducts() as $ix => $product) {
                    if ($orderLine = $this->_orderlines->find($product->productid)->current()) {
                        //var_dump($orderLine->toArray(),$product->toArray());
                        $orderLine->statusid = $statusReleased->id;
                        //$orderLine->serviceid = null;
                        $orderLine->qtyavailable += $product->quantity;
                        if ($orderLine->qtyavailable > $orderLine->quantity) {
                            $form->getElement('quantity-' . $ix)->setErrors(array('quantity-' . $ix => 'Wystąpił ze zmianą produktu'));
                            return;
                        }
                        $orderLine->save();
                    }
                    $product->delete();
                }
                $productIds = array_filter((array)$request->getParam('productid'));
                if (!empty($productIds)) {
                    $quantities = array_filter((array)$request->getParam('quantity'));
                    $table = new Application_Model_Services_Products_Table();
                    foreach ($productIds as $ix => $orderLineId) {
                        $orderLineId = current($orderLineId);
                        preg_match("/\d+/", $ix, $found);
                        $ix = $found[0];
                        $params = array('serviceid' => $service->id);
                        if ($orderLine = $this->_orderlines->find($orderLineId)->current()) {
                            $params['productid'] = $orderLine->id;
                            $params['productname'] = $orderLine->getProduct()->name;
                            if ($products->find($orderLineId)->serial) {  
                                $quantity = $params['quantity'] = 1;
                            } else {
                                if (!($quantity = $quantities['quantity-' . $ix])) {
                                    $form->getElement('quantity-' . $ix)->setErrors(array('quantity-' . $ix => 'Brak ilości'));
                                    return;
                                }
                                $params['quantity'] = $quantity;
                            }
                            $orderLine->qtyavailable -= (int) $quantity;
                            if ($orderLine->qtyavailable < 0) {
                                $form->getElement('quantity-' . $ix)->setErrors(array('quantity-' . $ix => 'Wystąpił problem z dodaniem produktu'));
                                return;
                            }
                            if ($orderLine->qtyavailable == 0) {
                                $orderLine->statusid = $this->_dictionaries->getStatusList('orders')->find('invoiced', 'acronym')->id;
                            }
                            $orderLine->save();
                        } else {
                            $id = $ix + 1;
                            $params['productid'] = "-$id";
                            $params['productname'] = $orderLineId;
                            $params['quantity'] = 1;
                            $form->getElement('productid-' . $ix)->addMultiOption($orderLineId, $orderLineId);
                        }
                        if ($serviceProduct->quantity < 0) {
                            $form->getElement('quantity-' . $ix)->setErrors(array('quantity-' . $ix => 'Wystąpił problem z dodaniem produktu'));
                            return;
                        }
                        $serviceProduct = $table->createRow($params);
                        //var_dump($serviceProduct->toArray());//exit;
                        $serviceProduct->save();
                        //$this->_orderlines->update(array('statusid' => $status->id, 'serviceid' => $service->id), $this->_orderlines->getAdapter()->quoteInto('id = ?', $orderLineId));
                    }
                }//return;
                foreach ($service->getCodes() as $attribute) {
                    $attribute->delete();
                }
                if (!empty($codeTypes)) {
                    $table = new Application_Model_Services_Codes_Table();
                    foreach ($codeTypes as $type) {
                        switch ($type) {
                            case 'cancellation':
                                if ($values['performed'] == 1) {
                                    continue 2;
                                }
                                break;
                            case 'error':
                            case 'solution':
                            case 'modeminterchange':
                            case 'decoderinterchange':
                                if ($values['performed'] != 1) {
                                    continue 2;
                                }
                                break;
                        }
                        if (!$code = $dictionary->find($type . 'code', 'acronym')) {
                            continue;
                        }
                        $attributeId = $code->id;
                        foreach ((array) $this->_getParam($type . 'codeid') as $codeId) {
                            if (empty($codeId)) {
                                continue;
                            }
                            switch ($type) {
                                case 'solution':
                                    $code = $dictionary->find($type . 'code', 'acronym')->getChildren()->find($codeId);
                                    if (empty($code)) {
                                        break;
                                    }
                                    list($error, $solution) = explode('-', $code->acronym);//var_dump($error,$solution,$values);exit;
                                    switch ($solution) {
                                        case 'WKW':
                                        case 'WKX':
                                            if (!$values['decoderinterchangecodeid'] && (in_array($error, array('SCI', 'ST1')) || (0 /*&& $service->complaintcode == 'STB'*/))) {
                                                $form->getElement('decoderinterchangecodeid')->setErrors(array('decoderinterchangecodeid' => 'Wymagany kod wymiany dekodera'));
                                                return;
                                            }
                                            if (!$values['modeminterchangecodeid'] && (in_array($error, array('WIF', 'UMD', 'SIP')) || (0 /*&& $service->complaintcode == 'WCH'*/))) {
                                                $form->getElement('modeminterchangecodeid')->setErrors(array('modeminterchangecodeid' => 'Wymagany kod wymiany modemu'));
                                                return;
                                            }
                                            if (in_array($error, array('HZ1','PAY'))) {
                                                if (!$values['decoderinterchangecodeid'] && !$values['modeminterchangecodeid']) {
                                                    $form->setDescription('Wymagany kod wymiany modemu lub dekodera');
                                                    return;
                                                }
                                                if ($values['decoderinterchangecodeid'] && $values['modeminterchangecodeid']) {
                                                    $form->setDescription('Dozwolone podanie tylko jednego kodu: wymiany modemu lub dekodera');
                                                    return;
                                                }
                                            }
                                            if (!array_filter((array)$request->getParam('productid')) && !$values['productid']) {
                                                $form->getElement('quantity-0')->setErrors(array('quantity-0' => 'Wymagane kody produktów wydanych'));
                                                return;
                                            }
                                            if (!($productsReturned) && !($values['productreturnedid']) && !((array)$request->getParam('productreturnedid'))) {
                                                $form->getElement('demaged-0')->setErrors(array('demaged-0' => 'Wymagane kody produktów odebranych'));
                                                return;
                                            }
                                            break;
                                        case 'PLI':
                                            if (!$values['technicalcomments']) {
                                                $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie komentarza'));
                                                return;
                                            }
                                            break;
                                    }
                                    switch ($error) {
                                        case 'PAY':
                                        case 'SOW':
                                        case 'WOK':
                                        case 'ZMT':
                                            if (!$values['technicalcomments']) {
                                                $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie komentarza'));
                                                return;
                                            }
                                            break;
                                    }
                                    //$attribute = $table->createRow();
                                    //$attribute->serviceid = $service->id;
                                    //$attribute->attributeid = $errorAttributeId;
                                    //$attribute->codeid = $code->errorcodeid;
                                    //$attribute->save();
                                    break;
                                case 'modeminterchange':
                                    switch ($code) {
                                        case 'MW0':
                                        case 'MW1':
                                            if (!$values['technicalcomments']) {
                                                $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie komentarza'));
                                                return;
                                            }
                                            break;
                                    }
                                    break;
                                case 'decoderinterchange':
                                    switch ($code) {
                                        case 'WK1':
                                        case 'WKK':
                                            if (!$values['technicalcomments']) {
                                                $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie komentarza'));
                                                return;
                                            }
                                            break;
                                    }
                                    break;
                                case 'cancellation':
                                    switch ($code) {
                                        case 'P01':
                                        case 'PRT':
                                            if (!$values['technicalcomments']) {
                                                $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie komentarza'));
                                                return;
                                            }
                                            break;
                                    }
                                    break;
                                case 'installation':
                                    $codeId = current($codeId);
                                    break;
                                default:
                                    break;
                            }
                            $attribute = $table->createRow();
                            $attribute->serviceid = $service->id;
                            $attribute->attributeid = $attributeId;
                            $attribute->codeid = $codeId;
                            $attribute->save();
                        }
                    }
                }

                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Zgłoszenie zakończone';
            }
        }
    }

    public function closeAction() {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $typeid = $request->getParam('typeid');
        //$id = array_unique((array)$id);
        $types = $this->_dictionaries->getTypeList('service');
        switch ($this->_getParam('typeid')) {
            case $types->find('installation', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Installation');
                break;
            case $types->find('service', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Service');
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
        $service = $this->_services->get($id);
        if (!$service) {
            throw new Exception('Nie znaleziono zgłoszenia');
        }
        $this->_users->setOrderBy(array('lastname','firstname'));
        $technicians = $this->_users->getAll();
        //$order = $this->_orders->getAll(array('technicianid' => $service->technicianid))->current();
        $statusInvoiced = $this->_dictionaries->getStatusList('orders')->find('invoiced', 'acronym');
        $statusReleased = $this->_dictionaries->getStatusList('orders')->find('released', 'acronym');
        $units = $this->_dictionaries->getDictionaryList('warehouse', 'unit');
        $this->_orderlines->setLazyLoading(false);
        if ($service->technicianid) {
            $this->_orderlines->setWhere("technicianid = '{$service->technicianid}' AND (statusid = {$statusReleased->id} OR (statusid = {$statusInvoiced->id} AND serviceid = {$service->id}))");
            $products = $this->_orderlines->getAll(
                //array(
                //'statusid' => array(
                //    $statusInvoiced->id, 
                //    $statusReleased->id
                //), 
                //'technicianid' => $service->technicianid)
            );//var_dump($products->toArray());
            $options = array('products' => $products);
        }
        $this->_dictionaries->setLazyLoading(false);
        $types = $this->_dictionaries->getTypeList('service');
        //$options = array('products' => $products->toArray());
        $productsReleased = $service->getProducts()->toArray();
        $defaults = array(//'performed' => 1,
            'productid' => $productsReleased);
        if ($service->performed !== null) {
            //$defaults['performed'] = $service->performed;
        }
        if ($request->getParam('performed') !== null) {
            //$defaults['performed'] = $request->getParam('performed');
        }
        $productsReturned = explode(',', $service->productsreturned);
        $productsReturned = array_combine($productsReturned, $productsReturned);
        $defaults['productreturnedid'] = $productsReturned;
        $defaults['productreturnedid'] = $service->getReturns();
        $serialsReturned = explode(',', $service->serialnumbers);
        $serialsReturned = array_combine($serialsReturned, $serialsReturned);
        $serialsReturned = array_merge($serialsReturned, $productsReturned);
        $options['productsreturned'] = $serialsReturned;//var_dump($service->productsreturned);
        switch ($service->typeid) {
            // zlecenie instalacyjne
            case $types->find('installation', 'acronym')->id:
                $codeTypes = array('installation', 'installationcancel', 'modeminterchange', 'decoderinterchange');
                break;
            // zlecenie serwisowe
            case $types->find('service', 'acronym')->id:
                $codeTypes = array('error', 'solution', 'cancellation', 'modeminterchange', 'decoderinterchange');
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
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
                $attributeId = $code->id;
                if ($codes = $service->getCodes()->filter(array('attributeid' => $attributeId))) {
                    $defaults[$type . 'codeid'] = $codes->toArray();
                }
            }
        }
        $options['demagecodes'] = array_merge($options['modeminterchangecodes'], $options['decoderinterchangecodes']);
        $this->_catalog->setWhere("statusid != {$status->id}");
        $catalog = $this->_catalog->getAll();
        $options['catalog'] = $catalog->toArray();
        if (!empty($defaults['installationcodeid']))
            foreach ($defaults['installationcodeid'] as $i => $item) {
                $defaults['installationcodeid-' . $i] = $item;
            }
        if (!empty($defaults['productreturnedid']))
            foreach ($defaults['productreturnedid'] as $i => $item) {
                $defaults['productreturnedid-' . $i] = $item;
            }
        if (!empty($defaults['productid'])) {
            foreach ($defaults['productid'] as $i => $item) {
                $defaults['productid-' . $i] = $item;
            }
        }
        if ($types->find('installation', 'acronym')->id == $typeid /* && !in_array($this->_auth->getIdentity(), array('admin', 'coordinator')) */) {
            $service->datefinished = date('Y-m-d', strtotime($service->datefinished ? $service->datefinished : $service->planneddate)); //$service->timetill;
        } else if ($types->find('service', 'acronym')->id == $typeid) {
            $service->datefinished = $service->datefinished ? date('H:i', strtotime($service->datefinished)) : null; //$service->timetill;
        }
        $productsReturnedCount = @sizeof($defaults['productreturnedid']);
        $installationCodesCount = @sizeof($defaults['installationcodeid']);
        $productsCount = @sizeof($defaults['productid']);
        if ($this->getRequest()->isPost()) {
            $data = $request->getPost();
            $productsReturnedCount = @sizeof($this->getRequest()->getParam('productreturnedid'));
            $installationCodesCount = @sizeof($this->getRequest()->getParam('installationcodeid'));
            $productsCount = @sizeof($this->getRequest()->getParam('productid'));
        }
        switch ($typeid) {
            // zlecenie instalacyjne
            case $types->find('installation', 'acronym')->id:
                $form = new Application_Form_Services_Installation(array('installationCodesCount' => $installationCodesCount,
                    'productsReturnedCount' => $productsReturnedCount,
                    'productsCount' => $productsCount));
                break;
            // zlecenie serwisowe
            case $types->find('service', 'acronym')->id:
                $form = new Application_Form_Services_Service(array('installationCodesCount' => $installationCodesCount,
                    'productsReturnedCount' => $productsReturnedCount,
                    'productsCount' => $productsCount));
                break;
        }
        if ($service) {
            $form->setDefaults($service->toArray());
        }
        $form->setOptions($options);
        $form->setDefaults($defaults);
        if ($service->isNew()) {
            $form->getElement('performed')->setValue(0)->setAttrib('readonly', 'readonly');
        }
        $this->view->form = $form;
        $this->view->service = $service;
        $this->view->types = $types;
        
        $status = $this->_dictionaries->getStatusList('service')->find('closed', 'acronym');

        if ($this->getRequest()->isPost()) {
            $data = $request->getPost();
            if (!empty($data['installationcodeid'])) 
                foreach ($data['installationcodeid'] as $key => $value) {
                    $data[$key] = $value;
                }
            if (!empty($data['productid'])) 
                foreach ($data['productid'] as $key => $value) {
                    $data[$key] = $value;
                }
            if (!empty($data['quantity'])) 
                foreach ($data['quantity'] as $key => $value) {
                    $data[$key] = $value;
                }
            if (!empty($data['productreturnedid'])) 
                foreach ($data['productreturnedid'] as $key => $value) {
                    $data[$key] = $value;
                }
            if (!empty($data['demaged']))
                foreach ($data['demaged'] as $key => $value) {
                    $data[$key] = $value;
                }
            if (!empty($data['demagecodeid']))
                foreach ($data['demagecodeid'] as $key => $value) {
                    $data[$key] = $value;
                }
            $form->setDefaults($data);
            if ($form->isValid($data)) {
                $values = $form->getValues();
                if (!$service->isAssigned()) {
                    //$form->setDescription('Nieprawidłowy status zlecenia');
                    //return;
                }
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                if ($service->isNew() && $values['performed'] != 0) {
                    $form->getElement('performed')->setErrors(array('performed' => 'Nieprawidłowy status wykonania'));
                    return;
                }
                if ($types->find('service', 'acronym')->id == $typeid) {
                    if ($values['performed'] === '0' && !$values['cancellationcodeid']) {
                        $form->getElement('cancellationcodeid')->setErrors(array('cancellationcodeid' => 'Wymagane podanie kodu odwołania'));
                        return;
                    }
                    if ($values['performed'] === '1' && empty($data['solutioncodeid'])) {
                        $form->getElement('solutioncodeid')->setErrors(array('solutioncodeid' => 'Wymagane podanie kodu rozwiązania'));
                        return;
                    }
                } else {
                    if ($values['performed'] === '0' && !$values['installationcancelcodeid']) {
                        $form->getElement('installationcancelcodeid')->setErrors(array('installationcancelcodeid' => 'Wymagane podanie kodu odwołania'));
                        return;
                    }
                    if ($values['performed'] === '0' && !$values['technicalcomments']) {
                        $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie uzasadnienia'));
                        return;
                    }
                    if ($values['performed'] === '1' && empty($data['installationcodeid'])) {
                        $form->getElement('installationcodeid-0')->setErrors(array('installationcodeid-0' => 'Wymagane podanie kodu instalacji'));
                        return;
                    }
                }
                if ($values['performed'] === '0' && !$values['datefinished']) {
                    $form->getElement('datefinished')->setErrors(array('datefinished' => 'Wymagane podanie daty zakończenia'));
                    return;
                }
                if (($service->isAssigned() || $types->find('installation', 'acronym')->id == $typeid) && $values['performed'] === '0' && !$values['technicalcomments'] && !$values['coordinatorcomments']) {
                    //$form->getElement('coordinatorcomments')->setErrors(array('coordinatorcomments' => 'Wymagane podanie komentarza koordynatora'));
                    //return;
                }
                $service->technicalcomments = $values['technicalcomments'];
                $service->coordinatorcomments = $values['coordinatorcomments'];
                if (!empty($values['datefinished'])) {
                    if ($types->find('service', 'acronym')->id == $typeid) {
                        $service->datefinished = date('Y-m-d H:i', strtotime($service->planneddate . ' ' . $values['datefinished']));
                    } else {
                        $service->datefinished = date('Y-m-d H:i', strtotime($values['datefinished']));
                    }
                }
                $service->statusid = $status->id;
                if (!strlen($values['performed'])) {
                    $service->performed = null;
                } else {
                    $service->performed = $values['performed'];
                }
                $productsReturned = (array)$request->getParam('productreturnedid');
                $demaged = (array)$request->getParam('demaged');
                $demagecode = (array)$request->getParam('demagecodeid');
                $table = new Application_Model_Services_Returns_Table();
                foreach ($service->getReturns() as $product) {
                    $return = null;
                    foreach ($productsReturned as $ix => $productId) {
                        $productId = current($productId);
                        preg_match("/\d+/", $ix, $found);
                        $ix = $found[0];
                        if ($productId == $product->name) {
                            $return = $table->find($product->id)->current();
                            unset($productsReturned['productreturnedid-' . $ix]);
                            break;
                        }
                    }
                    
                    if ($return) {
                        if (!$product->isNew()
                                && $return->demaged != (int)$demaged['demaged-' . $ix]
                                 && $return->demagecodeid != (int)$demagecode['demagecodeid-' . $ix]) {
                            $form->getElement('demaged-' . $ix)->setErrors(array('demaged-' . $ix => 'Nie można zmodyfikować potwierdzonego zwrotu'));
                            return;
                        }
                        $return->setFromArray(array('demaged' => (int)$demaged['demaged-' . $ix],
                            'demagecodeid' => (int)$demagecode['demagecodeid-' . $ix]))->save();
                    } else {
                        if (!$product->isNew()) {
                            $form->setDescription('Nie można usunąć potwierdzonego zwrotu');
                            return;
                        }
                        $product->delete();
                        continue;
                    }
                }
                if ($productsReturned) {
                    $returns = array();
                    foreach($productsReturned as $ix => $productId) {
                        $productId = current($productId);//var_dump($productId);exit;
                        preg_match("/\d+/", $ix, $found);
                        $ix = $found[0];
                        //if (!$form->getElement('productreturnedid-' . $ix)) {
                        //    var_dump($ix,$productId,$data,array_keys($form->getElements()));exit;
                        //}
                        //$form->getElement('productreturnedid-' . $ix)->addMultiOption($productId, $productId);
                        $params = array('serviceid' => $service->id, 
                            'name' => $productId, 
                            'quantity' => 1, 
                            'unitid' => $units->find('szt', 'acronym') -> id,
                            'demaged' => (int)$demaged['demaged-' . $ix],
                            'demagecodeid' => (int)$demagecode['demagecodeid-' . $ix],
                            'statusid' => $this->_dictionaries->getStatusList('returns')->find('new', 'acronym')->id
                        );
                        $serviceProduct = $table->createRow($params);//var_dump($serviceProduct->toArray());
                        try {
                            if ($serviceProduct->demaged && !$serviceProduct->demagecodeid) {
                                throw new Exception("Brak kodu uszkodzenia");
                            }
                            $serviceProduct->save();
                        } catch (Exception $e) {
                            $form->getElement('demaged-' . $ix)->setErrors(array('demaged-' . $ix => $e->getMessage()));
                            return;
                        }
                        $returns[] = $productId;
                    }
                    $service->productsreturned = join(', ', $returns);
                } else {
                    $service->productsreturned = '';
                }//var_dump($data,$values);return;
                $service->save();
                $status = $this->_dictionaries->getStatusList('orders')->find('invoiced', 'acronym');
                $statusReleased = $this->_dictionaries->getStatusList('orders')->find('released', 'acronym');
                $this->_orderlines->setLazyLoading(true);
                foreach ($service->getProducts() as $ix => $product) {
                    if ($orderLine = $this->_orderlines->find($product->productid)->current()) {
                        //var_dump($orderLine->toArray(),$product->toArray());
                        $orderLine->statusid = $statusReleased->id;
                        //$orderLine->serviceid = null;
                        $orderLine->qtyavailable += $product->quantity;
                        if ($orderLine->qtyavailable > $orderLine->quantity) {
                            $form->getElement('quantity-' . $ix)->setErrors(array('quantity-' . $ix => 'Wystąpił ze zmianą produktu'));
                            return;
                        }
                        $orderLine->save();
                    }
                    $product->delete();
                }
                $productIds = array_filter((array)$request->getParam('productid'));
                if (!empty($productIds)) {
                    $quantities = array_filter((array)$request->getParam('quantity'));
                    $table = new Application_Model_Services_Products_Table();
                    foreach ($productIds as $ix => $orderLineId) {
                        $orderLineId = current($orderLineId);
                        preg_match("/\d+/", $ix, $found);
                        $ix = $found[0];
                        $params = array('serviceid' => $service->id);
                        if ($orderLine = $this->_orderlines->find($orderLineId)->current()) {
                            $params['productid'] = $orderLine->id;
                            $params['productname'] = $orderLine->getProduct()->name;
                            if ($products->find($orderLineId)->serial) {  
                                $quantity = $params['quantity'] = 1;
                            } else {
                                if (!($quantity = $quantities['quantity-' . $ix])) {
                                    $form->getElement('quantity-' . $ix)->setErrors(array('quantity-' . $ix => 'Brak ilości'));
                                    return;
                                }
                                $params['quantity'] = $quantity;
                            }
                            $orderLine->qtyavailable -= (int) $quantity;
                            if ($orderLine->qtyavailable < 0) {
                                $form->getElement('quantity-' . $ix)->setErrors(array('quantity-' . $ix => 'Wystąpił problem z dodaniem produktu'));
                                return;
                            }
                            if ($orderLine->qtyavailable == 0) {
                                $orderLine->statusid = $this->_dictionaries->getStatusList('orders')->find('invoiced', 'acronym')->id;
                            }
                            $orderLine->save();
                        } else {
                            $id = $ix + 1;
                            $params['productid'] = "-$id";
                            $params['productname'] = $orderLineId;
                            $params['quantity'] = 1;
                            $form->getElement('productid-' . $ix)->addMultiOption($orderLineId, $orderLineId);
                        }
                        if ($serviceProduct->quantity < 0) {
                            $form->getElement('quantity-' . $ix)->setErrors(array('quantity-' . $ix => 'Wystąpił problem z dodaniem produktu'));
                            return;
                        }
                        $serviceProduct = $table->createRow($params);
                        //var_dump($serviceProduct->toArray());//exit;
                        $serviceProduct->save();
                        //$this->_orderlines->update(array('statusid' => $status->id, 'serviceid' => $service->id), $this->_orderlines->getAdapter()->quoteInto('id = ?', $orderLineId));
                    }
                }//return;
                foreach ($service->getCodes() as $attribute) {
                    $attribute->delete();
                }
                if (!empty($codeTypes)) {
                    $table = new Application_Model_Services_Codes_Table();
                    foreach ($codeTypes as $type) {
                        switch ($type) {
                            case 'cancellation':
                                if ($values['performed'] == 1) {
                                    continue 2;
                                }
                                break;
                            case 'error':
                            case 'solution':
                            case 'modeminterchange':
                            case 'decoderinterchange':
                                if ($values['performed'] != 1) {
                                    continue 2;
                                }
                                break;
                        }
                        if (!$code = $dictionary->find($type . 'code', 'acronym')) {
                            continue;
                        }
                        $attributeId = $code->id;
                        foreach ((array) $this->_getParam($type . 'codeid') as $codeId) {
                            if (empty($codeId)) {
                                continue;
                            }
                            switch ($type) {
                                case 'solution':
                                    $code = $dictionary->find($type . 'code', 'acronym')->getChildren()->find($codeId);
                                    if (empty($code)) {
                                        break;
                                    }
                                    list($error, $solution) = explode('-', $code->acronym);//var_dump($error,$solution,$values);exit;
                                    switch ($solution) {
                                        case 'WKW':
                                        case 'WKX':
                                            if (!$values['decoderinterchangecodeid'] && (in_array($error, array('SCI', 'ST1')) || (0 /*&& $service->complaintcode == 'STB'*/))) {
                                                $form->getElement('decoderinterchangecodeid')->setErrors(array('decoderinterchangecodeid' => 'Wymagany kod wymiany dekodera'));
                                                return;
                                            }
                                            if (!$values['modeminterchangecodeid'] && (in_array($error, array('WIF', 'UMD', 'SIP')) || (0 /*&& $service->complaintcode == 'WCH'*/))) {
                                                $form->getElement('modeminterchangecodeid')->setErrors(array('modeminterchangecodeid' => 'Wymagany kod wymiany modemu'));
                                                return;
                                            }
                                            if (in_array($error, array('HZ1','PAY'))) {
                                                if (!$values['decoderinterchangecodeid'] && !$values['modeminterchangecodeid']) {
                                                    $form->setDescription('Wymagany kod wymiany modemu lub dekodera');
                                                    return;
                                                }
                                                if ($values['decoderinterchangecodeid'] && $values['modeminterchangecodeid']) {
                                                    $form->setDescription('Dozwolone podanie tylko jednego kodu: wymiany modemu lub dekodera');
                                                    return;
                                                }
                                            }
                                            if (!array_filter((array)$request->getParam('productid')) && !$values['productid']) {
                                                $form->getElement('quantity-0')->setErrors(array('quantity-0' => 'Wymagane kody produktów wydanych'));
                                                return;
                                            }
                                            if (!($productsReturned) && !($values['productreturnedid']) && !((array)$request->getParam('productreturnedid'))) {
                                                $form->getElement('demaged-0')->setErrors(array('demaged-0' => 'Wymagane kody produktów odebranych'));
                                                return;
                                            }
                                            break;
                                        case 'PLI':
                                            if (!$values['technicalcomments']) {
                                                $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie komentarza'));
                                                return;
                                            }
                                            break;
                                    }
                                    switch ($error) {
                                        case 'PAY':
                                        case 'SOW':
                                        case 'WOK':
                                        case 'ZMT':
                                            if (!$values['technicalcomments']) {
                                                $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie komentarza'));
                                                return;
                                            }
                                            break;
                                    }
                                    //$attribute = $table->createRow();
                                    //$attribute->serviceid = $service->id;
                                    //$attribute->attributeid = $errorAttributeId;
                                    //$attribute->codeid = $code->errorcodeid;
                                    //$attribute->save();
                                    break;
                                case 'modeminterchange':
                                    switch ($code) {
                                        case 'MW0':
                                        case 'MW1':
                                            if (!$values['technicalcomments']) {
                                                $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie komentarza'));
                                                return;
                                            }
                                            break;
                                    }
                                    break;
                                case 'decoderinterchange':
                                    switch ($code) {
                                        case 'WK1':
                                        case 'WKK':
                                            if (!$values['technicalcomments']) {
                                                $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie komentarza'));
                                                return;
                                            }
                                            break;
                                    }
                                    break;
                                case 'cancellation':
                                    switch ($code) {
                                        case 'P01':
                                        case 'PRT':
                                            if (!$values['technicalcomments']) {
                                                $form->getElement('technicalcomments')->setErrors(array('technicalcomments' => 'Wymagane podanie komentarza'));
                                                return;
                                            }
                                            break;
                                    }
                                    break;
                                case 'installation':
                                    $codeId = current($codeId);
                                    break;
                                default:
                                    break;
                            }
                            $attribute = $table->createRow();
                            $attribute->serviceid = $service->id;
                            $attribute->attributeid = $attributeId;
                            $attribute->codeid = $codeId;
                            $attribute->save();
                        }
                    }
                }

                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Zgłoszenie zakończone';
            }
        }
    }

    public function returnAction() {
        $request = $this->getRequest();
        $id = (array) $request->getParam('id');
        $typeid = $request->getParam('typeid');
        //$id = array_unique((array)$id);
        $types = $this->_dictionaries->getTypeList('service');
        switch ($this->_getParam('typeid')) {
            case $types->find('installation', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Installation');
                break;
            case $types->find('service', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Service');
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
        $service = $this->_services->get($id);
        if (!$service) {
            throw new Exception('Nie znaleziono zgłoszenia');
        }
        $technicians = $this->_users->getAll();
        $form = new Application_Form_Services_Return(array('servicesCount' => $service->count()));
        $form->setServices($service);
        $form->setDefaults($service->toArray());
        $this->view->form = $form;
        $this->view->service = $service;
        $this->view->types = $types;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                $values = $form->getValues();
                foreach ($service as $i => $item) {
                    if (!$item->isFinished() && !$item->isClosed()) {
                        $form->getElement('id-' . $i)->setErrors(array('id-' . $i => 'Nieprawidłowy status zlecenia'));
                        return;
                    }
                    $item->coordinatorcomments = $item->coordinatorcomments . "\n" . $values['coordinatorcomments'];
                    if ($item->technicianid)
                        $status = $this->_dictionaries->getStatusList('service')->find('reassigned', 'acronym');
                    else
                        $status = $this->_dictionaries->getStatusList('service')->find('new', 'acronym');
                    $item->statusid = $status->id;
                    $item->save();
                }
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Zgłoszenie cofnięte';
            }
        }
    }

    public function withdrawAction() {
        $request = $this->getRequest();
        $id = (array) $request->getParam('id');
        $typeid = $request->getParam('typeid');
        //$id = array_unique((array)$id);
        $types = $this->_dictionaries->getTypeList('service');
        switch ($this->_getParam('typeid')) {
            case $types->find('installation', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Installation');
                break;
            case $types->find('service', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Service');
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
        $services = $this->_services->get($id);
        if (!$services) {
            throw new Exception('Nie znaleziono zgłoszenia');
        }
        $technicians = $this->_users->getAll();
        $form = new Application_Form_Services_Withdraw(array('servicesCount' => $services->count()));
        $form->setServices($services);
        $status = $this->_dictionaries->getStatusList('service')->find('withdrawn', 'acronym');
        $form->setDefaults($services->toArray());
        $this->view->form = $form;
        $this->view->service = $services;
        $this->view->types = $types;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                foreach ($services as $i => $service) {
                    if (!$service->isAssigned() && !$service->isFinished()) {
                        //$form->getElement('id-' . $i)->setErrors(array('id-' . $i => 'Nieprawidłowy status zlecenia'));
                        //return;
                    }
                    $service->setFromArray($values);
                    $service->statusid = $status->id;
                    $service->technicianid = null;
                    $service->performed = null;
                    $service->technicalcomments = null;
                    $service->save();
                    $statusReleased = $this->_dictionaries->getStatusList('orders')->find('released', 'acronym');
                    $this->_orderlines->setLazyLoading(true);
                    foreach ($service->getReturns() as $product) {
                        $product->delete();
                    }
                    foreach ($service->getProducts() as $product) {
                        if ($orderLine = $this->_orderlines->find($product->productid)->current()) {
                            $orderLine->statusid = $statusReleased->id;
                            //$orderLine->serviceid = null;
                            $orderLine->qtyavailable += $orderLine->quantity;
                            if ($orderLine->quantity > $orderLine->qtyavailable) {
                                $form->getElement('id-' . $i)->setErrors(array('productid' => 'Wystąpił problem z dodaniem produktu'));
                                return;
                            }
                            $orderLine->save();
                        }
                        $product->delete();
                    }
                    foreach ($service->getCodes() as $attribute) {
                        $attribute->delete();
                    }
                }
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Zgłoszenie wycofane';
            }
        }
    }

    public function deleteAction() {
        $request = $this->getRequest();
        $id = (array) $request->getParam('id');
        $typeid = $request->getParam('typeid');
        //$id = array_unique((array)$id);
        $types = $this->_dictionaries->getTypeList('service');
        switch ($this->_getParam('typeid')) {
            case $types->find('installation', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Installation');
                break;
            case $types->find('service', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Service');
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
        $service = $this->_services->get($id);
        if (!$service) {
            throw new Exception('Nie znaleziono zgłoszenia');
        }
        $form = new Application_Form_Services_Delete(array('servicesCount' => $service->count()));
        $form->setServices($service);
        $status = $this->_dictionaries->getStatusList('service')->find('deleted', 'acronym');
        $this->view->form = $form;
        $this->view->service = $service;
        $this->view->types = $types;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                foreach ($service as $i => $item) {
                    if (!$item->isNew()) {
                        $form->getElement('id-' . $i)->setErrors(array('id-' . $i => 'Nieprawidłowy status zlecenia'));
                        return;
                    }
                    $item->delete();
                    //$item->statusid = $status->id;
                    //$item->save();
                }
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Zgłoszenie usunięte';
            }
        }
    }

    public function closeMultiAction() {
        $request = $this->getRequest();
        $id = (array) $request->getParam('id');
        $typeid = $request->getParam('typeid');
        //$id = array_unique((array)$id);
        $types = $this->_dictionaries->getTypeList('service');
        switch ($this->_getParam('typeid')) {
            case $types->find('installation', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Installation');
                break;
            case $types->find('service', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_Service');
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
        $service = $this->_services->get($id);
        if (!$service) {
            throw new Exception('Nie znaleziono zgłoszenia');
        }
        $form = new Application_Form_Services_Delete(array('servicesCount' => $service->count()));
        $form->setServices($service);
        $status = $this->_dictionaries->getStatusList('service')->find('closed', 'acronym');
        $this->view->form = $form;
        $this->view->service = $service;
        $this->view->types = $types;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                foreach ($service as $i => $item) {
                    if ($item->isClosed()) {
                        $form->getElement('id-' . $i)->setErrors(array('id-' . $i => 'Nieprawidłowy status zlecenia'));
                        return;
                    }
                    $item->statusid = $status->id;
                    $item->save();
                }
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Zgłoszenie zamknięte';
            }
        }
    }

    public function checkSolutionCodeAction() {
        $id = explode(',', $this->_getParam('id'));
        if (!$solutionCodes = $this->_dictionaries->find($id)) {
            throw new Exception('Nie znaleziono kodu rozwiązania');
        }
        foreach ($solutionCodes as $solutionCode) {
            $errorCode = $solutionCode->getErrorcode();
            if (!$errorCode = $this->_dictionaries->getDictionaryList('service', 'errorcode')->find($errorCode->value, 'id')) {
                //throw new Exception('Nie znaleziono kodu błędu');
            }
            $errorCode = $errorCode->toArray();
            $codes[] = $errorCode['id'];
        }
        $this->view->errorCodes = $codes;
    }

    public function getSolutionCodeListAction() {
        $id = explode(',', $this->_getParam('id'));
        if (!$errorCodes = $this->_dictionaries->find($id)) {
            throw new Exception('Nie znaleziono kodu błędu');
        }
        foreach ($errorCodes as $errorCode) {
            $solutionCodes = $errorCode->getSolutioncodes();
            foreach ($solutionCodes as $solutionCode) {
                $solutionCode = $this->_dictionaries->find($solutionCode->entryid);
                $codes[] = $solutionCode->toArray();
            }
        }
        $this->view->solutionCodes = $codes;
    }

    public function sendCommentsAction() {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $typeid = $request->getParam('typeid');
        $types = $this->_dictionaries->getTypeList('service');
        $service = $this->_services->get($id);
        if (!$service) {
            throw new Exception('Nie znaleziono zlecenia');
        }
        //$defaults = array('recipient' => $this->_auth->getIdentity()->role == 'technician' ? 'koordynatorzy.lublin@nplay.pl' : 'lublin.instalacje@upc.com.pl;koordynatorzy.lublin@nplay.pl',
        //    'content' => $service->technicalcomments);
        $defaults = array('recipient' => $this->_auth->getIdentity()->role == 'technician' ? 
                'koordynatorzy.lublin@nplay.pl' : 
            $this->_config->get(APPLICATION_ENV)->comments->mail->recipients,
            'content' => $service->technicalcomments);
        $dictionary = $this->_dictionaries->getDictionaryList('service');
        switch ($this->_getParam('typeid')) {
            case $types->find('installation', 'acronym')->id:
                $defaults['subject'] = "ID: {$service->getClient()->number} WO: {$service->number}";
                $code = $dictionary->find('installationcancelcode', 'acronym');
                break;
            case $types->find('service', 'acronym')->id:
                $defaults['subject'] = "ID: {$service->getClient()->number} WO: {$service->number}";
                $code = $dictionary->find('cancellationcode', 'acronym');
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
        $defaults['content'] = "ID: {$service->getClient()->number} WO: {$service->number}\n{$service->planneddate}\n{$service->client}\n";
        $attributeId = $code->id;
        if ($codes = $service->getCodes()->filter(array('attributeid' => $attributeId))) {
            $data = array();
            foreach ($codes->toArray() as $code) {
                if (empty($code['codeacronym']) && empty($code['codename'])) continue;
                $data[] = $code['codeacronym'] . ' - ' . $code['codename'];
            }
            if ($data)
                $defaults['content'] .= implode(', ', $data) . "\n";
        }
        if ($service->technicalcomments) {
            $defaults['content'] .= $service->technicalcomments . "\n";
        }
        $form = new Application_Form_Services_Comments_Send();
        $form->setDefaults($defaults);
        if ($this->_auth->getIdentity()->role == 'technician') {
            $form->getElement('recipient')->setAttrib('readonly', 'readonly');
        }

        $this->view->form = $form;
        $this->view->service = $service;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
                $transport = $bootstrap->getResource('mail');
                $mail = new Zend_Mail('UTF-8');
                $mail->setDefaultTransport($transport);
                $this->_auth->getIdentity()->role == 'technician' ? 
                        $mail->setFrom($this->_auth->getIdentity()->email,$this->_auth->getIdentity()->email) :
                    //$mail->setFrom('koordynatorzy.lublin@nplay.pl','koordynatorzy.lublin@nplay.pl');
                    $mail->setFrom($this->_config->get(APPLICATION_ENV)->comments->mail->from);
                //$mail->setFrom($this->_auth->getIdentity()->email, $this->_auth->getIdentity()->lastname . ' ' . $this->_auth->getIdentity()->firstname);
                foreach(explode(';',$values['recipient']) as $recipient)
                    $mail->addTo(trim($recipient));
                if ($this->_config->get(APPLICATION_ENV)->comments->mail->bcc)
                    $mail->addBcc($this->_config->get(APPLICATION_ENV)->comments->mail->bcc);
                $mail->setSubject($values['subject']);
                $mail->setBodyHtml(nl2br($values['content']));//var_dump($mail);exit;
                $log = $bootstrap->getResource('log');
                $log->info(print_r($mail,1));//exit;
                $result = $mail->send();
                $log->info(print_r($result,1));
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Wysłano komentarz';
            }
        }
    }

    public function migrationAction() {
        // action body
        //var_dump(PHPExcel_Shared_Date::ExcelToPHP('42121'));
        $request = $this->getRequest();
        $typeid = $request->getParam('typeid');
        $form = new Application_Form_Services_Migration();
        $this->_dictionaries->setCacheInClass(false);
        $this->_dictionaries->clearCache();
        $types = $this->_dictionaries->getTypeList('service');
        $units = $this->_dictionaries->getDictionaryList('warehouse', 'unit');
        $form->setOptions(array('types' => $types));
        $this->_products = new Application_Model_Products_Table();
        $this->_orderlines = new Application_Model_Orders_Lines_Table();
        $this->_orders = new Application_Model_Orders_Table();
        $this->_serviceproducts = new Application_Model_Services_Products_Table();
        $this->_servicecodes = new Application_Model_Services_Codes_Table();
        $status = $this->_dictionaries->getStatusList('orders')->find('released', 'acronym');
        if (!$order = $this->_orders->getAll(array('userid' => $this->_auth->getIdentity()->id))->current()) {
            $order = $this->_orders->createRow();
            $order->setFromArray(array('userid' => $this->_auth->getIdentity()->id));
            $order->save();
        }
        
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            switch ($typeid) {
                case $types->find('installation', 'acronym')->id:
                    $codeTypes = array('installation', 'installationcancel', 'modeminterchange', 'decoderinterchange');
                    break;
                case $types->find('service', 'acronym')->id:
                    $codeTypes = array('error', 'solution', 'cancellation', 'modeminterchange', 'decoderinterchange');
                    break;
                default:
                    throw new Exception('Nieprawidłowy typ zlecenia');
            }
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();

                $upload = new Zend_File_Transfer_Adapter_Http();
                $upload->setDestination(APPLICATION_PATH . "/../data/pliki/migration/");

                try {
                    $userStatus = $this->_dictionaries->getStatusList('users')->find('active', 'acronym');
                    $serviceStatusAssigned = $this->_dictionaries->getStatusList('service')->find('assigned', 'acronym');
                    $serviceStatusNew = $this->_dictionaries->getStatusList('service')->find('new', 'acronym');
                    $serviceStatusFinished = $this->_dictionaries->getStatusList('service')->find('finished', 'acronym');
                    $serviceStatusClosed = $this->_dictionaries->getStatusList('service')->find('closed', 'acronym');
                    $productStatusReleased = $this->_dictionaries->getStatusList('products')->find('released', 'acronym');

                    switch ($typeid) {
                        // zlecenie instalacyjne
                        case $types->find('installation', 'acronym')->id:
                            $rowNumber = 2;
                            $this->_services->setRowClass('Application_Model_Services_Migration_Installation');
                            break;
                        // zlecenie serwisowe
                        case $types->find('service', 'acronym')->id:
                            $rowNumber = 2;
                            $this->_services->setRowClass('Application_Model_Services_Migration_Service');
                            break;
                        default:
                            throw new Exception('Nieprawidłowy typ zlecenia');
                    }
                    $params = array('statusid' => $serviceStatusNew->id, 'typeid' => $values['typeid']/* , 'warehouseid' => $values['warehouseid'] */);

                    $upload->receive();

                    $reader = new Utils_File_Reader_PHPExcel(array('readerType' => 'Excel5', 'readOnly' => true));
                    $data = $reader->read($upload->getFileName('import', true), 1);
                    $rows = $data->getRowIterator($rowNumber);
                    
                    if (!empty($values['report'])) {
                        $objPHPExcel = new PHPExcel();
                        $objPHPExcel->setActiveSheetIndex(0);
                    }

                    $this->_dictionaries->setLazyLoading(true);
                    foreach ($rows as $i => $row) {
                        $dictionary = $this->_dictionaries->getDictionaryList('service');

                        $service = $this->_services->createRow();
                        $service->setFromArray($params);
                        $i++;
                        $line = array();
                        $columnNo = 0;

                        $cellIterator = $row->getCellIterator();

                        // This loops all cells, even if it is not set.
                        // By default, only cells that are set will be iterated.
                        $cellIterator->setIterateOnlyExistingCells(false);

                        $service->setFromCellIterator($cellIterator);
                        if (!$service->number) {
                            continue;
                        }

                        try {   
                            Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                            $this->_services->setOrderBy(array('id'));
                            if ($existing = $this->_services->getAll(array('number' => $service->number, 'planneddate' => $service->planneddate))->current()) {
                                
                                foreach ($codeTypes as $type) {
                                    if ($code = $dictionary->find($type . 'code', 'acronym')) {
                                        $attributeId = $code->id;
                                        $codesOld = $existing->getCodes()->filter(array('attributeid' => $attributeId));
                                        switch ($type) {
                                            case 'installation':
                                                //var_dump($codesOld->toArray(),$service->getInstallationcode());exit;
                                                if ($codesOld->count() && $service->getInstallationcode()) {
                                                    break 2;
                                                }
                                                if ($codesOld->count() && !$service->getInstallationcode()) {
                                                    throw new Application_Model_ServicesRowExist();
                                                }
                                                if (!$codesOld->count() && $service->getInstallationcode()) {
                                                    $service->id = $existing->id;
                                                }
                                                if (!$codesOld->count() && !$service->getInstallationcode()) {
                                                    $service->id = $existing->id;
                                                }
                                                break;
                                            case 'solution':
                                                //var_dump($codesOld->toArray(),$service->getSolutioncode());exit;
                                                if ($codesOld->count() && $service->getSolutioncode()) {
                                                    break 2;
                                                }
                                                if ($codesOld->count() && !$service->getSolutioncode()) {
                                                    throw new Application_Model_ServicesRowExist();
                                                }
                                                if (!$codesOld->count() && $service->getSolutioncode()) {
                                                    $service->id = $existing->id;
                                                    //var_dump($service->number,$service->id,$existing->number,$existing->id,$service->getSolutioncode(),$service->getCancellationcode());
                                                }
                                                if (!$codesOld->count() && !$service->getSolutioncode()) {
                                                    $service->id = $existing->id;
                                                }
                                                break;
                                            case 'cancelation':
                                                //var_dump($codesOld->toArray(),$service->getCancellationcode());exit;
                                                if ($codesOld->count() && $service->getCancellationcode()) {
                                                    break 2;
                                                }
                                                if ($codesOld->count() && !$service->getCancellationcode()) {
                                                    throw new Application_Model_ServicesRowExist();
                                                }
                                                if (!$codesOld->count() && $service->getCancellationcode()) {
                                                    $service->id = $existing->id;
                                                }
                                                if (!$codesOld->count() && !$service->getCancellationcode()) {
                                                    $service->id = $existing->id;
                                                }
                                                break;
                                        }
                                    }
                                }
                            }
                            $data = $service->getServicetype();
                            $servicetype = $dictionary->find('type', 'acronym')->getChildren()->find(strtoupper($data), 'acronym');
                            if (!$servicetype) {
                                $servicetype = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('type', 'acronym')->id, 'acronym' => $data));
                                $servicetype->save();
                            }
                            $service->servicetypeid = $servicetype->id;
                            $service->statusid = $serviceStatusNew->id;

                            if ($data = $service->getTechnician()) {
                                if (!empty($data['firstname']) && !empty($data['lastname'])) {
                                    $this->_users->clearWhere();
                                    $this->_users->setWhere($this->_users->getAdapter()->quoteInto("firstname LIKE ?", "{$data['firstname']}%"));
                                    //$this->_users->setWhere($this->_users->getAdapter()->quoteInto("lastname = ?", "{$data['lastname']}%"));
                                    $user = $this->_users->getAll(array('lastname' => $data['lastname']))->current();
                                    if (!$user) {
                                        //throw new Exception('Nie znaleziono technika ' . print_r($data, 1));
                                        $user = $this->_users->createRow($data);
                                        $user->role = 'technician';
                                        $user->statusid = $userStatus->id;
                                        //$user->save();
                                    }
                                    if ($user && $user->id) {
                                        $service->statusid = $serviceStatusAssigned->id;
                                        $service->technicianid = $user->id;
                                    }
                                }
                            }//var_dump($data,$service->number,$user->toArray());continue;if($service->number=='34365812')exit;
                            if ($data = $service->getClient()) {
                                $client = null;
                                $addressId = 1;
                                foreach ($this->_clients->getAll(array('number' => $data['number'])) as $c) {
                                    if ($c->city == $data['city'] && $c->street == $data['street'] && $c->apartmentno == $data['apartmentno'] && $c->streetno == $data['streetno']) {
                                        $client = $c;
                                        break;
                                    }
                                    $addressId++;
                                }
                                $data['addressid'] = $addressId;
                                if (!$client) {
                                    $client = $this->_clients->createRow($data);
                                    $client->save();
                                }
                                $service->clientid = $client->id;
                            }
                            if ($typeid == $types->find('service', 'acronym')->id) {
                                if ($data = $service->getCalendar()) {
                                    $calendar = $dictionary->find('calendar', 'acronym')->getChildren()->find(strtoupper($data), 'acronym');
                                    if (!$calendar) {
                                        $calendar = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('calendar', 'acronym')->id, 'acronym' => $data));
                                        $calendar->save();
                                    }
                                    $service->calendarid = $calendar->id;
                                }
                                if (($data = $service->getFinished()) == 'Y') {
                                    $service->statusid = $serviceStatusFinished->id;
                                }
                                if (($data = $service->getClosed()) == 'Y') {
                                    $service->statusid = $serviceStatusClosed->id;
                                }
                            } else if ($typeid == $types->find('installation', 'acronym')->id) {
                                if ($data = $service->getCalendar()) {
                                    $calendar = $dictionary->find('calendar', 'acronym')->getChildren()->find(strtoupper($data), 'acronym');
                                    if (!$calendar) {
                                        $calendar = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('calendar', 'acronym')->id, 'acronym' => $data));
                                        $calendar->save();
                                    }
                                    $service->calendarid = $calendar->id;
                                }
                                if (($data = $service->getFinished()) == 'Y') {
                                    $service->statusid = $serviceStatusFinished->id;
                                }
                                if (($data = $service->getClosed()) == 'Y') {
                                    $service->statusid = $serviceStatusClosed->id;
                                }
                            }

                            if (!$service->typeid || $service->typeid == 0) {
                                $service->typeid = $values['typeid'];
                            }
                            $service->performed = 0;
                            //var_dump($service->toArray());exit;
                            if ($service->id) 
                                $service->update();
                            else 
                                $service->save();
                            if ($data = $service->getProductsreleased()) {
                                    /*foreach ($data as $product) {
                                        if (!$product) continue;
                                        foreach ($units as $unit) {
                                            if (preg_match("/([A-Z0-9]{12,}\s)?(.*)(\s([0-9]){1,}\s?{$unit['acronym']})/", $product, $matches)) {
                                                $params = array();
                                                if (!$matches[3] || !$matches[2]) {
                                                    throw new Exception('Wystąpił problem z parsowaniem produktów');
                                                }
                                                $this->_products->clearWhere();
                                                if ($matches[1]) {
                                                    //$params['serial'] = trim($matches[1], ' HdWr-');
                                                    $params['serial'] = preg_replace("/^((?=^)(\s*))|((\s*)(?>$))/si", "", trim($matches[1], '-'));
                                                    $product = $this->_products->getAll(array('serial' => $params['serial']))->current();
                                                } else if ($matches[2]) {
                                                    //$params['name'] = trim($matches[2], ' HdWr-');
                                                    $params['name'] = preg_replace("/^((?=^)(\s*))|((\s*)(?>$))/si", "", trim($matches[2], '-'));
                                                    $product = $this->_products->getAll(array('name' => $params['name']))->current();
                                                }
                                                if (!$product) {
                                                    $product = $this->_products->createRow($params);
                                                    $product->unitid = $unit->id;
                                                }//var_dump($params,$product->toArray());//exit;
                                                $product->statusid = $productStatusReleased->id;
                                                $product->qtyreleased += trim($matches[4]);
                                                $product->quantity += trim($matches[4]);
                                                $product->save();
                                                $orderline = $this->_orderlines->createRow();
                                                $orderline->setFromArray(array('orderid' => $order->id,
                                                    'productid' => $product->id,
                                                    'quantity' => trim($matches[4]),
                                                    'statusid' => $status->id));
                                                $orderline->releasedate = date('Y-m-d H:i:s');
                                                $orderline->technicianid = $service->technicianid;
                                                $orderline->serviceid = $service->id;
                                                $orderline->save();
                                                $serviceProduct = $this->_serviceproducts->createRow();
                                                $serviceProduct->serviceid = $service->id;
                                                $serviceProduct->productid = $product->id;
                                                $serviceProduct->save();
                                                //var_dump($product->toArray(),$orderline->toArray());exit;
                                                //continue;
                                            }
                                        }
                                    }*/
                                    $serviceProduct = $this->_serviceproducts->createRow();
                                    $serviceProduct->serviceid = $service->id;
                                    $serviceProduct->productid = -1;
                                    $serviceProduct->productname = $data;
                                    $serviceProduct->save();
                                }
                            if ($typeid == $types->find('service', 'acronym')->id) {
                                //if ($data = array_filter(explode(',', $service->getProductsreleased()))) {
                                
                                if ($data = array_filter(explode(',', $service->getSolutioncode()))) {
                                    foreach ($data as $code) {
                                        if (!$code) continue;
                                        $solutioncode = $dictionary->find('solutioncode', 'acronym')->getChildren()->find(strtoupper($code), 'acronym');
                                        if (!$solutioncode) {
                                            $solutioncode = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('solutioncode', 'acronym')->id,
                                                'acronym' => $code));
                                            $solutioncode->save();
                                        }
                                        list($errcode) = explode('-', $code);
                                        if (!($errorcode = $dictionary->find('errorcode', 'acronym')->getChildren()->find(strtoupper($errcode), 'acronym'))) {
                                            $errorcode = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('errorcode', 'acronym')->id,
                                                'acronym' => $errcode));
                                            $errorcode->save();
                                        }
                                        $errorAttributeId = $dictionary->find('errorcode', 'acronym')->id;
                                        $attribute = $this->_servicecodes->createRow();
                                        $attribute->serviceid = $service->id;
                                        $attribute->attributeid = $errorAttributeId;
                                        $attribute->codeid = $errorcode->id;
                                        $attribute->save();
                                        $attributeId = $dictionary->find('solutioncode', 'acronym')->id;
                                        $attribute = $this->_servicecodes->createRow();
                                        $attribute->serviceid = $service->id;
                                        $attribute->attributeid = $attributeId;
                                        $attribute->codeid = $solutioncode->id;
                                        $attribute->save();
                                        //var_dump($attribute->toArray());//exit;
                                    }
                                    $service->performed = 1;
                                }//if($service->number=='34367128')exit;
                                if ($data = array_filter(explode(',', $service->getCancellationcode()))) {
                                    foreach ($data as $code) {
                                        if (!$code) continue;
                                        $cancellationcode = $dictionary->find('cancellationcode', 'acronym')->getChildren()->find(strtoupper($code), 'acronym');
                                        if (!$cancellationcode) {
                                            $cancellationcode = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('cancellationcode', 'acronym')->id,
                                                'acronym' => $code));
                                            $cancellationcode->save();
                                        }
                                        $attributeId = $dictionary->find('cancellationcode', 'acronym')->id;
                                        $attribute = $this->_servicecodes->createRow();
                                        $attribute->serviceid = $service->id;
                                        $attribute->attributeid = $attributeId;
                                        $attribute->codeid = $cancellationcode->id;
                                        $attribute->save();
                                    }
                                    $service->performed = 0;
                                }
                                if ($data = array_filter(explode(',', $service->getModeminterchangecode()))) {
                                    foreach ($data as $code) {
                                        if (!$code) continue;
                                        $modeminterchangecode = $dictionary->find('modeminterchangecode', 'acronym')->getChildren()->find(strtoupper($code), 'acronym');
                                        if (!$modeminterchangecode) {
                                            $modeminterchangecode = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('modeminterchangecode', 'acronym')->id,
                                                'acronym' => $code));
                                            $modeminterchangecode->save();
                                        }
                                        $attributeId = $dictionary->find('modeminterchangecode', 'acronym')->id;
                                        $attribute = $this->_servicecodes->createRow();
                                        $attribute->serviceid = $service->id;
                                        $attribute->attributeid = $attributeId;
                                        $attribute->codeid = $modeminterchangecode->id;
                                        $attribute->save();
                                    }
                                    //$service->performed = 1;
                                }
                                if ($data = array_filter(explode(',', $service->getDecoderinterchangecode()))) {
                                    foreach ($data as $code) {
                                        if (!$code) continue;
                                        $decoderinterchangecode = $dictionary->find('decoderinterchangecode', 'acronym')->getChildren()->find(strtoupper($code), 'acronym');
                                        if (!$decoderinterchangecode) {
                                            $decoderinterchangecode = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('decoderinterchangecode', 'acronym')->id,
                                                'acronym' => $code));
                                            $decoderinterchangecode->save();
                                        }
                                        $attributeId = $dictionary->find('decoderinterchangecode', 'acronym')->id;
                                        $attribute = $this->_servicecodes->createRow();
                                        $attribute->serviceid = $service->id;
                                        $attribute->attributeid = $attributeId;
                                        $attribute->codeid = $decoderinterchangecode->id;
                                        $attribute->save();
                                    }
                                    //$service->performed = 1;
                                }
                            } else if ($typeid == $types->find('installation', 'acronym')->id) {
                                if ($data = array_filter(explode(',', $service->getInstallationcode()))) {
                                    foreach ($data as $code) {
                                        if (!$code) continue;
                                        $installationcode = $dictionary->find('installationcode', 'acronym')->getChildren()->find(strtoupper($code), 'acronym');
                                        if (!$installationcode) {
                                            $installationcode = $this->_dictionaries->createRow(array('parentid' => $dictionary->find('installationcode', 'acronym')->id,
                                                'acronym' => $code));
                                            $installationcode->save();
                                        }
                                        $attributeId = $dictionary->find('installationcode', 'acronym')->id;
                                        $attribute = $this->_servicecodes->createRow();
                                        $attribute->serviceid = $service->id;
                                        $attribute->attributeid = $attributeId;
                                        $attribute->codeid = $installationcode->id;//var_dump($attribute->toArray());exit;
                                        $attribute->save();
                                    }
                                    $service->performed = 1;
                                }
                            }
                            
                            if ($service->isModified()) {
                                //var_dump($service);exit;
                                if ($service->id) 
                                    $service->update();
                                else 
                                    $service->save();
                            }

                            //array_unshift($line, 'OK');
                            $line[] = 'OK';
                            Zend_Db_Table::getDefaultAdapter()->commit();
                        } catch (Application_Model_ServicesRowExist $e) {
                            $line[] = 'OK';
                            //var_dump($e->getMessage(),$e->getTraceAsString());exit;
                            Zend_Db_Table::getDefaultAdapter()->commit();
                        } catch (Exception $e) {
                            //var_dump($e->getMessage(),$e->getTraceAsString());exit;
                            //exit;
                            //array_unshift($line, $e->getMessage());
                            $line[] = $e->getMessage();
                            Zend_Db_Table::getDefaultAdapter()->rollBack();
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
                } catch (Zend_File_Transfer_Exception $e) {
                    echo $e->message();
                }
            }
        }
    }

}
