<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Services_ClientsController extends Application_Controller_Abstract
{
    
    protected $_clients;
        
    public function init()
    {
        /* Initialize action controller here */
        $this->_clients = new Application_Model_Clients_Table();
        parent::init();
        
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'html')
            ->addActionContext('edit', 'html')
            ->setSuffix('html', '')
            ->initContext();
    }
    
    public function listAction()
    {
        $this->_forward('index');
    }
    
    public function indexAction()
    {
        // action body
        
        $request = $this->getRequest();
        $this->view->clients = $this->_clients->getAll();
    }
    
    public function editAction()
    {
        // action body
        
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $form    = new Application_Form_Clients();
        if ($id) {
            $client = $this->_clients->get($id);
            $form->setDefaults($client->toArray());
        }
        $this->view->form = $form;
 
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                if ($id) {
                    $client->setFromArray($values);
                } else {
                    $client = $this->_clients->createRow($values);
                    $client->id = null;
                }
                $client->save();
                $this->view->success = 'Client successfully saved';
            }
        }
    }  
    
}