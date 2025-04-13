<?php

session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: pages/login.php");
    exit();
}

// Inclui a classe Database e outras classes relevantes
include '../includes/database.php';
include '../models/Receita.php';
include '../models/Cardapio.php';

$mensagem = "";

// Instancia a classe Database e obtém a conexão
$database = new Database();
$conn = $database->getConnection();

// Instancia as classes
$receita = new Receita($conn);
$cardapio = new Cardapio($conn);

// Busca todas as receitas disponíveis
$receitas_disponiveis = $receita->buscarTodos();

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST['data'];
    $dia_semana = $_POST['dia_semana'];
    $descricao = $_POST['descricao'];

    // Verifica se a data já existe na tabela cardapios
    $stmt = $conn->prepare("SELECT id FROM cardapios WHERE data = :data");
    $stmt->bindParam(':data', $data);
    $stmt->execute();
    $cardapio_id = $stmt->fetchColumn();

    if ($cardapio_id) {
        // Atualiza o cardápio existente com a nova descrição
        $stmt = $conn->prepare("UPDATE cardapios SET dia_semana = :dia_semana, descricao = :descricao WHERE id = :id");
        $stmt->bindParam(':id', $cardapio_id);
    } else {
        // Insere um novo cardápio se não existir
        $stmt = $conn->prepare("INSERT INTO cardapios (data, dia_semana, descricao) VALUES (:data, :dia_semana, :descricao)");
    }
    $stmt->bindParam(':data', $data);
    $stmt->bindParam(':dia_semana', $dia_semana);
    $stmt->bindParam(':descricao', $descricao);

    if ($stmt->execute()) {
        if (!$cardapio_id) {
            $cardapio_id = $conn->lastInsertId();
        }

        // Inserção ou atualização das refeições associadas com data, arranchamento e cardapio_id
        $refeicoes = ["cafe_manha" => "Café da manhã", "almoco" => "Almoço", "lanche" => "Lanche", "janta" => "Janta", "ceia" => "Ceia"];
        foreach ($refeicoes as $refeicao => $tipo_refeicao) {
            $arranchamento = $_POST[$refeicao . '_arranchamento'] ?? 0;

            // Verifica se o registro para o tipo de refeição e data já existe
            $stmtCheck = $conn->prepare("SELECT id FROM cardapios_refeicoes WHERE tipo_refeicao = :tipo_refeicao AND data = :data");
            $stmtCheck->bindParam(':tipo_refeicao', $tipo_refeicao);
            $stmtCheck->bindParam(':data', $data);
            $stmtCheck->execute();
            $refeicao_id = $stmtCheck->fetchColumn();

            if ($refeicao_id) {
                // Atualiza o registro existente e associa o cardapio_id
                $stmtUpdate = $conn->prepare("UPDATE cardapios_refeicoes SET arranchamento = :arranchamento, cardapio_id = :cardapio_id WHERE id = :id");
                $stmtUpdate->bindParam(':arranchamento', $arranchamento);
                $stmtUpdate->bindParam(':cardapio_id', $cardapio_id);
                $stmtUpdate->bindParam(':id', $refeicao_id);
                $stmtUpdate->execute();
            } else {
                // Insere um novo registro se não existir
                $stmtInsert = $conn->prepare("INSERT INTO cardapios_refeicoes (cardapio_id, tipo_refeicao, data, arranchamento) VALUES (:cardapio_id, :tipo_refeicao, :data, :arranchamento)");
                $stmtInsert->bindParam(':cardapio_id', $cardapio_id);
                $stmtInsert->bindParam(':tipo_refeicao', $tipo_refeicao);
                $stmtInsert->bindParam(':data', $data);
                $stmtInsert->bindParam(':arranchamento', $arranchamento);
                $stmtInsert->execute();
            }

            // Associa as receitas, se houverem, para a refeição
            if (!empty($_POST[$refeicao])) {
                foreach ($_POST[$refeicao] as $item) {
                    $receita_id = $item['item_id'];

                    // Verifica se a associação de receita já existe
                    $stmtCheckReceita = $conn->prepare("SELECT id FROM cardapios_refeicoes WHERE cardapio_id = :cardapio_id AND tipo_refeicao = :tipo_refeicao AND data = :data AND receita_id = :receita_id");
                    $stmtCheckReceita->bindParam(':cardapio_id', $cardapio_id);
                    $stmtCheckReceita->bindParam(':tipo_refeicao', $tipo_refeicao);
                    $stmtCheckReceita->bindParam(':data', $data);
                    $stmtCheckReceita->bindParam(':receita_id', $receita_id);
                    $stmtCheckReceita->execute();
                    $receitaAssociation = $stmtCheckReceita->fetchColumn();

                    if (!$receitaAssociation) {
                        // Insere uma nova associação de receita se não existir
                        $stmtInsertReceita = $conn->prepare("INSERT INTO cardapios_refeicoes (cardapio_id, tipo_refeicao, receita_id, data, arranchamento) VALUES (:cardapio_id, :tipo_refeicao, :receita_id, :data, :arranchamento)");
                        $stmtInsertReceita->bindParam(':cardapio_id', $cardapio_id);
                        $stmtInsertReceita->bindParam(':tipo_refeicao', $tipo_refeicao);
                        $stmtInsertReceita->bindParam(':receita_id', $receita_id);
                        $stmtInsertReceita->bindParam(':data', $data);
                        $stmtInsertReceita->bindParam(':arranchamento', $arranchamento);
                        $stmtInsertReceita->execute();
                    }
                }
            }
        }

        $mensagem = "Cardápio cadastrado com sucesso!";
    } else {
        $mensagem = "Erro ao cadastrar o cardápio.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Cardápio</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .refeicoes-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
        }
        .refeicao {
            flex: 1 1 20%;
            min-width: 250px;
            max-width: 250px;
            margin-bottom: 20px;
        }
        .refeicao table {
            width: 100%;
        }
        .adicionar-item {
            width: 100%;
            margin-top: 10px;
        }
        .usuario-logado {
            float: right;
            margin-right: 20px;
            background-color: #4CAF50; /* Fundo verde para destaque */
            padding: 10px;
            border-radius: 5px;
            color: white;
        }
        .usuario-logado a {
            color: #FFF;
            text-decoration: none;
            margin-left: 10px;
        }
        .usuario-logado a:hover {
            text-decoration: underline;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Adicionar receita
            $('.adicionar-item').click(function() {
                var refeicao = $(this).data('refeicao');
                var novaLinha = `
                <tr>
                    <td>
                        <select name="` + refeicao + `[][item_id]" required>
                            <option value="">Selecione a receita</option>
                            <?php foreach ($receitas_disponiveis as $rec): ?>
                                <option value="<?php echo $rec['id']; ?>"><?php echo $rec['nome_receita']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><button type="button" onclick="$(this).closest('tr').remove();">Remover</button></td>
                </tr>`;
                $('#' + refeicao + '-tabela tbody').append(novaLinha);
            });

            // Preenche o dia da semana ao selecionar a data e busca descrição e arranchamento
            $('#data').change(function() {
                const dataSelecionada = new Date($(this).val() + 'T00:00:00');
                const diasDaSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];

                if (!isNaN(dataSelecionada)) {
                    const diaSemana = diasDaSemana[dataSelecionada.getDay()];
                    $('#dia_semana').val(diaSemana);
                } else {
                    $('#dia_semana').val("");
                }

                // Faz a requisição para buscar descrição e arranchamento de cada refeição
                $.ajax({
                    url: '../pages/buscar_arranchamento.php',
                    type: 'GET',
                    data: { data: $(this).val() },
                    success: function(response) {
                        const dados = JSON.parse(response);
                        $('#descricao').val(dados.descricao || '');

                        // Preenche cada campo de arranchamento com os valores retornados
                        $('#cafe_manha-arranchamento').val(dados['Café da manhã'].arranchamento || 0);
                        $('#almoco-arranchamento').val(dados['Almoço'].arranchamento || 0);
                        $('#lanche-arranchamento').val(dados['Lanche'].arranchamento || 0);
                        $('#janta-arranchamento').val(dados['Janta'].arranchamento || 0);
                        $('#ceia-arranchamento').val(dados['Ceia'].arranchamento || 0);
                    },
                    error: function() {
                        alert('Erro ao buscar arranchamento.');
                    }
                });
            });

            // Exibe mensagem de feedback
            var mensagem = "<?php echo $mensagem; ?>";
            if (mensagem !== "") {
                alert(mensagem);
            }
        });
    </script>
    <style>
        .usuario-logado {
            float: right;
            margin-right: 20px;
            background-color: #4CAF50; /* Fundo verde para destaque */
            padding: 10px;
            border-radius: 5px;
            color: white;
        }
        .usuario-logado a {
            color: #FFF;
            text-decoration: none;
            margin-left: 10px;
        }
        .usuario-logado a:hover {
            text-decoration: underline;
        }
    </style>
    <!-- Adicionando Bootstrap ao projeto -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<header>
    <!-- Navbar do Bootstrap -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <!-- Logo do sistema -->
            <a class="navbar-brand text-white" href="#">ZAYON - Sistema de Gerenciamento de UAN</a>
            <!-- Botão para o menu aparecer em dispositivos móveis -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Links do menu -->
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav mr-auto">
                    <!-- Receitas -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownReceitas" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Receitas
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownReceitas">
                            <a class="dropdown-item" href="../pages/cadastrar_receita.php">Cadastrar Receita</a>
                            <a class="dropdown-item" href="../pages/consultar_receita.php">Consultar Receita</a>
                        </div>
                    </li>

                    <!-- Cardápios -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownCardapios" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Cardápios
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownCardapios">
                            <a class="dropdown-item" href="../pages/cadastrar_cardapio.php">Cadastrar Cardápio</a>
                            <a class="dropdown-item" href="../pages/consultar_cardapio.php">Consultar Cardápio</a>
                            <a class="dropdown-item" href="../pages/processa_arranchamento.php">Arranchamento</a>
                            <a class="dropdown-item" href="../pages/relatorio_provisao.php">Relatório de Provisão</a>
                        </div>
                    </li>

                    <!-- Ingredientes -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownIngredientes" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Ingredientes
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownIngredientes">
                            <a class="dropdown-item" href="../pages/cadastrar_ingrediente.php">Cadastrar Ingrediente</a>
                            <a class="dropdown-item" href="../pages/consultar_ingrediente.php">Consultar Ingrediente</a>
                        </div>
                    </li>

                    <!-- Estoque -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownEstoque" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Estoque
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownEstoque">
                            <a class="dropdown-item" href="../pages/gerenciar_estoque.php">Gerenciar Estoque</a>
                        </div>
                    </li>

                    <!-- Fornecedores -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownFornecedores" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Fornecedores
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownFornecedores">
                            <a class="dropdown-item" href="../pages/cadastrar_fornecedor.php">Cadastrar Fornecedor</a>
                            <a class="dropdown-item" href="../pages/consultar_fornecedor.php">Consultar Fornecedor</a>
                            <a class="dropdown-item" href="../pages/cotacao.php">Cotação</a>
                            <a class="dropdown-item" href="../pages/consultar_cotacoes.php">Consultar Cotações</a>
                        </div>
                    </li>
                </ul>
                <!-- Área do usuário logado -->
                <span class="navbar-text usuario-logado">
                    Bem-vindo, <?php echo $_SESSION['username']; ?>!
                    <a href="../pages/logout.php" class="text-white ml-2">Sair</a>
                </span>
            </div>
        </div>
    </nav>
</header>
<main>
    <section class="form-section">
        <h2>Cadastrar Cardápio</h2>
        <form method="POST" action="cadastrar_cardapio.php">
            <label for="data">Data:</label>
            <input type="date" id="data" name="data" required>
            <br><br>
            <label for="dia_semana">Dia da Semana:</label>
            <input type="text" id="dia_semana" name="dia_semana" readonly>  
            <label for="descricao">Descrição:</label>
            <input type="text" id="descricao" name="descricao" required>
            <br><br>

            <!-- Refeições dispostas lado a lado -->
            <div class="refeicoes-container">
                <?php
                $refeicoes = ["Café da manhã" => "cafe_manha", "Almoço" => "almoco", "Lanche" => "lanche", "Janta" => "janta", "Ceia" => "ceia"];
                foreach ($refeicoes as $nomeRefeicao => $refeicaoId):
                ?>
                    <div class="refeicao">
                        <h3><?php echo $nomeRefeicao; ?></h3>
                        <label for="<?= $refeicaoId ?>-arranchamento">Arranchamento:</label>
                        <input type="number" id="<?= $refeicaoId ?>-arranchamento" name="<?= $refeicaoId ?>_arranchamento" min="0">
                        <table id="<?php echo $refeicaoId; ?>-tabela" border="1">
                            <thead>
                                <tr>
                                    <th>Receita</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <button type="button" class="adicionar-item" data-refeicao="<?php echo $refeicaoId; ?>">Adicionar Receita</button>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit">Salvar Cardápio</button>
        </form>
    </section>
</main>
<footer>
    <p>&copy; 2024 ZAYON - Todos os direitos reservados.</p>
</footer>
</body>
</html>
