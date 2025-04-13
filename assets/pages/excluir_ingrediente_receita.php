<?php
// Inclui a classe Database e a classe Receita
include_once '../includes/database.php';
include '../models/Receita.php';

// Verifica se o método é POST e se os dados necessários foram passados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ingrediente_id = $_POST['ingrediente_id'];
    $receita_id = $_POST['receita_id'];

    // Verifica se ambos os valores foram fornecidos
    if ($ingrediente_id && $receita_id) {
        // Instancia a classe Database e obtém a conexão
        $database = new Database();
        $conn = $database->getConnection();

        // Instancia a classe Receita
        $receita = new Receita($conn);
        $receita->id = $receita_id;

        // Chama o método para excluir o ingrediente da receita
        if ($receita->excluirIngrediente($ingrediente_id)) {
            echo "Ingrediente excluído com sucesso!";
        } else {
            http_response_code(500);
            echo "Erro ao excluir o ingrediente.";
        }
    } else {
        http_response_code(400);
        echo "Dados insuficientes fornecidos.";
    }
} else {
    http_response_code(405);
    echo "Método não permitido.";
}
