<?php
/**
 * Config parameters
 */
return [
    //Max orders in one day
    'maxOrderOneDay' => 10000,

    //QR image should be placed in the www/ directory
    //Wechat qr image file name
    'wechatQr' => '/img/wechat.png',

    //Alipay qr image file name
    'alipayQr' => '/img/alipay.png',

    //If you don't have one, keep it empty
    //Notify url to get paid order
    'notifyUrl' => '',
    //Timeout when callback to notify url
    'notifyTimeout' => 10,  //seconds

    //Redis config
    'redis' => [
        'host' => 'redis-server',
        'port' => 6379,
        'secret' => '',
        'keyPrefix' => 'usc',
    ],

    //Password for administrator login
    'admpwd' => '666666',

    //for debug, log directory: ../runtime/logs/
    'debug' => false,
];
