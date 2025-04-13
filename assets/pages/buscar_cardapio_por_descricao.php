<?php

// Inclui as classes necessárias

include '../includes/database.php';

include '../models/Cardapio.php';

include '../models/Receita.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['descricao'])) {

    // Recebe a descrição enviada via POST

    $descricao = $_POST['descricao'];



    // Instancia a classe Database e obtém a conexão

    $database = new Database();

    $conn = $database->getConnection();



    // Instancia as classes Cardapio e Receita

    $cardapioModel = new Cardapio($conn);

    $receitaModel = new Receita($conn);



    // Busca os cardápios que correspondem à descrição fornecida

    $cardapios = $cardapioModel->buscarPorDescricao($descricao);



    if ($cardapios) {

        // Inicia a construção do HTML de resposta

        $html = '<table border="1">

                    <thead>

                        <tr>

                            <th>Data</th>

                            <th>Dia da Semana</th>

                            <th>Café da manhã</th>

                            <th>Almoço</th>

                            <th>Lanche</th>

                            <th>Janta</th>

                            <th>Ceia</th>

                        </tr>

                    </thead>

                    <tbody>';



        foreach ($cardapios as $cardapio) {

            // Busca as refeições associadas ao cardápio

            $refeicoes = $cardapioModel->buscarRefeicoesPorCardapio($cardapio->id);



            $html .= '<tr>

                        <td>' . date('d/m/Y', strtotime($cardapio->data)) . '</td>

                        <td>' . htmlspecialchars($cardapio->dia_semana) . '</td>

                        <td>' . exibirReceitas($refeicoes, 'Café da manhã', $receitaModel) . '</td>

                        <td>' . exibirReceitas($refeicoes, 'Almoço', $receitaModel) . '</td>

                        <td>' . exibirReceitas($refeicoes, 'Lanche', $receitaModel) . '</td>

                        <td>' . exibirReceitas($refeicoes, 'Janta', $receitaModel) . '</td>

                        <td>' . exibirReceitas($refeicoes, 'Ceia', $receitaModel) . '</td>

                    </tr>';

        }



        $html .= '</tbody></table>';

        echo $html;

    } else {

        echo '<p>Nenhum cardápio encontrado para a descrição fornecida.</p>';

    }

} else {

    echo '<p>Descrição não fornecida ou método incorreto.</p>';

}



// Função auxiliar para exibir as receitas de cada refeição com arranchamento

function exibirReceitas($refeicoes, $tipoRefeicao, $receitaModel) {

    if (isset($refeicoes[$tipoRefeicao]) && !empty($refeicoes[$tipoRefeicao])) {

        $resultado = "";

        $receitasExibidas = []; // Para evitar duplicação de receitas

        foreach ($refeicoes[$tipoRefeicao] as $receita) {

            $nomeReceita = $receitaModel->buscarNomePorId($receita['receita_id']);

            if ($nomeReceita) {

                $receitasExibidas[] = htmlspecialchars($nomeReceita) . " (Arranchamento: " . htmlspecialchars($receita['arranchamento']) . ")";

            }

        }

        return implode("<br>", $receitasExibidas);

    }

    return 'Sem receitas cadastradas';

}
