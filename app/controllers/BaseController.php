<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/helpers/SessionHelper.php';

class BaseController {
    protected function render($viewPath, $data = []) {
        extract($data);
        require_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/' . $viewPath . '.php';
    }

    protected function redirect($url) {
        header("Location: $url");
        exit();
    }
}