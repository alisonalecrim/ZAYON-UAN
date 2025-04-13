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



// Verifica se o formulário foi enviado

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $receita->nome_receita = trim($_POST['nome_receita']);

    $receita->classificacao_custo = $_POST['classificacao_custo']; 

    $receita->modo_preparo = $_POST['modo_preparo'];

    $receita->producao = $_POST['producao'];

    $ingredientes = $_POST['ingredientes'];



    if (!empty($receita->nome_receita) && !empty($ingredientes)) {

        if ($receita->cadastrar()) {

            foreach ($ingredientes as $ingrediente_data) {

                $ingrediente->id = $ingrediente_data['ingrediente_id'];

                $quantidade = $ingrediente_data['quantidade'];

                $unidade_medida = $ingrediente_data['unidade_medida'];

                $custo = $ingrediente_data['custo'];

                $receita->adicionarIngrediente($ingrediente->id, $quantidade, $unidade_medida, $custo);

            }

            $mensagem = "Receita cadastrada com sucesso!";

        } else {

            $mensagem = "Erro ao cadastrar a receita.";

        }

    } else {

        $mensagem = "Preencha todos os campos obrigatórios.";

    }

}



// Busca os ingredientes disponíveis

$ingredientes_disponiveis = $ingrediente->buscarTodos();

?>



<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar Receita</title>

    <link rel="stylesheet" href="../css/style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Adicionando Bootstrap ao projeto -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>

        $(document).ready(function() {

            <?php if (!empty($mensagem)): ?>

                alert("<?php echo $mensagem; ?>");

            <?php endif; ?>



            function adicionarIngrediente() {

                var novoIngrediente = `

                <div class="ingrediente-item">

                    <select name="ingredientes[${$('.ingrediente-item').length}][ingrediente_id]" class="ingrediente-selecionado" required>

                        <option value="">Selecione o ingrediente</option>

                        <?php foreach ($ingredientes_disponiveis as $ing): ?>

                            <option value="<?php echo $ing['id']; ?>"><?php echo $ing['descricao']; ?></option>

                        <?php endforeach; ?>

                    </select>

                    <input type="number" name="ingredientes[${$('.ingrediente-item').length}][quantidade]" placeholder="Quantidade" step="0.01" required>

                    <input type="text" name="ingredientes[${$('.ingrediente-item').length}][unidade_medida]" class="unidade_medida" placeholder="Unidade de Medida" readonly required>

                    <input type="number" name="ingredientes[${$('.ingrediente-item').length}][custo]" class="custo" placeholder="Custo (R$)" step="0.01" readonly required>

                    <button type="button" onclick="removerIngrediente(this)">Remover</button>

                </div>`;

                $('#ingredientes-container').append(novoIngrediente);

            }



            window.removerIngrediente = function(button) {

                $(button).closest('.ingrediente-item').remove();

            }



            $('#adicionar-ingrediente').click(function() {

                adicionarIngrediente();

            });



            adicionarIngrediente();



            $('#ingredientes-container').on('change', '.ingrediente-selecionado', function() {

                var ingredienteId = $(this).val();

                var $parent = $(this).closest('.ingrediente-item');

                

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

        <h2>Cadastrar Nova Receita</h2>



        <form method="POST" action="cadastrar_receita.php">

            <label for="nome_receita">Nome da Receita:</label>

            <input type="text" id="nome_receita" name="nome_receita" required>



            <label for="classificacao_custo">Classificação de Custo:</label>

            <select id="classificacao_custo" name="classificacao_custo" required>

                <option value="">Selecione</option>

                <option value="$">$</option>

                <option value="$$">$$</option>

                <option value="$$$">$$$</option>

            </select>



            <label for="modo_preparo">Modo de Preparo:</label>

            <select id="modo_preparo" name="modo_preparo" required>

                <option value="">Selecione</option>

                <option value="Assado">Assado</option>

                <option value="Grelhado">Grelhado</option>

                <option value="Frito">Frito</option>

                <option value="Cozido">Cozido</option>

            </select>



            <label for="producao">Produção:</label>

            <select id="producao" name="producao" required>

                <option value="">Selecione</option>

                <option value="Fácil">Fácil</option>

                <option value="Médio">Médio</option>

                <option value="Difícil">Difícil</option>

            </select>



            <div id="ingredientes-container">

                <!-- Ingredientes serão adicionados dinamicamente aqui -->

            </div>

            <br>

            <button type="button" id="adicionar-ingrediente">Adicionar Ingrediente</button>

            <button type="submit">Salvar Receita</button>

        </form>

    </section>

</main>



<footer>

    <p>&copy; 2024 ZAYON - Todos os direitos reservados.</p>

</footer>



</body>

</html>

