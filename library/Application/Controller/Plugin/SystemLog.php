<?php

class Application_Controller_Plugin_SystemLog extends Zend_Controller_Plugin_Abstract {

    public function __construct($log = null) {
        if ($log) {
            $this->_log = $log;
        } else {
            $this->_log = new Zend_Log();
            $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/../data/logs/systemlogs-' . date('Y-m-d') . '.log');
            $this->_log->addWriter($writer);
        }
        $this->_start = microtime();
    }

    public function mark($marker) {
        $this->_timer[$marker] = microtime() - $this->_start;
        $this->_memoryusage[$marker] = memory_get_usage()/1024/1024;
        $this->_memorypeakusage[$marker] = memory_get_peak_usage()/1024/1024;
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
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $profiler = $db->getProfiler();

        $totalQueries = $profiler->getTotalNumQueries();
        $queryTime = $profiler->getTotalElapsedSecs();

        $longestTime = 0;
        $longestQuery = null;
        $queries = $profiler->getQueryProfiles();

        $content = "Executed $totalQueries database queries in $queryTime seconds\n";

        if ($queries !== false) { // loop over each query issued
            foreach ($queries as $query) {
                $this->_log->log($query->getQuery(), Zend_Log::DEBUG, array(
                    'usage' => $query->getElapsedSecs(),
                    'url' => Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(),
                    'userid' => ($identity = Zend_Auth::getInstance()->getIdentity()) ? $identity->id : null
                ));
                $content .= "\n<!-- Query (" . $query->getElapsedSecs() . "s): " . $query->getQuery() . " -->\n";
                if ($query->getElapsedSecs() > $longestTime) {
                    $longestTime = $query->getElapsedSecs();
                    //$longestQuery = htmlspecialchars(addcslashes($query->getQuery(), '"'));
                    $longestQuery = $query->getQuery();
                }
            }

            $content .= "Longest query ({$longestTime} s): $longestQuery\n";
        }
        $this->_log->log('Total query time', Zend_Log::DEBUG, array(
            'usage' => $profiler->getTotalElapsedSecs(),
            'url' => Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(),
            'userid' => ($identity = Zend_Auth::getInstance()->getIdentity()) ? $identity->id : null
        ));
        
        $this->_timer['dispatch-loop-shutdown'] = microtime() - $this->_start;//var_dump(microtime(), $this->_start, $this->_timer);
        $totalTime = $this->_timer['dispatch-loop-shutdown'];
        $content = "Executed in {$totalTime} seconds\n";
        $longestTime = 0;
        foreach ($this->_timer as $key => $value) {
            $_explode = explode(" ", $value);
            if (count($_explode) > 0) {
                @list($usec, $sec) = $_explode;
                $time = ((float) $usec + (float) $sec);
                if ($time < 0) {
                    continue;
                }
                $content .= "<!-- {$key} (" . $value . "s) -->\n";
                if ($time > $longestTime) {
                    $longestTime = $time;
                }
            }
        }
        $content .= "Longest time ({$longestTime} s)";
        $this->_log->log('Memory peak usage', Zend_Log::DEBUG, array(
            'usage' => memory_get_peak_usage(),
            'url' => Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(),
                    'userid' => ($identity = Zend_Auth::getInstance()->getIdentity()) ? $identity->id : null
        ));
        $this->_log->log('Total execute time', Zend_Log::DEBUG, array(
            'usage' => $totalTime,
            'url' => Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(),
                    'userid' => ($identity = Zend_Auth::getInstance()->getIdentity()) ? $identity->id : null
        ));
    }

}
