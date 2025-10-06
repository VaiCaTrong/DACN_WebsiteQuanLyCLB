<?php

// Định nghĩa BASE_URL để đảm bảo đường dẫn nhất quán
require_once 'app/config/database.php';
require_once 'app/controllers/TeamController.php';
require_once 'app/helpers/SessionHelper.php';

// require_once 'app/models/TeamModel.php';

// require_once 'app/controllers/TeamApiController.php';
require_once 'app/controllers/AccountController.php';
require_once 'app/models/AccountModel.php';
require_once 'app/controllers/ImportantDayController.php';
require_once 'app/controllers/DefaultController.php';

spl_autoload_register(function ($class) {
    $file = $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
// THÊM 2 DÒNG NÀY


// if ($uri === '/webdacn_quanlyclb/Team' && $method === 'GET') {
//     $controller->index();
// } elseif ($uri === '/webdacn_quanlyclb/Team/add' && $method === 'GET') {
//     $controller->add();
// } elseif ($uri === '/webdacn_quanlyclb/Team/add' && $method === 'POST') {
//     $controller->add();
// } elseif (preg_match('/\/webdacn_quanlyclb\/Team\/edit\/(\d+)/', $uri, $matches) && $method === 'GET') {
//     $controller->edit($matches[1]);
// } elseif (preg_match('/\/webdacn_quanlyclb\/Team\/edit\/(\d+)/', $uri, $matches) && $method === 'POST') {
//     $controller->edit($matches[1]);
// } elseif (preg_match('/\/webdacn_quanlyclb\/Team\/delete\/(\d+)/', $uri, $matches) && $method === 'GET') {
//     $controller->delete($matches[1]);
// } else {
// }

// Khởi tạo session bằng SessionHelper
SessionHelper::start();
// Kiểm tra xem người dùng đã đăng nhập hay chưa
// Lấy URL và xử lý định tuyến
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Xác định controller
$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'DefaultController';

// Xác định action
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// Định tuyến các yêu cầu API
if ($controllerName === 'ApiController' && isset($url[1])) {
    $apiControllerName = ucfirst($url[1]) . 'ApiController';
    if (file_exists('app/controllers/' . $apiControllerName . '.php')) {
        require_once 'app/controllers/' . $apiControllerName . '.php';
        $controller = new $apiControllerName();
        $method = $_SERVER['REQUEST_METHOD'];
        $id = $url[2] ?? null;
        switch ($method) {
            case 'GET':
                $action = $id ? 'show' : 'index';
                break;
            case 'POST':
                $action = 'store';
                break;
            case 'PUT':
                $action = $id ? 'update' : null;
                break;
            case 'DELETE':
                $action = $id ? 'destroy' : null;
                break;
            default:
                http_response_code(405);
                echo json_encode(['message' => 'Method Not Allowed']);
                exit;
        }
        if ($action && method_exists($controller, $action)) {
            if ($id) {
                call_user_func_array([$controller, $action], [$id]);
            } else {
                call_user_func_array([$controller, $action], []);
            }
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Action not found']);
        }
        exit;
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Controller not found']);
        exit;
    }
}

// Giả sử đây là phần router trong index.php hoặc router.php
// Thêm vào file routing


// Tải controller không phải API
if (file_exists('app/controllers/' . $controllerName . '.php')) {
    require_once 'app/controllers/' . $controllerName . '.php';
    $controller = new $controllerName();
} else {
    http_response_code(404);
    die("Controller not found: $controllerName");
}

// Gọi action
if (method_exists($controller, $action)) {
    call_user_func_array([$controller, $action], array_slice($url, 2));
} else {
    http_response_code(404);
    die("Action not found: $action in $controllerName");
}
