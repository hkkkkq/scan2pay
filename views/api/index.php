<?php
//both alipay and wechat
$gridCls = 'pure-u-12-24 pure-u-sm-12-24';
$offsetGrid = '';

//only one wechat or alipay
if (empty(USC::$app['config']['wechatQr']) || empty(USC::$app['config']['alipayQr'])) {
    $gridCls = 'pure-u-18-24 pure-u-sm-12-24';
    $offsetGrid = '<div class="pure-u-3-24 pure-u-sm-6-24"></div>';
}

?>
<div class="pay">
    <h1 class="text-center">付款金额：<strong><?=$viewData['order']['price']?></strong><small>元</small></h1>
    <p class="text-center">
        扫码后<strong class="label-warning">务必</strong>先在备注里填订单编号：
        <strong class="num label-warning"><?=$viewData['order']['index']?></strong>
        <br><small>付款后请等待卖家发货或联系他</small>
    </p>

    <div class="pure-g">
        <?php if (!empty(USC::$app['config']['wechatQr'])) { ?>
        <?=$offsetGrid?>
        <div class="<?=$gridCls?>">
            <div class="con wxcon text-center">
                <img class="hd" src="/img/logo_wx.png" width="100">
                <div class="bd">
                    <p>打开微信[扫一扫]</p>
                    <img src="<?=USC::$app['config']['wechatQr']?>">
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (!empty(USC::$app['config']['alipayQr'])) { ?>
        <?=$offsetGrid?>
        <div class="<?=$gridCls?>">
            <div class="con alicon text-center">
                <img class="hd" src="/img/logo_ali.png" width="100">
                <div class="bd text-center">
                    <p>打开支付宝[扫一扫]</p>
                    <img src="<?=USC::$app['config']['alipayQr']?>">
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <p class="text-center">手机浏览器里请长按图片选择“<strong>识别二维码</strong>”
        <br>如果不支持直接识别请先保存，再到微信或支付宝里从“<strong>扫一扫</strong>”的“相册”打开
    </p>

</div>
