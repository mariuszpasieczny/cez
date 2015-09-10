<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_View_Helper_Table {

    /**
     * View instance
     * 
     * @var Zend_View_Instance
     */
    public $view = null;

    /**
     * Default view partial
     *
     * @var string
     */
    protected static $_defaultViewPartial = null;

    /**
     * Sets the view instance.
     *
     * @param  Zend_View_Interface $view View instance
     * @return Zend_View_Helper_PaginationControl
     */
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
        return $this;
    }

    /**
     * Sets the default view partial.
     *
     * @param string $partial View partial
     */
    public static function setDefaultViewPartial($partial) {
        self::$_defaultViewPartial = $partial;
    }

    /**
     * Gets the default view partial
     *
     * @return string
     */
    public static function getDefaultViewPartial() {
        return self::$_defaultViewPartial;
    }
    
    public function table($data = array(), $partial = null, $headers = array(), $addOns = null) {
        if ($partial === null) {
            if (self::$_defaultViewPartial === null) {
                /**
                 * @see Zend_View_Exception
                 */
                require_once 'Zend/View/Exception.php';

                throw new Zend_View_Exception('No view partial provided and no default set');
            }
            
            $partial = self::$_defaultViewPartial;
        }
        
        $table = array('headers' => $headers, 'data' => $data, 'addOns' => $addOns);
        return $this->view->partial($partial, $table);
    }

}
