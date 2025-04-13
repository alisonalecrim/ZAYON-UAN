<?php

include '../includes/database.php';
include '../models/Fornecedor.php';

$database = new Database();
$conn = $database->getConnection();
$fornecedor = new Fornecedor($conn);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Cotação - ZAYON</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-success">
            <div class="container">
                <a class="navbar-brand text-white" href="#">ZAYON - Sistema de Gerenciamento de UAN | Cotação de Preços Para a Unidade CEICS</a>
            </div>
        </nav>
    </header>
    
    <main class="container mt-5">
        <h2 class="mb-4">Enviar Cotação</h2>

        <form id="cotacao-form" method="POST" action="processar_cotacao.php">
            <div class="mb-3">
                <label for="cnpj" class="form-label">CNPJ:</label>
                <input type="text" class="form-control" id="cnpj" name="cnpj" required>
            </div>

            <div class="mb-3">
                <label for="razao_social" class="form-label">Razão Social:</label>
                <input type="text" class="form-control" id="razao_social" name="razao_social" required>
            </div>

            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone:</label>
                <input type="text" class="form-control" id="telefone" name="telefone" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <h4 class="mt-4">Itens da Cotação</h4>
            <table class="table table-bordered mt-3" id="tabela-itens">
                <thead class="thead-dark">
                    <tr>
                        <th>Descrição do Item</th>
                        <th>Unidade de Medida</th>
                        <th>Preço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="descricao[]" class="form-control" required></td>
                        <td><input type="text" name="unidade[]" class="form-control" required></td>
                        <td><input type="number" step="0.01" name="preco[]" class="form-control" required></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remover-linha">Remover</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="button" id="adicionar-linha">Adicionar Item</button>

            <button type="submit" class="button">Enviar Cotação</button>
        </form>
    </main>

    <script>
        $(document).ready(function () {
            $('#cnpj').on('blur', function () {
                let cnpj = $(this).val();

                if (cnpj) {
                    $.ajax({
                        url: 'buscar_fornecedor.php',
                        method: 'POST',
                        data: { cnpj: cnpj },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                $('#razao_social').val(response.data.razao_social);
                                $('#telefone').val(response.data.telefone);
                                $('#email').val(response.data.email);
                            } else {
                                $('#razao_social').val('');
                                $('#telefone').val('');
                                $('#email').val('');
                            }
                        },
                        error: function () {
                            alert('Erro ao buscar fornecedor. Por favor, tente novamente.');
                        }
                    });
                }
            });

            $('#adicionar-linha').on('click', function () {
                let novaLinha = `
                    <tr>
                        <td><input type="text" name="descricao[]" class="form-control" required></td>
                        <td><input type="text" name="unidade[]" class="form-control" required></td>
                        <td><input type="number" step="0.01" name="preco[]" class="form-control" required></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remover-linha">Remover</button>
                        </td>
                    </tr>
                `;
                $('#tabela-itens tbody').append(novaLinha);
            });

            $(document).on('click', '.remover-linha', function () {
                $(this).closest('tr').remove();
            });
        });
    </script>
</body>

</html>
