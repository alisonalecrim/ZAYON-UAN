<?php
class Fornecedor {
    private $conn;
    private $table_name = "fornecedores";

    public $id;
    public $razao_social;
    public $nome_fantasia;
    public $cnpj;
    public $inscricao_estadual;
    public $telefone;
    public $email;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para cadastrar um novo fornecedor
    public function cadastrar() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (razao_social, nome_fantasia, cnpj, inscricao_estadual, telefone, email) 
                  VALUES (:razao_social, :nome_fantasia, :cnpj, :inscricao_estadual, :telefone, :email)";

        $stmt = $this->conn->prepare($query);

        // Bind dos parâmetros
        $stmt->bindParam(':razao_social', $this->razao_social);
        $stmt->bindParam(':nome_fantasia', $this->nome_fantasia);
        $stmt->bindParam(':cnpj', $this->cnpj);
        $stmt->bindParam(':inscricao_estadual', $this->inscricao_estadual);
        $stmt->bindParam(':telefone', $this->telefone);
        $stmt->bindParam(':email', $this->email);

        return $stmt->execute();
    }

    // Método para buscar todos os fornecedores
    public function buscarTodos() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function buscarPorNomeOuCNPJ($nome, $cnpj) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";

        if (!empty($nome)) {
            $query .= " AND (razao_social LIKE :nome OR nome_fantasia LIKE :nome)";
        }

        if (!empty($cnpj)) {
            $query .= " AND cnpj LIKE :cnpj";
        }

        $stmt = $this->conn->prepare($query);

        if (!empty($nome)) {
            $nome_param = "%" . $nome . "%";
            $stmt->bindParam(':nome', $nome_param);
        }

        if (!empty($cnpj)) {
            $cnpj_param = "%" . $cnpj . "%";
            $stmt->bindParam(':cnpj', $cnpj_param);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function excluir($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
     // Método para buscar um fornecedor pelo ID
    public function buscarPorId() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Método para atualizar os dados de um fornecedor
    public function atualizar() {
        $query = "UPDATE " . $this->table_name . " 
                  SET razao_social = :razao_social, nome_fantasia = :nome_fantasia, 
                      cnpj = :cnpj, inscricao_estadual = :inscricao_estadual, 
                      telefone = :telefone, email = :email 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Bind dos parâmetros
        $stmt->bindParam(':razao_social', $this->razao_social);
        $stmt->bindParam(':nome_fantasia', $this->nome_fantasia);
        $stmt->bindParam(':cnpj', $this->cnpj);
        $stmt->bindParam(':inscricao_estadual', $this->inscricao_estadual);
        $stmt->bindParam(':telefone', $this->telefone);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }
    public function buscarPorCNPJ($cnpj) {
        $query = "SELECT id, razao_social, nome_fantasia, email, telefone 
                  FROM " . $this->table_name . " 
                  WHERE cnpj = :cnpj LIMIT 1";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cnpj', $cnpj);
    
        $stmt->execute();
    
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC); // Retorna os dados do fornecedor
        } else {
            return null; // Retorna null se o CNPJ não for encontrado
        }
    }        
}
?>
