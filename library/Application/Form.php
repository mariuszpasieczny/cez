<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form extends Zend_Form
{
    
    public function init()
    {
        parent::init();
        
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setOptions(array('class' => 'form-load'));
        $this->setDecorators(
            array(
                'FormElements',
                'Form',
                array('HtmlTag', array('tag' => 'dl', 'class' => 'overflow zend_form')),
            )
        );
        /**/
 
        // Add a captcha
        /*$this->addElement('captcha', 'captcha', array(
            'label'      => 'Please enter the 5 letters displayed below:',
            'required'   => true,
            'captcha'    => array(
                'captcha' => 'Figlet',
                'wordLen' => 5,
                'timeout' => 300
            )
        ));*/
 
        // Add the submit button
        $element = $this->createElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Zapisz',
            'class' => 'btn btn-primary'
        ));
        $element->setOrder(1000);
        $this->addElement($element);
 
        // Add the cancel button
        $element = $this->createElement('button', 'cancel', array(
            'ignore'   => true,
            'label'    => 'Anuluj',
            'class' => 'btn btn-default'
        ));
        $element->setOrder(1001);
        $element->removeDecorator('DtDdWrapper');
        $this->addElement($element);
 
        // And finally add some CSRF protection
        /*$this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));*/
    }
    
    /*public function render()
    {
        foreach ($this->getElements() as $element) {
            $element->addDecorators(array(
                //array('Description', array('escape' => false, 'tag' => 'span', 'class' => '', 'placement' => 'prepend')),
                //array('Label', array('tag' => 'td')),
                //array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-group')),
                //array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
                //array('HtmlTag', array('tag' => 'div', 'class' => 'form-control'))
            ));
        }
        return parent::render();
    }*/
    
}