<?php
include '../includes/database.php';
include '../models/Cardapio.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['ids']) || !isset($data['novaDescricao'])) {
    echo json_encode(["success" => false, "message" => "Dados incompletos."]);
    exit();
}

$ids = $data['ids'];
$novaDescricao = $data['novaDescricao'];

try {
    $database = new Database();
    $conn = $database->getConnection();
    $cardapio = new Cardapio($conn);

    foreach ($ids as $id) {
        $detalhes = $cardapio->buscarPorId($id);
        if ($detalhes) {
            $detalhes['descricao'] = $novaDescricao; // Atualiza a descrição
            $cardapio->cadastrar($detalhes); // Insere como novo cardápio
        }
    }

    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
