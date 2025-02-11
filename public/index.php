<?php
require_once '../controllers/TesteCapitaniController.php';

$controller = new TesteCapitaniController();
$method = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];

// Configuração de CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

switch ($method) {
    case 'GET':
        if ($requestUri == '/consulta-demanda') {
            $controller->index();
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Método GET não permitido para esta rota."]);
        }
        break;
    
    case 'POST':
        if ($requestUri == '/consulta-demanda') {
            $controller->store();
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Método POST não permitido para esta rota."]);
        }
        break;

    case 'PUT':
        if (preg_match('/\/consulta-demanda\/(\d+)/', $requestUri, $matches)) {
            $codigo = (int)$matches[1]; // Validando o código como inteiro
            $controller->update($codigo);
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Método PUT não permitido para esta rota."]);
        }
        break;

    case 'DELETE':
        if ($requestUri == '/consulta-demanda') {
            $data = json_decode(file_get_contents("php://input"), true);
            $codigo = $data['codigo'] ?? null;
            if ($codigo) {
                $controller->destroy($codigo);
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Código da demanda não informado."]);
            }
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Método DELETE não permitido para esta rota."]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Rota não encontrada!"]);
}

