<?php  

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: assets/pages/login.php");
    exit();
}

// Inclui classes e outros
include'../includes/database.php';
include_once'../models/CardapioRefeicao.php';

// Função para gerar as datas e dias da semana no intervalo fornecido
function gerarDatas($inicio, $fim) {
    $diasSemana = ['Sunday' => 'Domingo', 'Monday' => 'Segunda-feira', 'Tuesday' => 'Terça-feira', 'Wednesday' => 'Quarta-feira', 'Thursday' => 'Quinta-feira', 'Friday' => 'Sexta-feira', 'Saturday' => 'Sábado'];
    $periodo = [];
    $dataInicio = new DateTime($inicio);
    $dataFim = new DateTime($fim);

    while ($dataInicio <= $dataFim) {
        $nomeDiaSemana = $dataInicio->format('l');
        $periodo[] = [
            'data' => $dataInicio->format('Y-m-d'),
            'dia_semana' => $diasSemana[$nomeDiaSemana]
        ];
        $dataInicio->modify('+1 day');
    }
    return $periodo;
}

// Inicializa a conexão com o banco de dados
$database = new Database();
$db = $database->getConnection();
$cardapioRefeicao = new CardapioRefeicao($db);

// Verifica se o formulário foi enviado para gerar a grade de datas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inicio'], $_POST['fim'])) {
    $inicio = $_POST['inicio'];
    $fim = $_POST['fim'];
    $datas = gerarDatas($inicio, $fim);

    // Consulta o banco de dados para buscar arranchamentos existentes para o intervalo de datas
    $arranchamentosExistentes = [];
    foreach ($datas as $data) {
        foreach (['Café da manhã', 'Almoço', 'Lanche', 'Janta', 'Ceia'] as $refeicao) {
            $arranchamentoExistente = $cardapioRefeicao->buscarArranchamento($data['data'], $refeicao);
            if ($arranchamentoExistente !== null) {
                $arranchamentosExistentes[$refeicao][$data['data']] = $arranchamentoExistente;
            }
        }
    }
} else {
    $datas = [];
    $arranchamentosExistentes = [];
}

// Verifica se o formulário de atualização foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['arranchamento'])) {
    foreach ($_POST['arranchamento'] as $refeicao => $quantidades) {
        foreach ($quantidades as $data => $quantidade) {
            $cardapioRefeicao->tipo_refeicao = $refeicao;
            $cardapioRefeicao->data = $data;
            $cardapioRefeicao->arranchamento = $quantidade;

            // Atualiza ou insere o arranchamento com o método carregarArranchamento()
            $cardapioRefeicao->carregarArranchamento();
        }
    }
    echo "<script>alert('Dados inseridos ou atualizados com sucesso!');</script>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Preenchimento de Grade de Arranchamento</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        table {
            margin: 20px auto;
        }

        .form-inline {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-inline label {
            margin-right: 5px;
        }

        .form-inline input[type="date"] {
            padding: 5px;
            font-size: 16px;
        }

        .form-inline button {
            padding: 5px 10px;
            font-size: 16px;
            cursor: pointer;
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
<div class="container">
    <h1>Preenchimento de Grade de Arranchamento</h1>
    <br>
    <!-- Formulário de seleção de período -->
    <form method="POST" class="form-inline">
        <label for="inicio">Data Início:</label>
        <input type="date" id="inicio" name="inicio" required>
        
        <label for="fim">Data Fim:</label>
        <input type="date" id="fim" name="fim" required>
        
        <button type="submit">Gerar Grade</button>
    </form>
    <br>
    <a href="../pages/upload_arranchamento.php">Ou carregue uma planilha padrão</a>
    <br>
    <?php if (!empty($datas)): ?>
        <!-- Tabela de preenchimento de arranchamento -->
        <form method="POST" action="">
            <input type="hidden" name="inicio" value="<?= $inicio ?>">
            <input type="hidden" name="fim" value="<?= $fim ?>">

            <table border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th rowspan="2">Refeição</th>
                        <?php foreach ($datas as $data): ?>
                            <th><?= date('d/m/Y', strtotime($data['data'])) ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php foreach ($datas as $data): ?>
                            <th><?= ucfirst($data['dia_semana']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $refeicoes = ['Café da manhã', 'Almoço', 'Lanche', 'Janta', 'Ceia'];

                    foreach ($refeicoes as $refeicao): ?>
                        <tr>
                            <td><?= $refeicao ?></td>
                            <?php foreach ($datas as $data): ?>
                                <td>
                                    <input type="number" name="arranchamento[<?= $refeicao ?>][<?= $data['data'] ?>]" 
                                           min="0" style="width: 50px;"
                                           value="<?= $arranchamentosExistentes[$refeicao][$data['data']] ?? '' ?>">
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit">Atualizar</button>
        </form>
    <?php endif; ?>
</div>
</main>
<footer>
    <p>&copy; 2024 ZAYON - Todos os direitos reservados.</p>
</footer>
</body>
</html>
                                