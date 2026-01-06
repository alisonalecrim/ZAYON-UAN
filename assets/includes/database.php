<?php
class Database {
    private $host = "sql103.infinityfree.com";  // Host do banco de dados
    private $db_name = "if0_40835529_zayon_uan";  // Nome do banco de dados
    private $username = "if0_40835529";  // Usuário do banco de dados
    private $password = "";  // Senha do banco de dados
    public $conn;  // A conexão será armazenada aqui

    // Método que faz a conexão com o banco de dados
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");  // Define a codificação para UTF-8
        } catch(PDOException $exception) {
            echo "Erro de conexão: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
