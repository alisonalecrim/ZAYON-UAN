<?php
include '../includes/database.php';
include '../models/Fornecedor.php';

header('Content-Type: application/json');

if (!isset($_POST['cnpj']) || empty($_POST['cnpj'])) {
    echo json_encode(["success" => false, "message" => "CNPJ não informado."]);
    exit();
}

$cnpj = $_POST['cnpj'];

try {
    $database = new Database();
    $conn = $database->getConnection();
    $fornecedor = new Fornecedor($conn);

    $resultado = $fornecedor->buscarPorCNPJ($cnpj);

    if ($resultado) {
        echo json_encode(["success" => true, "data" => $resultado]);
    } else {
        echo json_encode(["success" => false, "message" => "Fornecedor não encontrado."]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Erro ao buscar fornecedor: " . $e->getMessage()]);
}
