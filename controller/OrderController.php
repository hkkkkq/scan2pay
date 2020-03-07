<?php
/**
 * Order Controller
 */
require __DIR__ . '/../lib/DataFactory.php';
require __DIR__ . '/../lib/OrderFactory.php';

Class OrderController extends Controller {
    protected $minDay = 20200301;       //release date
    protected $curlResult;         //curl result
    protected $curlStatus;         //curl status

    //get all orders by date
    public function actionByday() {     //--{{{
        $this->loginCheck();

        $today = date('Ymd');
        $day = (int)$this->get('day', $today);

        if (!preg_match('/^\d{8}$/', $day) || $day < $this->minDay) {
            throw new Exception("请检查参数day（{$day}）格式是否为：Ymd，如：20200301，且不早于：" . $this->minDay);
        }
    
        $configs = USC::$app['config'];
        $of = new OrderFactory($configs);
        $orders = $of->getAll($day);

        $stats = [
            'total' => 0,
            'total_refund' => 0,
            'amount' => 0,
            'amount_paid' => 0,
            'amount_refund' => 0,
        ];
        if (!empty($orders)) {
            $arrNums = [];
            foreach ($orders as $item) {
                $arrNums[] = $item['index'];

                $stats['total'] += 1;
                $stats['amount'] += $item['price'];
                if ($item['status'] == $of->orderStatus['paid']) {
                    $stats['total_paid'] += 1;
                    $stats['amount_paid'] += $item['price'];
                }else if ($item['status'] == $of->orderStatus['refund']) {
                    $stats['total_refund'] += 1;
                    $stats['amount_refund'] += $item['price'];
                }
            }

            //sort by index desc
            array_multisort($arrNums, SORT_DESC, $orders);
        }

        $pageTitle = "{$day}的订单";
        $viewName = 'orders';
        return $this->render($viewName, compact('day', 'orders', 'stats'), $pageTitle);
    }   //--}}}

    //set order paid
    public function actionSetpaid() {       //--{{{
        $this->loginCheck();

        $day = (int)$this->post('day', 0);
        $orderNumber = $this->post('num', 0);

        if (empty($day) || empty($orderNumber)) {
            throw new Exception("参数不完整！");
        }else if (!preg_match('/^\d{8}$/', $day) || $day < $this->minDay) {
            throw new Exception("请检查参数day（{$day}）格式是否为：Ymd，如：20200301，且不早于：" . $this->minDay);
        }

        //标记订单已付款
        $configs = USC::$app['config'];
        $of = new OrderFactory($configs);
        $orderData = $of->setOrderPaid($orderNumber, $day);
        if (!empty($orderData) && USC::$app['config']['notifyUrl']) {
            $this->sendNotify($orderData);
        }


        $backUrl = "/order/byday/?day={$day}";
        return $this->redirect($backUrl);
    }   //--}}}

    //set order refund
    public function actionSetrefund() {       //--{{{
        $this->loginCheck();

        $day = (int)$this->post('day', 0);
        $orderNumber = $this->post('num', 0);

        if (empty($day) || empty($orderNumber)) {
            throw new Exception("参数不完整！");
        }else if (!preg_match('/^\d{8}$/', $day) || $day < $this->minDay) {
            throw new Exception("请检查参数day（{$day}）格式是否为：Ymd，如：20200301，且不早于：" . $this->minDay);
        }

        //标记订单已付款
        $configs = USC::$app['config'];
        $of = new OrderFactory($configs);
        $orderData = $of->setOrderRefund($orderNumber, $day);
        if (!empty($orderData) && USC::$app['config']['notifyUrl']) {
            $this->sendNotify($orderData);
        }


        $backUrl = "/order/byday/?day={$day}";
        return $this->redirect($backUrl);
    }   //--}}}

    //resend notify
    public function actionNotify() {        //--{{{
        $this->loginCheck();

        $day = (int)$this->post('day', 0);
        $orderNumber = $this->post('num', 0);

        if (empty($day) || empty($orderNumber)) {
            throw new Exception("参数不完整！");
        }else if (!preg_match('/^\d{8}$/', $day) || $day < $this->minDay) {
            throw new Exception("请检查参数day（{$day}）格式是否为：Ymd，如：20200301，且不早于：" . $this->minDay);
        }

        $configs = USC::$app['config'];
        $of = new OrderFactory($configs);
        $orderData = $of->getOrderByNumber($orderNumber, $day);
        if (!empty($orderData) && USC::$app['config']['notifyUrl']) {
            $this->sendNotify($orderData);
        }

        $backUrl = "/order/byday/?day={$day}";
        return $this->redirect($backUrl);
    }       //--}}}

    //list date, group by month and year
    public function actionList() {      //--{{{
        $this->loginCheck();

        $configs = USC::$app['config'];
        $of = new OrderFactory($configs);
        $dataDir = $of->getCacheDir();

        $files = scandir($dataDir, SCANDIR_SORT_DESCENDING);
        //group by month and year
        $years = [];
        if (!empty($files)) {
            foreach ($files as $file) {
                if (in_array($file, ['.', '..'])) {continue;}

                $date = str_replace('.json', '', $file);
                $arr = $this->getYearAndMonthFromDate("{$date}");
                if (!empty($arr['year']) && !empty($arr['month'])) {
                    $year = $arr['year'];
                    $month = $arr['month'];

                    if (empty($years[$year])) {
                        $years[$year] = [];
                    }

                    if (empty($years[$year][$month])) {
                        $years[$year][$month] = [];
                    }

                    $years[$year][$month][] = $date;
                }

            }
        }


        $pageTitle = "订单列表";
        $viewName = 'list';
        return $this->render($viewName, compact('years'), $pageTitle);
    }       //--}}}

    //request url via curl
    protected function request($url, $postFields = [], $timeout = 10) {    //--{{{
        $s = curl_init();

        curl_setopt($s, CURLOPT_URL, $url);
        curl_setopt($s, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);

        if (!empty($postFields)) {
            curl_setopt($s, CURLOPT_POST, true);
            curl_setopt($s, CURLOPT_POSTFIELDS, $postFields);
        }


        $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.3 USC/1.0';
        curl_setopt($s, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($s, CURLOPT_REFERER, '-');

        $this->curlResult = curl_exec($s);
        $this->curlStatus = curl_getinfo($s, CURLINFO_HTTP_CODE);
        curl_close($s);
    }   //--}}}

    //send order status to notify url
    protected function sendNotify($order) {       //--{{{
        //send notify
        $notifyUrl = USC::$app['config']['notifyUrl'];

        //max execute time set
        $timeout = (int)USC::$app['config']['notifyTimeout'];
        $maxExecuteTime = (int)ini_get('max_execution_time');
        if ($timeout > $maxExecuteTime) {
            set_time_limit($timeout + 5);
        }

        $postData = [
            'order_id' => $order['order_id'],
            'status' => $order['status'],
            'update_time' => time(),
        ];
        $this->request($notifyUrl, $postData, $timeout);

        if ($this->curlStatus != 200) {
            $jsonData = json_encode($postData);
            $errorMsg = <<<eof
回调网址返回HTTP状态不是200，请确认回调被正确处理！<br>
<br>
通知地址：<br>
<a href="{$notifyUrl}" target="_blank">{$notifyUrl}</a><br>
<br>
POST 参数：<br>
{$jsonData}<br>
<br>
返回状态：<br>
{$this->curlStatus}<br>
回调网址返回错误信息：<br>
<textarea rows="10" style="width:80%">{$this->curlResult}</textarea>
eof;
            throw new Exception($errorMsg);
        }
    }       //--}}}

    protected function getYearAndMonthFromDate($date) {     //--{{{
        $time = strtotime($date);
        $year = date('Y', $time);
        $month = date('m', $time);

        return compact('year', 'month');
    }       //--}}}

    //login check
    protected function loginCheck() {   //--{{{
        session_start();

        if(empty($_SESSION['login_time'])) {
            return $this->redirect('/admin/login/');
        }
    }   //--}}}

}
