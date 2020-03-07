<?php
date_default_timezone_set('Asia/Shanghai');

require_once __DIR__ . '/../lib/DataFactory.php';
require_once __DIR__ . '/../lib/OrderFactory.php';


$config = [
    'maxOrderOneDay' => 1000,
    'redis' => [
        'host' => 'redis-server',
        'port' => 6379,
    ]
];

try {
    $of = new OrderFactory($config);

    $price = 100;
    $user_id = 'tester';
    $newOrder = $of->newOrder($price, $user_id);
    echo "New order\n";
    print_r($newOrder);

    $orderNumber = $of->getOrderNumber($newOrder);
    $newOrder = $of->setOrderPaid($orderNumber);
    echo "Paid order\n";
    print_r($newOrder);

    $orderNumber = $of->getOrderNumber($newOrder);
    $newOrder = $of->setOrderRefund($orderNumber);
    echo "Refund order\n";
    print_r($newOrder);

    $orderNumber = $of->getOrderNumber($newOrder);
    $newOrder = $of->getOrderByNumber($orderNumber);
    echo "Get order by number: {$orderNumber}\n";
    print_r($newOrder);

    $allOrders = $of->allOrders();
    echo "All orders\n";
    print_r($allOrders);
    

}catch(Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}

echo "\n";
echo "All done.\n";
