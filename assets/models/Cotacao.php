<?php
class Cotacao
{
    private $conn;
    private $table_cotacao = "cotacoes";
    private $table_itens = "cotacao_itens";

    public $id;
    public $cnpj;
    public $razao_social;
    public $telefone;
    public $email;
    public $created_at;
    public $itens = [];

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Método para cadastrar uma nova cotação
    public function cadastrar()
    {
        try {
            // Inicia uma transação
            $this->conn->beginTransaction();

            // Insere os dados principais da cotação
            $queryCotacao = "INSERT INTO " . $this->table_cotacao . " 
                (cnpj, razao_social, telefone, email, created_at) 
                VALUES (:cnpj, :razao_social, :telefone, :email, NOW())";

            $stmtCotacao = $this->conn->prepare($queryCotacao);

            $stmtCotacao->bindParam(':cnpj', $this->cnpj);
            $stmtCotacao->bindParam(':razao_social', $this->razao_social);
            $stmtCotacao->bindParam(':telefone', $this->telefone);
            $stmtCotacao->bindParam(':email', $this->email);

            $stmtCotacao->execute();

            // Pega o ID da cotação recém-inserida
            $this->id = $this->conn->lastInsertId();

            // Insere os itens da cotação
            $queryItem = "INSERT INTO " . $this->table_itens . " 
                (cotacao_id, descricao, unidade, preco) 
                VALUES (:cotacao_id, :descricao, :unidade, :preco)";

            $stmtItem = $this->conn->prepare($queryItem);

            foreach ($this->itens as $item) {
                $stmtItem->bindParam(':cotacao_id', $this->id);
                $stmtItem->bindParam(':descricao', $item['descricao']);
                $stmtItem->bindParam(':unidade', $item['unidade']);
                $stmtItem->bindParam(':preco', $item['preco']);
                $stmtItem->execute();
            }

            // Confirma a transação
            $this->conn->commit();

            return true;
        } catch (Exception $e) {
            // Faz rollback em caso de erro
            $this->conn->rollBack();
            throw new Exception("Erro ao cadastrar cotação: " . $e->getMessage());
        }
    }

    // Método para buscar todas as cotações
    public function buscarTodas()
    {
        $query = "SELECT * FROM " . $this->table_cotacao . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para buscar uma cotação por ID
    public function buscarPorId($id)
    {
        // Busca a cotação
        $queryCotacao = "SELECT * FROM " . $this->table_cotacao . " WHERE id = :id";
        $stmtCotacao = $this->conn->prepare($queryCotacao);
        $stmtCotacao->bindParam(':id', $id);
        $stmtCotacao->execute();

        $cotacao = $stmtCotacao->fetch(PDO::FETCH_ASSOC);

        if (!$cotacao) {
            return null;
        }

        // Busca os itens da cotação
        $queryItens = "SELECT * FROM " . $this->table_itens . " WHERE cotacao_id = :cotacao_id";
        $stmtItens = $this->conn->prepare($queryItens);
        $stmtItens->bindParam(':cotacao_id', $id);
        $stmtItens->execute();

        $cotacao['itens'] = $stmtItens->fetchAll(PDO::FETCH_ASSOC);

        return $cotacao;
    }
}
