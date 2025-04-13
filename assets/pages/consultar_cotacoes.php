<?php
session_start();
include '../includes/database.php';
include '../models/Cotacao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->getConnection();
$cotacao = new Cotacao($conn);

$registros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filtro'])) {
    $filtro = $_POST['filtro'] ?? '';
    if (!empty($filtro)) {
        $query = "SELECT * FROM cotacoes WHERE cnpj LIKE :filtro OR razao_social LIKE :filtro ORDER BY created_at DESC";
        $stmt = $conn->prepare($query);
        $param = "%" . $filtro . "%";
        $stmt->bindParam(':filtro', $param);
    } else {
        $query = "SELECT * FROM cotacoes ORDER BY created_at DESC";
        $stmt = $conn->prepare($query);
    }
    $stmt->execute();
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para buscar itens de cotações selecionadas
function buscarItensCotacoes($conn, $ids)
{
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $query = "SELECT ci.*, c.razao_social FROM cotacao_itens ci 
              JOIN cotacoes c ON ci.cotacao_id = c.id 
              WHERE ci.cotacao_id IN ($placeholders)";
    $stmt = $conn->prepare($query);
    $stmt->execute($ids);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Cotações - ZAYON</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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

    <main class="container mt-5">
        <h2 class="mb-4">Consultar Cotações</h2>

        <form method="POST" action="" class="mb-4">
            <div class="form-group">
                <label for="filtro">Digite o CNPJ ou parte da Razão Social:</label>
                <input type="text" class="form-control" id="filtro" name="filtro"
                    placeholder="Exemplo: 00.000.000/0001-00 ou Empresa X">
            </div>
            <button type="submit" class="button">Pesquisar</button>
        </form>

        <form method="POST" action="" id="cotacao-form">
            <?php if (!empty($registros)): ?>
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>ID</th>
                            <th>Data de Criação</th>
                            <th>CNPJ</th>
                            <th>Razão Social</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registros as $registro): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="cotacao_id[]" value="<?php echo $registro['id']; ?>">
                                </td>
                                <td><?php echo $registro['id']; ?></td>
                                <td><?php echo date("d/m/Y H:i:s", strtotime($registro['created_at'])); ?></td>
                                <td><?php echo $registro['cnpj']; ?></td>
                                <td><?php echo $registro['razao_social']; ?></td>
                                <td>
                                    <a href="abrir_cotacao.php?id=<?php echo $registro['id']; ?>"
                                        class="btn btn-info btn-sm">Abrir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="button" id="btn-comparar" class="button">Comparar Selecionados</button>
                <button type="submit" name="excluir" class="button">Excluir Selecionados</button>
            <?php else: ?>
                <div class="alert alert-info">Nenhum registro encontrado.</div>
            <?php endif; ?>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 ZAYON - Todos os direitos reservados.</p>
    </footer>

    <!-- Modal para Comparar Cotações -->
    <div class="modal fade" id="modalComparar" tabindex="-1" role="dialog" aria-labelledby="modalCompararLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCompararLabel">Comparar Cotações</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="conteudoComparacao">
                    <!-- Conteúdo gerado dinamicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="imprimirComparacao()">Imprimir</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Selecionar todos os checkboxes
        document.getElementById('select-all').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('input[name="cotacao_id[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });

        function imprimirComparacao() {
            const conteudo = document.getElementById('conteudoComparacao').innerHTML;
            const janelaImpressao = window.open('', '_blank');
            janelaImpressao.document.write(`
        <html>
            <head>
                <title>Comparação de Cotações</title>
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
            </head>
            <body>
                ${conteudo}
            </body>
        </html>
    `);
            janelaImpressao.document.close();
            janelaImpressao.print();
        }


        // Abrir modal de comparação
        document.getElementById('btn-comparar').addEventListener('click', function () {
            const selected = Array.from(document.querySelectorAll('input[name="cotacao_id[]"]:checked')).map(cb => cb.value);

            if (selected.length === 0) {
                alert('Selecione pelo menos uma cotação para comparar.');
                return;
            }

            fetch('comparar_cotacoes.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ cotacao_ids: selected })
            })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('conteudoComparacao').innerHTML = data;
                    $('#modalComparar').modal('show');
                })
                .catch(error => {
                    console.error('Erro ao buscar dados para comparação:', error);
                });
        });
    </script>
</body>

</html>