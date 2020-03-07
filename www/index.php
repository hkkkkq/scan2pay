<?php
/* All php controller enter from here */
date_default_timezone_set('Asia/Shanghai');
require_once __DIR__ . '/../lib/USC.php';
require_once __DIR__ . '/../controller/Controller.php';

//call function in controller
USC::run();
