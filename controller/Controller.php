<?php
/**
 * Controller
 */
Class Controller {
    protected $layout = 'main';

    function __construct() {
        //do some thing
    }

    function __destruct() {
        $this->logTimeCost();
    }

    //redirect url
    protected function redirect($url, $code = 302) {    //--{{{
        header("Location: {$url}", true, $code);
        exit;
    }   //--}}}

    //render view
    public function render($viewName, $viewData = [], $pageTitle = '') {   //--{{{
        $layoutFile = __DIR__ . '/../views/layout/' . $this->layout . '.php';

        //include layout and view
        if (file_exists($layoutFile)) {
            //show time cost
            $end_time = microtime(true);
            $page_time_cost = ceil( ($end_time - USC::$app['start_time']) * 1000 );   //ms

            ob_start();
            include_once $layoutFile;

            $htmlCode = ob_get_contents();
            ob_end_clean();

            //enable gzip
            ob_start('ob_gzhandler');
            echo $htmlCode;
            ob_end_flush();
        }else {
            throw Exception("Layout file not exist: {$layoutFile}", 500);
        }
    }   //--}}}

    //render json data
    public function renderJson($data) {     //--{{{
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($data);
        exit;
    }   //--}}}

    //get params by key
    protected function get($key, $defaultValue = '') {      //--{{{
        return !empty($_GET[$key]) ? $_GET[$key] : $defaultValue;
    }   //--}}}

    //post params by key
    protected function post($key, $defaultValue = '') {      //--{{{
        return !empty($_POST[$key]) ? $_POST[$key] : $defaultValue;
    }   //--}}}

    //debug log
    protected function logTimeCost() {      //--{{{
        if (!empty(USC::$app['config']['debug'])) {
            $end_time = microtime(true);
            $timeCost = ceil( ($end_time - USC::$app['start_time']) * 1000 );   //ms
            $thisUrl = USC::$app['requestUrl'];
            $logTime = date('Y-m-d H:i:s');
            $logDir = __DIR__ . '/../runtime/logs/';
            $logOk = @error_log("{$logTime}\t{$thisUrl}\ttime cost: {$timeCost} ms\n", 3, "{$logDir}debug.log");
            if (!$logOk) {
                mkdir($logDir, 0700);
                @error_log("{$logTime}\t{$thisUrl}\ttime cost: {$timeCost} ms\n", 3, "{$logDir}debug.log");
            }
        }
    }       //--}}}

}
