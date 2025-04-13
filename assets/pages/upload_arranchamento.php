<?php 



session_start();



// Verifica se o usuário está logado

if (!isset($_SESSION['user_id'])) {

    // Se não estiver logado, redireciona para a página de login

    header("Location: assets/pages/login.php");

    exit();

}



// Inclui classes e outros

include '../includes/database.php';

include '../models/CardapioRefeicao.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Conecta ao banco de dados

    $database = new Database();

    $db = $database->getConnection();



    // Cria uma nova instância da classe CardapioRefeicao

    $cardapioRefeicao = new CardapioRefeicao($db);



    // Inicializa uma variável para armazenar se houve erro

    $erro = false;



    // Percorre as refeições e armazena os dados

    foreach ($_POST['arranchamento'] as $tipo_refeicao => $quantidades) {

        foreach ($quantidades as $data => $quantidade) {

            // Atribui valores aos atributos da classe

            $cardapioRefeicao->tipo_refeicao = $tipo_refeicao;

            $cardapioRefeicao->data = $data;

            $cardapioRefeicao->arranchamento = (int)$quantidade;



            // Chama o método para inserir os dados no banco de dados

            if (!$cardapioRefeicao->carregarArranchamento()) {

                $erro = true;

                echo "Erro ao inserir dados para {$tipo_refeicao} na data {$data}.<br>";

            }

        }

    }



    if (!$erro) {

        echo "Dados inseridos com sucesso!";

    }

}

?>



<!DOCTYPE html> 

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Upload de Arranchamento</title>

    <link rel="stylesheet" href="../css/style.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>

    <!-- Adicionando Bootstrap ao projeto -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>

        /* Centraliza o conteúdo da página */

        body {

            font-family: Arial, sans-serif;

            background-color: #f9f9f9;

        }

        .container {

            display: flex;

            flex-direction: column;

            align-items: center;

            text-align: center;

            margin-top: 20px;

        }

        table {

            margin: 20px auto;

            border-collapse: collapse;

            width: 80%;

            max-width: 900px;

            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);

        }

        th, td {

            border: 1px solid #000;

            padding: 8px;

            text-align: center;

        }

        th {

            background-color: #f2f2f2;

        }

        input[type="number"], input[type="text"] {

            width: 100%;

            box-sizing: border-box;

        }

        input[type="text"] {

            background-color: #e9ecef;

        }

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

        <br>

<div class="container">

    <!-- Formulário para upload da planilha -->

    <h2>Carregar Planilha de Arranchamento</h2>

    <input type="file" id="fileInput" accept=".xlsx, .xls" />

    

    <!-- Tabela para atualizar os dados -->

    <form id="arranchamentoForm" method="POST" action="upload_arranchamento.php">

        <table id="data-table">

            <thead>

                <tr>

                    <th>REFEIÇÃO</th>

                    <th id="header-date-0"></th>

                    <th id="header-date-1"></th>

                    <th id="header-date-2"></th>

                    <th id="header-date-3"></th>

                    <th id="header-date-4"></th>

                    <th id="header-date-5"></th>

                    <th id="header-date-6"></th>

                </tr>

            </thead>

            <tbody>

                <!-- As linhas das refeições serão preenchidas dinamicamente -->

            </tbody>

        </table>

        <button type="button" id="submitButton">Atualizar</button>

    </form>

</div>



<script>

document.getElementById('fileInput').addEventListener('change', handleFile, false);



function handleFile(e) {

    const file = e.target.files[0];

    const reader = new FileReader();



    reader.onload = function (event) {

        const data = new Uint8Array(event.target.result);

        const workbook = XLSX.read(data, { type: 'array' });

        const firstSheetName = workbook.SheetNames[0];

        const worksheet = workbook.Sheets[firstSheetName];

        const json = XLSX.utils.sheet_to_json(worksheet, { header: 1 });



        const tbody = document.getElementById('data-table').getElementsByTagName('tbody')[0];

        tbody.innerHTML = '';



        const dates = json[0].slice(1);

        dates.forEach((date, index) => {

            if (typeof date === 'number' && date > 25568) {

                const dateValue = new Date((date - 25569) * 86400 * 1000);

                const formattedDate = dateValue.toISOString().split('T')[0];

                document.getElementById(`header-date-${index}`).textContent = formattedDate;

            }

        });



        json.slice(1).forEach((row) => {

            const tr = document.createElement('tr');

            const refeicao = row[0];



            const tdRef = document.createElement('td');

            tdRef.textContent = refeicao;

            tr.appendChild(tdRef);



            for (let i = 1; i < row.length; i++) {

                const tdQuantidade = document.createElement('td');

                const date = dates[i - 1];

                const formattedDate = new Date((date - 25569) * 86400 * 1000).toISOString().split('T')[0];



                const inputQuantidade = document.createElement('input');

                inputQuantidade.type = 'number';

                inputQuantidade.name = `arranchamento[${refeicao}][${formattedDate}]`;

                inputQuantidade.value = row[i] || 0;

                inputQuantidade.min = 0;



                tdQuantidade.appendChild(inputQuantidade);

                tr.appendChild(tdQuantidade);

            }

            tbody.appendChild(tr);

        });

    };



    reader.readAsArrayBuffer(file);

}



document.getElementById('submitButton').addEventListener('click', function() {

    const formData = new FormData(document.getElementById('arranchamentoForm'));



    fetch('upload_arranchamento.php', {

        method: 'POST',

        body: formData,

    })

    .then(response => response.text())

    .then(data => {

        alert('Dados atualizados com sucesso!');

        console.log(data);

    })

    .catch(error => {

        console.error('Erro ao atualizar os dados:', error);

    });

});



</script>



</body>

</html>

