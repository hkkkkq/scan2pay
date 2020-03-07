<style>
body{max-width:1024px;margin:0 auto}
</style>
<h2>订单列表 <small>- 点击日期查看当天的订单</small></h2>
<div class="grid">

<?php
foreach($viewData['years'] as $year => $arr) {
    echo "<h3>{$year}年</h3>";

    foreach ($arr as $month => $days) {
        echo "<h4>{$year}年{$month}月</h4><hr>";

        foreach ($days as $day) {
            echo <<<eof
<a href="/order/byday/?day={$day}" class="pure-button btn-info btn-sm" target="_blank">{$day}</a>

eof;
        }
    }

}
?>

</div>
