<?php



session_start();



// Verifica se o usuário está logado

if (!isset($_SESSION['user_id'])) {

    // Se não estiver logado, redireciona para a página de login

    header("Location: assets/pages/login.php");

    exit();

}



// Inclui a classe Database e a classe Receita

include '../includes/database.php';

include '../models/Receita.php';

include '../models/Ingrediente.php';



$mensagem = "";



// Instancia a classe Database e obtém a conexão

$database = new Database();

$conn = $database->getConnection();



// Instancia a classe Receita e Ingrediente

$receita = new Receita($conn);

$ingrediente = new Ingrediente($conn);



// Verifica se o ID da receita foi passado na URL

if (isset($_GET['id'])) {

    $receita->id = $_GET['id'];



    // Verifica se o formulário foi enviado

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Captura os dados atualizados da receita

        $receita->nome_receita = trim($_POST['nome_receita']);

        $receita->classificacao_custo = $_POST['classificacao_custo'];

        $receita->modo_preparo = $_POST['modo_preparo'];

        $receita->producao = $_POST['producao'];



        // Atualiza a receita

        if ($receita->atualizar()) {

            // Processa os ingredientes novos ou atualizados

            if (isset($_POST['ingredientes_novos']) && is_array($_POST['ingredientes_novos'])) {

                foreach ($_POST['ingredientes_novos'] as $ingrediente_novo) {

                    if (!empty($ingrediente_novo['ingrediente_id'])) {

                        $ingrediente_id = $ingrediente_novo['ingrediente_id'];

                        $quantidade = $ingrediente_novo['quantidade'] ?? 0;

                        $unidade_medida = $ingrediente_novo['unidade_medida'] ?? '';

                        $custo = $ingrediente_novo['custo'] ?? 0;

                        $receita->adicionarIngrediente($ingrediente_id, $quantidade, $unidade_medida, $custo);

                    }

                }

            }

            $mensagem = "Receita atualizada com sucesso!";

        } else {

            $mensagem = "Erro ao atualizar a receita.";

        }

    }



    // Busca a receita e seus ingredientes

    $dados_receita = $receita->buscarPorId();

    if ($dados_receita) {

        $receita->nome_receita = $dados_receita['nome_receita'];

        $receita->classificacao_custo = $dados_receita['classificacao_custo'];

        $receita->modo_preparo = $dados_receita['modo_preparo'];

        $receita->producao = $dados_receita['producao'];

        $ingredientes_receita = $receita->buscarIngredientes();

    } else {

        $mensagem = "Receita não encontrada!";

    }

} else {

    $mensagem = "ID da receita não foi fornecido.";

}



// Buscar todos os ingredientes disponíveis

$ingredientes_disponiveis = $ingrediente->buscarTodos();

?>



<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Receita</title>

    <link rel="stylesheet" href="../css/style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Adicionando Bootstrap ao projeto -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>

        $(document).ready(function() {

            // Função para adicionar uma nova linha editável na tabela

            $('#adicionar-ingrediente').click(function() {

                var novaLinha = `

                <tr>

                    <td>

                        <select class="ingrediente-selecionado" name="ingredientes_novos[][ingrediente_id]" required>

                            <option value="">Selecione o ingrediente</option>

                            <?php foreach ($ingredientes_disponiveis as $ing): ?>

                                <option value="<?php echo $ing['id']; ?>"><?php echo $ing['descricao']; ?></option>

                            <?php endforeach; ?>

                        </select>

                    </td>

                    <td><input type="number" name="ingredientes_novos[][quantidade]" placeholder="Quantidade" step="0.01" required></td>

                    <td><input type="text" class="unidade_medida" name="ingredientes_novos[][unidade_medida]" readonly></td>

                    <td><input type="number" class="custo" name="ingredientes_novos[][custo]" readonly></td>

                    <td><button type="button" onclick="$(this).closest('tr').remove();">Remover</button></td>

                </tr>`;

                $('#ingredientes-tabela tbody').append(novaLinha);

            });



            // Preenche automaticamente a unidade de medida e custo ao selecionar um ingrediente

            $('#ingredientes-tabela').on('change', '.ingrediente-selecionado', function() {

                var ingredienteId = $(this).val();

                var $parent = $(this).closest('tr');



                if (ingredienteId !== "") {

                    $.ajax({

                        url: 'buscar_ingrediente.php',

                        type: 'GET',

                        data: { id: ingredienteId },

                        dataType: 'json',

                        success: function(data) {

                            $parent.find('.unidade_medida').val(data.unidade_medida);

                            $parent.find('.custo').val(data.custo);

                        },

                        error: function() {

                            alert("Erro ao buscar os detalhes do ingrediente.");

                        }

                    });

                }

            });

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

        <h2>Editar Receita</h2>

        <?php if (!empty($mensagem)): ?>

            <p class="mensagem <?php echo (strpos($mensagem, 'Erro') !== false) ? 'erro' : 'sucesso'; ?>">

                <?php echo $mensagem; ?>

            </p>

        <?php endif; ?>



        <?php if (isset($dados_receita)): ?>

        <form method="POST" action="editar_receita.php?id=<?php echo $receita->id; ?>">

            <label for="nome_receita">Nome da Receita:</label>

            <input type="text" id="nome_receita" name="nome_receita" value="<?php echo htmlspecialchars($receita->nome_receita); ?>" required>



            <label for="classificacao_custo">Classificação de Custo:</label>

            <select id="classificacao_custo" name="classificacao_custo" required>

                <option value="$" <?php if ($receita->classificacao_custo == '$') echo 'selected'; ?>>$</option>

                <option value="$$" <?php if ($receita->classificacao_custo == '$$') echo 'selected'; ?>>$$</option>

                <option value="$$$" <?php if ($receita->classificacao_custo == '$$$') echo 'selected'; ?>>$$$</option>

            </select>



            <label for="modo_preparo">Modo de Preparo:</label>

            <select id="modo_preparo" name="modo_preparo" required>

                <option value="Assado" <?php if ($receita->modo_preparo == 'Assado') echo 'selected'; ?>>Assado</option>

                <option value="Grelhado" <?php if ($receita->modo_preparo == 'Grelhado') echo 'selected'; ?>>Grelhado</option>

                <option value="Frito" <?php if ($receita->modo_preparo == 'Frito') echo 'selected'; ?>>Frito</option>

                <option value="Cozido" <?php if ($receita->modo_preparo == 'Cozido') echo 'selected'; ?>>Cozido</option>

            </select>



            <label for="producao">Produção:</label>

            <select id="producao" name="producao" required>

                <option value="Fácil" <?php if ($receita->producao == 'Fácil') echo 'selected'; ?>>Fácil</option>

                <option value="Médio" <?php if ($receita->producao == 'Médio') echo 'selected'; ?>>Médio</option>

                <option value="Difícil" <?php if ($receita->producao == 'Difícil') echo 'selected'; ?>>Difícil</option>

            </select>



            <h3>Ingredientes da Receita</h3>

            <table id="ingredientes-tabela" border="1">

                <thead>

                    <tr>

                        <th>Descrição</th>

                        <th>Quantidade</th>

                        <th>Unidade de Medida</th>

                        <th>Custo (R$)</th>

                        <th>Ação</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (!empty($ingredientes_receita)): ?>

                        <?php foreach ($ingredientes_receita as $ing): ?>

                            <tr>

                                <td><?php echo htmlspecialchars($ing['descricao'] ?? ''); ?></td>

                                <td><input type="number" name="ingredientes[<?php echo $ing['ingrediente_id']; ?>][quantidade]" value="<?php echo htmlspecialchars($ing['quantidade'] ?? 0); ?>" step="0.01"></td>

                                <td><?php echo htmlspecialchars($ing['unidade_medida'] ?? ''); ?></td>

                                <td><input type="number" name="ingredientes[<?php echo $ing['ingrediente_id']; ?>][custo]" value="<?php echo htmlspecialchars($ing['custo'] ?? 0); ?>" step="0.01"></td>

                                <td><a href="#" onclick="excluirIngrediente(<?php echo $ing['ingrediente_id']; ?>, <?php echo $receita->id; ?>)">Excluir</a></td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="5">Nenhum ingrediente encontrado para esta receita.</td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

            <br>

            <button type="button" id="adicionar-ingrediente">Adicionar Ingrediente</button>

            <button type="submit" name="atualizar_receita">Atualizar Receita</button>

        </form>

        <?php endif; ?>

    </section>

</main>



<footer>

    <p>&copy; 2024 ZAYON - Todos os direitos reservados.</p>

</footer>



</body>

</html>

