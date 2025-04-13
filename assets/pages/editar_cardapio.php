<?php

session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: assets/pages/login.php");
    exit();
}

// Inclui as classes necessárias
include '../includes/database.php';
include '../models/Receita.php';
include '../models/Cardapio.php';
include '../models/CardapioRefeicao.php';

// Instancia a conexão com o banco de dados e as classes necessárias
$database = new Database();
$conn = $database->getConnection();
$receita = new Receita($conn);
$cardapio = new Cardapio($conn);
$cardapioRefeicao = new CardapioRefeicao($conn);

// Busca o cardápio pelo ID
if (isset($_GET['id'])) {
    $cardapio_id = $_GET['id'];
    $dados_cardapio = $cardapio->buscarPorId($cardapio_id);

    if ($dados_cardapio) {
        // Dados do cardápio
        $data = $dados_cardapio['data'];
        $dia_semana = $dados_cardapio['dia_semana'];
        $descricao = $dados_cardapio['descricao'];
        $receitas_disponiveis = $receita->buscarTodos();

        // Inicializa o array de refeições com valores padrão
        $tipos_refeicoes = [
            "cafe_manha" => "Café da manhã",
            "almoco" => "Almoço",
            "lanche" => "Lanche",
            "janta" => "Janta",
            "ceia" => "Ceia"
        ];
        $receitas_por_refeicao = [];

        // Garante que cada refeição tenha uma entrada no array, mesmo que esteja vazia
        foreach ($tipos_refeicoes as $refeicao_key => $tipo_refeicao) {
            $arranchamento = $cardapioRefeicao->buscarArranchamento($data, $tipo_refeicao) ?? 0;
            $receitas = $cardapioRefeicao->buscarReceitasPorCardapioDataTipo($cardapio_id, $data, $tipo_refeicao) ?? [];
            
            $receitas_por_refeicao[$refeicao_key] = [
                'arranchamento' => $arranchamento,
                'receitas' => $receitas
            ];
        }
    } else {
        echo "<script>alert('Cardápio não encontrado.');</script>";
        exit;
    }
} else {
    echo "<script>alert('ID do cardápio não fornecido.');</script>";
    exit;
}

// Atualiza o cardápio ao enviar o formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST['data'];
    $dia_semana = $_POST['dia_semana'];
    $descricao = $_POST['descricao'];
    $cardapio->id = $cardapio_id;

    $cardapioAtualizado = $cardapio->atualizarCardapio($data, $dia_semana, $descricao);

    foreach ($tipos_refeicoes as $refeicao_key => $tipo_refeicao) {
        $arranchamento = $_POST[$refeicao_key . '_arranchamento'] ?? 0;

        // Atualiza ou insere o arranchamento e associa as receitas da refeição
        $cardapioRefeicao->atualizarOuInserirRefeicao($cardapio_id, $tipo_refeicao, $data, $arranchamento, null);

        // Atualiza receitas associadas a essa refeição
        if (!empty($_POST[$refeicao_key])) {
            foreach ($_POST[$refeicao_key] as $receita) {
                $receita_id = $receita['item_id'];
                $cardapioRefeicao->atualizarOuInserirRefeicao($cardapio_id, $tipo_refeicao, $data, $arranchamento, $receita_id);
            }
        }
    }

    echo "<script>alert('Cardápio atualizado com sucesso!'); window.location.href = '../../index.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cardápio</title>
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
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
        });
    </script>
</head>
<body>
<header>
        <!-- Navbar do Bootstrap -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-success">
            <div class="container">
                <!-- Logo do sistema -->
                <a class="navbar-brand text-white" href="#">ZAYON - Sistema de Gerenciamento de UAN</a>
                <!-- Botão para o menu aparecer em dispositivos móveis -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                    aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Links do menu -->
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav mr-auto">
                        <!-- Receitas -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownReceitas"
                                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Receitas
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownReceitas">
                                <a class="dropdown-item" href="../pages/cadastrar_receita.php">Cadastrar Receita</a>
                                <a class="dropdown-item" href="../pages/consultar_receita.php">Consultar Receita</a>
                            </div>
                        </li>

                        <!-- Cardápios -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownCardapios"
                                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Cardápios
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownCardapios">
                                <a class="dropdown-item" href="../pages/cadastrar_cardapio.php">Cadastrar Cardápio</a>
                                <a class="dropdown-item" href="../pages/consultar_cardapio.php">Consultar Cardápio</a>
                                <a class="dropdown-item" href="../pages/processa_arranchamento.php">Arranchamento</a>
                                <a class="dropdown-item" href="../pages/relatorio_provisao.php">Relatório de
                                    Provisão</a>
                            </div>
                        </li>

                        <!-- Ingredientes -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownIngredientes"
                                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Ingredientes
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownIngredientes">
                                <a class="dropdown-item" href="../pages/cadastrar_ingrediente.php">Cadastrar
                                    Ingrediente</a>
                                <a class="dropdown-item" href="../pages/consultar_ingrediente.php">Consultar
                                    Ingrediente</a>
                            </div>
                        </li>

                        <!-- Estoque -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownEstoque"
                                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Estoque
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownEstoque">
                                <a class="dropdown-item" href="../pages/gerenciar_estoque.php">Gerenciar Estoque</a>
                            </div>
                        </li>

                        <!-- Fornecedores -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownFornecedores"
                                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Fornecedores
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownFornecedores">
                                <a class="dropdown-item" href="../pages/cadastrar_fornecedor.php">Cadastrar
                                    Fornecedor</a>
                                <a class="dropdown-item" href="../pages/consultar_fornecedor.php">Consultar
                                    Fornecedor</a>
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
        <h2>Editar Cardápio</h2>
        <form method="POST">
            <label for="data">Data:</label>
            <input type="date" id="data" name="data" value="<?php echo $data; ?>" required>
            <br><br>

            <label for="dia_semana">Dia da Semana:</label>
            <input type="text" id="dia_semana" name="dia_semana" value="<?php echo $dia_semana; ?>" readonly>
            <br><br>

            <label for="descricao">Descrição:</label>
            <input type="text" id="descricao" name="descricao" value="<?php echo $descricao; ?>" required>
            <br><br>

            <!-- Exibir refeições com arranchamento e receitas lado a lado -->
            <div class="refeicoes-container">
                <?php foreach ($tipos_refeicoes as $refeicao_key => $tipo_refeicao): ?>
                    <div class="refeicao">
                        <h3><?php echo $tipo_refeicao; ?></h3>
                        <label for="<?= $refeicao_key ?>-arranchamento">Arranchamento:</label>
                        <input type="number" id="<?= $refeicao_key ?>-arranchamento" name="<?= $refeicao_key ?>_arranchamento" value="<?php echo $receitas_por_refeicao[$refeicao_key]['arranchamento']; ?>" min="0">
                        <table id="<?php echo $refeicao_key; ?>-tabela" border="1">
                            <thead>
                                <tr>
                                    <th>Receita</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($receitas_por_refeicao[$refeicao_key]['receitas'] as $rec): ?>
                                    <tr>
                                        <td>
                                            <select name="<?= $refeicao_key ?>[][item_id]" required>
                                                <?php foreach ($receitas_disponiveis as $receita): ?>
                                                    <option value="<?php echo $receita['id']; ?>" <?php echo ($receita['id'] == $rec['receita_id']) ? 'selected' : ''; ?>>
                                                        <?php echo $receita['nome_receita']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><button type="button" onclick="$(this).closest('tr').remove();">Remover</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="button" class="adicionar-item" data-refeicao="<?= $refeicao_key ?>">Adicionar Receita</button>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit">Salvar Alterações</button>
        </form>
    </section>
</main>
<footer>
    <p>&copy; 2024 ZAYON - Todos os direitos reservados.</p>
</footer>
</body>
</html>
