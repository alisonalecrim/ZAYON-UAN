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

include '../models/Cardapio.php';

include '../models/CardapioRefeicao.php';

include '../models/Ingrediente.php';



// Instancia a conexão com o banco de dados

$database = new Database();

$conn = $database->getConnection();



// Verifica se a conexão foi estabelecida

if ($conn === null) {

    die("Erro ao conectar ao banco de dados.");

}



// Inicializa os resultados

$resultados = null;



// Verifica se o formulário foi enviado

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $descricao_cardapio = $_POST['descricao_cardapio'];



    // Consulta para buscar ingredientes, porção individual, unidade de medida, soma do arranchamento

    // e o cálculo entre porção individual e soma do arranchamento

    $queryCardapio = "

        SELECT 

            i.descricao AS ingrediente, 

            i.unidade_medida AS unidade_medida,

            i.porcao_individual, 

            SUM(cr.arranchamento) AS soma_arranchamento,

            (i.porcao_individual * SUM(cr.arranchamento)) AS total_provisao

        FROM cardapios c

        INNER JOIN cardapios_refeicoes cr ON c.id = cr.cardapio_id

        INNER JOIN receitas r ON cr.receita_id = r.id

        INNER JOIN receita_ingredientes ri ON r.id = ri.receita_id

        INNER JOIN ingredientes i ON ri.ingrediente_id = i.id

        WHERE c.descricao = :descricao_cardapio

        GROUP BY i.id, i.descricao, i.porcao_individual, i.unidade_medida

    ";



    $stmt = $conn->prepare($queryCardapio);

    $stmt->bindParam(':descricao_cardapio', $descricao_cardapio);

    $stmt->execute();

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

}

?>



<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Relatório de Provisão de Cardápios</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>

        /* Estilos para impressão */

        @media print {

            body * {

                visibility: hidden; /* Esconde tudo no corpo da página */

            }

            section, section * {

                visibility: visible; /* Mostra apenas a seção e seu conteúdo */

            }

            section label {

                display: none; /* Esconde o <h2> */

            }

            section input {

                display: none; /* Esconde o <h2> */

            }

            section h1 {

                display: none; /* Esconde o <h2> */

            }

            section button {

                display: none; /* Esconde o <h2> */

            }

            section {

                position: absolute;

                top: 0;

                left: 0;

                width: 100%;

            }

            .print-button {

                display: none; /* Esconde o botão de impressão na impressão */

            }

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

    <script>

        // Função para imprimir a página

        function imprimirPagina() {

            window.print();

        }

    </script>
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

    <h2>Relatório de Provisão de Cardápio</h2>

    <form method="post" action="">

        <label for="descricao_cardapio">Descrição do Cardápio:</label>

        <input type="text" id="descricao_cardapio" name="descricao_cardapio" required>

        <button type="submit">Consultar</button>

    </form>



    <?php if (isset($resultados)): ?>

        <h1>Resultados</h1>

        <?php if (count($resultados) > 0): ?>

            <table border="1">

                <thead>

                    <tr>

                        <th>Ingrediente</th>

                        <th>Unidade de Medida</th>

                        <th>Porção Individual</th>

                        <th>Soma do Arranchamento</th>

                        <th>Total (Porção x Arranchamento)</th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($resultados as $linha): ?>

                        <tr>

                            <td><?= htmlspecialchars($linha['ingrediente']) ?></td>

                            <td><?= htmlspecialchars($linha['unidade_medida']) ?></td>

                            <td><?= htmlspecialchars($linha['porcao_individual']) ?></td>

                            <td><?= htmlspecialchars($linha['soma_arranchamento']) ?></td>

                            <td><?= htmlspecialchars($linha['total_provisao']) ?></td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table><br>

            <!-- Botão para imprimir a página -->

            <button class="print-button" onclick="imprimirPagina()">Imprimir</button>

        <?php else: ?>

            <p>Nenhum ingrediente encontrado para esse cardápio.</p>

        <?php endif; ?>

    <?php endif; ?>

</section>
</main>
<footer>
    <p>&copy; 2024 ZAYON - Todos os direitos reservados.</p>
</footer>
</body>

</html>

