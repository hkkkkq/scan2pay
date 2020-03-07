<style>
body{max-width:355px;padding:0 10px;margin:0 auto}
h2{margin-bottom:10px}
.pure-control-group{margin-bottom:15px}
#error{color:#FF0000;font-weight:bold}
</style>
<form class="pure-form pure-form-stacked" method="POST" action="">
    <fieldset>
        <legend><h2>管理员登录</h2></legend>

        <div id="error"><?=$viewData['errorMsg']?></div>
        <div class="pure-control-group">
            <label for="pwd">密码</label>
            <input id="pwd" type="password" name="pwd">
            <span class="pure-form-message">输入系统中配置的管理员密码</span>
        </div>

        <button type="submit" class="pure-button pure-button-primary">登录</button>
    </fieldset>
</form>
