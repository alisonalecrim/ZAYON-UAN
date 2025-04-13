<?php
class Estoque {
    private $conn;
    private $table_name = "ingredientes"; // Vamos usar a tabela de ingredientes para o estoque

    public $ingrediente_id;
    public $quantidade;

    // Construtor com a conexão ao banco de dados
    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para adicionar ao estoque
    public function adicionarEstoque() {
        $query = "UPDATE " . $this->table_name . " 
                  SET quantidade_estoque = quantidade_estoque + :quantidade 
                  WHERE id = :ingrediente_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantidade', $this->quantidade);
        $stmt->bindParam(':ingrediente_id', $this->ingrediente_id);

        return $stmt->execute();
    }

    // Método para retirar do estoque
    public function retirarEstoque() {
        $query = "UPDATE " . $this->table_name . " 
                  SET quantidade_estoque = quantidade_estoque - :quantidade 
                  WHERE id = :ingrediente_id AND quantidade_estoque >= :quantidade";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantidade', $this->quantidade);
        $stmt->bindParam(':ingrediente_id', $this->ingrediente_id);

        return $stmt->execute();
    }

    // Método para buscar o estoque de um ingrediente específico
    public function buscarEstoquePorId() {
        $query = "SELECT quantidade_estoque FROM " . $this->table_name . " WHERE id = :ingrediente_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ingrediente_id', $this->ingrediente_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
