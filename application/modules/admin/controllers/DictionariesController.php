<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// application/controllers/IndexController.php

class Admin_DictionariesController extends Application_Controller_Abstract {

    protected $_dictionaries;

    public function init() {
        /* Initialize action controller here */
        parent::init();

        $this->_dictionaries->setItemCountPerPage($this->_hasParam('count') ? $this->_getParam('count') : Application_Db_Table::ITEMS_PER_PAGE);
        $this->_dictionaries->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : 'acronym');

        $ajaxContext = $this->_helper->getHelper('AjaxContext');

        $ajaxContext->addActionContext('index', 'html')
                ->addActionContext('edit', 'html')
                ->addActionContext('import', 'html')
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
            $this->_dictionaries->setPageNumber($pageNumber);
        }
        $orderBy = $request->getParam('orderBy');
        if (@in_array($this->parent->id, array($this->errorCode,$this->cancellationCode,$this->decoderinterchangeCode,$this->installationcancelCode,$this->installationCode,$this->modeminterchangeCode))):
            $columns = array('acronym', 'name', 'price', 'datefrom', 'dateto');
        elseif (@in_array($this->parent->id, array($this->solutionCode))):;
            $columns = array('acronym', 'name', 'errorcodename');
        else:
            $columns = array('acronym', 'name');
        endif;
        if ($orderBy) {
            $orderBy = explode(" ", $orderBy);
            $this->_dictionaries->setOrderBy("{$columns[$orderBy[0]]} {$orderBy[1]}");
        }
        $orderBy = explode(" ", $this->_dictionaries->getOrderBy());
        foreach ($columns as $ix => $columnName) {
            if ($columnName != $orderBy[0]) {
                continue;
            }
            $orderBy = "$ix {$orderBy[1]}";
        }
        $request->setParam('orderBy', $orderBy);
        $request->setParam('count', $this->_dictionaries->getItemCountPerPage());
        $status = $this->_dictionaries->getStatusList('dictionaries')->find('deleted', 'acronym');
        $this->_dictionaries->setWhere("statusid != {$status->id}");
        $errorCode = $this->_dictionaries->getDictionaryList('service')->find('errorcode', 'acronym');
        $solutionCode = $this->_dictionaries->getDictionaryList('service')->find('solutioncode', 'acronym');
        $cancellationCode = $this->_dictionaries->getDictionaryList('service')->find('cancellationcode', 'acronym');
        $decoderinterchangeCode = $this->_dictionaries->getDictionaryList('service')->find('decoderinterchangecode', 'acronym');
        $installationcancelCode = $this->_dictionaries->getDictionaryList('service')->find('installationcancelcode', 'acronym');
        $installationCode = $this->_dictionaries->getDictionaryList('service')->find('installationcode', 'acronym');
        $modeminterchangeCode = $this->_dictionaries->getDictionaryList('service')->find('modeminterchangecode', 'acronym');
        $this->view->errorCode = $errorCode->id;
        $this->view->solutionCode = $solutionCode->id;
        $this->view->cancellationCode = $cancellationCode->id;
        $this->view->decoderinterchangeCode = $decoderinterchangeCode->id;
        $this->view->installationcancelCode = $installationcancelCode->id;
        $this->view->installationCode = $installationCode->id;
        $this->view->modeminterchangeCode = $modeminterchangeCode->id;
        $this->_dictionaries->setWhere("system != '1'");
        if (!$parentid = $request->getParam('parentid')) {
            if ($this->_auth->getIdentity()->role != 'admin') {
                $parentid = $this->_dictionaries->getDictionaryList()->find('service', 'acronym')->id;
                $this->_dictionaries->setWhere("acronym NOT IN ('area','blockadecode','calendar','laborcode','complaintcode','region','system','type')");
            } else {
                $parentid = 1;
            }
        }

        if ($this->_auth->getIdentity()->role != 'admin') {
            $parents = $this->_dictionaries->getDictionaryList()->find('service', 'acronym')->getChildren();
        } else {
            $parents = $this->_dictionaries->getDictionaryList();
        }
        $this->_dictionaries->setCacheInClass(false);
        $parent = $this->_dictionaries->get($parentid);
        $this->view->parent = $parent;
        $this->_dictionaries->setLazyLoading(false);
        $this->view->dictionaries = $this->_dictionaries->getAll(array('parentid' => $parentid));
        $this->view->paginator = $this->_dictionaries->getPaginator();
        $this->view->request = $request->getParams();
        $this->view->parents = $parents;
    }

    public function deleteAction() {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $this->_dictionaries->setLazyLoading(true);
        $dictionary = $this->_dictionaries->find($id)->current();
        if (!$dictionary) {
            throw new Exception('Nie znaleziono pozycji słownika');
        }
        $form = new Application_Form_Dictionaries_Delete();
        $form->setDefaults($dictionary->toArray());
        $this->view->form = $form;
        $this->view->dictionary = $dictionary;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                $status = $this->_dictionaries->getStatusList('dictionaries')->find('deleted', 'acronym');
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                if ($solutionCodes = $dictionary->getSolutioncodes()) {
                    foreach ($solutionCodes as $code) {
                        //$code->delete();
                    }
                }
                //$dictionary->current()->delete();
                $dictionary->statusid = $status->id;
                $dictionary->save();
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Wpis usunięty';
            }
        }
    }

    public function editAction() {
        // action body

        $request = $this->getRequest();
        $id = $request->getParam('id');
        $form = new Application_Form_Dictionaries();
        $this->_dictionaries->setCacheInClass(false);
        $this->_dictionaries->setLazyLoading(false);
        $this->_dictionaries->clearCache();
        if (!$parentid = $request->getParam('parentid')) {
            if ($this->_auth->getIdentity()->role != 'admin') {
                $parentid = $this->_dictionaries->getDictionaryList()->find('service', 'acronym')->id;
            } else {
                $parentid = 1;
            }
        }

        $parent = $this->_dictionaries->get($parentid);
        if ($id) {
            $dictionary = $this->_dictionaries->get($id);
            $form->setDefaults($dictionary->toArray());
            //$parents = $this->_dictionaries->find($dictionary->parentid);
            $errorCode = $dictionary->getErrorcode();
            if ($errorCode->value) {
                $form->setDefault('errorcodeid', $errorCode->value);
            }
            $solutionCodes = $dictionary->getSolutioncodes();
            if ($solutionCodes) {
                $form->setDefault('solutioncodeid', $solutionCodes->toArray()); //var_dump($solutionCodes->toArray());
            }
            $price = $dictionary->getPrice();
            if ($price->value) {
                $form->setDefault('price', $price->value);
            }
            $dateFrom = $dictionary->getDatefrom();
            if ($dateFrom->value) {
                $form->setDefault('datefrom', $dateFrom->value);
            } else {
                //$form->setDefault('datefrom', date('Y-m-d'));
            }
            $dateTill = $dictionary->getDatetill();
            if ($dateTill->value) {
                $form->setDefault('datetill', $dateTill->value);
            } else {
                //$form->setDefault('datetill', date('Y-m-d'));
            }
        } else {
            $form->setDefaults(array('parentid' => $parent->id));
        }
        $parents = $this->_dictionaries->getDictionaryList();
        $error = $this->_dictionaries->getDictionaryList('service')->find('errorcode', 'acronym');
        $solution = $this->_dictionaries->getDictionaryList('service')->find('solutioncode', 'acronym');
        $cancellation = $this->_dictionaries->getDictionaryList('service')->find('cancellationcode', 'acronym');
        $decoderinterchange = $this->_dictionaries->getDictionaryList('service')->find('decoderinterchangecode', 'acronym');
        $installationcancel = $this->_dictionaries->getDictionaryList('service')->find('installationcancelcode', 'acronym');
        $installation = $this->_dictionaries->getDictionaryList('service')->find('installationcode', 'acronym');
        $modeminterchange = $this->_dictionaries->getDictionaryList('service')->find('modeminterchangecode', 'acronym');
        $this->view->errorCode = $error->id;
        $this->view->solutionCode = $solution->id;
        $this->view->cancellationCode = $cancellation->id;
        $this->view->decoderinterchangeCode = $decoderinterchange->id;
        $this->view->installationcancelCode = $installationcancel->id;
        $this->view->installationCode = $installation->id;
        $this->view->modeminterchangeCode = $modeminterchange->id;
        $this->view->parent = $parent;
        $form->setOptions(array('parents' => $parents, 'errorcodes' => $error->getChildren(), 'solutioncodes' => $solution->getChildren()));

        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                if ($id && !$this->_hasParam('copy')) {
                    $dictionary->setFromArray($values);
                } else {
                    $dictionary = $this->_dictionaries->createRow($values);
                    $dictionary->id = null;
                }
                $this->_dictionaries->setLazyLoading(true);
                if ($dictionary->parentid == $this->_dictionaries->getDictionaryList('service')->find('solutioncode', 'acronym')->id) {
                    $error = $this->_dictionaries->get($values['errorcodeid']);
                    if (strpos($dictionary->acronym, $error->acronym) === false) {
                        $dictionary->acronym = $error->acronym . '-' . $values['acronym'];
                    }
                }
                $dictionary->save();
                $table = new Application_Model_Dictionaries_Attributes_Table();
                /* $attributes->createRow();
                  $attributes->price = number_format(str_replace(',', '.', $attributes->price), 2);
                  $attributes->entryid = $dictionary->id; //var_dump($dictionary->toArray(),$attributes->toArray());//exit;
                  $attributes->save(); */
                $attributes = $this->_dictionaries->getAttributeList();
                if (!empty($errorCode)) {
                    if (!$attribute = $table->find($errorCode->id)->current()) {
                        $attribute = $table->createRow($errorCode->toArray());
                    }
                }
                if ($values['errorcodeid']) {
                    if (!$attribute) {
                        $attribute = $table->createRow();
                        $attribute->attributeid = $attributes->find('errorcodeid', 'acronym')->id;
                    }
                    $attribute->entryid = $dictionary->id;
                    $attribute->value = $values['errorcodeid'];
                    $attribute->save();
                }
                if ($values['solutioncodeid']) {
                    foreach ($solutionCodes as $solutionCode) {
                        if (!in_array($solutionCode->id, $values['solutioncodeid']) && !$this->_hasParam('copy')) {
                            $solutionCode->delete();
                        }
                    }
                    foreach ($values['solutioncodeid'] as $solutionCodeId) {
                        $attribute = $solutionCodes->filter(array('attributeid' => $attributes->find('errorcodeid', 'acronym')->id, 'value' => $dictionary->id, 'entryid' => $solutionCodeId))->current();
                        if ($attribute && !$this->_hasParam('copy')) {
                            $params = $attribute->toArray();
                            $attribute = $table->get($params['id']);
                        } else {
                            $attribute = $table->createRow();
                            $attribute->attributeid = $attributes->find('errorcodeid', 'acronym')->id;
                        }
                        $attribute->entryid = $solutionCodeId;
                        $attribute->value = $dictionary->id;
                        $attribute->save();
                    }
                }
                unset($attribute);
                if (!empty($price)) {
                    if (!$attribute = $table->find($price->id)->current()) {
                        $attribute = $table->createRow($price->toArray());
                        $attribute->attributeid = $attributes->find('price', 'acronym')->id;
                    }
                }
                if ($values['price']) {
                    if (!$attribute) {
                        $attribute = $table->createRow();
                        $attribute->attributeid = $attributes->find('price', 'acronym')->id;
                    }
                    $attribute->entryid = $dictionary->id;
                    $attribute->value = number_format(str_replace(',', '.', $values['price']), 2);
                    $attribute->save();
                }
                unset($attribute);
                if (!empty($dateFrom)) {
                    if (!$attribute = $table->find($dateFrom->id)->current()) {
                        $attribute = $table->createRow($dateFrom->toArray());
                    }
                }
                if ($values['datefrom']) {
                    if (!$attribute) {
                        $attribute = $table->createRow();
                        $attribute->attributeid = $attributes->find('datefrom', 'acronym')->id;
                    }
                    $attribute->entryid = $dictionary->id;
                    $attribute->value = !empty($values['datefrom']) ? date('Y-m-d', strtotime($values['datefrom'])) : '';
                    $attribute->save();
                }
                unset($attribute);
                if (!empty($dateTill)) {
                    if (!$attribute = $table->find($dateTill->id)->current()) {
                        $attribute = $table->createRow($dateTill->toArray());
                    }
                }
                if ($values['datetill']) {
                    if (!$attribute) {
                        $attribute = $table->createRow();
                        $attribute->attributeid = $attributes->find('datetill', 'acronym')->id;
                    }
                    $attribute->entryid = $dictionary->id;
                    $attribute->value = !empty($values['datetill']) ? date('Y-m-d', strtotime($values['datetill'])) : '';
                    $attribute->save();
                }
                $this->view->success = 'Słownik zapisany';
                Zend_Db_Table::getDefaultAdapter()->commit();
            }
        }
    }

    public function importAction() {
        // action body

        $request = $this->getRequest();
        $typeid = $request->getParam('parentid');
        $types = $this->_dictionaries->getDictionaryList('service');
        $form = new Application_Form_Dictionaries_Import();
        //$this->_dictionaries->setItemCountPerPage(1000);
        //$parents = $this->_dictionaries->getAll();
        $this->_dictionaries->setCacheInClass(false);
        $this->_dictionaries->setLazyLoading(false);
        $this->_dictionaries->clearCache();
        $parents = $this->_dictionaries->getDictionaryList();
        $form->setOptions(array('parents' => $parents));

        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                $upload = new Zend_File_Transfer_Adapter_Http();
                $upload->setDestination(APPLICATION_PATH . "/../data/temp");

                try {
                    $upload->receive();
                    //var_dump($typeid,$types->find('errorcode', 'acronym')->id,$types->find('solutioncode', 'acronym')->id);exit;
                    switch ($typeid) {
                        case $types->find('errorcode', 'acronym')->id:
                            $rowNumber = 2;
                            $this->_dictionaries->setRowClass('Application_Model_Dictionaries_XLS_Error');
                            $sheet = 1;
                            break;
                        case $types->find('solutioncode', 'acronym')->id:
                            $rowNumber = 2;
                            $this->_dictionaries->setRowClass('Application_Model_Dictionaries_XLS_Solution');
                            $sheet = 1;
                            break;
                        case $types->find('modeminterchangecode', 'acronym')->id:
                            $rowNumber = 2;
                            $this->_dictionaries->setRowClass('Application_Model_Dictionaries_XLS_ModemInterchange');
                            $sheet = 1;
                            break;
                        case $types->find('decoderinterchangecode', 'acronym')->id:
                            $rowNumber = 2;
                            $this->_dictionaries->setRowClass('Application_Model_Dictionaries_XLS_DecoderInterchange');
                            $sheet = 1;
                            break;
                        case $types->find('cancellationcode', 'acronym')->id:
                            $rowNumber = 2;
                            $this->_dictionaries->setRowClass('Application_Model_Dictionaries_XLS_Cancellation');
                            $sheet = 1;
                            break;
                        case $types->find('installationcode', 'acronym')->id:
                            $rowNumber = 1;
                            $this->_dictionaries->setRowClass('Application_Model_Dictionaries_XLS_Installation');
                            $sheet = 2;
                            break;
                        case $types->find('installationcancelcode', 'acronym')->id:
                            $rowNumber = 2;
                            $this->_dictionaries->setRowClass('Application_Model_Dictionaries_XLS_Installationcancel');
                            $sheet = 1;
                            break;
                        default:
                            $content = file_get_contents($upload->getFileName('import', true));
                            $data = Zend_Json::decode(iconv('ISO-8859-2', 'UTF-8', $content));
                            $table = new Application_Model_Dictionaries_JSON_Table();
                            Zend_Db_Table::getDefaultAdapter()->beginTransaction(); //echo'<pre>';
                            $table->proccess($data); //exit;
                            Zend_Db_Table::getDefaultAdapter()->commit();
                            $this->view->success = 'Zaimportowano słownik podstawowy';
                            return;
                    }

                    $reader = new Utils_File_Reader_PHPExcel(array('readerType' => 'Excel2007', 'readOnly' => true));
                    $data = $reader->read($upload->getFileName('import', true), $sheet);
                    $rows = $data->getRowIterator($rowNumber);

                    Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                    $this->_dictionaries->cacheInClass(false);
                    $this->_dictionaries->setLazyLoading(true);
                    // relacje
                    if ($typeid == $types->find('solutioncode', 'acronym')->id) {
                        $attributes = array();

                        $table = new Application_Model_Dictionaries_Attributes_Table();
                        $attributes = $this->_dictionaries->getAttributeList();
                        $attributeId = $attributes->find('errorcodeid', 'acronym')->id;
                        $errorCodes = $this->_dictionaries->getDictionaryList('service', 'errorcode');
                        $solutionCodes = $this->_dictionaries->getDictionaryList('service', 'solutioncode');
                        foreach ($rows as $i => $row) {
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(false);
                            $attribute = $table->createRow();
                            $attribute->setFromCellIterator($cellIterator);
                            $errorCode = $attribute->getErrorcode();
                            if (!$errorCode) {
                                continue;
                            }
                            $attribute->attributeid = $attributeId;

                            $relations[$attribute->getSolutioncode()][] = $errorCode;
                        }
                    }

                    foreach ($rows as $i => $row) {
                        $i++;

                        $cellIterator = $row->getCellIterator();

                        // This loops all cells, even if it is not set.
                        // By default, only cells that are set will be iterated.
                        $cellIterator->setIterateOnlyExistingCells(false);
                        $entry = $this->_dictionaries->createRow();
                        $entry->setFromCellIterator($cellIterator);

                        if (!$entry->acronym) {
                            continue;
                        }

                        $line = $entry->toArray();

                        try {
                            if (!empty($relations)) {
                                foreach ($relations[$entry->acronym] as $code) {
                                    //if (!($entry = $this->_dictionaries->getAll(array('parentid' => $entry->parentid, 'acronym' => $entry->acronym))->current())) {
                                    $entry = $this->_dictionaries->createRow();
                                    $entry->setFromCellIterator($cellIterator);
                                    $entry->acronym = $code . '-' . $entry->acronym;
                                    $entry->parentid = $typeid;
                                    $entry->save();
                                    //}
                                }
                            } else {
                                if (!($entry = $this->_dictionaries->getAll(array('parentid' => $entry->parentid, 'acronym' => $entry->acronym))->current())) {
                                    $entry = $this->_dictionaries->createRow();
                                    $entry->setFromCellIterator($cellIterator);
                                    $entry->acronym = $entry->acronym;
                                    $entry->parentid = $typeid;
                                    $entry->save();
                                }
                            }

                            array_unshift($line, 'OK');
                        } catch (Exception $e) {
                            //var_dump($e->getMessage(), $e->getTraceAsString(), $entry->toArray());
                            //exit;
                            array_unshift($line, $e->getMessage());
                        }

                        $lines[] = $line;
                    }

                    if ($typeid == $types->find('solutioncode', 'acronym')->id) {
                        $table = new Application_Model_Dictionaries_Attributes_Table();
                        $attributes = $this->_dictionaries->getAttributeList();
                        $attributeId = $attributes->find('errorcodeid', 'acronym')->id;
                        $errorCodes = $this->_dictionaries->getDictionaryList('service', 'errorcode');
                        $solutionCodes = $this->_dictionaries->getDictionaryList('service', 'solutioncode');
                        foreach ($rows as $i => $row) {
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(false);
                            $attribute = $table->createRow();
                            $attribute->setFromCellIterator($cellIterator);
                            $errorCode = $attribute->getErrorcode();
                            if (!$errorCode) {
                                continue;
                            }
                            $attribute->attributeid = $attributeId;
                            $attribute->value = $errorCodes->find($attribute->getErrorcode(), 'acronym')->id;
                            if (!$solutionCode = $solutionCodes->find($attribute->getErrorcode() . '-' . $attribute->getSolutioncode(), 'acronym')) {
                                //var_dump($attribute->getErrorcode() . '-' . $attribute->getSolutioncode());
                                //continue;
                            }
                            $attribute->entryid = $solutionCode->id;
                            //var_dump($attribute->toArray(),$solutionCode->toArray());

                            try {
                                //if (!$table->getAll(array('attributeid' => $attribute->attributeid, 'entryid' => $attribute->entryid, 'value' => $attribute->value))) {
                                $attribute->save();
                                //}
                            } catch (Exception $e) {
                                array_unshift($line, $e->getMessage());
                            }
                        }
                    }

                    Zend_Db_Table::getDefaultAdapter()->commit();
                    $this->view->data = $lines;
                } catch (Zend_File_Transfer_Exception $e) {
                    echo $e->getMessage();
                }
                //if ($form->isValid($request->getPost())) {
                //    $data = $form->getData();var_dump($data);exit;
                //    $this->view->success = 'Order successfully saved';
                //} 
            }
        }
    }

}
