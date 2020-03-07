<?php
/**
 * Api Controller
 */
require __DIR__ . '/../lib/DataFactory.php';
require __DIR__ . '/../lib/OrderFactory.php';

Class ApiController extends Controller {

    /**
     * Http api to create new order and scan to pay.
     * @price - float number, CN Yuan
     * @order_id - [optional] order id from your system
     * @user_id - [optional] user id from your system
     */
    public function actionCreateorder() {   //--{{{
        //GET parameters support
        $price = (float)$this->get('price');
        $order_id = $this->get('order_id');
        $user_id = $this->get('user_id');

        //POST parameters support
        $price = $this->post('price', $price);
        $order_id = $this->post('order_id', $order_id);
        $user_id = $this->post('user_id', $user_id);

        //user_id should be input
        if (empty($user_id)) {
            return $this->redirect("/api/userid?price={$price}");
        }

        //parameters check
        $this->checkFormInput($price, $user_id, $order_id);

        //create new order
        $code = 0;
        $msg = '';

        try {
            $configs = USC::$app['config'];
            $of = new OrderFactory($configs);
            $order = $of->newOrder($price, $user_id, $order_id);
            $code = 1;
            $msg = 'OK';
        }catch(Exception $e) {
            $msg = $e->getMessage();
        }

        $viewName = 'index';
        $viewData = compact('code', 'msg', 'order');
        $pageTitle = '扫码付款';
        return $this->render($viewName, $viewData, $pageTitle);
    }   //--}}}

    //User contact form
    public function actionUserid() {        //--{{{
        //GET parameters support
        $price = (float)$this->get('price');
        $user_id = $this->get('user_id');
        $user_id_confirm = $this->get('user_id_confirm');

        //POST parameters support
        $price = $this->post('price', $price);
        $user_id = $this->post('user_id', $user_id);
        $user_id_confirm = $this->post('user_id_confirm', $user_id_confirm);

        //parameters check
        $this->checkFormInput($price, $user_id);

        $errorCls = [];
        if (!empty($user_id) && !$this->isCellphoneOk($user_id)) {
            $errorCls['user_id'] = 'alert-danger';
        }else if (!empty($user_id) && $user_id != $user_id_confirm) {
            $errorCls['user_id_confirm'] = 'alert-danger';
        }else if (!empty($user_id)){
            return $this->redirect("/api/createorder?price={$price}&user_id={$user_id}");
        }

        $viewName = 'contact';
        $viewData = compact('price', 'user_id', 'errorCls');
        $pageTitle = 'Scan2Pay - 输入联系方式';
        return $this->render($viewName, $viewData, $pageTitle);
    }   //--}}}

    //form input value check
    protected function checkFormInput($price, $user_id = '', $order_id = '') {        //--{{{
        if (empty($price)) {
            throw new Exception('Parameter "price" should not be empty.');
        }
        if (!empty($user_id)) {
            $check_user_id = preg_replace('/\W/', '', $user_id);
            if ($user_id != $check_user_id) {
                throw new Exception('Parameter "user_id" should only contains letters in [0-9a-zA-Z_].');
            }
        }
        if (!empty($order_id)) {
            $check_order_id = preg_replace('/\W/', '', $order_id);
            if ($order_id != $check_order_id) {
                throw new Exception('Parameter "order_id" should only contains letters in [0-9a-zA-Z_].');
            }
        }
    }   //--}}}

    //check cellphone format
    protected function isCellphoneOk($cellphone) {  //--{{{
        $ok = true;

        if (!preg_match('/^1[3-9]\d{9}$/', $cellphone)) {
            $ok = false;
        }

        return $ok;
    }   //--}}}

}
