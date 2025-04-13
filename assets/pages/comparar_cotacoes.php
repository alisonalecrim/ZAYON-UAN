<?php
include '../includes/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$cotacaoIds = $data['cotacao_ids'] ?? [];

if (empty($cotacaoIds)) {
    echo "Nenhuma cotação selecionada.";
    exit();
}

$database = new Database();
$conn = $database->getConnection();
$placeholders = implode(',', array_fill(0, count($cotacaoIds), '?'));

$query = "SELECT ci.descricao, ci.unidade, ci.preco, c.razao_social 
          FROM cotacao_itens ci 
          JOIN cotacoes c ON ci.cotacao_id = c.id 
          WHERE ci.cotacao_id IN ($placeholders)";
$stmt = $conn->prepare($query);
$stmt->execute($cotacaoIds);

$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

$agrupados = [];
foreach ($itens as $item) {
    $agrupados[$item['descricao']][] = $item;
}

echo '<table class="table table-bordered">
        <thead>
            <tr>
                <th>Descrição</th>
                <th>Unidade de Medida</th>
                <th>Preço</th>
                <th>Fornecedor</th>
            </tr>
        </thead>
        <tbody>';

foreach ($agrupados as $descricao => $itens) {
    foreach ($itens as $item) {
        echo "<tr>
                <td>{$item['descricao']}</td>
                <td>{$item['unidade']}</td>
                <td>R$ " . number_format($item['preco'], 2, ',', '.') . "</td>
                <td>{$item['razao_social']}</td>
              </tr>";
    }
}

echo '</tbody></table>';
