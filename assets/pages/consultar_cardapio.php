<?php



session_start();



// Verifica se o usuário está logado

if (!isset($_SESSION['user_id'])) {

    // Se não estiver logado, redireciona para a página de login

    header("Location: assets/pages/login.php");

    exit();

}



// Inclui a classe Database e as classes Cardapio e Receita

include '../includes/database.php';

include '../models/Cardapio.php';

include '../models/Receita.php';



$mensagem = "";



// Instancia a classe Database e obtém a conexão

$database = new Database();

$conn = $database->getConnection();



// Instancia as classes Cardapio e Receita

$cardapioModel = new Cardapio($conn);

$receitaModel = new Receita($conn);



// Obtemos todas as descrições de cardápios para preencher a lista suspensa

$descricoesCardapio = [];

$stmt = $conn->prepare("SELECT DISTINCT descricao FROM cardapios ORDER BY descricao");

if ($stmt->execute()) {

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $descricoesCardapio[] = $row['descricao'];

    }

}



// Função para exibir as receitas de uma refeição específica sem mostrar o arranchamento

function exibirReceitas($refeicoes, $tipoRefeicao, $receitaModel)
{

    if (isset($refeicoes[$tipoRefeicao]) && !empty($refeicoes[$tipoRefeicao])) {

        $resultado = "";

        foreach ($refeicoes[$tipoRefeicao] as $receita) {

            $nomeReceita = $receitaModel->buscarNomePorId($receita['receita_id']);

            if (!empty($nomeReceita)) { // Adiciona somente se houver um nome válido

                $resultado .= htmlspecialchars($nomeReceita) . "<br>";

            }

        }

        return !empty($resultado) ? $resultado : 'Sem receitas cadastradas';

    }

    return 'Sem receitas cadastradas';

}





// Verifica se uma descrição foi selecionada para buscar o cardápio correspondente

if (isset($_POST['descricao'])) {

    $descricaoSelecionada = $_POST['descricao'];

    $cardapios = $cardapioModel->buscarPorDescricao($descricaoSelecionada);

} else {

    $descricaoSelecionada = '';

    $cardapios = [];

}



// Verifica se o botão de excluir foi clicado

if (isset($_POST['excluir_selecionados']) && isset($_POST['cardapios'])) {

    $cardapiosParaExcluir = $_POST['cardapios'];



    foreach ($cardapiosParaExcluir as $cardapio_id) {

        if ($cardapioModel->excluir($cardapio_id)) {

            $mensagem = "Cardápios excluídos com sucesso!";

        } else {

            $mensagem = "Erro ao excluir o cardápio.";

        }

    }

}

?>



<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Cardápio</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
        <h2 class="text-center">Consultar Cardápio</h2>

        <!-- Mensagem de feedback -->
        <?php if (!empty($mensagem)): ?>
            <div class="alert <?php echo (strpos($mensagem, 'Erro') !== false) ? 'alert-danger' : 'alert-success'; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <!-- Formulário de Pesquisa -->
        <form method="POST" action="consultar_cardapio.php" class="mb-4">
            <div class="form-group">
                <label for="descricao">Descrição do Cardápio:</label>
                <select id="descricao" name="descricao" class="form-control" required>
                    <option value="">Selecione uma descrição</option>
                    <?php foreach ($descricoesCardapio as $descricao): ?>
                        <option value="<?php echo htmlspecialchars($descricao); ?>" <?php echo ($descricao == $descricaoSelecionada) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($descricao); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Pesquisar</button>
        </form>

        <!-- Tabela de Cardápios -->
        <form method="POST" action="consultar_cardapio.php">
            <?php if (!empty($cardapios)): ?>
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>Data</th>
                            <th>Dia da Semana</th>
                            <th>Café da manhã</th>
                            <th>Almoço</th>
                            <th>Lanche</th>
                            <th>Janta</th>
                            <th>Ceia</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cardapios as $cardapio): ?>
                            <?php $refeicoes = $cardapioModel->buscarRefeicoesPorCardapio($cardapio->id); ?>
                            <tr>
                                <td><input type="checkbox" name="cardapios[]" value="<?php echo $cardapio->id; ?>"></td>
                                <td><?php echo date('d/m/Y', strtotime($cardapio->data)); ?></td>
                                <td><?php echo htmlspecialchars($cardapio->dia_semana); ?></td>
                                <td><?php echo exibirReceitas($refeicoes, 'Café da manhã', $receitaModel); ?></td>
                                <td><?php echo exibirReceitas($refeicoes, 'Almoço', $receitaModel); ?></td>
                                <td><?php echo exibirReceitas($refeicoes, 'Lanche', $receitaModel); ?></td>
                                <td><?php echo exibirReceitas($refeicoes, 'Janta', $receitaModel); ?></td>
                                <td><?php echo exibirReceitas($refeicoes, 'Ceia', $receitaModel); ?></td>
                                <td>
                                    <a href="editar_cardapio.php?id=<?php echo $cardapio->id; ?>"
                                        class="btn btn-info btn-sm">Editar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" id="excluir-selecionados" name="excluir_selecionados" class="btn btn-danger">Excluir Selecionados</button>
                <!-- <button type="button" class="btn btn-success" id="gerarCopiaBtn">Gerar Cópia</button> --> 
            <?php else: ?>
                <div class="alert alert-info">Nenhum cardápio encontrado para a descrição fornecida.</div>
            <?php endif; ?>
        </form>
    </main>

    <!-- Modal para Gerar Cópia -->
    <div class="modal fade" id="modalGerarCopia" tabindex="-1" role="dialog" aria-labelledby="modalGerarCopiaLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalGerarCopiaLabel">Gerar Cópia dos Cardápios</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Descrição</th>
                                <th>Data</th>
                                <th>Dia da Semana</th>
                            </tr>
                        </thead>
                        <tbody id="cardapiosSelecionados"></tbody>
                    </table>
                    <div class="form-group">
                        <label for="novaDescricao">Nova Descrição:</label>
                        <input type="text" class="form-control" id="novaDescricao" placeholder="Digite a nova descrição"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="criarCopiaBtn">Criar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("gerarCopiaBtn").addEventListener("click", function () {
            const selecionados = document.querySelectorAll('input[name="cardapios[]"]:checked');
            const tabela = document.getElementById("cardapiosSelecionados");

            if (selecionados.length === 0) {
                alert("Nenhum cardápio selecionado!");
                return;
            }

            tabela.innerHTML = "";
            selecionados.forEach(checkbox => {
                const row = checkbox.closest("tr");
                const id = row.querySelector("td:nth-child(2)").textContent.trim();
                const descricao = row.querySelector("td:nth-child(3)").textContent.trim();
                const data = row.querySelector("td:nth-child(1)").textContent.trim();
                const diaSemana = row.querySelector("td:nth-child(4)").textContent.trim();

                tabela.innerHTML += `
                    <tr>
                        <td>${id}</td>
                        <td>${descricao}</td>
                        <td>${data}</td>
                        <td>${diaSemana}</td>
                    </tr>
                `;
            });

            $("#modalGerarCopia").modal("show");
        });
    </script>

    <footer>
        <p>&copy; 2024 ZAYON - Todos os direitos reservados.</p>
    </footer>
</body>

</html>
