<?php 



session_start();



// Verifica se o usuário está logado

if (!isset($_SESSION['user_id'])) {

    // Se não estiver logado, redireciona para a página de login

    header("Location: assets/pages/login.php");

    exit();

}



// Inclui a classe Database e a classe Ingrediente

include '../includes/database.php';

include '../models/Ingrediente.php';



$mensagem = "";



// Instancia a classe Database e obtém a conexão

$database = new Database();

$conn = $database->getConnection();



// Instancia a classe Ingrediente

$ingrediente = new Ingrediente($conn);



// Verifica se o ID do ingrediente foi passado na URL

if (isset($_GET['id'])) {

    $ingrediente->id = $_GET['id'];



    // Busca os dados do ingrediente a ser editado

    $dados_ingrediente = $ingrediente->buscarPorId();

    if (!$dados_ingrediente) {

        $mensagem = "Ingrediente não encontrado!";

    } else {

        // Preenche os campos com os dados do ingrediente

        $ingrediente->descricao = $dados_ingrediente['descricao'];

        $ingrediente->unidade_medida = $dados_ingrediente['unidade_medida'];

        $ingrediente->custo = $dados_ingrediente['custo'];

        $ingrediente->porcao_individual = $dados_ingrediente['porcao_individual'];

        $ingrediente->quantidade_estoque = $dados_ingrediente['quantidade_estoque'];

    }

}



// Verifica se o formulário foi enviado

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ingrediente->id = $_POST['ingrediente_id'];

    $ingrediente->descricao = trim($_POST['descricao']);

    $ingrediente->unidade_medida = trim($_POST['unidade_medida']);

    $ingrediente->custo = trim($_POST['custo']);

    $ingrediente->porcao_individual = trim($_POST['porcao_individual']);

    $ingrediente->quantidade_estoque = trim($_POST['quantidade_estoque']);



    // Atualiza os dados do ingrediente

    if ($ingrediente->atualizar()) {

        $mensagem = "Ingrediente atualizado com sucesso!";

    } else {

        $mensagem = "Erro ao atualizar o ingrediente.";

    }

}

?>



<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Ingrediente</title>

    <link rel="stylesheet" href="../css/style.css">

    <script>

        // Exibe uma mensagem em popup se houver

        document.addEventListener("DOMContentLoaded", function() {

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

        <h2>Editar Ingrediente</h2>



        <form method="POST" action="editar_ingrediente.php?id=<?php echo $ingrediente->id; ?>">

            <input type="hidden" name="ingrediente_id" value="<?php echo $ingrediente->id; ?>">



            <label for="descricao">Descrição:</label>

            <input type="text" id="descricao" name="descricao" value="<?php echo htmlspecialchars($ingrediente->descricao ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>



            <label for="unidade_medida">Unidade de Medida:</label>

            <select id="unidade_medida" name="unidade_medida" required>

                <option value="Kg" <?php if ($ingrediente->unidade_medida == "Kg") echo 'selected'; ?>>Kg</option>

                <option value="L" <?php if ($ingrediente->unidade_medida == "L") echo 'selected'; ?>>L</option>

                <option value="Un" <?php if ($ingrediente->unidade_medida == "Un") echo 'selected'; ?>>Un</option>

            </select>



            <label for="custo">Custo (R$):</label>

            <input type="number" id="custo" name="custo" step="0.01" value="<?php echo number_format($ingrediente->custo ?? 0, 2, '.', ''); ?>" required>



            <label for="porcao_individual">Porção Individual (g/ml/un):</label>

            <input type="number" id="porcao_individual" name="porcao_individual" step="0.01" value="<?php echo number_format($ingrediente->porcao_individual ?? 0, 2, '.', ''); ?>" required>



            <label for="quantidade_estoque">Quantidade em Estoque:</label>

            <input type="number" id="quantidade_estoque" name="quantidade_estoque" step="0.01" value="<?php echo number_format($ingrediente->quantidade_estoque ?? 0, 2, '.', ''); ?>" required>



            <input type="submit" value="Atualizar Ingrediente">

        </form>

    </section>

</main>



<footer>

    <p>&copy; 2024 ZAYON - Todos os direitos reservados.</p>

</footer>



</body>

</html>

