<?php
$route = explode('/', $_GET['url'] ?? '');
include 'php/functions.php';
$data = json_decode(file_get_contents("php://input"));

switch ($route[0]) {
    case '':
    case 'index':
        include 'html/index.html';
        break;

    case 'start':
        include 'html/start.html';
        break;

    case 'start-access':
        if (!isset($data->student_id) || !isset($data->access_code)) {
            echo json_encode(['status' => false, 'message' => 'Missing data']);
            break;
        }
        $response = startExam($connection, $data->student_id, $data->access_code);
        echo json_encode($response);
        break;
    
        case 'admin':
            include 'html/admin.html';
            break;
        
        case 'admin-login':
            $id = $data->id ?? '';
            $pass = $data->pass ?? '';
            echo json_encode(adminLogin($connection, $id, $pass));
            break;
    
    case 'admin-panel':
        include 'html/admin-panel.html';
        break;
    
    case 'admin-dashboard':
        include 'html/admin-dashboard.html';
        break;
    
    case 'search-student':
        search_student($connection, $_GET['q'], $limit = 10);
        break;
    
    case 'admin-view-student':
        include 'html/admin-view-student.html';
        break;

    case 'admin-view-student-profile':
        include 'html/admin-view-student-profile.html';
        break;
    
    case 'create-promotional-exam':
        include 'html/create-promotional-exam.html';
        break;

    case 'admin-view-student':
        include 'html/admin-view-student.html';
        break;
    
    case 'admin-view-student':
        include 'html/admin-view-student.html';
        break;

    default:
        http_response_code(404);
        echo "Page not found";
        break;
}

$connection->close();
?>
