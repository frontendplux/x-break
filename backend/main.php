<?php
header('Content-Type: application/json');
// Validate JSON input
$datas = json_decode(file_get_contents('php://input'));
$router = explode('/', $_GET['url']);

switch ($router[0]) {
    case 'post':
        echo post($conn, $input);
        break;

    case 'upload':
        echo upload($conn, $input);
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Unknown endpoint']);
        break;
}
