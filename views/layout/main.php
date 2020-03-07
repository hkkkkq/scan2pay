<?php
/**
 * Main layout
 */

?>
<!DocType html>
<head>
<meta charset="utf-8">
<title><?=$pageTitle?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
<link href="/css/pure-min.css" rel="stylesheet">
<link href="/css/grids-responsive-min.css" rel="stylesheet">
<link href="/css/main.css?v<?=time()?>" rel="stylesheet">
</head>
<body>


<?php
//### Render view file
$viewFile = __DIR__ . '/../' . USC::$app['controller'] . '/' . $viewName . '.php';
include_once $viewFile;
?>

    <div class="footer">
        &copy;Scan2pay 2020 - execute time: <?=$page_time_cost?> ms
    </div>

</body>
</html>
