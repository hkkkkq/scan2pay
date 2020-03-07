<style>
body{max-width:1024px;margin:0 auto}
table{width:100%}
form{display:inline}
</style>
<h2>我的订单 <small>- <?=$viewData['day']?>的订单</small></h2>
<p class="stats">
    订单总数：<strong><?=$viewData['stats']['total']?></strong>，
    已付款：<strong><?=$viewData['stats']['total_paid']?></strong>，
    已退款：<strong><?=$viewData['stats']['total_refund']?></strong>，
    总金额：<strong><?=$viewData['stats']['amount']?></strong>，
    付款总额：<strong class="label-success"><?=$viewData['stats']['amount_paid']?></strong>
    退款总额：<strong class="label-danger"><?=$viewData['stats']['amount_refund']?></strong>
</p>
<div class="grid">

    <table class="pure-table pure-table-bordered pure-table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>订单编号</th>
                <th>价格</th>
                <th>用户</th>
                <th>创建时间</th>
                <th>状态</th>
                <th>付款备注</th>
                <th>操作</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $notifyUrl = USC::$app['config']['notifyUrl'];      //回调网址

            foreach ($viewData['orders'] as $index => $item) {
                $statusCls = '';
                if ($item['status'] == 'paid') {$statusCls = 'label-success';}
                if ($item['status'] == 'refund') {$statusCls = 'label-danger';}
            ?>
            <tr>
                <td><?=$index+1?></td>
                <td><?=$item['order_id']?></td>
                <td><?=$item['price']?></td>
                <td><span class="label-info"><?=$item['user_id']?></span></td>
                <td><?=date('Y-m-d H:i:s', $item['create_time'])?></td>
                <td><span class="<?=$statusCls?>"><?=$item['status']?></span></td>
                <td><span class="label-info"><?=$item['index']?></span></td>
                <td>
                    <?php if ($item['status'] == 'new') { ?>
                    <form action="/order/setpaid/" method="POST">
                        <input type="hidden" name="num" value="<?=$item['index']?>">
                        <input type="hidden" name="day" value="<?=$viewData['day']?>">
                        <button class="pure-button btn-success btn-xs" type="button" onclick="setPaid(this)">已付款</button>
                    </form>
                    <form action="/order/setrefund/" method="POST">
                        <input type="hidden" name="num" value="<?=$item['index']?>">
                        <input type="hidden" name="day" value="<?=$viewData['day']?>">
                        <button class="pure-button btn-danger btn-xs" type="button" onclick="setRefund(this)">退款</button>
                    </form>
                    <?php }else if (!empty($notifyUrl)) { ?>
                    <form action="/order/notify/" method="POST">
                        <input type="hidden" name="num" value="<?=$item['index']?>">
                        <input type="hidden" name="day" value="<?=$viewData['day']?>">
                        <button class="pure-button btn-info btn-xs" type="button" onclick="sendNotify(this)">重发通知</button>
                    </form>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</div>
<script>
function setPaid(btn) {
    if (confirm('确定此订单已收款？')) {
        btn.disabled = true;
        btn.parentNode.submit();
    }
}

function setRefund(btn) {
    if (confirm('确定设置此订单退款？')) {
        btn.disabled = true;
        btn.parentNode.submit();
    }
}

function sendNotify(btn) {
    if (confirm('确定重发通知？')) {
        btn.disabled = true;
        btn.parentNode.submit();
    }
}
</script>
