<?php
/**
 * Tool Controller
 */
Class ToolController extends Controller {

    public function actionNeworder() {
        $pageTitle = "订单网址和二维码生成工具";
        $viewName = 'neworder';
        $params = [
            'foo' => 'bar',
        ];
        return $this->render($viewName, $params, $pageTitle);
    }

}
