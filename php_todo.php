<?php

header('Content-Type: application/json');

$todos = [];
$nextId = 1;

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));

switch ($method) {
    case 'GET':
        if (empty($request)) {
            echo json_encode(array_values($todos));
        } else {
            $id = intval($request[0]);
            if (isset($todos[$id])) {
                echo json_encode($todos[$id]);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Todo not found']);
            }
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['task'])) {
            global $nextId;
            $todo = [
                'id' => $nextId,
                'task' => $data['task'],
                'completed' => false
            ];
            $todos[$nextId] = $todo;
            $nextId++;
            http_response_code(201);
            echo json_encode($todo);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Task is required']);
        }
        break;
    case 'PUT':
        if (!empty($request)) {
            $id = intval($request[0]);
            if (isset($todos[$id])) {
                $data = json_decode(file_get_contents('php://input'), true);
                if (isset($data['task'])) {
                    $todos[$id]['task'] = $data['task'];
                }
                if (isset($data['completed'])) {
                     $todos[$id]['completed'] = $data['completed'];
                }
                echo json_encode($todos[$id]);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Todo not found']);
            }
        } else {
            http_response_code(400);
             echo json_encode(['message' => 'Id is required']);
        }
        break;
    case 'DELETE':
        if (!empty($request)) {
            $id = intval($request[0]);
            if (isset($todos[$id])) {
                unset($todos[$id]);
                echo json_encode(['message' => 'Todo deleted']);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Todo not found']);
            }
        } else {
              http_response_code(400);
             echo json_encode(['message' => 'Id is required']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

?>