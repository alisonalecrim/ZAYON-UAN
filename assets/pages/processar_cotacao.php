<?php

include '../includes/database.php';
include '../models/Cotacao.php';

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cotacao = new Cotacao($conn);

    $cotacao->cnpj = $_POST['cnpj'] ?? '';
    $cotacao->razao_social = $_POST['razao_social'] ?? '';
    $cotacao->telefone = $_POST['telefone'] ?? '';
    $cotacao->email = $_POST['email'] ?? '';
    $descricao = $_POST['descricao'] ?? [];
    $unidade = $_POST['unidade'] ?? [];
    $preco = $_POST['preco'] ?? [];

    if (empty($descricao) || empty($unidade) || empty($preco)) {
        echo "Nenhum item de cotação foi enviado.";
        exit();
    }

    foreach ($descricao as $index => $desc) {
        $cotacao->itens[] = [
            'descricao' => $desc,
            'unidade' => $unidade[$index],
            'preco' => $preco[$index]
        ];
    }

    try {
        $cotacao->cadastrar();
        echo "Cotação enviada com sucesso!";
    } catch (Exception $e) {
        echo "Erro ao salvar a cotação: " . $e->getMessage();
    }
} else {
    echo "Requisição inválida.";
}
