<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Catalog_Delete extends Application_Form
{
    
    protected $_defaultDisplayGroupClass = 'Application_Form_DisplayGroup';
    
    public function __construct($options = null)
    {
        $this->addPrefixPath('Application_Form', 'Application/Form');
        parent::__construct($options);
    }
    
    public function init()
    {
        parent::init();
        
        $this->addPrefixPath('ZendX_JQuery_Form_Element', 'ZendX/JQuery/Form/Element', 'element');
        
        // ad an id element
        $this->addElement('hidden', 'id', array(
            //'belongsTo' => 'id', 
            //'id' => 'id[' . $i . ']',
            //'isArray' => true
        ));
    }
    
}