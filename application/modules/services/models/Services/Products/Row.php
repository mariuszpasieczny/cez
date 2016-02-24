<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Services_Products_Row extends Application_Db_Table_Row {

    public function getProduct() {
        $products = new Application_Model_Orders_Lines_Table();
        //$products->setLazyLoading(false);
        //$products->setOrderBy(array('product ASC'));
        $products->setSchema($this->getTable()->getSchema());
        return ($product = $products->getAll(array('id' => $this->productid))->current()) ? $product->getProduct() : null;
        
        if (!$product = parent::findParentRow('Application_Model_Orders_Lines_Table', 'Product')) {
            return null;
        }
        return $product->getProduct();
    }
    
}