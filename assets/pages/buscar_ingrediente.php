<?php
include '../includes/database.php';
include '../models/Ingrediente.php';

// Conectar ao banco de dados
$database = new Database();
$conn = $database->getConnection();

// Instanciar a classe Ingrediente
$ingrediente = new Ingrediente($conn);

// Verifica se o ID do ingrediente foi passado
if (isset($_GET['id'])) {
    $ingrediente->id = $_GET['id'];
    $dados_ingrediente = $ingrediente->buscarPorId();

    if ($dados_ingrediente) {
        // Retorna os detalhes do ingrediente em formato JSON
        echo json_encode([
            'unidade_medida' => $dados_ingrediente['unidade_medida'],
            'custo' => $dados_ingrediente['custo']
        ]);
    } else {
        echo json_encode(['error' => 'Ingrediente não encontrado']);
    }
} else {
    echo json_encode(['error' => 'ID do ingrediente não informado']);
}
