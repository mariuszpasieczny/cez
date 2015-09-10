<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// application/forms/Auth.php
 
class Application_Form_RemindPassword extends Application_Form
{

    protected $_defaultDisplayGroupClass = 'Application_Form_DisplayGroup';
    
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setOptions(array('class' => 'form-load form-signin', /*'placeholder' => 'navbar', */));
        $this->setAttrib('action', '/auth/remind-password');
 
        // Add an email element
        $element = $this->createElement('text', 'email', array(
            //'label'      => 'Your email address:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'EmailAddress',
            ),
            'class' => 'form-control',
        ));
        $element->addDecorator('HtmlTag', array('class' => 'form-group'));
        $element->addDecorator('Label', array('tag' => ''));
        $this->addElement($element);
 
        // Add the submit button
        $element = $this->createElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Kontynuuj',
            'class' => 'btn btn-lg btn-primary btn-block btn-signin'
        ));
        $element->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'form-group'));
        //$element->addDecorator('Label', array('tag' => '', 'placement' => 'append'));
        $element->removeDecorator('DtDdWrapper');
        $this->addElement($element);
 
        // And finally add some CSRF protection
        /*$this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));*/
    }
}