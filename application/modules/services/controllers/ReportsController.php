<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Services_ReportsController extends Application_Controller_Abstract {

    protected $_servicereports;
    protected $_services;

    public function init() {
        $this->_servicereports = new Application_Model_Services_Reports_Table();
        $this->_servicereports->setItemCountPerPage($this->_hasParam('count') ? $this->_getParam('count') : Application_Db_Table::ITEMS_PER_PAGE);
        $this->_servicereports->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : 'id DESC');
        $this->_users = new Application_Model_Users_Table();
        $this->_services = new Application_Model_Services_Table();
        $this->_services->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : array('planneddate', new Zend_Db_Expr('clientstreet COLLATE utf8_polish_ci'), 'clientstreetno', 'clientapartment'));

        parent::init();

        $context = $this->_helper->getHelper('xlsContext');
        $context->addActionContext('index', 'html')
                ->addActionContext('list', array('html', 'json'))
                ->addActionContext('generate', array('html', 'xls'))
                ->addActionContext('send', 'html')
                ->addActionContext('codes-list', array('html', 'xls'))
                ->setSuffix('html', '')
                ->initContext();

        if ($context->getCurrentContext() == 'xls') {
            $this->_services->setItemCountPerPage(null);
        }
    }

    public function indexAction() {
        $this->_forward('list');
    }

    public function listAction() {
        // action body

        $request = $this->getRequest();
        $warehouseid = $request->getParam('warehouseid');
        $typeid = $request->getParam('typeid');
        if ($warehouseid) {
            $warehouse = $this->_warehouses->get($warehouseid);
            $this->view->warehouse = $warehouse;
        }
        $pageNumber = $request->getParam('page');
        if ($pageNumber) {
            $this->_servicereports->setPageNumber($pageNumber);
        }
        $orderBy = $request->getParam('orderBy');
        /*$columns = array('number', 'servicetype', 'client', 'technician', 'planneddate', 'status');
        if ($orderBy) {
            $orderBy = explode(" ", $orderBy);
            $this->_servicereports->setOrderBy("{$columns[$orderBy[0]]} {$orderBy[1]}");
        }
        $orderBy = explode(" ", $this->_servicereports->getOrderBy()); //var_dump($orderBy);
        foreach ($columns as $ix => $columnName) {
            if ($columnName != $orderBy[0]) {
                continue;
            }
            $orderBy = "$ix {$orderBy[1]}";
        }*/
        $this->view->request['orderBy'] = $orderBy;
        //$this->_servicereports->setLazyLoading(false);$this->view->filepath = '/../data/temp/';
        $types = $this->_dictionaries->getTypeList('service');
        $statuses = $this->_dictionaries->getStatusList('reports');
        $this->_servicereports->setWhere("statusid != '{$statuses->find('deleted', 'acronym')->id}'");

        $this->view->reports = $this->_servicereports->getAll($request->getParams());
        $this->view->paginator = $this->_servicereports->getPaginator();
        $this->view->statuses = $this->_dictionaries->getStatusList('service');
    }

    public function generateAction() {
        $request = $this->getRequest();
        $this->view->typeid = $typeid = $request->getParam('typeid');
        $warehouseid = $request->getParam('warehouseid');
        $types = $this->_dictionaries->getTypeList('service');
        $form = new Application_Form_Services_Reports_Generate();
        $defaults = array('warehouseid' => $warehouseid, 'typeid' => $typeid, 'format' => 'xls');
        $form->setDefaults($defaults);
        $types = $this->_dictionaries->getTypeList('service');
        $this->view->filepath = '/../data/pliki/';
        $this->view->filepath = '/../data/pliki/export/';
        switch ($this->_getParam('typeid')) {
            case $types->find('installation', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_XLS_Installation');
                //$this->view->filename = date('YmdH') . '_zestawienie_instalacyjne-' . date('YmdHis') . '.xlsx';
                $this->view->filename = '_NPLAY_zestawienie_instalacyjne.xlsx';
                $this->view->template = $_SERVER['DOCUMENT_ROOT'] . '/../data/pliki/zestawienie instalacyjne puste.xls';
                $this->view->rowNo = 2;
                $this->view->codeTypes = array('installation', 'installationcancel');
                break;
            case $types->find('service', 'acronym')->id:
                $this->_services->setRowClass('Application_Model_Services_XLS_Service');
                //$this->view->filename = date('YmdH') . '_zestawienie_serwisowe-' . date('YmdHis') . '.xlsx';
                $this->view->filename = '_NPLAY_zestawienie_serwisowe.xlsx';
                $this->view->template = $_SERVER['DOCUMENT_ROOT'] . '/../data/pliki/zestawienie serwisowe puste.xls';
                $this->view->rowNo = 3;
                $this->view->codeTypes = array('error', 'solution', 'cancellation', 'modeminterchange', 'decoderinterchange');
                $this->_services->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : array('planneddate', 'timefrom', 'clientstreet', 'clientstreetno', 'clientapartment'));
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
        $types = $this->_dictionaries->getTypeList('service');
        $this->view->dictionary = $dictionary = $this->_dictionaries->getDictionaryList('service');

        $this->view->form = $form;
        $this->view->types = $types;
        $this->view->serviceCode = $this->_config->get(APPLICATION_ENV)->reports->servicecode;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                $this->_services->setLazyLoading(false);
                $this->view->services = $this->_services->getAll($request->getParams());
                if (!$this->view->services->count()) {
                    $form->setDescription('Brak zgłoszeń dla podanej daty');
                    return;
                }
                $this->view->filename = date('Ymd', strtotime($values['planneddate'])) . $this->view->filename;
                $statuses = $this->_dictionaries->getStatusList('reports');
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                $planneddate = $values['planneddate'];
                $this->_servicereports->update(array('statusid' => $statuses->find('deleted', 'acronym')->id), "planneddate = '$planneddate'");
                $status = $statuses->find('new', 'acronym');
                $report = $this->_servicereports->createRow($values);
                $report->userid = $this->view->auth->id;
                $report->dateadd = date('Y-m-d H:i:s');
                $report->dateadd = null;
                $report->statusid = $status->id;
                $report->file = $this->view->filepath . $this->view->filename;
                $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
                $view = $viewRenderer->view;
                $script = 'services/report.xls.phtml';
                $vars = $view->getVars();
                //$view->render($script);
                require_once $view->getScriptPath($script);
                $report->save();
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Wygenerowano raport';
            }
        }
    }

    public function sendAction() {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $typeid = $request->getParam('typeid');
        $report = $this->_servicereports->get($id);
        if (!$report) {
            throw new Exception('Nie znaleziono raportu');
        }
        $types = $this->_dictionaries->getTypeList('service');
        $this->view->filepath = $_SERVER['DOCUMENT_ROOT'] . '/../data/pliki/';
        //$defaults = array('recipient' => 'koordynatorzy.lublin@nplay.pl;Lublin.Dispatchers@upc.com.pl');
        $defaults = array('recipient' => $this->_config->get(APPLICATION_ENV)->reports->mail->recipients);
        switch ($this->_getParam('typeid')) {
            case $types->find('installation', 'acronym')->id:
                $defaults['subject'] = basename($report->file);//'Zestawienie instalacyjne za dzień ' . $report->planneddate;
                break;
            case $types->find('service', 'acronym')->id:
                $defaults['subject'] = basename($report->file);//'Zestawienie serwisowe za dzień ' . $report->planneddate;
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
        $form = new Application_Form_Services_Reports_Send();
        $form->setDefaults($defaults);

        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                $statuses = $this->_dictionaries->getStatusList('reports');
                $status = $statuses->find('sent', 'acronym');
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
                $transport = $bootstrap->getResource('mail');
                $mail = new Zend_Mail('UTF-8');
                $mail->setDefaultTransport($transport);
                foreach(explode(';',$values['recipient']) as $recipient)
                    $mail->addTo(trim($recipient));
                if ($this->_config->get(APPLICATION_ENV)->reports->mail->bcc)
                    $mail->addBcc($this->_config->get(APPLICATION_ENV)->reports->mail->bcc);
                //$mail->setFrom($this->_auth->getIdentity()->email, $this->_auth->getIdentity()->lastname . ' ' . $this->_auth->getIdentity()->firstname);
                $mail->setFrom($this->_config->get(APPLICATION_ENV)->reports->mail->from);
                $mail->setSubject(basename($report->file));
                $mail->setBodyHtml(nl2br($values['content']));
                $at = $mail->createAttachment(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $report->file));
                $at->type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                $at->disposition = Zend_Mime::DISPOSITION_INLINE;
                $at->encoding = Zend_Mime::ENCODING_BASE64;
                $at->filename = basename($report->file);//var_dump($mail);exit;
                $mail->send();
                $report->statusid = $status->id;
                $report->senddate = date('Y-m-d H:i:s');
                $report->save();
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Wysłano raport';
            }
        }
    }

    public function codesListAction() {
        // action body

        $request = $this->getRequest();
        $warehouseid = $request->getParam('warehouseid');
        if (!$typeid = $request->getParam('typeid')) {
            throw new Exception("Brak typu zleceń");
        }
        $this->view->typeid = $typeid;
        $types = $this->_dictionaries->getTypeList('service');
        switch ($typeid) {
            // zlecenie instalacyjne
            case $types->find('installation', 'acronym')->id:
                $codeTypes = array('installation', 'installationcancel');
                break;
            // zlecenie serwisowe
            case $types->find('service', 'acronym')->id:
                $codeTypes = array('error', 'solution', 'cancellation', 'modeminterchange', 'decoderinterchange');
                break;
            default:
                throw new Exception('Nieprawidłowy typ zlecenia');
        }
        if (!$type = $request->getParam('type')) {
        //    throw new Exception("Brak typu raportu");
        }
        $this->view->type = $type;
        $request->setParam('attributeacronym', "{$type}code");
        if ($warehouseid) {
            $warehouse = $this->_warehouses->get($warehouseid);
            $this->view->warehouse = $warehouse;
        }
        $pageNumber = $request->getParam('page');
        if ($pageNumber) {
            $this->_servicereports->setPageNumber($pageNumber);
        }
        $orderBy = $request->getParam('orderBy');
        /*$columns = array('number', 'servicetype', 'client', 'technician', 'planneddate', 'status');
        if ($orderBy) {
            $orderBy = explode(" ", $orderBy);
            $this->_servicereports->setOrderBy("{$columns[$orderBy[0]]} {$orderBy[1]}");
        }
        $orderBy = explode(" ", $this->_servicereports->getOrderBy()); //var_dump($orderBy);
        foreach ($columns as $ix => $columnName) {
            if ($columnName != $orderBy[0]) {
                continue;
            }
            $orderBy = "$ix {$orderBy[1]}";
        }*/
        $this->view->request['orderBy'] = $orderBy;
        //$this->_servicereports->setLazyLoading(false);$this->view->filepath = '/../data/temp/';
        $types = $this->_dictionaries->getTypeList('service');
        $statuses = $this->_dictionaries->getStatusList('reports');
        //$this->view->paginator = $model->getPaginator();
        $status = $this->_dictionaries->getStatusList('users')->find('active', 'acronym');
        $params['statusid'] = $status->id;
        $params['role'] = 'technician';
        if ($this->_auth->getIdentity()->role == 'technician') {
            $params['id'] = $this->_auth->getIdentity()->id;
        }
        $this->_users->setOrderBy(array('lastname','firstname'));
        $technicians = $this->_users->getAll($params);
        $this->view->technicians = $technicians;
        $dictionary = $this->_dictionaries->getDictionaryList('service');
        $statusDeleted = $this->_dictionaries->getStatusList('dictionaries', 'deleted')->current();
        $codes = array(); 
        foreach ($codeTypes as $codeType) {
            if ($type && $type != $codeType) {
                continue;
            }
            foreach ($dictionary->find("{$codeType}code", 'acronym')->getChildren() as $row) {
                if ($row['statusid'] == $statusDeleted->id) {
                    continue;
                }
                if ($row['datefrom'] && strtotime($row['datefrom']) < time()) {
                    //continue;
                }
                if ($row['datetill'] && strtotime($row['datetill']) < time()) {
                    //continue;
                }
                $codes[] = $row;
            }
        }
        $this->view->codes = $codes;
        $this->view->types = $types;
        $this->view->statuses = $this->_dictionaries->getStatusList('service');
        $params = array_filter(array_intersect_key($request->getParams(), array_flip(array('technicianid', 'codeacronym', 'planneddatefrom', 'planneddatetill'))));
        if (empty($params)) {
            $planneddatefrom = date('Y-m-01');
            $planneddatetill = date('Y-m-d');
            $this->_services->setWhere($this->_services->getAdapter()->quoteInto("planneddatefrom >= ?", $planneddatefrom));
            $this->_services->setWhere($this->_services->getAdapter()->quoteInto("planneddatetill <= ?", $planneddatetill));
            $request->setParam('planneddatefrom', $planneddatefrom);
            $request->setParam('planneddatetill', $planneddatetill);
        }
        $this->view->request = $request->getParams();
        $this->view->filepath = '/../data/temp/';
        switch ($this->_getParam('typeid')) {
            case $types->find('installation', 'acronym')->id:
                $this->view->filename = 'Raport_kodow_instalacyjnych-' . date('YmdHis') . '.xls';
                break;
            case $types->find('service', 'acronym')->id:
                $this->view->filename = 'Raport_kodow_serwisowych-' . date('YmdHis') . '.xls';
                break;
        }
        $this->view->rowNo = 1;
        if ($this->_auth->getIdentity()->role == 'technician') {
            $request->setParam('technicianid', $this->_auth->getIdentity()->id);
        }
        
        if (!$this->getRequest()->isPost()) {
            return;
        }
        
        $model = new Application_Model_Services_Statistics_Servicecodes_Table();
        $model->setLazyLoading(false);
        $model->setItemCountPerPage(null);
        $model->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : 'technicianid');

        $reports = array();
        foreach ($model->getAll($request->getParams()) as $stats) {
            $reports[$stats['technicianid']][$stats['codeacronym']] = $stats['quantity'];
        }
        $this->view->reports = $reports;
    }

}
