<?php 
class Receita {
    private $conn;
    private $table_name = "receitas";

    public $id;
    public $nome_receita;
    public $classificacao_custo; // Novo campo
    public $modo_preparo; // Novo campo
    public $producao; // Novo campo

    // Construtor com a conexão ao banco de dados
    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para cadastrar uma nova receita
    public function cadastrar() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nome_receita, classificacao_custo, modo_preparo, producao) 
                  VALUES (:nome_receita, :classificacao_custo, :modo_preparo, :producao)";
                  
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nome_receita', $this->nome_receita);
        $stmt->bindParam(':classificacao_custo', $this->classificacao_custo);
        $stmt->bindParam(':modo_preparo', $this->modo_preparo);
        $stmt->bindParam(':producao', $this->producao);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId(); // Captura o ID da receita recém-criada
            return true;
        }

        return false;
    }

    // Método para adicionar um ingrediente a uma receita
    public function adicionarIngrediente($ingrediente_id, $quantidade, $unidade_medida, $custo) {
        $query = "INSERT INTO receita_ingredientes (receita_id, ingrediente_id, quantidade, unidade_medida, custo) 
                  VALUES (:receita_id, :ingrediente_id, :quantidade, :unidade_medida, :custo)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':receita_id', $this->id);
        $stmt->bindParam(':ingrediente_id', $ingrediente_id);
        $stmt->bindParam(':quantidade', $quantidade);
        $stmt->bindParam(':unidade_medida', $unidade_medida);
        $stmt->bindParam(':custo', $custo);

        return $stmt->execute();
    }

    // Método para atualizar um ingrediente de uma receita
    public function atualizarIngrediente($ingrediente_id, $quantidade, $unidade_medida, $custo) {
        $query = "UPDATE receita_ingredientes 
                  SET quantidade = :quantidade, unidade_medida = :unidade_medida, custo = :custo
                  WHERE receita_id = :receita_id AND ingrediente_id = :ingrediente_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantidade', $quantidade);
        $stmt->bindParam(':unidade_medida', $unidade_medida);
        $stmt->bindParam(':custo', $custo);
        $stmt->bindParam(':receita_id', $this->id);
        $stmt->bindParam(':ingrediente_id', $ingrediente_id);

        return $stmt->execute();
    }

    // Método para excluir um ingrediente de uma receita
    public function excluirIngrediente($ingrediente_id) {
        $query = "DELETE FROM receita_ingredientes WHERE receita_id = :receita_id AND ingrediente_id = :ingrediente_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':receita_id', $this->id);
        $stmt->bindParam(':ingrediente_id', $ingrediente_id);

        return $stmt->execute();
    }

    // Método para buscar uma receita por ID
    public function buscarPorId() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método para buscar todos os ingredientes de uma receita
    public function buscarIngredientes() {
        $query = "SELECT ri.ingrediente_id, i.descricao, ri.quantidade, ri.unidade_medida, ri.custo 
                  FROM receita_ingredientes ri
                  JOIN ingredientes i ON ri.ingrediente_id = i.id
                  WHERE ri.receita_id = :receita_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':receita_id', $this->id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para buscar todas as receitas
public function buscarTodos($busca = '') {
    // Define a consulta SQL
    $query = "SELECT id, nome_receita, classificacao_custo, modo_preparo, producao 
              FROM " . $this->table_name;

    // Adiciona uma cláusula de busca se houver um termo de pesquisa
    if (!empty($busca)) {
        $query .= " WHERE nome_receita LIKE :busca";
    }

    $stmt = $this->conn->prepare($query);

    // Associa o termo de busca se ele estiver presente
    if (!empty($busca)) {
        $busca = '%' . $busca . '%';
        $stmt->bindParam(':busca', $busca);
    }

    // Executa a consulta e retorna os resultados
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // Método para atualizar uma receita existente
    public function atualizar() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nome_receita = :nome_receita, classificacao_custo = :classificacao_custo, 
                      modo_preparo = :modo_preparo, producao = :producao
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nome_receita', $this->nome_receita);
        $stmt->bindParam(':classificacao_custo', $this->classificacao_custo);
        $stmt->bindParam(':modo_preparo', $this->modo_preparo);
        $stmt->bindParam(':producao', $this->producao);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Método para excluir uma receita
    public function excluir() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function buscarNomePorId($id) {
        $query = "SELECT nome_receita FROM receitas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['nome_receita'] : ''; // Retorna o nome da receita ou uma mensagem padrão
    }    
}
?>
