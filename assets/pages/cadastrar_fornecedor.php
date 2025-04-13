<?php

session_start();

include '../includes/database.php';

include '../models/Fornecedor.php';



// Verifica se o usuário está logado

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");

    exit();

}



$database = new Database();

$conn = $database->getConnection();

$fornecedor = new Fornecedor($conn);



// Verifica se o formulário foi enviado

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fornecedor->razao_social = $_POST['razao_social'];

    $fornecedor->nome_fantasia = $_POST['nome_fantasia'];

    $fornecedor->cnpj = $_POST['cnpj'];

    $fornecedor->inscricao_estadual = $_POST['inscricao_estadual'];

    $fornecedor->telefone = $_POST['telefone'];

    $fornecedor->email = $_POST['email'];



    if ($fornecedor->cadastrar()) {

        $mensagem = "Fornecedor cadastrado com sucesso!";

    } else {

        $mensagem = "Erro ao cadastrar fornecedor.";

    }

}

?>



<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar Fornecedor - ZAYON</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>
        .usuario-logado {
            float: right;
            margin-right: 20px;
            background-color: #4CAF50;
            /* Fundo verde para destaque */
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
    <main class="container mt-5">

        <h2 class="mb-4">Cadastrar Fornecedor</h2>



        <?php if (isset($mensagem)): ?>

            <div class="alert alert-info"><?php echo $mensagem; ?></div>

        <?php endif; ?>



        <form method="POST" action="">

            <div class="mb-3">

                <label for="razao_social" class="form-label">Razão Social:</label>

                <input type="text" class="form-control" id="razao_social" name="razao_social" required>

            </div>

            <div class="mb-3">

                <label for="nome_fantasia" class="form-label">Nome Fantasia:</label>

                <input type="text" class="form-control" id="nome_fantasia" name="nome_fantasia">

            </div>

            <div class="mb-3">

                <label for="cnpj" class="form-label">CNPJ:</label>

                <input type="text" class="form-control" id="cnpj" name="cnpj" required>

            </div>

            <div class="mb-3">

                <label for="inscricao_estadual" class="form-label">Inscrição Estadual:</label>

                <input type="text" class="form-control" id="inscricao_estadual" name="inscricao_estadual">

            </div>

            <div class="mb-3">

                <label for="telefone" class="form-label">Telefone:</label>

                <input type="text" class="form-control" id="telefone" name="telefone" required>

            </div>

            <div class="mb-3">

                <label for="email" class="form-label">E-mail:</label>

                <input type="email" class="form-control" id="email" name="email" required>

            </div>

            <button type="submit" class="button">Cadastrar Fornecedor</button>

        </form>

    </main>
    <footer>

        <p>&copy; 2024 ZAYON - Todos os direitos reservados.</p>

    </footer>
</body>

</html>