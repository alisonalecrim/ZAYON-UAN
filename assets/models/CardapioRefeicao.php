<?php
class CardapioRefeicao {
    private $conn;
    private $table_name = "cardapios_refeicoes";

    public $id;
    public $cardapio_id;
    public $tipo_refeicao;
    public $descricao;
    public $data;
    public $arranchamento;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método genérico para executar consultas
    private function prepareAndExecute($query, $params) {
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt;
    }

    // Método para salvar uma nova refeição
    public function salvarRefeicao() {
        $query = "INSERT INTO {$this->table_name} (cardapio_id, tipo_refeicao, descricao, data, arranchamento) 
                  VALUES (:cardapio_id, :tipo_refeicao, :descricao, :data, :arranchamento)";

        $params = [
            ':cardapio_id' => $this->cardapio_id,
            ':tipo_refeicao' => $this->tipo_refeicao,
            ':descricao' => $this->descricao,
            ':data' => $this->data,
            ':arranchamento' => $this->arranchamento
        ];

        $stmt = $this->prepareAndExecute($query, $params);

        if ($stmt) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Método para atualizar uma refeição existente
    public function atualizarRefeicao() {
        $query = "UPDATE {$this->table_name} 
                  SET cardapio_id = :cardapio_id, 
                      tipo_refeicao = :tipo_refeicao, 
                      descricao = :descricao, 
                      data = :data, 
                      arranchamento = :arranchamento 
                  WHERE id = :id";

        $params = [
            ':cardapio_id' => $this->cardapio_id,
            ':tipo_refeicao' => $this->tipo_refeicao,
            ':descricao' => $this->descricao,
            ':data' => $this->data,
            ':arranchamento' => $this->arranchamento,
            ':id' => $this->id
        ];

        $stmt = $this->prepareAndExecute($query, $params);

        return $stmt->rowCount() > 0;
    }

    // Método para carregar o arranchamento baseado na refeição e data
    public function carregarArranchamento() {
        $queryCheck = "SELECT id FROM {$this->table_name} WHERE tipo_refeicao = :tipo_refeicao AND data = :data";
        $paramsCheck = [
            ':tipo_refeicao' => $this->tipo_refeicao,
            ':data' => $this->data
        ];

        $stmtCheck = $this->prepareAndExecute($queryCheck, $paramsCheck);

        if ($stmtCheck->rowCount() > 0) {
            $queryUpdate = "UPDATE {$this->table_name} 
                            SET arranchamento = :arranchamento 
                            WHERE tipo_refeicao = :tipo_refeicao AND data = :data";

            $paramsUpdate = array_merge($paramsCheck, [
                ':arranchamento' => $this->arranchamento
            ]);

            return $this->prepareAndExecute($queryUpdate, $paramsUpdate);
        } else {
            $queryInsert = "INSERT INTO {$this->table_name} (tipo_refeicao, data, arranchamento) 
                            VALUES (:tipo_refeicao, :data, :arranchamento)";

            $paramsInsert = $paramsCheck;
            $paramsInsert[':arranchamento'] = $this->arranchamento;

            return $this->prepareAndExecute($queryInsert, $paramsInsert);
        }
    }

    // Método para buscar o arranchamento de uma refeição e data específicas
    public function buscarArranchamento($data, $tipo_refeicao) {
        $query = "SELECT arranchamento FROM {$this->table_name} WHERE data = :data AND tipo_refeicao = :tipo_refeicao";
        $params = [
            ':data' => $data,
            ':tipo_refeicao' => $tipo_refeicao
        ];

        $stmt = $this->prepareAndExecute($query, $params);

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row['arranchamento'];
        }
        return null;
    }

    // Método para buscar receitas específicas por cardápio_id, data e tipo de refeição
    public function buscarReceitasPorCardapioDataTipo($cardapio_id, $data, $tipo_refeicao) {
        $query = "SELECT cr.receita_id, r.nome_receita, cr.arranchamento
                  FROM {$this->table_name} AS cr
                  JOIN receitas AS r ON cr.receita_id = r.id
                  WHERE cr.cardapio_id = :cardapio_id AND cr.data = :data AND cr.tipo_refeicao = :tipo_refeicao";

        $params = [
            ':cardapio_id' => $cardapio_id,
            ':data' => $data,
            ':tipo_refeicao' => $tipo_refeicao
        ];

        $stmt = $this->prepareAndExecute($query, $params);

        $receitas = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $receitas[] = [
                'receita_id' => $row['receita_id'],
                'nome_receita' => $row['nome_receita'],
                'arranchamento' => $row['arranchamento']
            ];
        }

        return $receitas;
    }

    // Método para atualizar ou inserir refeição
    public function atualizarOuInserirRefeicao($cardapio_id, $tipo_refeicao, $data, $arranchamento, $receita_id) {
        $queryCheck = "SELECT id FROM {$this->table_name} WHERE cardapio_id = :cardapio_id AND tipo_refeicao = :tipo_refeicao AND data = :data";
        $paramsCheck = [
            ':cardapio_id' => $cardapio_id,
            ':tipo_refeicao' => $tipo_refeicao,
            ':data' => $data
        ];

        $stmtCheck = $this->prepareAndExecute($queryCheck, $paramsCheck);

        if ($stmtCheck->rowCount() > 0) {
            $refeicao_id = $stmtCheck->fetch(PDO::FETCH_ASSOC)['id'];
            $queryUpdate = "UPDATE {$this->table_name} 
                            SET arranchamento = :arranchamento, receita_id = :receita_id 
                            WHERE id = :id";

            $paramsUpdate = [
                ':arranchamento' => $arranchamento,
                ':receita_id' => $receita_id,
                ':id' => $refeicao_id
            ];

            return $this->prepareAndExecute($queryUpdate, $paramsUpdate);
        } else {
            $queryInsert = "INSERT INTO {$this->table_name} 
                            (cardapio_id, tipo_refeicao, data, arranchamento, receita_id) 
                            VALUES (:cardapio_id, :tipo_refeicao, :data, :arranchamento, :receita_id)";

            $paramsInsert = array_merge($paramsCheck, [
                ':arranchamento' => $arranchamento,
                ':receita_id' => $receita_id
            ]);

            return $this->prepareAndExecute($queryInsert, $paramsInsert);
        }
    }

    // Método para excluir uma receita
    public function excluirReceita($cardapio_id, $receita_id) {
        $query = "DELETE FROM {$this->table_name} WHERE cardapio_id = :cardapio_id AND receita_id = :receita_id";
        $params = [
            ':cardapio_id' => $cardapio_id,
            ':receita_id' => $receita_id
        ];

        return $this->prepareAndExecute($query, $params);
    }

    // Método para verificar existência de refeição
    public function verificarExistenciaRefeicao($cardapio_id, $tipo_refeicao, $receita_id, $data) {
        $query = "SELECT 1 FROM {$this->table_name} 
                  WHERE cardapio_id = :cardapio_id AND tipo_refeicao = :tipo_refeicao 
                  AND receita_id = :receita_id AND data = :data";

        $params = [
            ':cardapio_id' => $cardapio_id,
            ':tipo_refeicao' => $tipo_refeicao,
            ':receita_id' => $receita_id,
            ':data' => $data
        ];

        $stmt = $this->prepareAndExecute($query, $params);

        return $stmt->fetch() !== false;
    }

    // Método para buscar receitas por cardápio
    public function buscarReceitasPorCardapio($cardapio_id) {
        $query = "SELECT cr.id AS refeicao_id, cr.arranchamento, cr.tipo_refeicao, r.id AS receita_id, r.nome_receita
                  FROM {$this->table_name} AS cr
                  JOIN receitas AS r ON cr.receita_id = r.id
                  WHERE cr.cardapio_id = :cardapio_id";

        $params = [':cardapio_id' => $cardapio_id];

        $stmt = $this->prepareAndExecute($query, $params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
