<?php
/**
 * Site Controller
 */
Class SiteController extends Controller {

    public function actionIndex() {
        $pageTitle = "Welcome to Scan2Pay";
        $viewName = 'index';
        $params = [
            'foo' => 'bar',
        ];
        return $this->render($viewName, $params, $pageTitle);
    }

}
