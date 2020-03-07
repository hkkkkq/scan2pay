<div class="pay">
    <form class="pure-form pure-form-stacked" method="GET" action="">
        <fieldset>
            <legend><h1 class="text-center">请填写联系方式</h1></legend>

            <div class="pure-control-group form-item">
                <label for="user_id">手机号码</label>
                <input id="user_id" name="user_id" type="text" placeholder="中国手机号" value="<?=$viewData['user_id']?>">
                <span class="pure-form-message <?=!empty($viewData['errorCls']['user_id']) ? $viewData['errorCls']['user_id'] : ''?>">请正确填写便于卖家联系</span>
            </div>

            <div class="pure-control-group form-item">
                <label for="user_id_confirm">重新输入一边</label>
                <input id="user_id_confirm" name="user_id_confirm" type="text" placeholder="手机号确认">
                <span class="pure-form-message <?=!empty($viewData['errorCls']['user_id_confirm']) ? $viewData['errorCls']['user_id_confirm'] : ''?>">确保两次输入相同</span>
            </div>

            <div class="pure-control-group form-item">
                <input type="hidden" name="price" value="<?=$viewData['price']?>">
                <button type="submit" class="button-small pure-button pure-button-primary">下一步</button>
            </div>
        </fieldset>
    </form>
</div>
