<?php 
session_start();

// Inclui a classe e realiza a conexão com o banco de dados
include_once '../includes/database.php';
include_once '../models/Fornecedor.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: pages/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$fornecedor = new Fornecedor($db);

// Variáveis para armazenar resultados e critérios de busca
$resultados = [];
$nome_busca = '';
$cnpj_busca = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['excluir'])) {
        // Excluir fornecedores selecionados
        $fornecedor_ids = $_POST['fornecedor_ids'] ?? [];
        foreach ($fornecedor_ids as $fornecedor_id) {
            $fornecedor->excluir($fornecedor_id);
        }
    } else {
        // Buscar fornecedores
        $nome_busca = $_POST['nome'] ?? '';
        $cnpj_busca = $_POST['cnpj'] ?? '';
        $resultados = $fornecedor->buscarPorNomeOuCNPJ($nome_busca, $cnpj_busca);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Fornecedor</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function confirmarExclusao() {
            return confirm('Tem certeza que deseja excluir os fornecedores selecionados?');
        }
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
    <div class="container mt-5">
        <div class="form-section">
            <h2 class="text-center mb-4">Consultar Fornecedor</h2>
            <form method="POST" action="" class="form-inline justify-content-center mb-4">
                <div class="form-group mx-sm-3 mb-2">
                    <label for="nome" class="sr-only">Nome ou Parte do Nome</label>
                    <input type="text" id="nome" name="nome" class="form-control" placeholder="Nome do fornecedor" value="<?php echo htmlspecialchars($nome_busca); ?>">
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <label for="cnpj" class="sr-only">CNPJ</label>
                    <input type="text" id="cnpj" name="cnpj" class="form-control" placeholder="CNPJ" value="<?php echo htmlspecialchars($cnpj_busca); ?>">
                </div>
                <button type="submit" class="btn btn-success">Buscar Fornecedor</button>
            </form>

            <?php if (!empty($resultados)): ?>
                <h3 class="text-center">Resultados da Busca</h3>
                <form method="POST" action="" onsubmit="return confirmarExclusao();">
                    <!-- Adicionando um contêiner responsivo ao redor da tabela -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mt-4">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Selecionar</th>
                                    <th>Razão Social</th>
                                    <th>Nome Fantasia</th>
                                    <th>CNPJ</th>
                                    <th>Inscrição Estadual</th>
                                    <th>Telefone</th>
                                    <th>Email</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resultados as $fornecedor): ?>
                                    <tr>
                                        <td><input type="checkbox" name="fornecedor_ids[]" value="<?php echo $fornecedor['id']; ?>"></td>
                                        <td><?php echo htmlspecialchars($fornecedor['razao_social']); ?></td>
                                        <td><?php echo htmlspecialchars($fornecedor['nome_fantasia']); ?></td>
                                        <td><?php echo htmlspecialchars($fornecedor['cnpj']); ?></td>
                                        <td><?php echo htmlspecialchars($fornecedor['inscricao_estadual']); ?></td>
                                        <td><?php echo htmlspecialchars($fornecedor['telefone']); ?></td>
                                        <td><?php echo htmlspecialchars($fornecedor['email']); ?></td>
                                        <td><a href="../pages/editar_fornecedor.php?id=<?php echo $fornecedor['id']; ?>" class="btn btn-sm btn-warning">Editar</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" name="excluir" class="btn btn-danger mt-3">Excluir Selecionados</button>
                </form>
            <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <div class="alert alert-warning" role="alert">
                    Nenhum fornecedor encontrado para o critério especificado.
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<footer>
    <p>&copy; 2024 ZAYON - Todos os direitos reservados.</p>
</footer>
</body>
</html>
