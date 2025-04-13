<?php



ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);





session_start();



// Verifica se o usuário está logado

if (!isset($_SESSION['user_id'])) {

    // Se não estiver logado, redireciona para a página de login

    header("Location: login.php");

    exit();

}



// Inclui o arquivo de conexão com o banco de dados e o modelo Fornecedor

include_once '../includes/database.php';

include_once '../models/Fornecedor.php';



$database = new Database();

$db = $database->getConnection();



$fornecedor = new Fornecedor($db);



// Verifica se o ID do fornecedor foi fornecido

if (!isset($_GET['id'])) {

    echo "ID do fornecedor não foi especificado.";

    exit();

}



// Carrega os dados do fornecedor pelo ID

$fornecedor->id = $_GET['id'];

$dados_fornecedor = $fornecedor->buscarPorId();



if (!$dados_fornecedor) {

    echo "Fornecedor não encontrado.";

    exit();

}



// Verifica se o formulário foi submetido

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Atualiza os dados do fornecedor com os valores fornecidos no formulário

    $fornecedor->razao_social = $_POST['razao_social'];

    $fornecedor->nome_fantasia = $_POST['nome_fantasia'];

    $fornecedor->cnpj = $_POST['cnpj'];

    $fornecedor->inscricao_estadual = $_POST['inscricao_estadual'];

    $fornecedor->telefone = $_POST['telefone'];

    $fornecedor->email = $_POST['email'];



    // Atualiza os dados no banco de dados

    if ($fornecedor->atualizar()) {

        echo "<script>

            $(document).ready(function() {

                $('#sucessoModal').modal('show');

            });

        </script>";

    } else {

        echo "<p class='mensagem erro'>Erro ao atualizar o fornecedor. Tente novamente.</p>";

    }

}



?>



<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Fornecedor - ZAYON</title>

    <link rel="stylesheet" href="../css/style.css">

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

            <h2>Editar Fornecedor</h2>

            <form method="POST">

                <label for="razao_social">Razão Social:</label>

                <input type="text" id="razao_social" name="razao_social" value="<?php echo htmlspecialchars($dados_fornecedor['razao_social']); ?>" required>



                <label for="nome_fantasia">Nome Fantasia:</label>

                <input type="text" id="nome_fantasia" name="nome_fantasia" value="<?php echo htmlspecialchars($dados_fornecedor['nome_fantasia']); ?>" >



                <label for="cnpj">CNPJ:</label>

                <input type="text" id="cnpj" name="cnpj" value="<?php echo htmlspecialchars($dados_fornecedor['cnpj']); ?>" required>



                <label for="inscricao_estadual">Inscrição Estadual:</label>

                <input type="text" id="inscricao_estadual" name="inscricao_estadual" value="<?php echo htmlspecialchars($dados_fornecedor['inscricao_estadual']); ?>">



                <label for="telefone">Telefone:</label>

                <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($dados_fornecedor['telefone']); ?>" required>



                <label for="email">E-mail:</label>

                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($dados_fornecedor['email']); ?>" required>



                <input type="submit" value="Salvar">

            </form>

        </section>

    </main>

    <footer>

        <p>&copy; 2024 ZAYON - Todos os direitos reservados.</p>

    </footer>

    <!-- Modal de Sucesso -->

<div class="modal fade" id="sucessoModal" tabindex="-1" role="dialog" aria-labelledby="sucessoModalLabel" aria-hidden="true">

  <div class="modal-dialog" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title" id="sucessoModalLabel">Sucesso</h5>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

        Fornecedor atualizado com sucesso!

      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="window.location.href='consultar_fornecedor.php'">OK</button>

      </div>

    </div>

  </div>

</div>

</body>

</html>

