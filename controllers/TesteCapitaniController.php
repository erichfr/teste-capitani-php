<?php
require_once '../config/config.php';

class TesteCapitaniController {
    private $baseUrl;
    private $usuario;
    private $password;

    public function __construct() {
        $this->baseUrl = BASE_URL;
        $this->usuario = API_USER;
        $this->password = API_PASS;
    }

    public function index() 
    {
        $tipo = $_GET['TIPO'] ?? null;
        $info = $_GET['INFO'] ?? null;

        // Validar os parâmetros de entrada antes de fazer a requisição
        if (!in_array($tipo, ['1', '2'])) {
            $_SESSION['errorMessage'] = "O campo Tipo deve ser 1 (Código) ou 2 (Descrição).";
            $demandas = [];
            require_once '../views/demanda_list.php';
            return;
        }

        if ($tipo == '1' && !preg_match('/^\d{1,3}$/', $info)) {
            $_SESSION['errorMessage'] = "O campo Informação deve conter apenas números (máx. 3 caracteres) quando o Tipo for 1.";
            $demandas = [];
            require_once '../views/demanda_list.php';
            return;
        }

        if ($tipo == '2' && !preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $info)) {
            $_SESSION['errorMessage'] = "O campo Informação deve conter apenas letras quando o Tipo for 2.";
            $demandas = [];
            require_once '../views/demanda_list.php';
            return;
        }

        $ch = curl_init("{$this->baseUrl}?TIPO={$tipo}&INFO=" . urlencode($info));
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->usuario}:{$this->password}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $_SESSION['errorMessage'] = "Erro ao conectar à API.";
            $demandas = [];
        } else {
            $demandas = json_decode($response, true);

            if ($demandas === null || !is_array($demandas)) {
                $demandas = [];
                $_SESSION['errorMessage'] = "Erro ao processar a resposta da API.";
            }
        }

        curl_close($ch);

        if (empty($demandas)) {
            $_SESSION['errorMessage'] = "Demanda não encontrada.";
        }

        require_once '../views/demanda_list.php';
    }


    public function store() {

        $errors = [];
    
        if (empty($_POST['descricao'])) $errors[] = "O campo 'descricao' é obrigatório.";
        if (empty($_POST['descriweb'])) $errors[] = "O campo 'descriweb' é obrigatório.";
        if (!isset($_POST['tipo']) || !in_array($_POST['tipo'], ['1', '2'])) $errors[] = "O campo 'tipo' deve ser 1 ou 2.";
        if (empty($_POST['grupo'])) $errors[] = "O campo 'grupo' é obrigatório.";
        if (empty($_POST['area'])) $errors[] = "O campo 'area' é obrigatório.";
        if (!isset($_POST['ativo']) || !in_array($_POST['ativo'], ['0', '1'])) $errors[] = "O campo 'ativo' deve ser 0 ou 1.";
        if (!isset($_POST['atendimento']) || !in_array($_POST['atendimento'], ['0', '1'])) $errors[] = "O campo 'atendimento' deve ser 0 ou 1.";
        if (!isset($_POST['prazo']) || !ctype_digit($_POST['prazo']) || $_POST['prazo'] < 1) $errors[] = "O campo 'prazo' deve ser um número inteiro maior que 0.";
    
        if (!empty($errors)) {
            $_SESSION['errorMessage'] = implode("<br>", $errors);
            header("Location: /consulta-demanda");
            exit;
        }
    
        $payload = [
            'descricao'   => $_POST['descricao'],
            'descriweb'   => $_POST['descriweb'],
            'tipo'        => $_POST['tipo'],
            'grupo'       => $_POST['grupo'],
            'area'        => $_POST['area'],
            'ativo'       => $_POST['ativo'],
            'atendimento' => $_POST['atendimento'],
            'prazo'       => (int) $_POST['prazo']
        ];
    
        try {
            $ch = curl_init($this->baseUrl);
            curl_setopt($ch, CURLOPT_USERPWD, "{$this->usuario}:{$this->password}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
    
            if ($httpCode >= 200 && $httpCode < 300) {
                $_SESSION['successMessage'] = "Demanda criada com sucesso!";
            } else {
                $_SESSION['errorMessage'] = "Erro ao criar demanda. Código HTTP: $httpCode. Resposta: " . $response;
            }
    
        } catch (Exception $e) {
            $_SESSION['errorMessage'] = "Erro ao conectar com a API: " . $e->getMessage();
        }
    
        header("Location: /consulta-demanda");
        exit;
    }

    public function update($codigo) {
        try {
            $_PUT = json_decode(file_get_contents("php://input"), true);
    
            if (!$codigo) {
                echo json_encode(["error" => "Erro: Código da demanda não informado."]);
                http_response_code(400);
                exit();
            }
    
            $payload = json_encode([
                'codigo' => $codigo,
                'descricao' => $_PUT['descricao'] ?? '',
                'descriweb' => $_PUT['descriweb'] ?? '',
                'tipo' => $_PUT['tipo'] ?? '',
                'grupo' => $_PUT['grupo'] ?? '',
                'area' => $_PUT['area'] ?? '',
                'ativo' => $_PUT['ativo'] ?? '',
                'atendimento' => $_PUT['atendimento'] ?? '',
                'prazo' => (int) ($_PUT['prazo'] ?? 0),
            ]);
    
            $ch = curl_init("{$this->baseUrl}"); 
    
            curl_setopt($ch, CURLOPT_USERPWD, "{$this->usuario}:{$this->password}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
    
            if ($http_code >= 400) {
                echo json_encode(["error" => "Erro ao atualizar a demanda: " . ($response ?: "Erro desconhecido.")]);
                http_response_code(400);
                exit();
            }
            $_SESSION['successMessage'] = "Demanda atualizada com sucesso!";
            echo json_encode(["success" => "Demanda atualizada com sucesso!"]);
            exit();
        } catch (Exception $e) {
            echo json_encode(["error" => "Erro inesperado: " . $e->getMessage()]);
            http_response_code(500);
            exit();
        }
    }
    
    public function destroy($codigo) {
        try {
            if (!$codigo) {
                echo json_encode(["error" => "Erro: Código da demanda não informado."]);
                http_response_code(400);
                exit();
            }
    
            $payload = json_encode(["codigo" => $codigo]);
    
            $ch = curl_init($this->baseUrl); 
            curl_setopt($ch, CURLOPT_USERPWD, "{$this->usuario}:{$this->password}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload); 

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);
    
            if ($curl_error) {
                echo json_encode(["error" => "Erro no cURL: " . $curl_error]);
                http_response_code(500);
                exit();
            }
    
            if ($http_code >= 400) {
                echo json_encode(["error" => "Erro ao excluir a demanda: " . $response]);
                http_response_code(400);
                exit();
            }
    
            echo json_encode(["success" => "Demanda excluída com sucesso!"]);
            exit();
        } catch (Exception $e) {
            echo json_encode(["error" => "Erro inesperado: " . $e->getMessage()]);
            http_response_code(500);
            exit();
        }
    }    
}
