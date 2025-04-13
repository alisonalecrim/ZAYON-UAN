<?php

// Inclui a conexão com o banco de dados
include '../includes/database.php';

if (isset($_GET['data'])) {
    $data = $_GET['data'];

    $database = new Database();
    $db = $database->getConnection();

    $response = [
        'descricao' => '',
        'Café da manhã' => ['arranchamento' => 0],
        'Almoço' => ['arranchamento' => 0],
        'Lanche' => ['arranchamento' => 0],
        'Janta' => ['arranchamento' => 0],
        'Ceia' => ['arranchamento' => 0],
    ];

    try {
        // Busca a descrição da data
        $stmtDescricao = $db->prepare("SELECT descricao FROM cardapios WHERE data = :data");
        $stmtDescricao->bindParam(':data', $data);
        $stmtDescricao->execute();

        if ($stmtDescricao->rowCount() > 0) {
            $rowDescricao = $stmtDescricao->fetch(PDO::FETCH_ASSOC);
            $response['descricao'] = $rowDescricao['descricao'];
        }

        // Busca o arranchamento para cada tipo de refeição
        $stmtArranchamento = $db->prepare("SELECT tipo_refeicao, arranchamento FROM cardapios_refeicoes WHERE data = :data");
        $stmtArranchamento->bindParam(':data', $data);
        $stmtArranchamento->execute();

        while ($row = $stmtArranchamento->fetch(PDO::FETCH_ASSOC)) {
            $tipoRefeicao = trim($row['tipo_refeicao']);

            // Confirma se o tipo de refeição está na resposta para evitar erros de correspondência
            if (array_key_exists($tipoRefeicao, $response)) {
                $response[$tipoRefeicao]['arranchamento'] = $row['arranchamento'];
            }
        }

        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Erro ao buscar dados: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Data não especificada.']);
}
