<style>
body{max-width:355px;padding:0 10px;margin:0 auto}
h2{margin-bottom:10px}
textarea{width:100%}
small{font-weight:normal}
.pure-control-group{margin-bottom:15px}
#error{color:#FF0000;font-weight:bold}
#copytip{color:green;font-size:13px}
</style>
<form class="pure-form pure-form-stacked" method="POST" action="" onsubmit="return makeCode()">
    <fieldset>
        <legend><h2>订单网址生成</h2></legend>

        <div id="error"></div>
        <div class="pure-control-group">
            <label for="user_id">手机号码</label>
            <input id="user_id" type="number" placeholder="手机号码">
            <span class="pure-form-message">可选，如果让用户自己填，请留空</span>
        </div>

        <div class="pure-control-group">
            <label for="price">售价</label>
            <input id="price" type="number" step="0.01" placeholder="商品价格">
        </div>

        <button type="submit" class="pure-button pure-button-primary">生成订单网址和二维码</button>
        <hr>
        <div class="hide" id="qrcon">
            <h3>订单付款网址 <small>复制网址发给客户</small></h3>
            <textarea id="orderurl" rows="2" realonly></textarea>
            <button id="copybtn" class="pure-button btn-info btn-sm" data-clipboard-target="#orderurl">复制网址</button>
            <span id="copytip"></span>
            <h3>订单二维码 <small>保存二维码后发给客户</small></h3>
            <div id="qrcode"></div>
        </div>
    </fieldset>
</form>
<script type="text/javascript" src="/js/qrcode.min.js"></script>
<script type="text/javascript" src="/js/clipboard.min.js"></script>
<script>
var $ = function(id) {
    return document.getElementById(id);
};

var clipboard = new ClipboardJS('#copybtn');
clipboard.on('success', function(e) {
    $('copytip').innerText = '复制完成';
    e.clearSelection();
    setTimeout(function() {
        $('copytip').innerText = '';
    }, 2000);
});

clipboard.on('error', function(e) {
    $('copytip').innerText = '不支持当前浏览器，请手动选择网址后复制';
});

var qrcode = new QRCode(document.getElementById("qrcode"), {
    width : 180,
    height : 180
});

function makeCode () {		
    var cellphone = $('user_id').value,
        price = $('price').value;
    
    if (!price) {
        $('error').innerText = '请输入商品价格';
        $('price').focus();
        return false;
    }else if (cellphone && !/^1[3-9]\d{9}$/.test(cellphone)) {
        $('error').innerText = '请输入正确的手机号码';
        $('user_id').focus();
        return false;
    }

    $('error').innerText = '';
    var txt = location.protocol + '//' + location.host + '/api/createorder/?price=' + encodeURIComponent(price);
    if (cellphone) {
        txt += '&user_id=' + encodeURIComponent(cellphone);
    }
    $('orderurl').value = txt;
    qrcode.makeCode(txt);

    $('qrcon').className = '';

    return false;
}
</script>
