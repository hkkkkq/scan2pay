<?php
date_default_timezone_set('Asia/Shanghai');

require_once __DIR__ . '/../lib/DataFactory.php';


$config = [
    'redis' => [
        'host' => 'redis-server',
        'port' => 6379,
    ]
];

try {
    $df = new DataFactory($config);

    $today = date('Ymd');
    $orderNumber = rand(1, 100);
    $data = ['orderid' => '2020021901', 'username' => '测试test ' . $orderNumber];
    $df->save($today, $orderNumber, $data);
    echo "Data append success\n";

    $cacheData = $df->getAll($today);
    echo "Cache data get all:\n";
    print_r($cacheData);

    $order_id = $orderNumber;
    $cacheData = $df->get($today, $order_id);
    echo "Cache data get by index:\n";
    print_r($cacheData);

    $order_id = $orderNumber;
    $newData = ['orderid' => '2020022101', 'username' => '测试test数据修改:' . $order_id];
    $df->save($today, $order_id, $newData);
    echo "New data update done.\n";

    $order_id = $orderNumber;
    $cacheData = $df->get($today, $order_id);
    echo "Cache data get by index:\n";
    print_r($cacheData);

    //get data from file and rewrite into redis
    if(!empty($argv[1])) {
        $theDay = $argv[1];
        $cacheData = $df->getAll($theDay);
        echo "Cache data get all and write back into redis:\n";
        print_r($cacheData);
    }

}catch(Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}

echo "\n";
echo "All done.\n";
