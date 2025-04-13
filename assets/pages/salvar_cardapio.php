<?php
include '../includes/database.php';
include '../models/Cardapio.php';
include '../models/CardapioRefeicao.php';

header('Content-Type: application/json');
$input = json_decode(file_get_contents("php://input"), true);

$database = new Database();
$conn = $database->getConnection();

$cardapio = new Cardapio($conn);
$cardapioRefeicao = new CardapioRefeicao($conn);

$data = $input['data'];
$dia_semana = $input['dia_semana'];
$descricao = $input['descricao'];
$refeicoes = $input['refeicoes'];
$receitasRemovidas = $input['receitasRemovidas'] ?? [];
$cardapio_id = $input['cardapio_id'] ?? null;

if (!$cardapio_id) {
    echo json_encode(['success' => false, 'message' => 'ID do cardápio não fornecido.']);
    exit;
}

$cardapio->id = $cardapio_id;
$cardapioAtualizado = $cardapio->atualizarCardapio($data, $dia_semana, $descricao);

if (!$cardapioAtualizado) {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar o cardápio.']);
    exit;
}

// Remover receitas
foreach ($receitasRemovidas as $receita_id) {
    $cardapioRefeicao->excluirReceita($cardapio_id, $receita_id);
}

// Adicionar ou atualizar receitas
foreach ($refeicoes as $refeicao) {
    $tipo_refeicao = $refeicao['tipo'];
    $arranchamento = intval($refeicao['arranchamento']);

    foreach ($refeicao['receitas'] as $receita) {
        $receita_id = $receita['receita_id'];
        $cardapioRefeicao->atualizarOuInserirRefeicao($cardapio_id, $tipo_refeicao, $data, $arranchamento, $receita_id);
    }
}

echo json_encode(['success' => true]);
?>
