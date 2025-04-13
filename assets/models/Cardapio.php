<?php   
class Cardapio {
    private $conn;
    private $table_name = "cardapios";

    public $id;
    public $dia_semana;
    public $data;
    public $descricao;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para excluir um cardápio e suas refeições associadas
    public function excluir($id) {
        try {
            // Inicia uma transação
            $this->conn->beginTransaction();

            // Exclui as refeições associadas ao cardápio
            $queryRefeicoes = "DELETE FROM cardapios_refeicoes WHERE cardapio_id = :id";
            $stmtRefeicoes = $this->conn->prepare($queryRefeicoes);
            $stmtRefeicoes->bindParam(':id', $id);
            $stmtRefeicoes->execute();

            // Exclui o cardápio
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);

            // Executa a query e confirma a transação
            if ($stmt->execute()) {
                $this->conn->commit();
                return true; // Exclusão bem-sucedida
            } else {
                $this->conn->rollBack();
                return false; // Falha na exclusão
            }
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw $e; // Lança exceção para que o erro seja tratado
        }
    }

    // Método para buscar as refeições de um cardápio por ID
    public function buscarRefeicoesPorCardapio($cardapio_id) {
        $query = "SELECT tipo_refeicao, receita_id, arranchamento 
                  FROM cardapios_refeicoes 
                  WHERE cardapio_id = :cardapio_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cardapio_id', $cardapio_id);
        
        if ($stmt->execute()) {
            $refeicoes = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $refeicoes[$row['tipo_refeicao']][] = ['receita_id' => $row['receita_id'], 'arranchamento' => $row['arranchamento']];
            }
            return $refeicoes; // Retorna um array com os tipos de refeições e suas respectivas receitas e arranchamento
        }
    
        return false; // Retorna false se a busca falhar
    }

    // Método para atualizar as refeições de um cardápio
    public function atualizarRefeicoes($cardapio_id, $refeicoes) {
        try {
            $this->conn->beginTransaction();

            // Exclui todas as refeições atuais associadas ao cardápio
            $query = "DELETE FROM cardapios_refeicoes WHERE cardapio_id = :cardapio_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':cardapio_id', $cardapio_id);
            $stmt->execute();
        
            // Insere as novas refeições com arranchamento
            $query = "INSERT INTO cardapios_refeicoes (cardapio_id, tipo_refeicao, receita_id, arranchamento) VALUES (:cardapio_id, :tipo_refeicao, :receita_id, :arranchamento)";
            $stmt = $this->conn->prepare($query);
        
            foreach ($refeicoes as $tipo_refeicao => $itens) {
                foreach ($itens as $item) {
                    $stmt->bindParam(':cardapio_id', $cardapio_id);
                    $stmt->bindParam(':tipo_refeicao', $tipo_refeicao);
                    $stmt->bindParam(':receita_id', $item['receita_id']);
                    $stmt->bindParam(':arranchamento', $item['arranchamento']);
                    $stmt->execute();
                }
            }

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    // Método para atualizar apenas os dados do cardápio (sem as refeições)
    public function atualizar() {
        $query = "UPDATE " . $this->table_name . " 
                  SET dia_semana = :dia_semana, data = :data, descricao = :descricao
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Bind dos parâmetros
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':dia_semana', $this->dia_semana);
        $stmt->bindParam(':data', $this->data);
        $stmt->bindParam(':descricao', $this->descricao);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Método para buscar o cardápio por ID
    public function buscarPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC); // Retorna os dados do cardápio
        }

        return false; // Retorna false se o cardápio não for encontrado
    }

    // Função para cadastrar um novo cardápio
    public function cadastrar() {
        try {
            $this->conn->beginTransaction();

            $query = "INSERT INTO " . $this->table_name . " (dia_semana, data, descricao) VALUES (:dia_semana, :data, :descricao)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':dia_semana', $this->dia_semana);
            $stmt->bindParam(':data', $this->data);
            $stmt->bindParam(':descricao', $this->descricao);

            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                $this->conn->commit();
                return true;
            }

            $this->conn->rollBack();
            return false;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
    public function buscarPorDescricao($descricao) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE descricao LIKE :descricao ORDER BY data ASC";
        $stmt = $this->conn->prepare($query);
        $descricao = "%" . $descricao . "%"; // Ajuste para buscar por substring
        $stmt->bindParam(':descricao', $descricao);
        
        $stmt->execute();
    
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cardapio = new Cardapio($this->conn);
            $cardapio->id = $row['id'];
            $cardapio->dia_semana = $row['dia_semana'];
            $cardapio->data = $row['data'];
            $cardapio->descricao = $row['descricao'];
            $result[] = $cardapio;
        }
    
        return $result; // Retorna uma lista de objetos Cardapio
    }
    // Na classe Cardapio
public function atualizarCardapio($data, $dia_semana, $descricao) {
    $query = "UPDATE cardapios SET data = :data, dia_semana = :dia_semana, descricao = :descricao WHERE id = :id";
    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(':data', $data);
    $stmt->bindParam(':dia_semana', $dia_semana);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':id', $this->id);

    return $stmt->execute();
}
public function buscarPorDescricaoEPeriodo($descricao, $dataInicio, $dataFim) {
    $query = "
        SELECT c.*, cr.arranchamento, cr.tipo_refeicao
        FROM cardapios c
        JOIN cardapios_refeicoes cr ON c.id = cr.cardapio_id
        WHERE c.descricao = :descricao
        AND c.data BETWEEN :dataInicio AND :dataFim
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':dataInicio', $dataInicio);
    $stmt->bindParam(':dataFim', $dataFim);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function buscarDescricoesUnicas() {
    $query = "SELECT DISTINCT descricao FROM " . $this->table_name . " ORDER BY descricao";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    
}
?>
