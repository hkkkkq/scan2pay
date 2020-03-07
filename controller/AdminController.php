<?php
/**
 * Admin Controller
 */
Class AdminController extends Controller {

    public function actionLogin() {
        $pwd = $this->post('pwd', '');

        $errorMsg = '';
        if (!empty($pwd) && $pwd != USC::$app['config']['admpwd']) {
            $errorMsg = '登录密码错误，请检查大小写是否正确！';
        }else if (!empty($pwd) && $pwd == USC::$app['config']['admpwd']) {
            session_start();

            //save login time for login check
            $_SESSION['login_time'] = time();

            return $this->redirect('/order/list/');
        }

        $pageTitle = "管理员登录";
        $viewName = 'login';
        $params = [
            'errorMsg' => $errorMsg,
        ];
        return $this->render($viewName, $params, $pageTitle);
    }

}
