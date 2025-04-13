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

// Instancia a classe Database e obtém a conexão
$database = new Database();
$conn = $database->getConnection();

// Instancia a classe Ingrediente
$ingrediente = new Ingrediente($conn);

// Verifica se foi enviado um termo de busca
$busca = isset($_POST['busca']) ? $_POST['busca'] : '';
$itens_por_pagina = isset($_POST['itens_por_pagina']) ? (int) $_POST['itens_por_pagina'] : 10;

// Verifica se foi solicitado a exclusão de algum ingrediente
if (isset($_GET['excluir'])) {
    $ingrediente->id = $_GET['excluir'];
    if ($ingrediente->excluir()) {
        echo "<script>alert('Ingrediente excluído com sucesso!'); window.location.href='consultar_ingrediente.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir o ingrediente!');</script>";
    }
}

// Busca os ingredientes de acordo com o termo de busca
$resultado = $ingrediente->buscarTodos($busca);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Ingrediente</title>
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
    </style>
    <script>
        function confirmarExclusao(id) {
            if (confirm("Tem certeza que deseja excluir este ingrediente?")) {
                window.location.href = 'consultar_ingrediente.php?excluir=' + id;
            }
        }

        function selecionarTodos(source) {
            const checkboxes = document.getElementsByName('ingrediente_selecionado[]');
            checkboxes.forEach(checkbox => checkbox.checked = source.checked);
        }

        function exportarExcel() {
            const selecionados = document.querySelectorAll('input[name="ingrediente_selecionado[]"]:checked');
            if (selecionados.length === 0) {
                alert('Nenhum item selecionado para exportação.');
                return;
            }

            let csvContent = "data:text/csv;charset=utf-8,Código de Barras,Descrição,Unidade de Medida,Custo,Porção Individual\n";

            selecionados.forEach(function (checkbox) {
                const row = checkbox.closest('tr');
                const valores = Array.from(row.querySelectorAll('td')).slice(1, 6).map(td => td.textContent.trim());
                csvContent += valores.join(",") + "\n";
            });

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "ingredientes_selecionados.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function imprimirLista() {
            const selecionados = document.querySelectorAll('input[name="ingrediente_selecionado[]"]:checked');
            if (selecionados.length === 0) {
                alert('Nenhum item selecionado para impressão.');
                return;
            }

            let printContent = `
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Código de Barras</th>
                            <th>Descrição</th>
                            <th>Unidade de Medida</th>
                            <th>Custo</th>
                            <th>Porção Individual</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            selecionados.forEach(function (checkbox) {
                const row = checkbox.closest('tr');
                const valores = Array.from(row.querySelectorAll('td')).slice(1, 6).map(td => td.textContent.trim());
                printContent += `<tr>${valores.map(valor => `<td>${valor}</td>`).join('')}</tr>`;
            });

            printContent += "</tbody></table>";

            const originalContent = document.body.innerHTML;
            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;
        }
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
    <main class="container mt-5">
        <h2 class="text-center">Consultar Ingredientes</h2>

        <form method="POST" action="consultar_ingrediente.php" class="form-inline mb-3">
            <div class="form-group mx-sm-3 mb-2">
                <input type="text" name="busca" class="form-control" placeholder="Código de Barras ou Descrição"
                    value="<?php echo htmlspecialchars($busca ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <select name="itens_por_pagina" class="form-control" onchange="this.form.submit()">
                    <option value="10" <?php if ($itens_por_pagina == 10)
                        echo 'selected'; ?>>10</option>
                    <option value="20" <?php if ($itens_por_pagina == 20)
                        echo 'selected'; ?>>20</option>
                    <option value="50" <?php if ($itens_por_pagina == 50)
                        echo 'selected'; ?>>50</option>
                    <option value="100" <?php if ($itens_por_pagina == 100)
                        echo 'selected'; ?>>100</option>
                </select>
            </div>
            <button type="submit" class="button">Buscar</button>
        </form>

        <div class="table-responsive">
            <form method="POST" action="consultar_ingrediente.php">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th><input type="checkbox" onclick="selecionarTodos(this)"></th>
                            <th>Código de Barras</th>
                            <th>Descrição</th>
                            <th>Unidade de Medida</th>
                            <th>Custo (R$)</th>
                            <th>Porção Individual</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($resultado) > 0): ?>
                            <?php foreach ($resultado as $ingrediente): ?>
                                <tr>
                                    <td><input type="checkbox" name="ingrediente_selecionado[]"
                                            value="<?php echo $ingrediente['id']; ?>"></td>
                                    <td><?php echo htmlspecialchars($ingrediente['codigo_barras'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($ingrediente['descricao'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($ingrediente['unidade_medida'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td><?php echo number_format($ingrediente['custo'] ?? 0, 2, ',', '.'); ?></td>
                                    <td><?php echo number_format($ingrediente['porcao_individual'] ?? 0, 2, ',', '.'); ?></td>
                                    <td>
                                        <a href="editar_ingrediente.php?id=<?php echo $ingrediente['id']; ?>"
                                            class="btn btn-info btn-sm">Editar</a>
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="confirmarExclusao(<?php echo $ingrediente['id']; ?>)">Excluir</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Nenhum ingrediente encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 ZAYON - Todos os direitos reservados.</p>
    </footer>
</body>

</html>