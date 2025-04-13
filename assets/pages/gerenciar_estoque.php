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

// Verifica se o formulário de busca foi enviado
$busca = isset($_POST['busca']) ? trim($_POST['busca']) : '';
$ingredientes_lista = $ingrediente->buscarPorDescricao($busca); // Função que busca ingredientes pela descrição

// Verifica se o formulário de atualização foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['atualizar_estoque'])) {
    $ingredientes_atualizados = $_POST['ingredientes'];

    foreach ($ingredientes_atualizados as $id => $quantidade) {
        $ingrediente->id = $id;
        $ingrediente->quantidade_estoque = $quantidade;
        $ingrediente->atualizarEstoque(); // Função para atualizar o estoque do ingrediente
    }
    $mensagem = "Estoque atualizado com sucesso!";

}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Estoque de Ingredientes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .usuario-logado {
            float: right;
            margin-right: 20px;
            background-color: #4CAF50;
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

        .table-responsive {
            margin-top: 20px;
        }

        .form-section {
            margin-top: 20px;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var mensagem = "<?php echo $mensagem; ?>";
            if (mensagem !== "") {
                alert(mensagem);
            }
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

    <main class="container mt-5">
        <section class="form-section">
            <h2 class="text-center">Gerenciar Estoque de Ingredientes</h2>

            <form method="POST" action="gerenciar_estoque.php" class="form-inline justify-content-center mb-4">
                <div class="form-group mx-sm-3">
                    <input type="text" id="busca" name="busca" class="form-control" placeholder="Descrição ou parte da descrição"
                        value="<?php echo htmlspecialchars($busca); ?>">
                </div>
                <button type="submit" class="button">Buscar</button>
            </form>

            <?php if (!empty($ingredientes_lista)): ?>
                <form method="POST" action="gerenciar_estoque.php">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Descrição</th>
                                    <th>Quantidade em Estoque</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ingredientes_lista as $ing): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ing['descricao'] ?? ''); ?></td>
                                        <td>
                                            <input type="number" name="ingredientes[<?php echo $ing['id']; ?>]" 
                                                value="<?php echo htmlspecialchars($ing['quantidade_estoque']); ?>" 
                                                step="0.01" class="form-control" required>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" name="atualizar_estoque" class="btn btn-success mt-3">Atualizar Estoque</button>
                </form>
            <?php else: ?>
                <div class="alert alert-info text-center mt-4">Nenhum ingrediente encontrado.</div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 ZAYON - Todos os direitos reservados.</p>
    </footer>
</body>

</html>
