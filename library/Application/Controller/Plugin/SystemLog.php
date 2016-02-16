<?php

require_once 'PEAR/Benchmark/Timer.php';

class Application_Controller_Plugin_SystemLog extends Zend_Controller_Plugin_Abstract {

    public function __construct($log = null) {
        if ($log) {
            $this->_log = $log;
        } else {
            $this->_log = new Zend_Log();
            //$writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/../data/logs/system-' . date('Y-m-d') . '.log');
            $writer = new Zend_Log_Writer_Null();
            $this->_log->addWriter($writer);
        }
        $this->_timer = new Benchmark_Timer(TRUE);
        $this->_timer->start();
        $this->_profiler = array();
    }

    public function mark($marker) {
        $this->_timer->setMarker($marker);
        $this->_profiler[$marker] = memory_get_usage() - end($this->_profiler);
    }

    public function routeStartup(Zend_Controller_Request_Abstract $request) {
        $this->mark('route-startup');
    }

    public function routeShutdown(Zend_Controller_Request_Abstract $request) {
        $this->mark('route-shutdown');
    }

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        $this->mark('dispatch-loop-startup');
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!$db->getProfiler()->getEnabled()) {
            $db->setProfiler(new Zend_Db_Profiler());
            $db->getProfiler()->setEnabled(true);
        }
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $this->mark('pre-dispatch');
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request) {
        $this->mark('post-dispatch');
    }

    public function dispatchLoopShutdown() {
        
        $this->mark('dispatch-loop-shutdown');
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $profiler = $db->getProfiler();

        $totalQueries = $profiler->getTotalNumQueries();
        $queryTime = $profiler->getTotalElapsedSecs();

        $longestTime = 0;
        $longestQuery = null;
        $queries = $profiler->getQueryProfiles();

        if ($queries !== false) { // loop over each query issued
            foreach ($queries as $query) {
                $this->_log->log($query->getQuery(), Zend_Log::DEBUG, array(
                    'time' => round($query->getElapsedSecs(), 6),
                    'url' => Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(),
                    'memory' => memory_get_usage(),
                    'userid' => ($identity = Zend_Auth::getInstance()->getIdentity()) ? $identity->id : null
                ));
            }
        }
        
        $this->_timer->stop();
        //$data = $this->_timer->display(true, 'plain');
        $data = $this->_timer->getProfiling();
        foreach ($data as $key => $value) {
            if (in_array($value['name'], array('Start','Stop'))) {
                continue;
            }
            $this->_log->log($value['name'], Zend_Log::DEBUG, array(
                'memory' => $this->_profiler[$value['name']],
                'time' => $value['diff'],
                'url' => Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(),
                'userid' => ($identity = Zend_Auth::getInstance()->getIdentity()) ? $identity->id : null
            ));
        }
        $value = array_pop($data);
        $this->_log->log('Total', Zend_Log::DEBUG, array(
            'time' => $value['total'],
            'memory' => memory_get_peak_usage(),
            'url' => Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(),
                    'userid' => ($identity = Zend_Auth::getInstance()->getIdentity()) ? $identity->id : null
        ));
    }

}
