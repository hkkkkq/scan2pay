<?php
/**
 * Order Factory
 */
Class OrderFactory extends DataFactory {
    protected $orderLockKey = 'orderNumber';
    protected $maxOrderOneDay = 10000;
    public $orderStatus = [
        'new' => 'new',
        'paid' => 'paid',
        'refund' => 'refund',
    ];

    /**
     * config: ['maxOrderOneDay' => 10000]
     */
    function __construct($config) {     //--{{{
        parent::__construct($config);

        if (!empty($config['maxOrderOneDay'])) {
            $this->maxOrderOneDay = $config['maxOrderOneDay'];
        }
    }   //--}}}

    //support get number from order data
    public function getOrderNumber($order = []) {      //--{{{
        $orderNumber = 0;

        if (empty($order)) {
            $today = date('Ymd');
            $cacheKey = $this->orderLockKey . ":{$today}";

            if ($this->redis->exists($cacheKey)) {
                $orderNumber = $this->redis->incr($cacheKey);
            }else {     //create the key
                $orderNumber = $this->redis->incr($cacheKey);
                $this->redis->expire($cacheKey, 90000);    //expired in 25 hours
            }
        }else {
            $orderNumber = !empty($order['index']) ? $order['index'] : 0;
        }

        return $orderNumber;
    }   //--}}}

    protected function getOrderId($orderNumber) {      //--{{{
        $maxLen = ceil( log10($this->maxOrderOneDay) );
        $today = date('Ymd');
        return $today . str_pad($orderNumber, $maxLen, '0', STR_PAD_LEFT);
    }   //--}}}

    /**
     * create new order
     * @price
     * @user_id
     * @order_id
     * @ext_arr: other attributes
     */
    public function newOrder($price, $user_id = '', $order_id = '', $ext_arr = []) {     //--{{{
        $number = $this->getOrderNumber();
        if ($number > $this->maxOrderOneDay) {
            throw new Exception("Oops, order number is grater than the max order number in one day: {$this->maxOrderOneDay}");
        }

        if (empty($order_id)) {
            $order_id = $this->getOrderId($number);
        }

        $status = $this->orderStatus['new'];
        $create_time = time();

        $arr = compact('order_id', 'price', 'status', 'create_time');
        if (!empty($user_id)) {
            $arr['user_id'] = $user_id;
        }

        //merge more attributes
        $newOrder = array_merge($arr, $ext_arr);

        $today = date('Ymd');
        $newOrder = $this->save($today, $number, $newOrder);

        return $newOrder;
    }       //--}}}

    //get all orders by date
    public function allOrders($day = 0) {       //--{{{
        $today = !empty($day) ? (int)$day : date('Ymd');
        return $this->getAll($today);
    }       //--}}}

    //get one order by date and order number
    public function getOrderByNumber($number, $day = 0) {       //--{{{
        $today = !empty($day) ? (int)$day : date('Ymd');
        return $this->get($today, $number);
    }       //--}}}

    /**
     * update order's status by date and order number
     * @status
     * @number
     * @day
     */
    protected function updateOrderStatus($status, $number, $day = 0) {     //--{{{
        $today = !empty($day) ? (int)$day : date('Ymd');
        $order = $this->getOrderByNumber($number, $today);
        if (!empty($order)) {
            if (!in_array($status, $this->orderStatus)) {
                throw new Exception('Order status should be: ' . implode(',', $this->orderStatus));
            }

            $order['status'] = $status;
            $this->save($today, $number, $order);

        }

        return $order;
    }       //--}}}

    //update order's status to paid
    public function setOrderPaid($number, $day = 0) {     //--{{{
        $today = !empty($day) ? (int)$day : date('Ymd');
        return $this->updateOrderStatus($this->orderStatus['paid'], $number, $today);
    }       //--}}}

    //update order's status to refund
    public function setOrderRefund($number, $day = 0) {     //--{{{
        $today = !empty($day) ? (int)$day : date('Ymd');
        return $this->updateOrderStatus($this->orderStatus['refund'], $number, $today);
    }   //--}}}

}
