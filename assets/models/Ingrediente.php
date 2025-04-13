<?php
class Ingrediente {
    private $conn;
    private $table_name = "ingredientes";

    public $id;
    public $descricao;
    public $unidade_medida;
    public $custo;
    public $porcao_individual;
    public $quantidade_estoque;

    // Construtor com a conexão ao banco de dados
    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para cadastrar um novo ingrediente
    public function cadastrar() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (descricao, unidade_medida, custo, porcao_individual, quantidade_estoque) 
                  VALUES (:descricao, :unidade_medida, :custo, :porcao_individual, :quantidade_estoque)";

        $stmt = $this->conn->prepare($query);

        // Limpeza e bind dos valores
        $stmt->bindParam(':descricao', $this->descricao);
        $stmt->bindParam(':unidade_medida', $this->unidade_medida);
        $stmt->bindParam(':custo', $this->custo);
        $stmt->bindParam(':porcao_individual', $this->porcao_individual);
        $stmt->bindParam(':quantidade_estoque', $this->quantidade_estoque);

        return $stmt->execute();
    }

    // Método para atualizar um ingrediente existente 
    public function atualizar() {
        $query = "UPDATE " . $this->table_name . " 
                  SET descricao = :descricao, unidade_medida = :unidade_medida, 
                      custo = :custo, porcao_individual = :porcao_individual, quantidade_estoque = :quantidade_estoque
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':descricao', $this->descricao);
        $stmt->bindParam(':unidade_medida', $this->unidade_medida);
        $stmt->bindParam(':custo', $this->custo);
        $stmt->bindParam(':porcao_individual', $this->porcao_individual);
        $stmt->bindParam(':quantidade_estoque', $this->quantidade_estoque);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Método para excluir um ingrediente
    public function excluir() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Método para buscar um ingrediente por ID
    public function buscarPorId() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método para buscar todos os ingredientes
    public function buscarTodos() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorDescricao($descricao) {
        $query = "SELECT * FROM ingredientes WHERE descricao LIKE :descricao";
        $stmt = $this->conn->prepare($query);
        $descricao = "%{$descricao}%";
        $stmt->bindParam(':descricao', $descricao);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function atualizarEstoque() {
        $query = "UPDATE ingredientes SET quantidade_estoque = :quantidade_estoque WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantidade_estoque', $this->quantidade_estoque);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
    public function buscarPorReceita($receita_id) {
        $query = "SELECT i.id, i.descricao, i.unidade_medida, i.porcao_individual, ri.quantidade
                  FROM ingredientes AS i
                  JOIN receita_ingredientes AS ri ON i.id = ri.ingrediente_id
                  WHERE ri.receita_id = :receita_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':receita_id', $receita_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    
    
}
?>
